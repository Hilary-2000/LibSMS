<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Server-side access control for the library.
 *
 * Usage on a route:
 *   ->middleware("library")                         // any logged-in library user
 *   ->middleware("library:Circulation")             // needs the Circulation privilege
 *   ->middleware("library:Acquisition,Circulation") // needs ANY of the listed privileges
 *
 * Privileges live in `ladybird_smis`.`user_tbl`.`library_previleges` as a JSON list, e.g.
 * ["Acquisition","Cataloging","Circulation","Report","Settings"]. An empty list means
 * full access - the same rule the sidebar already uses to decide which menus to show.
 */
class LibraryAccess
{
    public function handle(Request $request, Closure $next, string ...$privileges): Response
    {
        if (session("school_details") == null || session("user_id") == null) {
            return $this->deny($request, "Your session has expired, Login to proceed!", "/", 401);
        }

        // Re-read the user every request so revoked access / privilege changes apply immediately,
        // not only at the next login.
        $user = DB::connection("mysql")->selectOne(
            "SELECT `library_previleges`, `auth`, `deleted`, `activated` FROM `user_tbl` WHERE `user_id` = ? AND `school_code` = ?",
            [session("user_id"), session("school_details")->school_code]
        );
        if ($user === null || (int) $user->deleted === 1 || (isset($user->activated) && (int) $user->activated === 0)) {
            session()->flush();
            return $this->deny($request, "Your account is no longer active. Contact your administrator.", "/", 401);
        }
        $auth = $user->auth;
        if ($auth != 0 && $auth != 1 && strtoupper((string) $auth) !== "LIBRARIAN") {
            session()->flush();
            return $this->deny($request, "You have no rights to access this Library Account, contact your Administrator to be allowed back in.", "/", 403);
        }

        // keep the copy used by the sidebar in sync
        if (session("user_details") !== null) {
            $details = session("user_details");
            $details->library_previleges = $user->library_previleges;
            session()->put("user_details", $details);
        }

        if (count($privileges) > 0) {
            $granted = self::parsePrivileges($user->library_previleges);
            $allowed = count($granted) === 0 || count(array_intersect($privileges, $granted)) > 0;
            if (!$allowed) {
                return $this->deny($request, "You do not have permission to access that section. Contact your administrator.", "/Dashboard", 403);
            }
        }

        return $next($request);
    }

    /** @return string[] */
    public static function parsePrivileges($raw): array
    {
        $raw = trim((string) $raw);
        // the column default is the literal string '[]' including the quotes
        $raw = trim($raw, "'");
        $list = json_decode($raw, true);
        return is_array($list) ? array_values(array_filter($list, "is_string")) : [];
    }

    private function deny(Request $request, string $message, string $redirect, int $status): Response
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(["error" => $message], $status);
        }
        session()->flash("error", $message);
        return redirect($redirect);
    }
}

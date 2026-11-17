<?php

namespace App\Services;

use Illuminate\Http\Request;

/**
 * Who did what, and when - stored per school in `library_audit_log`.
 *
 * Most entries are written automatically by the "audit" route middleware (App\Http\Middleware\Audit)
 * for every state-changing request; call AuditLog::record() directly for events that are not a
 * single request (e.g. login, an import of many books).
 */
class AuditLog
{
    /** Request fields that must never be written to the log. */
    private const HIDDEN = ["_token", "_method", "password", "user-password", "user_password", "remember-check", "import_file", "notifications", "notification_count", "token"];

    public static function record(string $action, ?string $target = null, $details = null, string $outcome = "ok", ?Request $request = null): void
    {
        try {
            if (session("school_details") == null) {
                return;
            }
            $db = SchoolSchema::schoolConnection();
            SchoolSchema::ensure("library_audit_log");
            $user = session("user_details");
            $db->table("library_audit_log")->insert([
                "date_created" => date("YmdHis"),
                "user_id" => session("user_id"),
                "user_name" => $user->fullname ?? session("fullname"),
                "action" => mb_substr($action, 0, 200),
                "target" => $target !== null ? mb_substr($target, 0, 500) : null,
                "details" => $details === null ? null : (is_string($details) ? $details : json_encode($details, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)),
                "outcome" => $outcome,
                "ip_address" => ($request ?? request())->ip(),
            ]);
        } catch (\Throwable $e) {
            // auditing must never break the action being audited
            report($e);
        }
    }

    /** The request fields worth keeping, without secrets and with long text shortened. */
    public static function safeInput(Request $request): array
    {
        $out = [];
        foreach ($request->except(self::HIDDEN) as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            if ($value === null || $value === "") {
                continue;
            }
            $value = (string) $value;
            $out[$key] = mb_strlen($value) > 300 ? mb_substr($value, 0, 300)."…" : $value;
        }
        return $out;
    }
}

<?php

namespace App\Http\Middleware;

use App\Services\AuditLog;
use App\Services\SchoolSchema;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Records the request in the school's audit log after it has been handled.
 *
 * Usage: ->middleware("audit:Checked out a book")
 * Outcome is "failed" when the controller flashed an "error" message, otherwise "ok".
 * Target = the route parameters (book id, circulation id, ...); details = the submitted form
 * fields minus passwords/tokens.
 */
class Audit
{
    public function handle(Request $request, Closure $next, string $action): Response
    {
        // capture which book is affected BEFORE the action (e.g. a deleted book is gone afterwards)
        $params = $request->route() ? $request->route()->parameters() : [];
        $book = $this->describeBook($params + $request->only(["book_ids", "book_id", "circulation_id"]));

        $response = $next($request);

        $failed = session()->has("error") || $response->getStatusCode() >= 400;
        $target = $params ? collect($params)->map(fn ($v, $k) => "$k=$v")->implode(", ") : null;
        if ($book) {
            $target = trim(($target ? $target." | " : "").$book);
        }

        AuditLog::record($action, $target, AuditLog::safeInput($request) ?: null, $failed ? "failed" : "ok", $request);

        return $response;
    }

    /** "Title" (call no) of the book a request is about, when it can be told from its ids. */
    private function describeBook(array $ids): ?string
    {
        try {
            if (session("school_details") == null) {
                return null;
            }
            $db = SchoolSchema::schoolConnection();
            $bookId = $ids["book_id"] ?? $ids["book_ids"] ?? null;
            if (!$bookId && !empty($ids["circulation_id"])) {
                $bookId = $db->selectOne("SELECT `book_id` FROM `book_circulation` WHERE `circulation_id` = ?", [$ids["circulation_id"]])->book_id ?? null;
            }
            if (!$bookId || !is_scalar($bookId)) {
                return null;
            }
            $book = $db->selectOne("SELECT `book_title`, `call_no` FROM `library_details` WHERE `book_id` = ?", [$bookId]);
            return $book ? "\"".$book->book_title."\" (".$book->call_no.")" : null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}

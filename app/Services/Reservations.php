<?php

namespace App\Services;

/**
 * Waiting list for books that are out. Reservations are made by the librarian for a student or
 * staff member, per title (any copy with the same ISBN will do).
 *
 *   waiting   -> no copy free yet
 *   ready     -> a copy was returned and is kept aside for this borrower (held_book_id);
 *                librarians get an in-app notification
 *   fulfilled -> the borrower checked out a copy
 *   cancelled -> cancelled by a librarian
 *
 * First come, first served: the oldest waiting reservation gets the next returned copy.
 */
class Reservations
{
    public const NOTIFICATION_TYPE = 2; // library_notifications.notification_type for "hold this copy"

    private static function db()
    {
        $db = SchoolSchema::schoolConnection();
        SchoolSchema::ensure("library_reservations");
        return $db;
    }

    /** Open (waiting or ready) reservation of this borrower for this title, if any. */
    public static function openFor(string $bookKey, string $type, string $borrowerId): ?object
    {
        return self::db()->selectOne(
            "SELECT * FROM `library_reservations` WHERE `book_key` = ? AND `borrower_type` = ? AND `borrower_id` = ? AND `status` IN ('waiting','ready') ORDER BY `reservation_id` LIMIT 1",
            [$bookKey, $type, $borrowerId]
        );
    }

    public static function create(object $book, string $type, string $borrowerId, ?string $comments): int
    {
        return (int) self::db()->table("library_reservations")->insertGetId([
            "book_key" => BookCode::titleKey($book),
            "book_title" => $book->book_title,
            "borrower_type" => $type,
            "borrower_id" => $borrowerId,
            "status" => "waiting",
            "date_reserved" => date("YmdHis"),
            "reserved_by" => session("user_id"),
            "comments" => $comments,
        ]);
    }

    /** The reservation this copy is being held for, if any. */
    public static function holdOn($bookId): ?object
    {
        return self::db()->selectOne("SELECT * FROM `library_reservations` WHERE `status` = 'ready' AND `held_book_id` = ? LIMIT 1", [(string) $bookId]);
    }

    /** Number of people waiting for this title. */
    public static function waitingCount(object $book): int
    {
        return (int) self::db()->selectOne("SELECT COUNT(*) AS n FROM `library_reservations` WHERE `book_key` = ? AND `status` = 'waiting'", [BookCode::titleKey($book)])->n;
    }

    /**
     * A copy came back: hold it for the first person waiting for this title.
     * Returns the reservation now "ready", or null when nobody is waiting.
     */
    public static function onReturned(object $book): ?object
    {
        $db = self::db();
        if (self::holdOn($book->book_id)) {
            return null; // already held for someone
        }
        $next = $db->selectOne("SELECT * FROM `library_reservations` WHERE `book_key` = ? AND `status` = 'waiting' ORDER BY `reservation_id` LIMIT 1", [BookCode::titleKey($book)]);
        if (!$next) {
            return null;
        }
        $db->update("UPDATE `library_reservations` SET `status` = 'ready', `held_book_id` = ?, `date_ready` = ? WHERE `reservation_id` = ? AND `status` = 'waiting'",
            [(string) $book->book_id, date("YmdHis"), $next->reservation_id]);

        $name = Borrowers::name($next->borrower_type, $next->borrower_id);
        $db->insert("INSERT INTO `library_notifications` (`notification_title`,`notification_content`,`notification_action`,`date_created`,`book_id`,`notification_type`) VALUES (?,?,'[]',?,?,?)", [
            "Reserved book returned: keep \"".$book->book_title."\" aside",
            "The book <b>\"".e($book->book_title)."\"</b> (call number ".e($book->call_no).", label ".e(BookCode::encode($book->book_id)).") has been returned and is reserved for <b>".e($name)."</b> (".e($next->borrower_type)."). Keep it aside until they collect it. <a class='text-decoration-underline text-reset' href='/Circulation/Reservations?status=ready'>View reservations</a>.",
            date("YmdHis"), (string) $book->book_id, self::NOTIFICATION_TYPE,
        ]);
        $next->status = "ready";
        $next->held_book_id = (string) $book->book_id;
        $next->borrower_name = $name;
        return $next;
    }

    /**
     * Before a check-out: a copy held for someone else may not be lent.
     * Returns an error message, or null when the check-out may go ahead.
     */
    public static function blockReason(object $book, string $type, string $borrowerId): ?string
    {
        $hold = self::holdOn($book->book_id);
        if ($hold && !($hold->borrower_type === $type && (string) $hold->borrower_id === (string) $borrowerId)) {
            return "This copy is reserved for ".Borrowers::name($hold->borrower_type, $hold->borrower_id)." (".$hold->borrower_type."). Lend it to them, or cancel the reservation first.";
        }
        return null;
    }

    /** After a check-out: close the borrower's own reservation for this title and release the hold. */
    public static function onCheckedOut(object $book, string $type, string $borrowerId): void
    {
        $mine = self::openFor(BookCode::titleKey($book), $type, $borrowerId);
        if ($mine) {
            self::close($mine, "fulfilled");
        }
    }

    public static function close(object $reservation, string $status): void
    {
        $db = self::db();
        $db->update("UPDATE `library_reservations` SET `status` = ?, `date_closed` = ?, `closed_by` = ? WHERE `reservation_id` = ?",
            [$status, date("YmdHis"), session("user_id"), $reservation->reservation_id]);
        if ($reservation->status === "ready" && $reservation->held_book_id) {
            $db->delete("DELETE FROM `library_notifications` WHERE `book_id` = ? AND `notification_type` = ?", [$reservation->held_book_id, self::NOTIFICATION_TYPE]);
            // a cancelled hold frees the copy for the next person in line
            if ($status === "cancelled") {
                $book = $db->selectOne("SELECT * FROM `library_details` WHERE `book_id` = ?", [$reservation->held_book_id]);
                if ($book && (int) $book->availability_status === 1) {
                    self::onReturned($book);
                }
            }
        }
    }
}

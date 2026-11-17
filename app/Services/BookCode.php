<?php

namespace App\Services;

use Illuminate\Database\ConnectionInterface;

/**
 * The code printed on each copy's barcode label, and resolving whatever a scanner reads back.
 *
 * Every copy (one row of library_details) gets "LB" + its book_id padded to 6 digits, e.g. LB000012.
 * Code128 is used so any ordinary USB / Bluetooth barcode scanner can read it. A scanner simply
 * "types" the code followed by Enter, so scanning into any focused search box works.
 */
class BookCode
{
    public const PREFIX = "LB";

    public static function encode($bookId): string
    {
        return self::PREFIX.str_pad((string) (int) $bookId, 6, "0", STR_PAD_LEFT);
    }

    /**
     * Find the copy a scanned / typed code refers to.
     * Accepts the label code (LB000012), a bare book id (12), a call number, or an ISBN when exactly
     * one copy has it. Returns the library_details row or null.
     */
    public static function resolve(ConnectionInterface $db, ?string $code): ?object
    {
        $code = strtoupper(trim((string) $code));
        if ($code === "") {
            return null;
        }
        if (preg_match('/^'.self::PREFIX.'0*(\d+)$/', $code, $m) || preg_match('/^(\d{1,9})$/', $code, $m)) {
            $book = $db->selectOne("SELECT * FROM `library_details` WHERE `book_id` = ?", [(int) $m[1]]);
            if ($book) {
                return $book;
            }
        }
        $book = $db->selectOne("SELECT * FROM `library_details` WHERE `call_no` = ? ORDER BY `book_id` DESC LIMIT 1", [$code]);
        if ($book) {
            return $book;
        }
        $copies = $db->select("SELECT * FROM `library_details` WHERE `isbn_13` = ? OR `isbn_10` = ? LIMIT 2", [$code, $code]);
        return count($copies) === 1 ? $copies[0] : null;
    }

    /** Title-level key used by reservations: copies with the same ISBN are interchangeable. */
    public static function titleKey(object $book): string
    {
        $isbn = trim((string) ($book->isbn_13 ?? ""));
        return $isbn !== "" ? $isbn : "id:".$book->book_id;
    }
}

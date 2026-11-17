<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Creates "book is due for check-in" notifications for overdue loans.
 *
 * This used to run inside the notification middleware on EVERY page view for every user
 * (one query + one INSERT per overdue book). It now runs:
 *   - from the scheduler: `php artisan library:notify-overdue` every 15 minutes (all schools), and
 *   - as a fallback from the middleware, at most once every THROTTLE_MINUTES per school database,
 *     so notifications still appear on servers where cron is not set up.
 */
class OverdueNotifications
{
    const THROTTLE_MINUTES = 10;

    /** Run the scan for this school unless it already ran in the last THROTTLE_MINUTES. */
    public static function generateIfDue(string $database): void
    {
        if (Cache::add(self::cacheKey($database), 1, now()->addMinutes(self::THROTTLE_MINUTES))) {
            self::generate($database);
        }
    }

    /** Scan one school database now; returns how many notifications were created. */
    public static function generate(string $database): int
    {
        date_default_timezone_set('Africa/Nairobi');
        // point the per-school connection at this database (reconnect only if it changed)
        if (config('database.connections.mysql2.database') !== $database) {
            config(['database.connections.mysql2.database' => $database]);
            DB::purge('mysql2');
        }
        $db = DB::connection('mysql2');
        $inserted = 0;

        // first get all books that are overdue
        $select = $db->select("SELECT BC.circulation_id, BC.date_checked_out ,BC.book_call_number,BC.user_checked_out, BC.book_id, BC.expected_return_date, BC.user_borrowing,
                                CASE
                                    WHEN BC.user_borrowing = 'student' THEN CONCAT(SD.first_name, ' ', SD.second_name,' ',SD.surname)
                                    WHEN BC.user_borrowing = 'staff' THEN UT.fullname
                                    ELSE NULL
                                END AS borrower_name,
                                LD.book_title,LD.book_author,LD.isbn_13
                            FROM `book_circulation` AS BC
                            LEFT JOIN `student_data` AS SD ON BC.user_checked_out = SD.adm_no AND BC.user_borrowing = 'student'
                            LEFT JOIN `ladybird_smis`.`user_tbl` AS UT ON BC.user_checked_out = UT.user_id AND BC.user_borrowing = 'staff'
                            LEFT JOIN `library_details` AS LD ON BC.book_id = LD.book_id
                            WHERE `return_status` = 0 AND `expected_return_date` < ? ",[date("YmdHis")]);
        // return $select;

        // proceed and save the notification in  the notification table
        for ($index=0; $index < count($select); $index++) {
            // Everything that comes from user-entered data is HTML-escaped: notification_content is rendered
            // unescaped ({!! !!}) because it contains our own links, so a book title like <script> must not run.
            $book_title = e($select[$index]->book_title);
            $call_number = e($select[$index]->book_call_number);
            $borrower_name = e(ucwords(strtolower((string) $select[$index]->borrower_name)));
            $borrower_type = e($select[$index]->user_borrowing);
            $book_id_url = rawurlencode((string) $select[$index]->book_id);
            $circulation_id_url = rawurlencode((string) $select[$index]->circulation_id);
            $borrower_id_url = rawurlencode((string) $select[$index]->user_checked_out);
            $notification_title = "Book \"".$select[$index]->book_title."\" is due for Check-In";
            $stats_link = $select[$index]->user_borrowing == 'student' ? '/Circulation/Stats/View/Student/'.$borrower_id_url : '/Circulation/Stats/View/Staff/'.$borrower_id_url;
            $notification_content = "The book <b>\"".$book_title."\"</b> with a call number of <a class='text-decoration-underline text-reset' href='/Acquisitions/Book-details/".$book_id_url."' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-original-title='Click to view book details!'>".$call_number."</a>, borrowed by <a class='text-decoration-underline text-reset' href='".$stats_link."' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-original-title='Click to view borrower borrowing stats!'>".$borrower_name."</a> a ".$borrower_type." is due for check-in. Its expected return date was <b>".date("D dS M Y : h:i:sA",strtotime($select[$index]->expected_return_date))."</b>. Click here to <a class='text-decoration-underline text-reset' href='/Circulation/View/check-out/".$book_id_url."/".$circulation_id_url."'>Check-In</a>.";
            $date_created = date("YmdHis");
            $book_id = $select[$index]->book_id;

            // this insert statement will check if a record of the same book is present, if so, it won`t insert the record.
            $inserted += $db->affectingStatement("INSERT INTO `library_notifications` (`notification_title`,`notification_content`,`notification_action`,`date_created`,`book_id`)
                                    SELECT ?, ?, '[]',?,?
                                    WHERE NOT EXISTS (SELECT 1 FROM `library_notifications` WHERE `book_id` = ?);",[$notification_title, $notification_content, $date_created, $book_id, $book_id]);
        }


        Cache::put(self::cacheKey($database), 1, now()->addMinutes(self::THROTTLE_MINUTES));
        return $inserted;
    }

    private static function cacheKey(string $database): string
    {
        return "overdue_scan:".$database;
    }
}

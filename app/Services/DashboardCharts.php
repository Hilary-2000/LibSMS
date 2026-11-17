<?php

namespace App\Services;

/**
 * Data for the dashboard charts, over the last 12 months.
 * Dates in book_circulation are YmdHis strings, so months are grouped with LEFT(date, 6) = Ym and
 * days compared with LEFT(date, 8) = Ymd. A book is "late" when it came back after its due DAY
 * (expected_return_date is stored as midnight of the due day).
 */
class DashboardCharts
{
    public static function build(): array
    {
        $db = SchoolSchema::schoolConnection();
        date_default_timezone_set('Africa/Nairobi');

        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $months[] = date("Ym", strtotime("first day of -$i month"));
        }
        $from = $months[0]."01000000";
        $today = date("Ymd");

        $byMonth = function (array $rows) use ($months) {
            $map = array_fill_keys($months, 0);
            foreach ($rows as $r) {
                if (isset($map[$r->m])) {
                    $map[$r->m] = (int) $r->n;
                }
            }
            return array_values($map);
        };

        $checkedOut = $byMonth($db->select("SELECT LEFT(`date_checked_out`, 6) AS m, COUNT(*) AS n FROM `book_circulation` WHERE `date_checked_out` >= ? GROUP BY m", [$from]));
        $returned = $byMonth($db->select("SELECT LEFT(`return_date`, 6) AS m, COUNT(*) AS n FROM `book_circulation` WHERE `return_status` = 1 AND `return_date` >= ? GROUP BY m", [$from]));
        // late or overdue, by the month the book was due
        $late = $byMonth($db->select(
            "SELECT LEFT(`expected_return_date`, 6) AS m, COUNT(*) AS n FROM `book_circulation`
             WHERE `expected_return_date` >= ? AND LEFT(`expected_return_date`, 8) < ?
               AND ((`return_status` = 1 AND LEFT(`return_date`, 8) > LEFT(`expected_return_date`, 8)) OR `return_status` = 0)
             GROUP BY m", [$from, $today]));

        $topBooks = $db->select(
            "SELECT MAX(d.`book_title`) AS title, COUNT(*) AS n FROM `book_circulation` c
             LEFT JOIN `library_details` d ON d.`book_id` = c.`book_id`
             GROUP BY COALESCE(NULLIF(c.`book_isbn`, ''), CONCAT('id:', c.`book_id`))
             ORDER BY n DESC LIMIT 10");

        $classes = $db->select(
            "SELECT s.`stud_class` AS class, COUNT(*) AS n FROM `book_circulation` c
             JOIN `student_data` s ON s.`adm_no` = c.`user_checked_out`
             WHERE c.`user_borrowing` = 'student' AND c.`date_checked_out` >= ?
             GROUP BY s.`stud_class` ORDER BY n DESC", [$from]);

        $types = ["student" => 0, "staff" => 0];
        foreach ($db->select("SELECT `user_borrowing` AS t, COUNT(*) AS n FROM `book_circulation` WHERE `date_checked_out` >= ? GROUP BY `user_borrowing`", [$from]) as $r) {
            if (isset($types[$r->t])) {
                $types[$r->t] = (int) $r->n;
            }
        }
        $overdueNow = (int) $db->selectOne("SELECT COUNT(*) AS n FROM `book_circulation` WHERE `return_status` = 0 AND LEFT(`expected_return_date`, 8) < ?", [$today])->n;

        return [
            "months" => array_map(fn ($m) => date("M Y", strtotime($m."01")), $months),
            "checked_out" => $checkedOut,
            "returned" => $returned,
            "late" => $late,
            "top_books" => ["labels" => array_map(fn ($b) => mb_strimwidth((string) ($b->title ?? "Unknown"), 0, 40, "…"), $topBooks), "values" => array_map(fn ($b) => (int) $b->n, $topBooks)],
            "classes" => ["labels" => array_map(fn ($c) => Borrowers::className($c->class), $classes), "values" => array_map(fn ($c) => (int) $c->n, $classes)],
            "borrower_types" => ["labels" => ["Students", "Staff"], "values" => [$types["student"], $types["staff"]]],
            "overdue_now" => $overdueNow,
            "total_12m" => array_sum($checkedOut),
        ];
    }
}

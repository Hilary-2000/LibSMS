<?php

namespace App\Services;

/**
 * Everything on the Circulation > Statistics page (also used by the Excel export and the term report).
 *
 * Periods come from the school's academic_calendar (terms). Dates in the library tables are YmdHis
 * strings, so periods are compared as strings and days with LEFT(date, 8) = Ymd.
 *
 * Definitions (kept identical everywhere):
 *   loans            check-outs made in the period
 *   on-time rate     of the loans DUE in the period (and already due), the share returned on or before the due day
 *   late             due in the period, returned after the due day - or still not returned
 *   overdue now      not returned and past the due day, today (not period based)
 *   participation    current students (activated, not deleted, not alumni) with at least one loan / all current students
 *   loan length      days from check-out to return, for loans returned in the period
 */
class CirculationInsights
{
    private $db;
    private string $today;     // Ymd

    public function __construct()
    {
        date_default_timezone_set('Africa/Nairobi');
        $this->db = SchoolSchema::schoolConnection();
        $this->today = date("Ymd");
    }

    // ------------------------------------------------------------------ periods

    /** @return array{key:string,label:string,from:string,to:string} from/to as Ymd */
    public function period(string $key, ?string $from = null, ?string $to = null): array
    {
        $terms = $this->terms();
        $current = null;
        foreach ($terms as $t) {
            if ($t["from"] <= $this->today && $this->today <= $t["to"]) {
                $current = $t;
            }
        }
        if (!$current) {
            $past = array_values(array_filter($terms, fn ($t) => $t["to"] < $this->today));
            $current = $past ? end($past) : ["label" => "Last 4 months", "from" => date("Ymd", strtotime("-4 months")), "to" => $this->today];
        }

        switch ($key) {
            case "last_term":
                return ["key" => $key] + $this->before($current, $terms);
            case "this_year":
                $year = substr($current["from"], 0, 4);
                $starts = array_map(fn ($t) => $t["from"], array_filter($terms, fn ($t) => substr($t["from"], 0, 4) === $year));
                return ["key" => $key, "label" => "This year (".$year.")", "from" => $starts ? min($starts) : $year."0101", "to" => min($this->today, $year."1231")];
            case "custom":
                $f = $from && strtotime($from) ? date("Ymd", strtotime($from)) : date("Ymd", strtotime("-30 days"));
                $t = $to && strtotime($to) ? date("Ymd", strtotime($to)) : $this->today;
                if ($f > $t) {
                    [$f, $t] = [$t, $f];
                }
                return ["key" => $key, "label" => date("d M Y", strtotime($f))." – ".date("d M Y", strtotime($t)), "from" => $f, "to" => $t];
            default:
                return ["key" => "this_term"] + $current;
        }
    }

    /** The period compared against: the previous term, or the same number of days just before. */
    public function previous(array $period): array
    {
        if ($period["key"] === "this_year") {
            return ["label" => "same time last year", "from" => (substr($period["from"], 0, 4) - 1).substr($period["from"], 4), "to" => (substr($period["to"], 0, 4) - 1).substr($period["to"], 4)];
        }
        if (in_array($period["key"], ["this_term", "last_term"], true)) {
            $prev = $this->before($period, $this->terms());
            return ["label" => $prev["label"], "from" => $prev["from"], "to" => $prev["to"]];
        }
        return $this->sameLengthBefore($period) + ["label" => "previous ".$this->days($period)." days"];
    }

    private function terms(): array
    {
        $out = [];
        foreach ($this->db->select("SELECT * FROM `academic_calendar` ORDER BY `start_time`") as $row) {
            if (!strtotime((string) $row->start_time) || !strtotime((string) $row->end_time)) {
                continue;
            }
            $out[] = [
                "label" => ucwords(strtolower(str_replace("_", " ", (string) $row->term))).(isset($row->academic_year) ? " ".$row->academic_year : ""),
                "from" => date("Ymd", strtotime($row->start_time)),
                "to" => date("Ymd", strtotime($row->end_time)),
            ];
        }
        return $out;
    }

    private function before(array $period, array $terms): array
    {
        $prev = null;
        foreach ($terms as $t) {
            if ($t["to"] < $period["from"]) {
                $prev = $t;
            }
        }
        return $prev ?: ($this->sameLengthBefore($period) + ["label" => "Previous ".$this->days($period)." days"]);
    }

    private function sameLengthBefore(array $p): array
    {
        $days = $this->days($p);
        $to = date("Ymd", strtotime($p["from"]." -1 day"));
        return ["from" => date("Ymd", strtotime($to." -".($days - 1)." days")), "to" => $to];
    }

    private function days(array $p): int
    {
        return (int) round((strtotime($p["to"]) - strtotime($p["from"])) / 86400) + 1;
    }

    // ------------------------------------------------------------------ helpers

    private function bounds(array $p): array
    {
        return [$p["from"]."000000", $p["to"]."235959"];
    }

    /** current students: activated, not deleted, not alumni (class starting with "-") */
    private function currentStudentsWhere(string $alias = "s"): string
    {
        return "($alias.`deleted` IS NULL OR $alias.`deleted` = 0) AND ($alias.`activated` IS NULL OR $alias.`activated` = 1) AND $alias.`stud_class` NOT LIKE '-%'";
    }

    // ------------------------------------------------------------------ 1. headline tiles

    public function tiles(array $p): array
    {
        [$a, $b] = $this->bounds($p);
        $dueTo = min($p["to"], date("Ymd", strtotime($this->today." -1 day")));

        $loans = (int) $this->db->selectOne("SELECT COUNT(*) AS n FROM `book_circulation` WHERE `date_checked_out` BETWEEN ? AND ?", [$a, $b])->n;
        $due = $this->db->selectOne(
            "SELECT SUM(`return_status` = 1 AND LEFT(`return_date`, 8) <= LEFT(`expected_return_date`, 8)) AS on_time, COUNT(*) AS total
             FROM `book_circulation` WHERE LEFT(`expected_return_date`, 8) BETWEEN ? AND ?", [$p["from"], $dueTo]);
        $students = (int) $this->db->selectOne("SELECT COUNT(*) AS n FROM `student_data` s WHERE ".$this->currentStudentsWhere())->n;
        $readers = (int) $this->db->selectOne(
            "SELECT COUNT(DISTINCT c.`user_checked_out`) AS n FROM `book_circulation` c JOIN `student_data` s ON s.`adm_no` = c.`user_checked_out`
             WHERE c.`user_borrowing` = 'student' AND c.`date_checked_out` BETWEEN ? AND ? AND ".$this->currentStudentsWhere(), [$a, $b])->n;
        $length = $this->db->selectOne(
            "SELECT AVG(DATEDIFF(STR_TO_DATE(LEFT(`return_date`, 8), '%Y%m%d'), STR_TO_DATE(LEFT(`date_checked_out`, 8), '%Y%m%d'))) AS d
             FROM `book_circulation` WHERE `return_status` = 1 AND `return_date` BETWEEN ? AND ?", [$a, $b])->d;

        return [
            "loans" => $loans,
            "on_time_rate" => (int) $due->total > 0 ? round(100 * (int) $due->on_time / (int) $due->total) : null,
            "due_count" => (int) $due->total,
            "participation" => $students > 0 ? round(100 * $readers / $students) : null,
            "readers" => $readers,
            "students" => $students,
            "loan_days" => $length !== null ? round((float) $length, 1) : null,
        ];
    }

    public function overdueNow(): int
    {
        return (int) $this->db->selectOne("SELECT COUNT(*) AS n FROM `book_circulation` WHERE `return_status` = 0 AND LEFT(`expected_return_date`, 8) < ?", [$this->today])->n;
    }

    // ------------------------------------------------------------------ 2. who's reading

    public function topReaders(array $p, int $limit = 10): array
    {
        [$a, $b] = $this->bounds($p);
        return array_map(fn ($r) => [
            "adm_no" => $r->adm_no,
            "name" => ucwords(strtolower(trim($r->first_name." ".$r->second_name))),
            "class" => Borrowers::className($r->stud_class),
            "loans" => (int) $r->n,
        ], $this->db->select(
            "SELECT c.`user_checked_out` AS adm_no, MAX(s.`first_name`) AS first_name, MAX(s.`second_name`) AS second_name, MAX(s.`stud_class`) AS stud_class, COUNT(*) AS n
             FROM `book_circulation` c JOIN `student_data` s ON s.`adm_no` = c.`user_checked_out`
             WHERE c.`user_borrowing` = 'student' AND c.`date_checked_out` BETWEEN ? AND ?
             GROUP BY c.`user_checked_out` ORDER BY n DESC, adm_no LIMIT ".(int) $limit, [$a, $b]));
    }

    /** Loans per student, by class (fair across class sizes), plus totals. */
    public function byClass(array $p): array
    {
        [$a, $b] = $this->bounds($p);
        $sizes = [];
        foreach ($this->db->select("SELECT s.`stud_class` AS class, COUNT(*) AS n FROM `student_data` s WHERE ".$this->currentStudentsWhere()." GROUP BY s.`stud_class`") as $r) {
            $sizes[(string) $r->class] = (int) $r->n;
        }
        $loans = [];
        $readers = [];
        foreach ($this->db->select(
            "SELECT s.`stud_class` AS class, COUNT(*) AS n, COUNT(DISTINCT c.`user_checked_out`) AS readers
             FROM `book_circulation` c JOIN `student_data` s ON s.`adm_no` = c.`user_checked_out`
             WHERE c.`user_borrowing` = 'student' AND c.`date_checked_out` BETWEEN ? AND ? AND ".$this->currentStudentsWhere()."
             GROUP BY s.`stud_class`", [$a, $b]) as $r) {
            $loans[(string) $r->class] = (int) $r->n;
            $readers[(string) $r->class] = (int) $r->readers;
        }
        $rows = [];
        foreach ($sizes as $class => $size) {
            $n = $loans[$class] ?? 0;
            $rows[] = [
                "class" => Borrowers::className($class), "students" => $size, "loans" => $n,
                "per_student" => $size ? round($n / $size, 2) : 0,
                "participation" => $size ? round(100 * ($readers[$class] ?? 0) / $size) : 0,
            ];
        }
        usort($rows, fn ($x, $y) => strnatcasecmp($x["class"], $y["class"]));
        return $rows;
    }

    public function borrowerTypes(array $p): array
    {
        [$a, $b] = $this->bounds($p);
        $out = ["student" => 0, "staff" => 0];
        foreach ($this->db->select("SELECT `user_borrowing` AS t, COUNT(*) AS n FROM `book_circulation` WHERE `date_checked_out` BETWEEN ? AND ? GROUP BY `user_borrowing`", [$a, $b]) as $r) {
            if (isset($out[$r->t])) {
                $out[$r->t] = (int) $r->n;
            }
        }
        return $out;
    }

    // ------------------------------------------------------------------ 3. what's being read

    public function topTitles(array $p, int $limit = 10): array
    {
        [$a, $b] = $this->bounds($p);
        return array_map(fn ($r) => ["title" => (string) ($r->title ?? "Unknown"), "book_id" => $r->book_id, "loans" => (int) $r->n], $this->db->select(
            "SELECT MAX(d.`book_title`) AS title, MAX(c.`book_id`) AS book_id, COUNT(*) AS n
             FROM `book_circulation` c LEFT JOIN `library_details` d ON d.`book_id` = c.`book_id`
             WHERE c.`date_checked_out` BETWEEN ? AND ?
             GROUP BY COALESCE(NULLIF(c.`book_isbn`, ''), CONCAT('id:', c.`book_id`))
             ORDER BY n DESC LIMIT ".(int) $limit, [$a, $b]));
    }

    public function topCategories(array $p, int $limit = 8): array
    {
        [$a, $b] = $this->bounds($p);
        return array_map(fn ($r) => ["category" => ucwords(strtolower((string) $r->category)), "loans" => (int) $r->n], $this->db->select(
            "SELECT COALESCE(NULLIF(TRIM(SUBSTRING_INDEX(d.`book_category`, ',', 1)), ''), 'Uncategorised') AS category, COUNT(*) AS n
             FROM `book_circulation` c LEFT JOIN `library_details` d ON d.`book_id` = c.`book_id`
             WHERE c.`date_checked_out` BETWEEN ? AND ?
             GROUP BY category ORDER BY n DESC LIMIT ".(int) $limit, [$a, $b]));
    }

    /** Copies (not lost) nobody borrowed in the last 12 months, oldest first. */
    public function neverBorrowed(int $limit = 50): array
    {
        $since = date("YmdHis", strtotime("-365 days"));
        $where = "d.`lost_status` = 0 AND NOT EXISTS (SELECT 1 FROM `book_circulation` c WHERE c.`book_id` = d.`book_id` AND c.`date_checked_out` >= ?)";
        $count = (int) $this->db->selectOne("SELECT COUNT(*) AS n FROM `library_details` d WHERE $where", [$since])->n;
        $rows = $this->db->select(
            "SELECT d.`book_id`, d.`book_title`, d.`call_no`, d.`date_recorded`,
                    (SELECT MAX(c2.`date_checked_out`) FROM `book_circulation` c2 WHERE c2.`book_id` = d.`book_id`) AS last_borrowed
             FROM `library_details` d WHERE $where ORDER BY d.`date_recorded`, d.`book_id` LIMIT ".(int) $limit, [$since]);
        return ["count" => $count, "rows" => $rows];
    }

    /** Share of copies (not lost) borrowed at least once in the last 12 months. */
    public function collectionUse(): array
    {
        $since = date("YmdHis", strtotime("-365 days"));
        $copies = (int) $this->db->selectOne("SELECT COUNT(*) AS n FROM `library_details` WHERE `lost_status` = 0")->n;
        $used = (int) $this->db->selectOne(
            "SELECT COUNT(DISTINCT c.`book_id`) AS n FROM `book_circulation` c JOIN `library_details` d ON d.`book_id` = c.`book_id`
             WHERE d.`lost_status` = 0 AND c.`date_checked_out` >= ?", [$since])->n;
        return ["copies" => $copies, "used" => $used, "rate" => $copies ? round(100 * $used / $copies) : null];
    }

    // ------------------------------------------------------------------ 4. late returns

    private function lateExpr(): string
    {
        return "((c.`return_status` = 1 AND LEFT(c.`return_date`, 8) > LEFT(c.`expected_return_date`, 8)) OR c.`return_status` = 0)";
    }

    public function lateByClass(array $p): array
    {
        $dueTo = min($p["to"], date("Ymd", strtotime($this->today." -1 day")));
        $rows = array_map(fn ($r) => [
            "class" => Borrowers::className($r->class), "due" => (int) $r->due, "late" => (int) $r->late,
            "rate" => (int) $r->due ? round(100 * (int) $r->late / (int) $r->due) : 0,
        ], $this->db->select(
            "SELECT s.`stud_class` AS class, COUNT(*) AS due, SUM(".$this->lateExpr().") AS late
             FROM `book_circulation` c JOIN `student_data` s ON s.`adm_no` = c.`user_checked_out`
             WHERE c.`user_borrowing` = 'student' AND LEFT(c.`expected_return_date`, 8) BETWEEN ? AND ?
             GROUP BY s.`stud_class`", [$p["from"], $dueTo]));
        usort($rows, fn ($x, $y) => strnatcasecmp($x["class"], $y["class"]));
        return $rows;
    }

    /** Borrowers with 2+ late returns among loans due in the period. */
    public function repeatLate(array $p, int $limit = 15): array
    {
        $dueTo = min($p["to"], date("Ymd", strtotime($this->today." -1 day")));
        $rows = $this->db->select(
            "SELECT c.`user_borrowing` AS type, c.`user_checked_out` AS id, COUNT(*) AS late,
                    SUM(c.`return_status` = 0) AS still_out,
                    AVG(DATEDIFF(STR_TO_DATE(IF(c.`return_status` = 1, LEFT(c.`return_date`, 8), ?), '%Y%m%d'), STR_TO_DATE(LEFT(c.`expected_return_date`, 8), '%Y%m%d'))) AS avg_days
             FROM `book_circulation` c
             WHERE LEFT(c.`expected_return_date`, 8) BETWEEN ? AND ? AND ".$this->lateExpr()."
             GROUP BY c.`user_borrowing`, c.`user_checked_out` HAVING late >= 2
             ORDER BY late DESC, avg_days DESC LIMIT ".(int) $limit, [$this->today, $p["from"], $dueTo]);
        $classes = [];
        foreach ($rows as $r) {
            if ($r->type === "student") {
                $classes[] = $r->id;
            }
        }
        $classOf = [];
        if ($classes) {
            foreach ($this->db->select("SELECT `adm_no`, `stud_class` FROM `student_data` WHERE `adm_no` IN (".implode(",", array_fill(0, count($classes), "?")).")", $classes) as $s) {
                $classOf[(string) $s->adm_no] = Borrowers::className($s->stud_class);
            }
        }
        return array_map(fn ($r) => [
            "type" => $r->type, "id" => $r->id, "name" => Borrowers::name((string) $r->type, $r->id),
            "class" => $r->type === "student" ? ($classOf[(string) $r->id] ?? "") : "Staff",
            "late" => (int) $r->late, "still_out" => (int) $r->still_out, "avg_days" => round((float) $r->avg_days, 1),
        ], $rows);
    }

    /** Loans due in each week (or month, for periods over ~6 months): returned on time vs late / still out. */
    public function weeklyDue(array $p): array
    {
        $dueTo = min($p["to"], date("Ymd", strtotime($this->today." -1 day")));
        $monthly = $this->days($p) > 190;
        $bucket = $monthly
            ? "LEFT(c.`expected_return_date`, 6)"
            : "YEARWEEK(STR_TO_DATE(LEFT(c.`expected_return_date`, 8), '%Y%m%d'), 1)";
        $counts = [];
        foreach ($this->db->select(
            "SELECT $bucket AS k, COUNT(*) AS due, SUM(".$this->lateExpr().") AS late
             FROM `book_circulation` c WHERE LEFT(c.`expected_return_date`, 8) BETWEEN ? AND ? GROUP BY k", [$p["from"], $dueTo]) as $r) {
            $counts[(string) $r->k] = [(int) $r->due, (int) $r->late];
        }
        $labels = $onTime = $late = [];
        if ($monthly) {
            for ($d = strtotime(substr($p["from"], 0, 6)."01"); $d <= strtotime($dueTo); $d = strtotime("+1 month", $d)) {
                [$due, $l] = $counts[date("Ym", $d)] ?? [0, 0];
                $labels[] = date("M Y", $d);
                $onTime[] = $due - $l;
                $late[] = $l;
            }
        } else {
            // one bar per week (Monday start), including empty weeks
            for ($d = strtotime("monday this week", strtotime($p["from"])); $d <= strtotime($dueTo); $d = strtotime("+1 week", $d)) {
                [$due, $l] = $counts[date("oW", $d)] ?? [0, 0];
                $labels[] = date("d M", $d);
                $onTime[] = $due - $l;
                $late[] = $l;
            }
        }
        return ["labels" => $labels, "on_time" => $onTime, "late" => $late, "unit" => $monthly ? "month" : "week"];
    }

    // ------------------------------------------------------------------ 5. when the library is busy

    /** Check-outs + returns by weekday (Mon..Sun) and hour (06..19). */
    public function busyHours(array $p): array
    {
        [$a, $b] = $this->bounds($p);
        $grid = [];
        $sql = "SELECT WEEKDAY(STR_TO_DATE(LEFT(%1\$s, 8), '%%Y%%m%%d')) AS wd, CAST(SUBSTRING(%1\$s, 9, 2) AS UNSIGNED) AS hr, COUNT(*) AS n
                FROM `book_circulation` WHERE %2\$s BETWEEN ? AND ? GROUP BY wd, hr";
        foreach ([
            sprintf($sql, "`date_checked_out`", "`date_checked_out`"),
            sprintf($sql, "`return_date`", "`return_status` = 1 AND `return_date`"),
        ] as $q) {
            foreach ($this->db->select($q, [$a, $b]) as $r) {
                if ($r->wd === null) {
                    continue;
                }
                $hr = max(6, min(19, (int) $r->hr)); // outside 06-19 is folded into the first/last hour
                $grid[(int) $r->wd][$hr] = ($grid[(int) $r->wd][$hr] ?? 0) + (int) $r->n;
            }
        }
        $days = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
        $series = [];
        foreach ($days as $i => $day) {
            $data = [];
            for ($h = 6; $h <= 19; $h++) {
                $data[] = ["x" => sprintf("%02d:00", $h), "y" => $grid[$i][$h] ?? 0];
            }
            $series[] = ["name" => $day, "data" => $data];
        }
        $busiest = null;
        foreach ($grid as $wd => $hours) {
            foreach ($hours as $h => $n) {
                if (!$busiest || $n > $busiest["n"]) {
                    $busiest = ["n" => $n, "label" => $days[$wd]." ".sprintf("%02d:00", $h)."–".sprintf("%02d:00", $h + 1)];
                }
            }
        }
        return ["series" => array_reverse($series), "busiest" => $busiest]; // Mon at the top
    }

    // ------------------------------------------------------------------ everything

    public function all(string $periodKey, ?string $from = null, ?string $to = null): array
    {
        $p = $this->period($periodKey, $from, $to);
        $prev = $this->previous($p);
        return [
            "period" => $p,
            "previous" => $prev,
            "tiles" => $this->tiles($p),
            "tiles_prev" => $this->tiles($prev),
            "overdue_now" => $this->overdueNow(),
            "top_readers" => $this->topReaders($p),
            "by_class" => $this->byClass($p),
            "borrower_types" => $this->borrowerTypes($p),
            "top_titles" => $this->topTitles($p),
            "top_categories" => $this->topCategories($p),
            "never_borrowed" => $this->neverBorrowed(),
            "collection_use" => $this->collectionUse(),
            "late_by_class" => $this->lateByClass($p),
            "repeat_late" => $this->repeatLate($p),
            "weekly_due" => $this->weeklyDue($p),
            "busy" => $this->busyHours($p),
        ];
    }
}

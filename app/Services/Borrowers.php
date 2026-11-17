<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/** Students and staff who can borrow, for pick lists and for showing names. */
class Borrowers
{
    /** Students grouped by class (same grouping as the check-out page). */
    public static function studentsByClass(): array
    {
        $db = SchoolSchema::schoolConnection();
        $setting = $db->selectOne("SELECT `valued` FROM `settings` WHERE `sett` = 'class'");
        $classes = $setting ? array_filter(array_map("trim", explode(",", (string) $setting->valued)), "strlen") : [];
        $groups = [];
        foreach ($classes as $class) {
            $groups[] = (object) [
                "class_name" => self::className($class),
                "student_data" => $db->select("SELECT `adm_no`, `first_name`, `second_name` FROM `student_data` WHERE `stud_class` = ? ORDER BY `first_name`", [$class]),
            ];
        }
        return $groups;
    }

    public static function staff(): array
    {
        return DB::connection("mysql")->select(
            "SELECT `user_id`, `fullname` FROM `user_tbl` WHERE `school_code` = ? AND (`deleted` IS NULL OR `deleted` = 0) ORDER BY `fullname`",
            [session("school_details")->school_code]
        );
    }

    public static function name(string $type, $id): string
    {
        if ($type === "staff") {
            $row = DB::connection("mysql")->selectOne("SELECT `fullname` FROM `user_tbl` WHERE `user_id` = ?", [$id]);
            return $row ? ucwords(strtolower($row->fullname)) : "N/A";
        }
        $row = SchoolSchema::schoolConnection()->selectOne("SELECT `first_name`, `second_name` FROM `student_data` WHERE `adm_no` = ?", [$id]);
        return $row ? ucwords(strtolower(trim($row->first_name." ".$row->second_name))) : "N/A";
    }

    public static function className($data): string
    {
        if ($data == "-i") {
            return "Alumni";
        }
        return strlen((string) $data) > 1 ? (string) $data : "Grade ".$data;
    }
}

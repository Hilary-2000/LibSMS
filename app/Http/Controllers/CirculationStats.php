<?php

namespace App\Http\Controllers;

use App\Services\Borrowers;
use App\Services\CirculationInsights;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

date_default_timezone_set('Africa/Nairobi');

/**
 * Circulation > Statistics: insights for a period (this term by default), an Excel workbook of the
 * same numbers, and a one-page printable term report. All numbers come from CirculationInsights.
 */
class CirculationStats extends Controller
{
    const PERIODS = ["this_term" => "This term", "last_term" => "Last term", "this_year" => "This year", "custom" => "Custom dates"];

    private function data(Request $request): array
    {
        $key = array_key_exists($request->input("period"), self::PERIODS) ? $request->input("period") : "this_term";
        return (new CirculationInsights())->all($key, $request->input("from"), $request->input("to"));
    }

    function index(Request $request)
    {
        return view("circulation_insights", $this->layoutData($request, [
            "stats" => $this->data($request),
            "periods" => self::PERIODS,
            "students" => Borrowers::studentsByClass(),
            "staff" => Borrowers::staff(),
        ]));
    }

    /** Printable one-page report for the headteacher (print or "Save as PDF" from the browser). */
    function report(Request $request)
    {
        return view("circulation_report", [
            "stats" => $this->data($request),
            "school_name" => session("school_details")->school_name ?? "",
            "printed_by" => ucwords(strtolower((string) session("fullname"))),
        ]);
    }

    /** Every table on the page, one sheet each. */
    function export(Request $request)
    {
        $s = $this->data($request);
        $book = new Spreadsheet();
        $book->removeSheetByIndex(0);
        $t = $s["tiles"];
        $tp = $s["tiles_prev"];

        $sheets = [
            "Summary" => [
                ["Library statistics", $s["period"]["label"]." (".$s["period"]["from"]." - ".$s["period"]["to"].")"],
                ["Compared with", $s["previous"]["label"]],
                [],
                ["Measure", "This period", "Previous period"],
                ["Loans", $t["loans"], $tp["loans"]],
                ["On-time return rate (%)", $t["on_time_rate"], $tp["on_time_rate"]],
                ["Reading participation (%)", $t["participation"], $tp["participation"]],
                ["Students who borrowed", $t["readers"], $tp["readers"]],
                ["Average loan length (days)", $t["loan_days"], $tp["loan_days"]],
                ["Overdue right now", $s["overdue_now"], ""],
                ["Collection used in last 12 months (%)", $s["collection_use"]["rate"], ""],
            ],
            "Top readers" => array_merge([["Adm no", "Name", "Class", "Loans"]], array_map(fn ($r) => [$r["adm_no"], $r["name"], $r["class"], $r["loans"]], $s["top_readers"])),
            "By class" => array_merge([["Class", "Students", "Loans", "Loans per student", "Participation (%)"]], array_map(fn ($r) => [$r["class"], $r["students"], $r["loans"], $r["per_student"], $r["participation"]], $s["by_class"])),
            "Top titles" => array_merge([["Title", "Loans"]], array_map(fn ($r) => [$r["title"], $r["loans"]], $s["top_titles"])),
            "Categories" => array_merge([["Category", "Loans"]], array_map(fn ($r) => [$r["category"], $r["loans"]], $s["top_categories"])),
            "Never borrowed" => array_merge([["Title", "Call no", "Recorded", "Last borrowed"]], array_map(fn ($r) => [
                $r->book_title, $r->call_no, $r->date_recorded ? date("Y-m-d", strtotime($r->date_recorded)) : "", $r->last_borrowed ? date("Y-m-d", strtotime($r->last_borrowed)) : "never",
            ], $s["never_borrowed"]["rows"])),
            "Late by class" => array_merge([["Class", "Loans due", "Late", "Late (%)"]], array_map(fn ($r) => [$r["class"], $r["due"], $r["late"], $r["rate"]], $s["late_by_class"])),
            "Repeat late" => array_merge([["Name", "Class / staff", "Late returns", "Still out", "Average days late"]], array_map(fn ($r) => [$r["name"], $r["class"], $r["late"], $r["still_out"], $r["avg_days"]], $s["repeat_late"])),
            "Due per ".$s["weekly_due"]["unit"] => array_merge([["Starting", "Returned on time", "Late / still out"]], array_map(null, $s["weekly_due"]["labels"], $s["weekly_due"]["on_time"], $s["weekly_due"]["late"])),
            "Busy hours" => (function () use ($s) {
                $rows = [array_merge(["Day"], array_column($s["busy"]["series"][0]["data"] ?? [], "x"))];
                foreach (array_reverse($s["busy"]["series"]) as $day) {
                    $rows[] = array_merge([$day["name"]], array_column($day["data"], "y"));
                }
                return $rows;
            })(),
        ];
        foreach ($sheets as $title => $rows) {
            $sheet = $book->createSheet();
            $sheet->setTitle(mb_substr($title, 0, 31));
            $sheet->fromArray($rows, null, "A1", true);
            $sheet->getStyle("A1:Z1")->getFont()->setBold(true);
            foreach (range("A", "F") as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }
        $book->setActiveSheetIndex(0);

        $name = "library-statistics-".preg_replace('/[^a-z0-9]+/i', "-", strtolower($s["period"]["label"])).".xlsx";
        return response()->streamDownload(function () use ($book) {
            (new Xlsx($book))->save("php://output");
        }, $name, ["Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"]);
    }
}

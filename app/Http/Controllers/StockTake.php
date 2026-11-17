<?php

namespace App\Http\Controllers;

use App\Services\BookCode;
use App\Services\SchoolSchema;
use App\Support\RawPaginator;
use Illuminate\Http\Request;

date_default_timezone_set('Africa/Nairobi');

/**
 * Stock-take: scan every book on the shelves, then compare with the records.
 *   missing    - should be on the shelf (not lost, not on loan) but was not scanned
 *   misplaced  - scanned, but recorded in a different library
 *   on loan    - scanned, but the records say it is checked out (a return was not recorded)
 *   lost       - scanned, but marked as lost
 *   unknown    - the scanned code matches no book
 */
class StockTake extends Controller
{
    private function db()
    {
        $db = SchoolSchema::schoolConnection();
        SchoolSchema::ensure("library_stocktakes", "library_stocktake_scans");
        return $db;
    }

    private function libraries($db): array
    {
        $setting = $db->selectOne("SELECT `valued` FROM `settings` WHERE `sett` = 'libraries'");
        $out = [];
        foreach ($setting ? (json_decode($setting->valued) ?: []) : [] as $lib) {
            $out[(string) $lib->id] = $lib->Name;
        }
        return $out;
    }

    function index(Request $request)
    {
        $db = $this->db();
        [$sessions, $pager] = RawPaginator::paginate(
            "SELECT s.*, (SELECT COUNT(DISTINCT `book_id`) FROM `library_stocktake_scans` x WHERE x.`stocktake_id` = s.`stocktake_id` AND x.`book_id` IS NOT NULL) AS scanned
             FROM `library_stocktakes` s ORDER BY s.`stocktake_id` DESC", [], "page", $db
        );
        // in-progress stock-takes are shown first, with how far they got
        $open = [];
        foreach ($db->select("SELECT * FROM `library_stocktakes` WHERE `status` = 'open' ORDER BY `stocktake_id` DESC") as $take) {
            $take->summary = $this->summary($db, $take);
            $take->last_scan = $db->selectOne("SELECT MAX(`date_scanned`) AS d FROM `library_stocktake_scans` WHERE `stocktake_id` = ?", [$take->stocktake_id])->d;
            $open[] = $take;
        }
        return view("stocktake_index", $this->layoutData($request, [
            "sessions" => $sessions, "pager" => $pager, "libraries" => $this->libraries($db), "open" => $open,
        ]));
    }

    function start(Request $request)
    {
        $db = $this->db();
        $location = $request->input("library_location");
        $libraries = $this->libraries($db);
        $id = $db->table("library_stocktakes")->insertGetId([
            "title" => trim((string) $request->input("title")) ?: "Stock-take ".date("d M Y"),
            "library_location" => isset($libraries[(string) $location]) ? (string) $location : null,
            "status" => "open",
            "date_started" => date("YmdHis"),
            "started_by" => session("user_id"),
        ]);
        return redirect("/Acquisitions/Stock-take/".$id);
    }

    /** Books expected on the shelves for this stock-take (SQL fragment + bindings). */
    private function expectedWhere(object $take): array
    {
        $where = "d.`lost_status` = 0 AND d.`availability_status` = 1";
        $bindings = [];
        if ($take->library_location !== null) {
            $where .= " AND d.`library_location` = ?";
            $bindings[] = $take->library_location;
        }
        return [$where, $bindings];
    }

    private function summary($db, object $take): array
    {
        [$where, $b] = $this->expectedWhere($take);
        $id = $take->stocktake_id;
        $scannedBook = "EXISTS (SELECT 1 FROM `library_stocktake_scans` x WHERE x.`stocktake_id` = ? AND x.`book_id` = d.`book_id`)";
        $count = fn ($sql, $bind) => (int) $db->selectOne($sql, $bind)->n;
        return [
            "expected" => $count("SELECT COUNT(*) AS n FROM `library_details` d WHERE $where", $b),
            "found" => $count("SELECT COUNT(*) AS n FROM `library_details` d WHERE $where AND $scannedBook", array_merge($b, [$id])),
            "missing" => $count("SELECT COUNT(*) AS n FROM `library_details` d WHERE $where AND NOT $scannedBook", array_merge($b, [$id])),
            "scans" => $count("SELECT COUNT(*) AS n FROM `library_stocktake_scans` WHERE `stocktake_id` = ?", [$id]),
            "misplaced" => $take->library_location === null ? 0 : $count("SELECT COUNT(*) AS n FROM `library_details` d WHERE $scannedBook AND d.`library_location` <> ?", [$id, $take->library_location]),
            "on_loan" => $count("SELECT COUNT(*) AS n FROM `library_details` d WHERE $scannedBook AND d.`availability_status` = 0 AND d.`lost_status` = 0", [$id]),
            "lost" => $count("SELECT COUNT(*) AS n FROM `library_details` d WHERE $scannedBook AND d.`lost_status` = 1", [$id]),
            "unknown" => $count("SELECT COUNT(DISTINCT `code`) AS n FROM `library_stocktake_scans` WHERE `stocktake_id` = ? AND `book_id` IS NULL", [$id]),
        ];
    }

    function show($stocktake_id, Request $request)
    {
        $db = $this->db();
        $take = $db->selectOne("SELECT * FROM `library_stocktakes` WHERE `stocktake_id` = ?", [(int) $stocktake_id]);
        if (!$take) {
            session()->flash("error", "That stock-take does not exist.");
            return redirect("/Acquisitions/Stock-take");
        }
        $tab = in_array($request->input("tab"), ["missing", "misplaced", "on_loan", "lost", "unknown", "scans"], true) ? $request->input("tab") : "missing";
        $scanned = "EXISTS (SELECT 1 FROM `library_stocktake_scans` x WHERE x.`stocktake_id` = ? AND x.`book_id` = d.`book_id`)";
        [$where, $b] = $this->expectedWhere($take);
        $id = $take->stocktake_id;
        $queries = [
            "missing" => ["SELECT d.* FROM `library_details` d WHERE $where AND NOT $scanned ORDER BY d.`shelf_no_location`, d.`call_no`", array_merge($b, [$id])],
            "misplaced" => ["SELECT d.* FROM `library_details` d WHERE $scanned AND d.`library_location` <> ? ORDER BY d.`call_no`", [$id, (string) $take->library_location]],
            "on_loan" => ["SELECT d.* FROM `library_details` d WHERE $scanned AND d.`availability_status` = 0 AND d.`lost_status` = 0 ORDER BY d.`call_no`", [$id]],
            "lost" => ["SELECT d.* FROM `library_details` d WHERE $scanned AND d.`lost_status` = 1 ORDER BY d.`call_no`", [$id]],
            "unknown" => ["SELECT `code`, COUNT(*) AS times, MIN(`date_scanned`) AS first_scanned FROM `library_stocktake_scans` WHERE `stocktake_id` = ? AND `book_id` IS NULL GROUP BY `code` ORDER BY `code`", [$id]],
            "scans" => ["SELECT s.*, d.`book_title`, d.`call_no` FROM `library_stocktake_scans` s LEFT JOIN `library_details` d ON d.`book_id` = s.`book_id` WHERE s.`stocktake_id` = ? ORDER BY s.`scan_id` DESC", [$id]],
        ];
        if ($tab === "misplaced" && $take->library_location === null) {
            $rows = [];
            $pager = null;
        } else {
            [$rows, $pager] = RawPaginator::paginate($queries[$tab][0], $queries[$tab][1], "page", $db);
        }
        $libraries = $this->libraries($db);
        $take->last_scan = $db->selectOne("SELECT MAX(`date_scanned`) AS d FROM `library_stocktake_scans` WHERE `stocktake_id` = ?", [$take->stocktake_id])->d;
        return view("stocktake_show", $this->layoutData($request, [
            "take" => $take, "summary" => $this->summary($db, $take), "tab" => $tab, "rows" => $rows, "pager" => $pager,
            "libraries" => $libraries,
            "location_name" => $take->library_location === null ? "All libraries" : ($libraries[$take->library_location] ?? "Library ".$take->library_location),
        ]));
    }

    /** One scan. Answers JSON for the in-page scanner, or redirects back for a plain form post. */
    function scan($stocktake_id, Request $request)
    {
        $db = $this->db();
        $take = $db->selectOne("SELECT * FROM `library_stocktakes` WHERE `stocktake_id` = ?", [(int) $stocktake_id]);
        $code = trim((string) $request->input("code", ""));
        $result = ["ok" => false, "message" => "", "level" => "danger"];

        if (!$take || $take->status !== "open") {
            $result["message"] = "This stock-take is closed.";
        } elseif ($code === "") {
            $result["message"] = "Nothing was scanned.";
        } else {
            $book = BookCode::resolve($db, $code);
            $already = $book && $db->selectOne("SELECT 1 AS x FROM `library_stocktake_scans` WHERE `stocktake_id` = ? AND `book_id` = ?", [$take->stocktake_id, (string) $book->book_id]);
            if (!$already) {
                $db->table("library_stocktake_scans")->insert([
                    "stocktake_id" => $take->stocktake_id, "code" => mb_substr($code, 0, 200),
                    "book_id" => $book ? (string) $book->book_id : null,
                    "date_scanned" => date("YmdHis"), "scanned_by" => session("user_id"),
                ]);
            }
            if (!$book) {
                $result["message"] = "Unknown code \"$code\" - no book has it.";
            } else {
                $label = "\"".$book->book_title."\" (".BookCode::encode($book->book_id).", ".$book->call_no.")";
                $result["ok"] = true;
                $result["level"] = "success";
                $result["message"] = ($already ? "Already scanned: " : "Found: ").$label;
                if ((int) $book->lost_status === 1) {
                    $result["level"] = "warning";
                    $result["message"] .= " - it is marked as LOST. Mark it as found on its page.";
                } elseif ((int) $book->availability_status === 0) {
                    $result["level"] = "warning";
                    $result["message"] .= " - the records say it is ON LOAN. Check it in if it was returned.";
                } elseif ($take->library_location !== null && (string) $book->library_location !== (string) $take->library_location) {
                    $result["level"] = "warning";
                    $result["message"] .= " - it belongs to another library (misplaced).";
                }
            }
        }
        if ($take) {
            $result["summary"] = $this->summary($db, $take);
        }
        if ($request->expectsJson()) {
            return response()->json($result);
        }
        session()->flash($result["level"] === "danger" ? "error" : "success", $result["message"]);
        return redirect("/Acquisitions/Stock-take/".(int) $stocktake_id);
    }

    /** "Continue later": nothing to save (each scan is stored as it happens) - confirm progress and go back to the list. */
    function pause($stocktake_id)
    {
        $db = $this->db();
        $take = $db->selectOne("SELECT * FROM `library_stocktakes` WHERE `stocktake_id` = ?", [(int) $stocktake_id]);
        if ($take) {
            $sum = $this->summary($db, $take);
            session()->flash("success", "\"".$take->title."\" saved: ".number_format($sum["found"])." of ".number_format($sum["expected"])
                ." books found so far. Pick it up any time with Continue below - from this or any other computer.");
        }
        return redirect("/Acquisitions/Stock-take");
    }

    function close($stocktake_id)
    {
        $db = $this->db();
        $db->update("UPDATE `library_stocktakes` SET `status` = 'closed', `date_closed` = ?, `closed_by` = ? WHERE `stocktake_id` = ? AND `status` = 'open'",
            [date("YmdHis"), session("user_id"), (int) $stocktake_id]);
        session()->flash("success", "Stock-take finished. The results below are final and stay available under Stock-take.");
        return redirect("/Acquisitions/Stock-take/".(int) $stocktake_id);
    }
}

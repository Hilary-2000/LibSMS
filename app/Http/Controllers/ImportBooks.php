<?php

namespace App\Http\Controllers;

use App\Services\AuditLog;
use App\Services\GoogleBooks;
use App\Services\SchoolSchema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

date_default_timezone_set('Africa/Nairobi');

/**
 * Bulk book import from CSV / Excel: upload -> preview (with errors per row) -> confirm.
 * Each row is one title; a "copies" column creates that many copies (call numbers CALL-1, CALL-2, ...).
 * Optionally fills missing details from Google Books by ISBN.
 */
class ImportBooks extends Controller
{
    const MAX_ROWS = 1000;          // rows per file
    const MAX_COPIES = 50;          // copies per row
    const MAX_LOOKUPS = 150;        // Google Books requests per import (keeps the upload fast)

    /** Accepted column headings (case/space-insensitive) => field. */
    const COLUMNS = [
        "title" => "title", "book title" => "title", "book_title" => "title",
        "author" => "author", "authors" => "author", "book author" => "author", "book_author" => "author",
        "isbn" => "isbn_13", "isbn13" => "isbn_13", "isbn 13" => "isbn_13", "isbn_13" => "isbn_13", "isbn-13" => "isbn_13",
        "isbn10" => "isbn_10", "isbn 10" => "isbn_10", "isbn_10" => "isbn_10", "isbn-10" => "isbn_10",
        "publisher" => "publisher", "publishers" => "publisher", "book_publishers" => "publisher",
        "published" => "published", "year" => "published", "date published" => "published", "published_date" => "published",
        "call no" => "call_no", "call number" => "call_no", "call_no" => "call_no", "call no." => "call_no",
        "category" => "category", "subject" => "category", "book_category" => "category",
        "shelf" => "shelf", "shelf location" => "shelf", "shelf_no_location" => "shelf", "location" => "shelf",
        "copies" => "copies", "quantity" => "copies", "qty" => "copies",
        "pages" => "pages", "no of pages" => "pages", "no_of_pages" => "pages",
        "language" => "language", "edition" => "edition", "description" => "description", "keywords" => "keywords",
    ];

    function form(Request $request)
    {
        $db = SchoolSchema::schoolConnection();
        $setting = $db->selectOne("SELECT `valued` FROM `settings` WHERE `sett` = 'libraries'");
        return view("import_books", $this->layoutData($request, [
            "libraries" => $setting ? (json_decode($setting->valued) ?: []) : [],
        ]));
    }

    function template()
    {
        $rows = [
            ["title", "author", "isbn_13", "isbn_10", "publisher", "published", "call_no", "category", "shelf", "copies"],
            ["Things Fall Apart", "Chinua Achebe", "9780385474542", "", "Anchor Books", "1994", "ENG-001", "Literature", "Shelf A2", "3"],
            ["", "", "9780553381689", "", "", "", "ENG-002", "", "Shelf A3", "1"],
        ];
        $csv = implode("\r\n", array_map(fn ($r) => implode(",", array_map(fn ($c) => '"'.str_replace('"', '""', $c).'"', $r)), $rows))."\r\n";
        return response($csv, 200, [
            "Content-Type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => 'attachment; filename="library-import-template.csv"',
        ]);
    }

    function preview(Request $request)
    {
        $request->validate([
            "import_file" => "required|file|max:10240|mimes:csv,txt,xlsx,xls",
            "library_location" => "required",
        ], ["import_file.mimes" => "Upload a .csv, .xlsx or .xls file."]);

        try {
            $sheet = IOFactory::load($request->file("import_file")->getRealPath())->getActiveSheet();
            $raw = $sheet->toArray(null, true, false, false);
        } catch (\Throwable $e) {
            return back()->with("error", "The file could not be read: ".$e->getMessage());
        }
        $raw = array_values(array_filter($raw, fn ($r) => count(array_filter($r, fn ($c) => trim((string) $c) !== "")) > 0));
        if (count($raw) < 2) {
            return back()->with("error", "The file has no book rows under the heading row.");
        }

        // map the heading row
        $map = [];
        foreach ($raw[0] as $col => $heading) {
            $key = strtolower(trim(preg_replace('/\s+/', " ", (string) $heading)));
            if (isset(self::COLUMNS[$key])) {
                $map[$col] = self::COLUMNS[$key];
            }
        }
        if (!in_array("title", $map, true) && !in_array("isbn_13", $map, true)) {
            return back()->with("error", "The heading row needs at least a \"title\" or an \"isbn_13\" column. Download the template to see the expected headings.");
        }
        $dataRows = array_slice($raw, 1, self::MAX_ROWS);
        $truncated = count($raw) - 1 > self::MAX_ROWS;

        $db = SchoolSchema::schoolConnection();
        $useGoogle = $request->boolean("use_google");
        $lookups = 0;
        $seenCalls = [];
        $rows = [];
        foreach ($dataRows as $n => $cells) {
            $r = ["line" => $n + 2, "errors" => [], "notes" => []];
            foreach (array_unique(array_values(self::COLUMNS)) as $field) {
                $r[$field] = "";
            }
            foreach ($map as $col => $field) {
                $r[$field] = trim((string) ($cells[$col] ?? ""));
            }
            // spreadsheets often turn ISBNs into numbers like 9.78E+12
            foreach (["isbn_13", "isbn_10"] as $f) {
                if (preg_match('/^[0-9.]+E\+?\d+$/i', $r[$f])) {
                    $r[$f] = number_format((float) $r[$f], 0, "", "");
                }
                $r[$f] = preg_replace('/[^0-9Xx]/', "", $r[$f]);
            }
            if ($useGoogle && $r["isbn_13"].$r["isbn_10"] !== "" && ($r["title"] === "" || $r["author"] === "" || $r["publisher"] === "") && $lookups < self::MAX_LOOKUPS) {
                $lookups++;
                $g = GoogleBooks::lookup($r["isbn_13"] ?: $r["isbn_10"]);
                if ($g) {
                    foreach (["title" => "title", "author" => "author", "publisher" => "publisher", "published" => "published", "category" => "category", "pages" => "pages", "language" => "language", "description" => "description", "isbn_10" => "isbn_10", "isbn_13" => "isbn_13"] as $from => $to) {
                        if ($r[$to] === "" && $g[$from] !== "") {
                            $r[$to] = $g[$from];
                        }
                    }
                    $r["cover"] = $g["cover"];
                    $r["notes"][] = "Details completed from Google Books";
                } else {
                    $r["notes"][] = "Not found on Google Books";
                }
            }
            $r["cover"] = $r["cover"] ?? "";

            $copies = $r["copies"] === "" ? 1 : (int) $r["copies"];
            if ($r["title"] === "") $r["errors"][] = "Title is missing";
            if ($r["call_no"] === "") $r["errors"][] = "Call number is missing";
            if ($r["isbn_13"] !== "" && strlen($r["isbn_13"]) !== 13) $r["errors"][] = "ISBN-13 must have 13 digits (has ".strlen($r["isbn_13"]).")";
            if ($copies < 1 || $copies > self::MAX_COPIES) $r["errors"][] = "Copies must be between 1 and ".self::MAX_COPIES;
            $copies = max(1, min(self::MAX_COPIES, $copies));

            // one call number per copy: CALL (single copy) or CALL-1 .. CALL-n
            $r["call_numbers"] = $copies === 1 ? [$r["call_no"]] : array_map(fn ($i) => $r["call_no"]."-".$i, range(1, $copies));
            if ($r["call_no"] !== "") {
                foreach ($r["call_numbers"] as $call) {
                    if (isset($seenCalls[strtolower($call)])) {
                        $r["errors"][] = "Call number \"$call\" is repeated in the file (line ".$seenCalls[strtolower($call)].")";
                    } else {
                        $seenCalls[strtolower($call)] = $r["line"];
                    }
                }
                $placeholders = implode(",", array_fill(0, count($r["call_numbers"]), "?"));
                $taken = $db->select("SELECT `call_no` FROM `library_details` WHERE `call_no` IN ($placeholders)", $r["call_numbers"]);
                foreach ($taken as $t) {
                    $r["errors"][] = "Call number \"".$t->call_no."\" is already used by another book";
                }
            }
            $rows[] = $r;
        }

        $token = Str::random(32);
        Cache::put("book_import:".session("user_id").":".$token, [
            "rows" => $rows,
            "library_location" => (string) $request->input("library_location"),
            "database" => session("school_details")->database_name,
        ], now()->addMinutes(30));

        return view("import_books_preview", $this->layoutData($request, [
            "rows" => $rows,
            "token" => $token,
            "truncated" => $truncated,
            "ok_rows" => count(array_filter($rows, fn ($r) => !$r["errors"])),
            "ok_copies" => array_sum(array_map(fn ($r) => $r["errors"] ? 0 : count($r["call_numbers"]), $rows)),
            "lookups" => $lookups,
        ]));
    }

    function confirm(Request $request)
    {
        $key = "book_import:".session("user_id").":".$request->input("token");
        $import = Cache::get($key);
        if (!$import || $import["database"] !== session("school_details")->database_name) {
            session()->flash("error", "This import has expired. Upload the file again.");
            return redirect("/Acquisitions/Import");
        }
        Cache::forget($key); // a second click must not import twice

        $db = SchoolSchema::schoolConnection();
        $books = 0;
        $skipped = 0;
        $newIds = [];
        $db->transaction(function () use ($db, $import, &$books, &$skipped, &$newIds) {
            foreach ($import["rows"] as $r) {
                if ($r["errors"]) {
                    $skipped++;
                    continue;
                }
                foreach ($r["call_numbers"] as $call) {
                    // re-check: someone may have used the call number since the preview
                    if ($db->selectOne("SELECT 1 AS x FROM `library_details` WHERE `call_no` = ?", [$call])) {
                        continue;
                    }
                    $db->insert("INSERT INTO `library_details` (`book_title`,`book_author`,`book_publishers`,`published_date`,`thumbnail_location`,`book_category`,`isbn_13`,`isbn_10`,`date_recorded`,`physical_dimensions`,`no_of_revisions`,`call_no`,`language`,`description`,`shelf_no_location`,`no_of_pages`,`library_location`,`keywords`,`edition`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", [
                        $r["title"], $r["author"], $r["publisher"], $r["published"], $r["cover"], $r["category"],
                        $r["isbn_13"], $r["isbn_10"], date("YmdHis"), "", "0", $call, $r["language"], $r["description"],
                        $r["shelf"], $r["pages"], $import["library_location"], $r["keywords"], $r["edition"],
                    ]);
                    $newIds[] = (int) $db->getPdo()->lastInsertId();
                    $books++;
                }
            }
        });
        AuditLog::record("Imported books", "file import", ["copies_added" => $books, "rows_skipped" => $skipped]);

        session()->flash("success", "$books book cop".($books === 1 ? "y" : "ies")." imported".($skipped ? ", $skipped row(s) skipped because of errors" : "").".");
        // so the librarian can print labels for exactly the new copies
        session()->flash("import_ids", $newIds ? implode(",", $newIds) : null);
        return redirect("/Acquisitions/Import");
    }
}

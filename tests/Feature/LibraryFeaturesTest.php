<?php

namespace Tests\Feature;

use App\Http\Controllers\login;
use App\Services\BookCode;
use App\Services\DashboardCharts;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Pagination, barcode labels & scanning, reservations, bulk import, stock-take, audit log,
 * dashboard charts and report exports - against the demo school, inside a transaction.
 */
class LibraryFeaturesTest extends TestCase
{
    use DatabaseTransactions;

    protected $connectionsToTransact = ["mysql", "mysql2"];

    private function school()
    {
        return DB::connection("mysql2");
    }

    private function login(): void
    {
        DB::connection("mysql")->table("user_tbl")->insert([
            "user_id" => 990300, "school_code" => 35601110, "fullname" => "Feature Tester",
            "phone_number" => "0", "gender" => "F", "address" => "-", "nat_id" => "0", "tsc_no" => "0",
            "username" => "feature_tester", "password" => (new login())->encryptCode("Feat-123"),
            "auth" => "LIBRARIAN", "library_previleges" => "[]", "activated" => 1, "deleted" => 0,
        ]);
        $this->go()->post("/process_login", ["username" => "feature_tester", "user-password" => "Feat-123"])->assertRedirect("/Dashboard");
    }

    /** The app switches the default DB connection per request; reset before each call. */
    private function go(): self
    {
        DB::setDefaultConnection("mysql");
        return $this;
    }

    private function book(array $over = []): int
    {
        static $n = 0;
        $n++;
        return (int) $this->school()->table("library_details")->insertGetId(array_merge([
            "book_title" => "Feature Book $n", "book_author" => "Tester", "isbn_13" => "9990000001".str_pad((string) $n, 3, "0", STR_PAD_LEFT),
            "call_no" => "FT-$n-".uniqid(), "date_recorded" => date("YmdHis"), "availability_status" => 1, "lost_status" => 0, "library_location" => "1",
        ], $over));
    }

    private function lend(int $bookId, string $student): int
    {
        $this->go()->post("/Circulation/Confirm/check-out", [
            "book_ids" => $bookId, "book_borrower" => "student", "student_borrower" => $student,
            "staff_borrower" => "Select an option", "expected_return_date" => date("Y-m-d", strtotime("+7 days")),
        ]);
        return (int) $this->school()->table("book_circulation")->where("book_id", $bookId)->where("return_status", 0)->max("circulation_id");
    }

    public function test_lists_are_paginated_and_every_book_is_reachable(): void
    {
        $this->login();
        $first = $this->book(["book_title" => "Oldest Paging Book"]);
        for ($i = 0; $i < 30; $i++) {
            $this->book();
        }
        $total = $this->school()->table("library_details")->count();

        $page1 = $this->go()->get("/Acquisitions?per_page=10")->assertOk();
        $page1->assertSee("of <b>".number_format($total)."</b>", false)->assertSee("per_page=10", false);
        $this->assertStringNotContainsString("Oldest Paging Book", $page1->getContent());

        // the oldest book is on a later page (previously only the newest 100 were ever shown)
        $last = (int) ceil($total / 10);
        $found = false;
        for ($p = 2; $p <= $last && !$found; $p++) {
            $found = str_contains($this->go()->get("/Acquisitions?per_page=10&page=$p")->getContent(), "Oldest Paging Book");
        }
        $this->assertTrue($found, "oldest book not reachable through the pages");

        $this->go()->get("/Circulation/check-out?per_page=10")->assertOk()->assertSee('data-server-paginated="1"', false);
        $this->go()->get("/Cataloging?per_page=10")->assertOk();
        $this->go()->get("/Circulation?per_page=10")->assertOk();
        $this->go()->get("/Notification?per_page=10")->assertOk();
    }

    public function test_labels_and_front_desk_scanning(): void
    {
        $this->login();
        $book = $this->book(["book_title" => "Label Book"]);
        $code = BookCode::encode($book);
        $this->assertMatchesRegularExpression('/^LB\d{6}$/', $code);

        $labels = $this->go()->get("/Acquisitions/Labels?ids=$book")->assertOk();
        $labels->assertSee($code)->assertSee("Label Book")->assertSee("<svg", false);

        // on the shelf -> check-out page
        $this->go()->get("/Circulation/scan?code=$code")->assertRedirect("/Circulation/check-out/$book");
        $this->go()->get("/Circulation/scan?code=".strtolower($code))->assertRedirect("/Circulation/check-out/$book");
        // on loan -> its loan (where it is checked in)
        $student = $this->school()->table("student_data")->value("adm_no");
        $loan = $this->lend($book, $student);
        $this->go()->get("/Circulation/scan?code=$code")->assertRedirect("/Circulation/View/check-out/$book/$loan");
        // unknown code -> back with an error; external "back" URLs are ignored
        $this->go()->get("/Circulation/scan?code=NOPE-123&back=https://evil.example")->assertRedirect("/Circulation")->assertSessionHas("error");
    }

    public function test_reservation_holds_the_returned_copy_for_the_first_in_line(): void
    {
        $this->login();
        $students = $this->school()->table("student_data")->limit(3)->pluck("adm_no")->all();
        $this->assertCount(3, $students, "demo school needs 3 students");
        [$a, $b, $c] = $students;
        $book = $this->book(["book_title" => "Popular Book"]);

        // reserving while a copy is on the shelf is refused
        $this->go()->post("/Circulation/Reservations/New", ["book_id" => $book, "borrower_type" => "student", "student_borrower" => $b])->assertSessionHas("error");

        $loan = $this->lend($book, $a);
        $this->go()->post("/Circulation/Reservations/New", ["book_id" => $book, "borrower_type" => "student", "student_borrower" => $b])->assertSessionHas("success");
        $this->go()->post("/Circulation/Reservations/New", ["book_id" => $book, "borrower_type" => "student", "student_borrower" => $c])->assertSessionHas("success");
        // no duplicates for the same person
        $this->go()->post("/Circulation/Reservations/New", ["book_id" => $book, "borrower_type" => "student", "student_borrower" => $b])->assertSessionHas("error");

        // return -> held for B (first in line), librarians notified
        $this->go()->post("/Circulation/Confirm/check-in/$loan");
        $resB = $this->school()->table("library_reservations")->where("borrower_id", $b)->orderByDesc("reservation_id")->first();
        $this->assertSame("ready", $resB->status);
        $this->assertSame((string) $book, (string) $resB->held_book_id);
        $this->assertSame(1, $this->school()->table("library_notifications")->where("book_id", (string) $book)->where("notification_type", 2)->count());

        // C cannot take B's copy
        $this->go()->post("/Circulation/Confirm/check-out", ["book_ids" => $book, "book_borrower" => "student", "student_borrower" => $c,
            "staff_borrower" => "Select an option", "expected_return_date" => date("Y-m-d", strtotime("+7 days"))])->assertSessionHas("error");
        $this->assertSame(1, (int) $this->school()->table("library_details")->where("book_id", $book)->value("availability_status"));

        // B collects it -> fulfilled, hold notification removed, C still waiting
        $this->lend($book, $b);
        $this->assertSame("fulfilled", $this->school()->table("library_reservations")->where("reservation_id", $resB->reservation_id)->value("status"));
        $this->assertSame(0, $this->school()->table("library_notifications")->where("book_id", (string) $book)->where("notification_type", 2)->count());
        $this->assertSame("waiting", $this->school()->table("library_reservations")->where("borrower_id", $c)->orderByDesc("reservation_id")->value("status"));

        $this->go()->get("/Circulation/Reservations?status=all")->assertOk()->assertSee("Popular Book");
    }

    public function test_bulk_import_preview_and_confirm(): void
    {
        $this->login();
        $this->book(["call_no" => "DUP-EXISTING"]);
        $csv = "title,author,isbn_13,call_no,shelf,copies\n"
            ."Imported One,Author A,9781111111111,IMP-A,Shelf 1,1\n"
            ."Imported Many,Author B,,IMP-B,Shelf 2,3\n"
            ."Clash,Author C,,DUP-EXISTING,Shelf 3,1\n"
            .",No Title,,IMP-C,,1\n"
            ."Bad Isbn,Author D,12345,IMP-D,,1\n";
        $file = UploadedFile::fake()->createWithContent("books.csv", $csv);

        $preview = $this->go()->post("/Acquisitions/Import/Preview", ["import_file" => $file, "library_location" => "1"]);
        $preview->assertOk()->assertSee("Imported Many")->assertSee("IMP-B-1, IMP-B-2, IMP-B-3")
            ->assertSee("already used by another book")->assertSee("Title is missing")->assertSee("ISBN-13 must have 13 digits");
        preg_match('/name="token" value="([^"]+)"/', $preview->getContent(), $m);
        $this->assertNotEmpty($m[1] ?? null);

        $before = $this->school()->table("library_details")->count();
        $this->go()->post("/Acquisitions/Import/Confirm", ["token" => $m[1]])->assertRedirect("/Acquisitions/Import")->assertSessionHas("success");
        $this->assertSame($before + 4, $this->school()->table("library_details")->count(), "1 + 3 copies imported, 3 bad rows skipped");
        $this->assertSame(3, $this->school()->table("library_details")->where("call_no", "like", "IMP-B-%")->count());
        $this->assertSame(0, $this->school()->table("library_details")->where("call_no", "IMP-C")->count());

        // the same preview cannot be imported twice
        $this->go()->post("/Acquisitions/Import/Confirm", ["token" => $m[1]])->assertSessionHas("error");
        $this->go()->get("/Acquisitions/Import/Template")->assertOk()->assertHeader("Content-Disposition", 'attachment; filename="library-import-template.csv"');
    }

    public function test_stock_take_finds_missing_misplaced_and_unknown(): void
    {
        $this->login();
        $onShelf = $this->book(["book_title" => "Shelf Book", "library_location" => "1"]);
        $missing = $this->book(["book_title" => "Missing Book", "library_location" => "1"]);
        $elsewhere = $this->book(["book_title" => "Other Library Book", "library_location" => "2"]);

        $this->go()->post("/Acquisitions/Stock-take/Start", ["title" => "Test take", "library_location" => "1"]);
        $take = (int) $this->school()->table("library_stocktakes")->max("stocktake_id");

        $json = fn ($code) => $this->go()->postJson("/Acquisitions/Stock-take/$take/Scan", ["code" => $code])->assertOk()->json();
        $this->assertSame("success", $json(BookCode::encode($onShelf))["level"]);
        $this->assertStringStartsWith("Already scanned", $json(BookCode::encode($onShelf))["message"]);
        $this->assertSame("warning", $json(BookCode::encode($elsewhere))["level"]);
        $res = $json("GARBAGE-1");
        $this->assertSame("danger", $res["level"]);
        $this->assertSame(1, $res["summary"]["misplaced"]);
        $this->assertSame(1, $res["summary"]["unknown"]);

        $this->go()->get("/Acquisitions/Stock-take/$take?tab=missing&per_page=100")->assertOk()->assertSee("Missing Book")->assertDontSee("Shelf Book");
        $this->go()->get("/Acquisitions/Stock-take/$take?tab=misplaced")->assertOk()->assertSee("Other Library Book");
        // stop for now and come back: progress is kept and the stock-take is listed as in progress
        $this->go()->get("/Acquisitions/Stock-take/$take/Later")->assertRedirect("/Acquisitions/Stock-take")->assertSessionHas("success");
        if (getenv("SNAPSHOT_DIR")) {
            file_put_contents(getenv("SNAPSHOT_DIR")."/stocktake_open.html", $this->go()->get("/Acquisitions/Stock-take/$take")->getContent());
            file_put_contents(getenv("SNAPSHOT_DIR")."/stocktake_list.html", $this->go()->get("/Acquisitions/Stock-take")->getContent());
        }
        $this->go()->get("/Acquisitions/Stock-take")->assertOk()->assertSee("In progress")->assertSee("Test take")->assertSee("/Acquisitions/Stock-take/$take", false);
        $this->assertSame("success", $json(BookCode::encode($missing))["level"], "scanning resumes after Continue later");
        $this->school()->table("library_stocktake_scans")->where("stocktake_id", $take)->where("book_id", (string) $missing)->delete();

        $this->go()->post("/Acquisitions/Stock-take/$take/Close");
        $this->assertSame("closed", $this->school()->table("library_stocktakes")->where("stocktake_id", $take)->value("status"));
        $this->assertSame("danger", $json(BookCode::encode($missing))["level"], "closed stock-take accepts no scans");
    }

    public function test_audit_log_records_who_did_what(): void
    {
        $this->login();
        $book = $this->book(["book_title" => "Audited Book"]);
        $student = $this->school()->table("student_data")->value("adm_no");
        $this->lend($book, $student);
        $this->lend($book, $student); // second attempt fails (already out)

        $rows = $this->school()->table("library_audit_log")->where("user_id", "990300")->orderBy("log_id")->get();
        $this->assertSame("Logged in", $rows->first()->action);
        $checkouts = $rows->where("action", "Checked out a book")->values();
        $this->assertCount(2, $checkouts);
        $this->assertSame("ok", $checkouts[0]->outcome);
        $this->assertSame("failed", $checkouts[1]->outcome);
        $this->assertStringContainsString("Audited Book", $checkouts[0]->target);
        foreach ($rows as $row) {
            $this->assertStringNotContainsString("Feat-123", (string) $row->details, "passwords must never be logged");
        }
        $this->go()->get("/Settings/Audit-log?action=".urlencode("Checked out a book"))->assertOk()->assertSee("Audited Book");
    }

    public function test_dashboard_charts_and_report_exports(): void
    {
        $this->login();
        $charts = DashboardCharts::build();
        $this->assertCount(12, $charts["months"]);
        $this->assertCount(12, $charts["checked_out"]);
        $this->go()->get("/Dashboard")->assertOk()->assertSee('id="chart_activity"', false)->assertSee("View as table");
        $this->go()->get("/Reports")->assertOk()->assertSee("report-export", false)->assertSee("pdfmake.min.js", false)->assertSee("jszip.min.js", false);
    }

    public function test_statistics_numbers_export_and_report(): void
    {
        $this->login();
        [$a, $b] = $this->school()->table("student_data")
            ->where(function ($q) { $q->whereNull("deleted")->orWhere("deleted", 0); })
            ->where("stud_class", "not like", "-%")->limit(2)->pluck("adm_no")->all();
        $day = fn ($d) => date("YmdHis", strtotime("$d days 10:00"));
        $loan = function ($book, $who, $type, $out, $due, $returned = null) use ($day) {
            $this->school()->table("book_circulation")->insert([
                "book_id" => (string) $book, "book_isbn" => "", "user_checked_out" => $who, "user_borrowing" => $type,
                "date_checked_out" => $day($out), "expected_return_date" => date("Ymd000000", strtotime("$due days")),
                "return_status" => $returned === null ? 0 : 1, "return_date" => $returned === null ? "NULL" : $day($returned), "checked_out_by" => "1",
            ]);
        };
        // remove the demo history from the counts: everything below is inside the last 20 days
        $b1 = $this->book(["book_title" => "Stats Book One"]);
        $b2 = $this->book(["book_title" => "Stats Book Two"]);
        $b3 = $this->book(["book_title" => "Stats Book Three"]);
        $loan($b1, $a, "student", -20, -13, -14);   // on time
        $loan($b2, $a, "student", -18, -11, -6);    // late (5 days)
        $loan($b3, $a, "student", -15, -8, -2);     // late (6 days)
        $loan($b1, $b, "student", -10, -3);         // still out, overdue
        $loan($b2, "1", "staff", -5, +9);           // due later: not counted for lateness

        $from = date("Y-m-d", strtotime("-20 days"));
        $to = date("Y-m-d");
        $page = $this->go()->get("/Circulation/Stats?period=custom&from=$from&to=$to")->assertOk();
        if (getenv("SNAPSHOT_DIR")) {
            file_put_contents(getenv("SNAPSHOT_DIR")."/stats_seeded.html", $page->getContent());
        }
        $page->assertSee("Who's reading", false)->assertSee("Late returns")->assertSee("When the library is busy")->assertSee("Repeat late returners");

        $stats = (new \App\Services\CirculationInsights())->all("custom", $from, $to);
        // demo history before these 20 days does not count; only our 5 loans (+ anything else made in the window)
        $this->assertGreaterThanOrEqual(5, $stats["tiles"]["loans"]);
        $mine = collect($stats["repeat_late"])->firstWhere("id", $a);
        $this->assertNotNull($mine, "student with 2 late returns is a repeat late returner");
        $this->assertSame(2, $mine["late"]);
        $this->assertEquals(5.5, $mine["avg_days"]);
        $this->assertSame($a, $stats["top_readers"][0]["adm_no"]);
        $this->assertSame(3, $stats["top_readers"][0]["loans"]);
        $this->assertGreaterThanOrEqual(1, $stats["borrower_types"]["staff"]);
        $cells = array_sum(array_map(fn ($d) => array_sum(array_column($d["data"], "y")), $stats["busy"]["series"]));
        $this->assertGreaterThanOrEqual(5 + 3, $cells, "check-outs and returns appear in the busy-hours grid");
        $this->assertNotEmpty(array_filter($stats["weekly_due"]["late"]));

        $xlsx = $this->go()->get("/Circulation/Stats/Export?period=custom&from=$from&to=$to")->assertOk();
        $this->assertStringContainsString("spreadsheetml", $xlsx->headers->get("Content-Type"));
        $this->assertStringStartsWith("PK", $xlsx->streamedContent(), "a real .xlsx (zip) file");

        $this->go()->get("/Circulation/Stats/Report?period=this_term")->assertOk()->assertSee("Library report")->assertSee("Top readers");
        foreach (["this_term", "last_term", "this_year"] as $period) {
            $this->go()->get("/Circulation/Stats?period=$period")->assertOk();
        }
    }

    public function test_whats_new_opens_once_per_user_and_stays_in_the_top_bar(): void
    {
        $this->login();
        $first = $this->go()->get("/Dashboard")->assertOk();
        $first->assertSee('id="whatsNewModal"', false)->assertSee('data-auto-open="1"', false)
            ->assertSee("What's new", false)->assertSee(config("whats_new.title"))->assertSee("data-whats-new-dot>new", false);

        $this->go()->postJson("/Whats-new/Seen")->assertOk()->assertJson(["ok" => true]);
        $this->assertSame(1, $this->school()->table("library_whats_new_seen")->where("user_id", "990300")->where("version", config("whats_new.version"))->count());

        // still reachable from the top bar, but no longer opens by itself (also after a new login)
        $this->go()->get("/Circulation")->assertOk()->assertSee('data-auto-open="0"', false)->assertDontSee("data-whats-new-dot>new", false);
        $this->flushSession();
        $this->go()->post("/process_login", ["username" => "feature_tester", "user-password" => "Feat-123"]);
        $this->go()->get("/Dashboard")->assertOk()->assertSee('data-auto-open="0"', false);

        // a new release is shown once more
        config(["whats_new.version" => "next-release"]);
        $this->flushSession();
        $this->go()->post("/process_login", ["username" => "feature_tester", "user-password" => "Feat-123"]);
        $this->go()->get("/Dashboard")->assertOk()->assertSee('data-auto-open="1"', false);
    }

    public function test_theme_choice_follows_the_person_to_any_computer(): void
    {
        $this->login();
        // no choice saved yet: pages fall back to this browser's choice
        $this->go()->get("/Dashboard")->assertOk()->assertSee("account = null", false)->assertSee("\\/Preferences\\/Theme", false);

        $this->go()->postJson("/Preferences/Theme", ["mode" => "dark"])->assertOk()->assertJson(["mode" => "dark"]);
        $this->go()->postJson("/Preferences/Theme", ["mode" => "purple"])->assertStatus(422);
        $this->assertSame("dark", $this->school()->table("library_user_prefs")->where("user_id", "990300")->where("pref_key", "theme")->value("pref_value"));

        // "another computer": a brand-new session, log in again -> their dark theme is applied
        $this->flushSession();
        $this->go()->post("/process_login", ["username" => "feature_tester", "user-password" => "Feat-123"]);
        $this->go()->get("/Circulation")->assertOk()->assertSee('account = "dark"', false);

        // the login page (nobody signed in) never saves to an account
        $this->go()->get("/Logout");
        $this->go()->get("/")->assertOk()->assertSee("window.LIBRARY_THEME_SAVE = null", false);
    }
}

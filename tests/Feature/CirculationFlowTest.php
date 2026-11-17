<?php

namespace Tests\Feature;

use App\Http\Controllers\login;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Core library flow against the demo school (testimonytbl1): check a book out, refuse a second
 * check-out of the same copy, check it back in, and create overdue notifications.
 * Runs in a transaction on both connections - nothing is written permanently.
 */
class CirculationFlowTest extends TestCase
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
            "user_id" => 990200, "school_code" => 35601110, "fullname" => "Flow Tester",
            "phone_number" => "0", "gender" => "F", "address" => "-", "nat_id" => "0", "tsc_no" => "0",
            "username" => "flow_tester", "password" => (new login())->encryptCode("Flow-123"),
            "auth" => "LIBRARIAN", "library_previleges" => "[]", "activated" => 1, "deleted" => 0,
        ]);
        DB::setDefaultConnection("mysql");
        $this->post("/process_login", ["username" => "flow_tester", "user-password" => "Flow-123"])->assertRedirect("/Dashboard");
    }

    /** A fresh, available copy so the test does not depend on the demo data's current state. */
    private function newBook(string $title = "Flow Test Book"): int
    {
        return (int) $this->school()->table("library_details")->insertGetId([
            "book_title" => $title, "book_author" => "Tester", "isbn_13" => "9990000000001",
            "call_no" => "FT-001", "date_recorded" => date("YmdHis"), "availability_status" => 1, "lost_status" => 0,
        ]);
    }

    public function test_check_out_then_check_in(): void
    {
        $this->login();
        $book = $this->newBook();
        $student = $this->school()->table("student_data")->value("adm_no");

        DB::setDefaultConnection("mysql");
        $this->post("/Circulation/Confirm/check-out", [
            "book_ids" => $book, "book_borrower" => "student", "student_borrower" => $student,
            "staff_borrower" => "Select an option", "expected_return_date" => date("Y-m-d", strtotime("+7 days")),
            "checkout_comment" => "test",
        ])->assertRedirect("/Circulation/check-out")->assertSessionHas("success");

        $this->assertSame(0, (int) $this->school()->table("library_details")->where("book_id", $book)->value("availability_status"));
        $loan = $this->school()->table("book_circulation")->where("book_id", $book)->orderByDesc("circulation_id")->first();
        $this->assertNotNull($loan);
        $this->assertSame(0, (int) $loan->return_status);
        $this->assertSame((string) $student, (string) $loan->user_checked_out);

        // the same copy cannot be checked out twice
        DB::setDefaultConnection("mysql");
        $this->post("/Circulation/Confirm/check-out", [
            "book_ids" => $book, "book_borrower" => "student", "student_borrower" => $student,
            "staff_borrower" => "Select an option", "expected_return_date" => date("Y-m-d", strtotime("+7 days")),
        ])->assertRedirect("/Circulation")->assertSessionHas("error");
        $this->assertSame(1, $this->school()->table("book_circulation")->where("book_id", $book)->count());

        // check it back in
        DB::setDefaultConnection("mysql");
        $this->post("/Circulation/Confirm/check-in/".$loan->circulation_id)->assertRedirect();
        $this->assertSame(1, (int) $this->school()->table("library_details")->where("book_id", $book)->value("availability_status"));
        $this->assertSame(1, (int) $this->school()->table("book_circulation")->where("circulation_id", $loan->circulation_id)->value("return_status"));
    }

    public function test_overdue_job_creates_escaped_notifications_once(): void
    {
        $book = $this->newBook('<script>alert("x")</script> Overdue');
        $student = $this->school()->table("student_data")->value("adm_no");
        $this->school()->table("book_circulation")->insert([
            "book_id" => $book, "book_isbn" => "9990000000001", "book_call_number" => "FT-001",
            "user_checked_out" => $student, "user_borrowing" => "student",
            "date_checked_out" => date("YmdHis", strtotime("-10 days")),
            "expected_return_date" => date("YmdHis", strtotime("-3 days")),
            "checked_out_by" => "1", "return_status" => 0,
        ]);
        Cache::flush();

        $this->artisan("library:notify-overdue", ["--school" => 35601110])->assertSuccessful();
        $note = $this->school()->table("library_notifications")->where("book_id", $book)->first();
        $this->assertNotNull($note, "overdue notification was not created");
        $this->assertStringNotContainsString("<script>", $note->notification_content);
        $this->assertStringContainsString("&lt;script&gt;", $note->notification_content);

        // running again must not duplicate it
        $this->artisan("library:notify-overdue", ["--school" => 35601110])->assertSuccessful();
        $this->assertSame(1, $this->school()->table("library_notifications")->where("book_id", $book)->count());
    }

    public function test_page_views_do_not_rescan_within_the_throttle_window(): void
    {
        $this->login();
        Cache::flush();
        DB::setDefaultConnection("mysql");
        $this->get("/Dashboard")->assertOk();                 // first view runs the scan
        $this->assertTrue(Cache::has("overdue_scan:testimonytbl1"));

        // an overdue loan that appears now is NOT picked up by the next page view (throttled)...
        $book = $this->newBook("Throttle Book");
        $this->school()->table("book_circulation")->insert([
            "book_id" => $book, "user_checked_out" => "1", "user_borrowing" => "student",
            "date_checked_out" => date("YmdHis", strtotime("-5 days")),
            "expected_return_date" => date("YmdHis", strtotime("-1 day")), "return_status" => 0,
        ]);
        DB::setDefaultConnection("mysql");
        $this->get("/Dashboard")->assertOk();
        $this->assertSame(0, $this->school()->table("library_notifications")->where("book_id", $book)->count());

        // ...but is once the window has passed
        Cache::forget("overdue_scan:testimonytbl1");
        DB::setDefaultConnection("mysql");
        $this->get("/Dashboard")->assertOk();
        $this->assertSame(1, $this->school()->table("library_notifications")->where("book_id", $book)->count());
    }
}

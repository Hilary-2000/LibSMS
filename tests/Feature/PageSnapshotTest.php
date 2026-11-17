<?php

namespace Tests\Feature;

use App\Http\Controllers\login;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Renders every logged-in page of the demo school and checks it returns 200 with the shared layout
 * (topbar, sidebar, footer). Set SNAPSHOT_DIR=/some/dir to also save each page's HTML, e.g. to diff
 * the output before and after a refactor. Runs in a transaction: nothing is written permanently.
 */
class PageSnapshotTest extends TestCase
{
    use DatabaseTransactions;

    protected $connectionsToTransact = ["mysql", "mysql2"];

    public const PAGES = [
        "dashboard" => "/Dashboard",
        "acquisitions" => "/Acquisitions",
        "book_details" => "/Acquisitions/Book-details/1",
        "cataloging" => "/Cataloging",
        "catalogue_edit" => "/Cataloging/Edit/9780553381689",
        "circulation" => "/Circulation",
        "checkout_list" => "/Circulation/check-out",
        "checkout_book" => "/Circulation/check-out/1",
        "view_checkout" => "/Circulation/View/check-out/10/5",
        "circ_stats" => "/Circulation/Stats",
        "student_stats" => "/Circulation/Stats/View/Student/AM102",
        "staff_stats" => "/Circulation/Stats/View/Staff/1",
        "reports" => "/Reports",
        "user_mgmt" => "/Settings/User-mgmt",
        "user_detail" => "/Settings/User-mgmt/1",
        "library_mgmt" => "/Settings/Library-mgmt",
        "notifications" => "/Notification",
        "notification_view" => "/Notification/View/3",
        "reservations" => "/Circulation/Reservations",
        "import_books" => "/Acquisitions/Import",
        "stocktake" => "/Acquisitions/Stock-take",
        "audit_log" => "/Settings/Audit-log",
    ];

    public function test_every_page_renders_with_the_shared_layout(): void
    {
        DB::connection("mysql")->table("user_tbl")->insert([
            "user_id" => 990100, "school_code" => 35601110, "fullname" => "Snapshot Tester",
            "phone_number" => "0", "gender" => "F", "address" => "-", "nat_id" => "0", "tsc_no" => "0",
            "username" => "snapshot_tester", "password" => (new login())->encryptCode("Snap-123"),
            "auth" => "LIBRARIAN", "library_previleges" => "[]", "activated" => 1, "deleted" => 0,
        ]);
        DB::setDefaultConnection("mysql");
        $this->post("/process_login", ["username" => "snapshot_tester", "user-password" => "Snap-123"])->assertRedirect("/Dashboard");

        $dir = getenv("SNAPSHOT_DIR") ?: null;
        foreach (self::PAGES as $name => $url) {
            DB::setDefaultConnection("mysql");
            $res = $this->get($url);
            $this->assertSame(200, $res->getStatusCode(), "$url returned ".$res->getStatusCode());
            $html = $res->getContent();
            foreach (['id="page-topbar"', 'class="vertical-menu"', 'id="side-menu"', '<footer class="footer">'] as $needle) {
                $this->assertStringContainsString($needle, $html, "$url is missing $needle");
            }
            if ($dir) {
                // strip per-request noise (CSRF tokens) so before/after diffs only show real changes
                $html = preg_replace('/(name="_token" value=")[^"]+/', '$1X', $html);
                $html = preg_replace('/(name="csrf-token" content=")[^"]+/', '$1X', $html);
                file_put_contents("$dir/$name.html", $html);
            }
        }
    }
}

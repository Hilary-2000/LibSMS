<?php

namespace Tests\Feature;

use App\Http\Controllers\login;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Security regression tests. They run against the local MySQL (ladybird_smis + the demo school
 * database testimonytbl1). Every test runs inside a transaction on both connections, so the
 * temporary users created here are rolled back and nothing is left behind.
 */
class SecurityTest extends TestCase
{
    use DatabaseTransactions;

    protected $connectionsToTransact = ["mysql", "mysql2"];

    private const SCHOOL = 35601110;       // THIS IS DEMO SCHOOL -> testimonytbl1
    private const PASSWORD = "Test-Pass-123";

    private function makeUser(int $id, array $overrides = []): string
    {
        $username = "sec_test_".$id;
        DB::connection("mysql")->table("user_tbl")->insert(array_merge([
            "user_id" => $id,
            "school_code" => self::SCHOOL,
            "fullname" => "Security Test ".$id,
            "phone_number" => "0700000000",
            "gender" => "F",
            "address" => "test",
            "nat_id" => "0",
            "tsc_no" => "0",
            "username" => $username,
            "password" => (new login())->encryptCode(self::PASSWORD),
            "auth" => "LIBRARIAN",
            "library_previleges" => "[]",
            "activated" => 1,
            "deleted" => 0,
        ], $overrides));
        return $username;
    }

    /** The app switches the default connection to the school DB; reset it between requests. */
    private function resetDb(): void
    {
        DB::setDefaultConnection("mysql");
    }

    private function loginAs(string $username, bool $remember = false)
    {
        $this->resetDb();
        $data = ["username" => $username, "user-password" => self::PASSWORD];
        if ($remember) $data["remember-check"] = "on";
        return $this->post("/process_login", $data);
    }

    public function test_pages_require_login(): void
    {
        $this->get("/Dashboard")->assertRedirect("/");
        $this->get("/Circulation")->assertRedirect("/");
        $this->post("/Acquisitions/add-book", [])->assertRedirect("/");
        $this->post("/settings/store_privileges", ["user_id" => 1, "store_privileges" => "[]"])->assertRedirect("/");
    }

    public function test_login_works_and_does_not_store_password(): void
    {
        $username = $this->makeUser(990001);
        $response = $this->loginAs($username, true);

        $response->assertRedirect("/Dashboard");
        $response->assertSessionHas("user_id", 990001);
        $response->assertSessionMissing("user_password");
        $this->assertFalse(isset(session("user_details")->password), "password must not be kept in the session");
        $response->assertCookie(login::REMEMBER_COOKIE);
        $response->assertCookieExpired("user_password"); // old plain-text cookie is deleted, never set

        // the browser only ever sees an AES-encrypted value...
        $encrypted = $response->getCookie(login::REMEMBER_COOKIE, false)->getValue();
        $this->assertStringNotContainsString(self::PASSWORD, $encrypted);
        // ...and even decrypted it is just "<user_id>|<HMAC>", no password
        $plain = $response->getCookie(login::REMEMBER_COOKIE)->getValue();
        $this->assertMatchesRegularExpression('/^990001\|[0-9a-f]{64}$/', $plain);
    }

    public function test_wrong_password_and_inactive_accounts_are_refused(): void
    {
        $username = $this->makeUser(990002);
        $this->resetDb();
        $this->post("/process_login", ["username" => $username, "user-password" => "wrong"])
            ->assertRedirect("/")->assertSessionHas("error");

        $inactive = $this->makeUser(990003, ["activated" => 0]);
        $this->loginAs($inactive)->assertRedirect("/")->assertSessionMissing("user_id");

        $deleted = $this->makeUser(990004, ["deleted" => 1]);
        $this->loginAs($deleted)->assertRedirect("/")->assertSessionMissing("user_id");
    }

    public function test_remember_me_cookie_logs_in_and_rejects_tampering(): void
    {
        $username = $this->makeUser(990005);
        $token = $this->loginAs($username, true)->getCookie(login::REMEMBER_COOKIE, false)->getValue();

        // new browser session with only the cookie
        $this->flushSession();
        $this->resetDb();
        $this->withUnencryptedCookie(login::REMEMBER_COOKIE, $token)->get("/")->assertRedirect("/Dashboard");

        $this->flushSession();
        $this->resetDb();
        $forged = "990005|".str_repeat("0", 64);
        $this->withUnencryptedCookie(login::REMEMBER_COOKIE, $forged)->get("/")->assertOk()->assertSee("Login");

        // changing the password (in any Ladybird system) invalidates the cookie
        DB::connection("mysql")->table("user_tbl")->where("user_id", 990005)->update(["password" => "changed"]);
        $this->flushSession();
        $this->resetDb();
        $this->withUnencryptedCookie(login::REMEMBER_COOKIE, $token)->get("/")->assertOk()->assertSee("Login");
    }

    public function test_legacy_password_cookies_are_removed(): void
    {
        $this->withUnencryptedCookie("user_password", "secret")->get("/")->assertCookieExpired("user_password");
    }

    public function test_logout_ends_the_session(): void
    {
        $username = $this->makeUser(990006);
        $this->loginAs($username);
        $this->resetDb();
        $this->get("/Logout")->assertRedirect("/");
        $this->resetDb();
        $this->get("/Dashboard")->assertRedirect("/");
    }

    public function test_privileges_are_enforced_on_the_server(): void
    {
        $username = $this->makeUser(990007, ["library_previleges" => '["Circulation"]']);
        $this->loginAs($username);

        $this->resetDb();
        $this->get("/Circulation")->assertOk();
        $this->resetDb();
        $this->get("/Acquisitions")->assertRedirect("/Dashboard");
        $this->resetDb();
        $this->get("/Settings/User-mgmt")->assertRedirect("/Dashboard");
        $this->resetDb();
        $this->post("/settings/store_privileges", ["user_id" => 990007, "store_privileges" => "[]"])->assertRedirect("/Dashboard");
        $this->assertSame('["Circulation"]', DB::connection("mysql")->table("user_tbl")->where("user_id", 990007)->value("library_previleges"));
        // shared pages stay reachable for Circulation users
        $this->resetDb();
        $this->get("/Cataloguing/keyword_search/test")->assertOk();
    }

    public function test_privilege_changes_apply_without_relogin(): void
    {
        $username = $this->makeUser(990008);
        $this->loginAs($username);
        $this->resetDb();
        $this->get("/Acquisitions")->assertOk();

        DB::connection("mysql")->table("user_tbl")->where("user_id", 990008)->update(["library_previleges" => '["Report"]']);
        $this->resetDb();
        $this->get("/Acquisitions")->assertRedirect("/Dashboard");
    }

    public function test_stock_take_has_its_own_privilege(): void
    {
        // Acquisitions alone no longer opens Stock-take
        $username = $this->makeUser(990020, ["library_previleges" => '["Acquisition"]']);
        $this->loginAs($username);
        $this->resetDb();
        // neither the sidebar entry nor the button on the Acquisitions page is shown
        // (the "What's new" window may still mention it; the server refuses the page anyway)
        $this->get("/Acquisitions")->assertOk()
            ->assertDontSee('<a href="/Acquisitions/Stock-take" class="waves-effect">', false)
            ->assertDontSee('<a href="/Acquisitions/Stock-take" class="btn', false);
        $this->resetDb();
        $this->get("/Acquisitions/Stock-take")->assertRedirect("/Dashboard");

        // Stock-take alone opens Stock-take but not Acquisitions
        DB::connection("mysql")->table("user_tbl")->where("user_id", 990020)->update(["library_previleges" => '["Stock-take"]']);
        $this->resetDb();
        $this->get("/Acquisitions/Stock-take")->assertOk();
        $this->resetDb();
        $this->get("/Acquisitions")->assertRedirect("/Dashboard");
    }

    public function test_privilege_page_matches_the_menu_and_saves_only_known_privileges(): void
    {
        $this->makeUser(990021, ["library_previleges" => '["Acquisition"]']);
        $admin = $this->makeUser(990022, ["library_previleges" => '["Settings"]']);
        $this->loginAs($admin);
        $this->resetDb();
        $page = $this->get("/Settings/User-mgmt/990021")->assertOk();
        foreach (\App\Http\Middleware\LibraryAccess::PRIVILEGES as $privilege => $info) {
            $page->assertSee('value="'.$privilege.'"', false)->assertSee($info["label"]);
        }
        $page->assertSee("Lend a book, Borrowing history");

        $this->resetDb();
        $this->post("/settings/store_privileges", ["user_id" => 990021, "store_privileges" => '["Report","Stock-take","Hacker","Acquisition"]'])
            ->assertRedirect("/Settings/User-mgmt/990021");
        $this->assertSame('["Acquisition","Stock-take","Report"]', DB::connection("mysql")->table("user_tbl")->where("user_id", 990021)->value("library_previleges"));
    }

    public function test_upgrade_command_gives_acquisition_users_stock_take(): void
    {
        $this->makeUser(990023, ["library_previleges" => '["Acquisition","Report"]']);
        $this->makeUser(990024, ["library_previleges" => '["Circulation"]']);
        $this->artisan("library:add-stocktake-privilege")->assertSuccessful();
        $this->artisan("library:add-stocktake-privilege")->assertSuccessful(); // second run changes nothing
        $users = DB::connection("mysql")->table("user_tbl");
        $this->assertSame('["Acquisition","Stock-take","Report"]', (clone $users)->where("user_id", 990023)->value("library_previleges"));
        $this->assertSame('["Circulation"]', (clone $users)->where("user_id", 990024)->value("library_previleges"));
    }

    public function test_state_changing_actions_are_post_only(): void
    {
        $username = $this->makeUser(990009);
        $this->loginAs($username);
        foreach ([
            "/Acquisitions/Delete-book/1",
            "/Acquisitions/found-book/1",
            "/Circulation/Cancel/check-out/1",
            "/Circulation/Confirm/check-in/1",
            "/Circulation/Cancel/check-in/1",
            "/Settings/Lib-Management/Delete/1",
        ] as $url) {
            $this->resetDb();
            $this->get($url)->assertStatus(405);
        }
    }

    public function test_keyword_searches_are_not_injectable(): void
    {
        $username = $this->makeUser(990010);
        $this->loginAs($username);

        $payload = "zzz' OR '1'='1";
        $this->resetDb();
        $res = $this->get("/Cataloguing/keyword_search/".rawurlencode($payload))->assertOk();
        $this->assertSame([], json_decode($res->getContent(), true) ?: [], "injection payload must match nothing");

        $this->resetDb();
        $this->get("/Acquisitions/keyword_search/".rawurlencode($payload))->assertOk();
        $this->resetDb();
        $this->get("/Acquisitions?keyword_search=".rawurlencode($payload))->assertOk();
        $this->resetDb();
        $this->get("/Cataloging?keyword_search=".rawurlencode($payload))->assertOk();
        $this->resetDb();
        $this->get("/Circulation/check-out?keyword_search=".rawurlencode($payload))->assertOk();
    }
}

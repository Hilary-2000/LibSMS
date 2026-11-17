<?php

namespace App\Http\Controllers;

use App\Services\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

date_default_timezone_set('Africa/Nairobi');
class login extends Controller
{
    // Name of the "remember me" cookie. It holds "<user_id>|<signature>" only - never the password.
    const REMEMBER_COOKIE = "library_remember";
    const REMEMBER_MINUTES = 7200; // 5 days, same lifetime as before
    // Cookies used by the old remember-me, which stored the plain-text password. Always cleared.
    const LEGACY_COOKIES = ["school_code", "username", "user_password", "remember_check"];

    //this controller is used to manage accounts access
    function loginLibrary(Request $request){
        $username = $request->input("username");
        $user_password = $request->input("user-password");
        $remember_check = $request->input("remember-check");

        // keep the username and checkbox to refill the form after an error (never the password)
        session()->flash("username",$username);
        session()->flash("remember_check", $remember_check);

        // confirm if the user is the right one.
        // NOTE: passwords use the shared SIMS encoding (encryptCode); this is intentionally unchanged
        // because the same credentials are used by the other Ladybird systems.
        $encrypt_password = $this->encryptCode((string) $user_password);
        $user_data = DB::select("SELECT * FROM `user_tbl` WHERE `username` = ? AND `password` = ?",[$username,$encrypt_password]);

        // compare case sensitively (MySQL string comparison may be case-insensitive)
        if (count($user_data) == 0 || !hash_equals((string) $user_data[0]->password, $encrypt_password)) {
            session()->flash("error","Incorrect username or password try again!");
            return redirect("/");
        }

        return $this->completeLogin($user_data[0], $remember_check == "on");
    }

    /**
     * Shared by the login form and the remember-me cookie: checks the account may use the library,
     * starts a fresh session and (optionally) issues the remember-me cookie.
     */
    private function completeLogin($user, bool $remember){
        $fail = function (string $message) {
            $this->forgetLoginCookies();
            session()->flash("error", $message);
            return redirect("/");
        };

        // deactivated or deleted staff must not get in
        if ((isset($user->deleted) && (int) $user->deleted === 1) || (isset($user->activated) && (int) $user->activated === 0)) {
            return $fail("Your account is not active. Contact your administrator.");
        }

        // check if the school code is accepted to access the library
        $school_code = $user->school_code;
        $school_details = DB::select("SELECT * FROM `school_information` WHERE `school_code` = ?",[$school_code]);
        if (count($school_details) == 0) {
            return $fail("Cannot login, CONTACT US ASAP!");
        }
        if ($school_details[0]->library_access == 0) {
            return $fail("Your library account has not been activated! Contact us to activate your account!");
        }

        /**
         * 1 Headteacher
         * 0 Administrator
         * Librarian LIBRARIAN Librarian
         */
        $auth = $user->auth;
        if ($auth != 0 && $auth != 1 && strtoupper($auth) !== "LIBRARIAN") {
            return $fail("You have no rights to access this Library Account, contact your Administrator to be allowed back in.");
        }

        // new session id on login (prevents session fixation)
        session()->regenerate();

        session()->put("fullname",$user->fullname);
        session()->put("auth",$auth);
        session()->put("user_id",$user->user_id);
        session()->put("profile_loc",$user->profile_loc);
        session()->put("gender",$user->gender);

        // before adding user details check if their dp is valid
        $user->profile_loc = $this->isLinkValid("https://lsims.ladybirdsmis.com/sims/".$user->profile_loc) ? $user->profile_loc : "images/dp.png";
        // never keep the (encoded) password in the session
        unset($user->password);
        session()->put("user_details",$user);
        session()->put("school_details",$school_details[0]);

        foreach (self::LEGACY_COOKIES as $legacy) {
            Cookie::queue(Cookie::forget($legacy));
        }
        if ($remember) {
            Cookie::queue(Cookie::make(self::REMEMBER_COOKIE, $this->rememberToken($user->user_id), self::REMEMBER_MINUTES, null, null, null, true));
        }
        AuditLog::record("Logged in", null, $remember ? ["method" => "remember-me or form, remember on"] : null);

        return redirect("/Dashboard");
    }

    /**
     * "<user_id>|<HMAC>" signed with APP_KEY over the user id, school and the CURRENT stored password value,
     * so changing the password (in any Ladybird system) invalidates every remembered browser.
     * Laravel additionally encrypts the whole cookie (EncryptCookies middleware).
     */
    private function rememberToken($user_id, $row = null){
        $row = $row ?? DB::selectOne("SELECT `user_id`, `school_code`, `password` FROM `user_tbl` WHERE `user_id` = ?", [$user_id]);
        if ($row === null) return "";
        $signature = hash_hmac("sha256", $row->user_id."|".$row->school_code."|".$row->password, config("app.key"));
        return $row->user_id."|".$signature;
    }

    private function forgetLoginCookies(){
        Cookie::queue(Cookie::forget(self::REMEMBER_COOKIE));
        foreach (self::LEGACY_COOKIES as $legacy) {
            Cookie::queue(Cookie::forget($legacy));
        }
    }
    function dashboard(Request $request){
        // return $request;
        $notifications = $request->input("notifications") != null ? $request->input('notifications') : [];
        $notification_count = $request->input("notification_count");

        // get the dashboard details
        // GET THE BOOKS DETAILS BY GROUPING WITH THE ISBN NUMBER
        $database_name = session("school_details")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        // connect to mysql 2
        DB::setDefaultConnection("mysql2");

        // get total books in the library
        $data = DB::select("SELECT COUNT(*) AS 'Total' FROM `library_details`");
        $book_count = count($data) == 0 ? 0 : number_format($data[0]->Total);

        // books checked out today
        $today = date("Ymd");
        $data = DB::select("SELECT COUNT(*) AS 'Total' FROM `book_circulation` WHERE `date_checked_out` LIKE ?", [$today."%"]);
        $checked_out_today = count($data) == 0 ? 0 : number_format($data[0]->Total);

        // checked in today
        $data = DB::select("SELECT COUNT(*) AS 'Total' FROM `book_circulation` WHERE `return_date` LIKE ?", [$today."%"]);
        $checked_in_today = count($data) == 0 ? 0 : number_format($data[0]->Total);

        // books checked out by user
        $user_id = session("user_id");
        $data = DB::select("SELECT COUNT(*) AS 'Total' FROM `book_circulation` WHERE `checked_out_by` = ?", [$user_id]);
        $check_out_user = count($data) == 0 ? 0 : number_format($data[0]->Total);

        // books checked in by user
        $data = DB::select("SELECT COUNT(*) AS 'Total' FROM `book_circulation` WHERE `checked_in_by` = ?", [$user_id]);
        $check_in_user = count($data) == 0 ? 0 : number_format($data[0]->Total);

        // books checked this month
        $this_month = date("Ym");
        $data = DB::select("SELECT COUNT(*) AS 'Total' FROM `book_circulation` WHERE `date_checked_out` LIKE ?", [$this_month."%"]);
        $borrowed_this_month = count($data) == 0 ? 0 : $data[0]->Total;

        // last month
        $last_month = date("Ym",strtotime("-1 Month"));
        $data = DB::select("SELECT COUNT(*) AS 'Total' FROM `book_circulation` WHERE `date_checked_out` LIKE ?", [$last_month."%"]);
        $borrowed_last_month = count($data) == 0 ? 0 : $data[0]->Total;

        // GET THE DATA FOR THE MOST BORROWED BOOK
        $most_borrowed_books = DB::select("SELECT `book_isbn`, COUNT(*) AS 'Total' FROM `book_circulation` GROUP BY `book_isbn` ORDER BY `Total` DESC LIMIT 3;");
        // loop through the data to get the books details
        for ($index=0; $index < count($most_borrowed_books); $index++) { 
            $book_data  = DB::select("SELECT * FROM `library_details` WHERE `isbn_13` = ? OR `isbn_10` = ? LIMIT 1",[$most_borrowed_books[$index]->book_isbn,$most_borrowed_books[$index]->book_isbn]);
            $book_title = count($book_data) > 0 ? $book_data[0]->book_title : "N/A";

            // book details
            $most_borrowed_books[$index]->book_title = $book_title;
        }
        // return $most_borrowed_books;
        // get the latest books
        $book_data = DB::select("SELECT * FROM `library_details` ORDER BY `book_id` DESC LIMIT 15");

        return view("librarian_dash",["charts" => \App\Services\DashboardCharts::build(), "notification_count" => $notification_count,"notifications" => $notifications, "book_data" => $book_data,"most_borrowed_books" => $most_borrowed_books, "borrowed_last_month" => $borrowed_last_month ,"borrowed_this_month" => $borrowed_this_month, "check_in_user" => $check_in_user ,"check_out_user" => $check_out_user,"book_count" => $book_count,"checked_out_today" => $checked_out_today,"checked_in_today" => $checked_in_today]);
    }
    function getSchools(Request $request){
        // remove the old remember-me cookies that stored the plain-text password. Done unconditionally:
        // Laravel drops cookies it cannot decrypt before we see them, but the browser still holds them.
        foreach (self::LEGACY_COOKIES as $legacy) {
            Cookie::queue(Cookie::forget($legacy));
        }

        // already logged in -> straight to the dashboard
        if (session("school_details") != null && session("user_id") != null) {
            return redirect("/Dashboard");
        }

        // valid remember-me cookie -> log in without the form
        $token = (string) $request->cookie(self::REMEMBER_COOKIE, "");
        if ($token !== "") {
            [$user_id] = explode("|", $token, 2) + [null];
            $user = ctype_digit((string) $user_id) ? DB::selectOne("SELECT * FROM `user_tbl` WHERE `user_id` = ?", [$user_id]) : null;
            if ($user !== null && hash_equals($this->rememberToken($user->user_id, $user), $token)) {
                return $this->completeLogin($user, true);
            }
            // tampered, expired or password changed
            Cookie::queue(Cookie::forget(self::REMEMBER_COOKIE));
        }

        // PROCEED WITH DISPLAYING THE LOGIN PANE
        // connect to the database and get the schools present and activated
        $schools_list = DB::select("SELECT * FROM `school_information`");

        // return value
        for ($index=0; $index < count($schools_list); $index++) {
            $schools_list[$index]->school_name = ucwords(strtolower($schools_list[$index]->school_name));
        }
        return view("login",["school_list"=>$schools_list]);
    }

    function Logout(){
        AuditLog::record("Logged out");
        // end the session (previously only the cookies were cleared and the user stayed logged in)
        $this->forgetLoginCookies();
        session()->flush();
        session()->invalidate();
        session()->regenerateToken();
        return redirect("/");
    }
    function encryptCode($dataToEncrypt)
    {
        //first get char code for each name
        $revdata = strrev($dataToEncrypt);
        $data = str_split($revdata);
        $encrpted = "";
        for ($y = 0; $y < count($data); $y++) {
            $encrpted .= $this->getCode($data[$y]);
        }
        $encrypted = strrev($encrpted);
        return $encrypted;
    }
    function decryptcode($datatodecrypt)
    {
        $arrayeddata = str_split(strrev($datatodecrypt), 3);
        $data = "";
        for ($i = 0; $i < count($arrayeddata); $i++) {
            $data .= $arrayeddata[$i];
        }
        return strrev($data);
    }

    function getCode($code)
    {

        if ($code == 'A') {
            return '$rSv';
        } elseif ($code == 'B') {
            return 'Grp2';
        } elseif ($code == 'C') {
            return 'SnMp';
        } elseif ($code == 'D') {
            return 'Tr#4';
        } elseif ($code == 'E') {
            return '69!4';
        } elseif ($code == 'F') {
            return 'PpQr';
        } elseif ($code == 'G') {
            return 'TpSO';
        } elseif ($code == 'H') {
            return 'IvSr';
        } elseif ($code == 'I') {
            return 'LpTs';
        } elseif ($code == 'J') {
            return 'L496';
        } elseif ($code == 'K') {
            return '674S';
        } elseif ($code == 'L') {
            return 'IqRs';
        } elseif ($code == 'M') {
            return 'Rama';
        } elseif ($code == 'N') {
            return 'Kilo';
        } elseif ($code == 'O') {
            return 'PorT';
        } elseif ($code == 'P') {
            return 'Stea';
        } elseif ($code == 'Q') {
            return 'aTeM';
        } elseif ($code == 'R') {
            return '#4@p';
        } elseif ($code == 'S') {
            return '*9$N';
        } elseif ($code == 'T') {
            return 'NiPs';
        } elseif ($code == 'U') {
            return 'IobT';
        } elseif ($code == 'V') {
            return 'PpRT';
        } elseif ($code == 'W') {
            return 'wTvs';
        } elseif ($code == 'X') {
            return 'SunT';
        } elseif ($code == 'Y') {
            return 'umRT';
        } elseif ($code == 'Z') {
            return 'PrS!';
        } elseif ($code == 'a') {
            return 'ooEV';
        } elseif ($code == 'b') {
            return 'EmpT';
        } elseif ($code == 'c') {
            return 'Rt@P';
        } elseif ($code == 'd') {
            return '#41B';
        } elseif ($code == 'e') {
            return 'Yeyo';
        } elseif ($code == 'f') {
            return 'ZxMU';
        } elseif ($code == 'g') {
            return 'LuMk';
        } elseif ($code == 'h') {
            return 'SaWa';
        } elseif ($code == 'i') {
            return 'Eaws';
        } elseif ($code == 'j') {
            return 'GliM';
        } elseif ($code == 'k') {
            return 'NoNS';
        } elseif ($code == 'l') {
            return 'SiIB';
        } elseif ($code == 'm') {
            return 'prEA';
        } elseif ($code == 'n') {
            return 'ApEM';
        } elseif ($code == 'o') {
            return 'MoeN';
        } elseif ($code == 'p') {
            return 'NoST';
        } elseif ($code == 'q') {
            return 'SeTs';
        } elseif ($code == 'r') {
            return 'RasP';
        } elseif ($code == 's') {
            return 'PaRT';
        } elseif ($code == 't') {
            return 'TrUs';
        } elseif ($code == 'u') {
            return 'LuTr';
        } elseif ($code == 'v') {
            return 'rGgT';
        } elseif ($code == 'w') {
            return 'S@sY';
        } elseif ($code == 'x') {
            return 'YeTr';
        } elseif ($code == 'y') {
            return 'GeTr';
        } elseif ($code == 'z') {
            return 'TrSe';
        } elseif ($code == '0') {
            return 'OE#@';
        } elseif ($code == '1') {
            return 'PsT@';
        } elseif ($code == '2') {
            return 'TrO$';
        } elseif ($code == '3') {
            return '$sTp';
        } elseif ($code == '4') {
            return 'qoRp';
        } elseif ($code == '5') {
            return '?GrP';
        } elseif ($code == '6') {
            return 'OeMr';
        } elseif ($code == '7') {
            return 'StmR';
        } elseif ($code == '8') {
            return 'EpR!';
        } elseif ($code == '9') {
            return 'StpS';
        } elseif ($code == ' ') {
            return 'tP#3';
        } else {
            return "";
        }
    }

    function getChar($code)
    {
        if ($code == '$rSv') {
            return 'A';
        } elseif ($code == 'Grp2') {
            return 'B';
        } elseif ($code == 'SnMp') {
            return 'C';
        } elseif ($code == 'Tr#4') {
            return 'D';
        } elseif ($code == '69!4') {
            return 'E';
        } elseif ($code == 'PpQr') {
            return 'F';
        } elseif ($code == 'TpSO') {
            return 'G';
        } elseif ($code == 'IvSr') {
            return 'H';
        } elseif ($code == 'LpTs') {
            return 'I';
        } elseif ($code == 'L496') {
            return 'J';
        } elseif ($code == '674S') {
            return 'K';
        } elseif ($code == 'IqRs') {
            return 'L';
        } elseif ($code == 'Rama') {
            return 'M';
        } elseif ($code == 'Kilo') {
            return 'N';
        } elseif ($code == 'PorT') {
            return 'O';
        } elseif ($code == 'Stea') {
            return 'P';
        } elseif ($code == 'aTeM') {
            return 'Q';
        } elseif ($code == '#4@p') {
            return 'R';
        } elseif ($code == '*9$N') {
            return 'S';
        } elseif ($code == 'NiPs') {
            return 'T';
        } elseif ($code == 'IobT') {
            return 'U';
        } elseif ($code == 'PpRT') {
            return 'V';
        } elseif ($code == 'wTvs') {
            return 'W';
        } elseif ($code == 'SunT') {
            return 'X';
        } elseif ($code == 'umRT') {
            return 'Y';
        } elseif ($code == 'PrS!') {
            return 'Z';
        } elseif ($code == 'ooEV') {
            return 'a';
        } elseif ($code == 'EmpT') {
            return 'b';
        } elseif ($code == 'Rt@P') {
            return 'c';
        } elseif ($code == '#41B') {
            return 'd';
        } elseif ($code == 'Yeyo') {
            return 'e';
        } elseif ($code == 'ZxMU') {
            return 'f';
        } elseif ($code == 'LuMk') {
            return 'g';
        } elseif ($code == 'SaWa') {
            return 'h';
        } elseif ($code == 'Eaws') {
            return 'i';
        } elseif ($code == 'GliM') {
            return 'j';
        } elseif ($code == 'NoNS') {
            return 'k';
        } elseif ($code == 'SiIB') {
            return 'l';
        } elseif ($code == 'prEA') {
            return 'm';
        } elseif ($code == 'ApEM') {
            return 'n';
        } elseif ($code == 'MoeN') {
            return 'o';
        } elseif ($code == 'NoST') {
            return 'p';
        } elseif ($code == 'SeTs') {
            return 'q';
        } elseif ($code == 'RasP') {
            return 'r';
        } elseif ($code == 'PaRT') {
            return 's';
        } elseif ($code == 'TrUs') {
            return 't';
        } elseif ($code == 'LuTr') {
            return 'u';
        } elseif ($code == 'rGgT') {
            return 'v';
        } elseif ($code == 'S@sY') {
            return 'w';
        } elseif ($code == 'YeTr') {
            return 'x';
        } elseif ($code == 'GeTr') {
            return 'y';
        } elseif ($code == 'TrSe') {
            return 'z';
        } elseif ($code == 'OE#@') {
            return '0';
        } elseif ($code == 'PsT@') {
            return '1';
        } elseif ($code == 'TrO$') {
            return '2';
        } elseif ($code == '$sTp') {
            return '3';
        } elseif ($code == 'qoRp') {
            return '4';
        } elseif ($code == '?GrP') {
            return '5';
        } elseif ($code == 'OeMr') {
            return '6';
        } elseif ($code == 'StmR') {
            return '7';
        } elseif ($code == 'EpR!') {
            return '8';
        } elseif ($code == 'StpS') {
            return '9';
        } elseif ($code == 'tP#3') {
            return ' ';
        } else {
            return "";
        }
    }
}

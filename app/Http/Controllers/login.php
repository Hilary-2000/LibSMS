<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

date_default_timezone_set('Africa/Nairobi');
class login extends Controller
{
    //this controller is used to manage accounts access
    function loginLibrary(Request $request){
        // return $request->input();
        $school_code = $request->input("school_code");
        $username = $request->input("username");
        $user_password = $request->input("user-password");
        $remember_check = $request->input("remember-check");

        // set flash session to store the user credentials for the sake of those who forget
        session()->flash("school_code",$school_code);
        session()->flash("username",$username);
        session()->flash("user_password",$user_password);
        session()->flash("remember_check", $remember_check);

        // confirm if the user is the right one.
        $encrypt_password = $this->encryptCode($user_password);
        $user_data = DB::select("SELECT * FROM `user_tbl` WHERE `school_code` = ? AND `username` = ? AND `password` = ?",[$school_code,$username,$encrypt_password]);
        // return $user_data;

        // if the data returns the user data then the password is correct
        if (count($user_data) > 0) {
            // check if the username password match case sensitively
            if ($user_data[0]->password != $encrypt_password) {
                session()->flash("error","Incorrect credentials, check your school code, username or password for errors.");
                return redirect("/");
            }

            // set the cookies to let this user online until later
            if ($remember_check == "on") {
                // create a cookie to store user data store it for 7 days
                Cookie::queue(Cookie::make("school_code",$school_code,7200));
                Cookie::queue(Cookie::make("username",$username,7200));
                Cookie::queue(Cookie::make("user_password",$user_password,7200));
                Cookie::queue(Cookie::make("remember_check",$remember_check,7200));
            }

            // check if they are either administrator librarian or headteacher
            $fullname = $user_data[0]->fullname;
            $auth = $user_data[0]->auth;
            $user_id = $user_data[0]->user_id;
            $profile_loc = $user_data[0]->profile_loc;
            $gender = $user_data[0]->gender;

            // set the username fullname
            session()->put("fullname",$fullname);
            session()->put("auth",$auth);
            session()->put("user_id",$user_id);
            session()->put("profile_loc",$profile_loc);
            session()->put("gender",$gender);
            session()->put("user_details",$user_data[0]);

            // create the user librarian
            /**
             * 1 Headteacher
             * 0 Administrator
             * Librarian LIBRARIAN Librarian
             */
            // return $auth;
            if ($auth != 0 && $auth != 1 && strtoupper($auth) !== "LIBRARIAN") {
                // return an error to show they are not allowed in
                session()->flash("error","You have no rights to access this Library Account, contact your Administrator to be allowed back in.");
                
                // destroy all the cookies
                Cookie::queue(Cookie::forget("school_code"));
                Cookie::queue(Cookie::forget("username"));
                Cookie::queue(Cookie::forget("user_password"));

                // return to homepage
                return redirect("/");
            }

            // get the school details and store it in a session
            $school_details = DB::select("SELECT * FROM `school_information` WHERE `school_code` = ?",[$school_code]);

            // add the school information
            if (count($school_details) > 0) {
                session()->put("school_details",$school_details[0]);
            }else {
                session()->flash("error","An error occured, please contact your administrator.");

                // destroy all the cookies
                Cookie::queue(Cookie::forget("school_code"));
                Cookie::queue(Cookie::forget("username"));
                Cookie::queue(Cookie::forget("user_password"));
                return redirect("/");
            }

            return redirect("/Dashboard");
        }else{
            session()->flash("error","Incorrect credentials, check your school code, username or password for errors.");

            // destroy all the cookies
            Cookie::queue(Cookie::forget("school_code"));
            Cookie::queue(Cookie::forget("username"));
            Cookie::queue(Cookie::forget("user_password"));
            return redirect("/");
        }
    }
    function dashboard(){
        if (!session("fullname")) {
            session()->flash("error","Provide your credentials to Login!");
            return redirect("/");
        }

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
        $data = DB::select("SELECT COUNT(*) AS 'Total' FROM `book_circulation` WHERE `date_checked_out` LIKE '".$today."%'");
        $checked_out_today = count($data) == 0 ? 0 : number_format($data[0]->Total);

        // checked in today
        $data = DB::select("SELECT COUNT(*) AS 'Total' FROM `book_circulation` WHERE `return_date` LIKE '".$today."%'");
        $checked_in_today = count($data) == 0 ? 0 : number_format($data[0]->Total);

        // books checked out by user
        $user_id = session("user_id");
        $data = DB::select("SELECT COUNT(*) AS 'Total' FROM `book_circulation` WHERE `checked_out_by` = '".$user_id."'");
        $check_out_user = count($data) == 0 ? 0 : number_format($data[0]->Total);

        // books checked in by user
        $data = DB::select("SELECT COUNT(*) AS 'Total' FROM `book_circulation` WHERE `checked_in_by` = '".$user_id."'");
        $check_in_user = count($data) == 0 ? 0 : number_format($data[0]->Total);

        // books checked this month
        $this_month = date("Ym");
        $data = DB::select("SELECT COUNT(*) AS 'Total' FROM `book_circulation` WHERE `date_checked_out` LIKE '".$this_month."%'");
        $borrowed_this_month = count($data) == 0 ? 0 : $data[0]->Total;

        // last month
        $last_month = date("Ym",strtotime("-1 Month"));
        $data = DB::select("SELECT COUNT(*) AS 'Total' FROM `book_circulation` WHERE `date_checked_out` LIKE '".$last_month."%'");
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

        return view("librarian_dash",["book_data" => $book_data,"most_borrowed_books" => $most_borrowed_books, "borrowed_last_month" => $borrowed_last_month ,"borrowed_this_month" => $borrowed_this_month, "check_in_user" => $check_in_user ,"check_out_user" => $check_out_user,"book_count" => $book_count,"checked_out_today" => $checked_out_today,"checked_in_today" => $checked_in_today]);
    }
    function getSchools(){
        // get if cookies are still available so that we bypass the login process
        if (Cookie::has('school_code')) {
            // bypass and login
            $school_code = Cookie::get("school_code");
            $username = Cookie::get("username");
            $user_password = Cookie::get("user_password");

            // confirm if the user is the right one.
            $encrypt_password = $this->encryptCode($user_password);
            $user_data = DB::select("SELECT * FROM `user_tbl` WHERE `school_code` = ? AND `username` = ? AND `password` = ?",[$school_code,$username,$encrypt_password]);
            

            // if the data returns the user data then the password is correct
            if (count($user_data) > 0) {
                // check if the username password match case sensitively
                if ($user_data[0]->password != $encrypt_password) {
                    session()->flash("error","Incorrect credentials, check your school code, username or password for errors.");
                    // destroy all the cookies
                    Cookie::queue(Cookie::forget("school_code"));
                    Cookie::queue(Cookie::forget("username"));
                    Cookie::queue(Cookie::forget("user_password"));
                    return redirect("/");
                }

                // check if they are either administrator librarian or headteacher
                $fullname = $user_data[0]->fullname;
                $auth = $user_data[0]->auth;
                $user_id = $user_data[0]->user_id;
                $profile_loc = $user_data[0]->profile_loc;
                $gender = $user_data[0]->gender;

                // set the username fullname
                session()->put("fullname",$fullname);
                session()->put("auth",$auth);
                session()->put("user_id",$user_id);
                session()->put("profile_loc",$profile_loc);
                session()->put("gender",$gender);
                session()->put("user_details",$user_data[0]);

                // create the user librarian
                /**
                 * 1 Headteacher
                 * 0 Administrator
                 * Librarian LIBRARIAN Librarian
                 */
                // return $auth;
                if ($auth != 0 && $auth != 1 && strtoupper($auth) !== "LIBRARIAN") {
                    // return an error to show they are not allowed in
                    session()->flash("error","You have no rights to access this Library Account, contact your Administrator to be allowed back in.");
                    
                    // destroy all the cookies
                    Cookie::queue(Cookie::forget("school_code"));
                    Cookie::queue(Cookie::forget("username"));
                    Cookie::queue(Cookie::forget("user_password"));

                    // return to homepage
                    return redirect("/");
                }

                // get the school details and store it in a session
                $school_details = DB::select("SELECT * FROM `school_information` WHERE `school_code` = ?",[$school_code]);

                // add the school information
                if (count($school_details) > 0) {
                    session()->put("school_details",$school_details[0]);
                }else {
                    session()->flash("error","An error occured, please contact your administrator.");
    
                    // destroy all the cookies
                    Cookie::queue(Cookie::forget("school_code"));
                    Cookie::queue(Cookie::forget("username"));
                    Cookie::queue(Cookie::forget("user_password"));
                    return redirect("/");
                }

                return redirect("/Dashboard");
            }else{
                session()->flash("error","Incorrect credentials, check your school code, username or password for errors.");

                // destroy all the cookies
                Cookie::queue(Cookie::forget("school_code"));
                Cookie::queue(Cookie::forget("username"));
                Cookie::queue(Cookie::forget("user_password"));
                return redirect("/");
            }
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
        // destroy all the cookies
        Cookie::queue(Cookie::forget("school_code"));
        Cookie::queue(Cookie::forget("username"));
        Cookie::queue(Cookie::forget("user_password"));
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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

date_default_timezone_set('Africa/Nairobi');
class Circulation extends Controller
{
    function viewBookStats($book_isbn){
        if (session("school_details") == null) {
            session()->flash("error","Your session has expired, Login to proceed!");
            return redirect("/");
        }
        $date = "2023/11/10";
        $message = "The book stats dashboard is coming soon. Stay tuned!";
        $redirect_link = "";
        return view("admin.coming_soon",["date" => $date, "message" => $message, "redirect_link" => $redirect_link]);
    }
    // circulation stats
    function viewStudentStats($student_id){

        if (session("school_details") == null) {
            session()->flash("error","Your session has expired, Login to proceed!");
            return redirect("/");
        }
        // flash session
        session()->flash("student_id", $student_id);
        // GET THE BOOKS DETAILS BY GROUPING WITH THE ISBN NUMBER
        $database_name = session("school_details")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        // connect to mysql 2
        DB::setDefaultConnection("mysql2");

        // student id
        $student_data = DB::select("SELECT * FROM `student_data` WHERE `adm_no` = ?",[$student_id]);

        // set the user data
        $borrower_data = new stdClass();
        $borrower_data->fullname = count($student_data) > 0 ? $student_data[0]->first_name." ".$student_data[0]->second_name." ".$student_data[0]->surname : "N/A";
        $borrower_data->role = "Student";
        $borrower_data->dp = count($student_data) > 0 ? (strlen($student_data[0]->student_image) > 0 ? $student_data[0]->student_image : "https://lsims.ladybirdsmis.com/sims/images/dp.png") : "https://lsims.ladybirdsmis.com/sims/images/dp.png";
        

        // get the total number of books checked in, borrowed and returned
        $books_checked_out = DB::select("SELECT COUNT(*) AS 'Total' FROM `book_circulation` WHERE `user_checked_out` = ? AND `user_borrowing` = 'student' AND `return_status` = '0'",[$student_id]);
        $books_checked_out = count($books_checked_out) > 0 ? $books_checked_out[0]->Total : 0;

        // get the number of books borrowed and returned
        $books_checked_in = DB::select("SELECT COUNT(*) AS 'Total' FROM `book_circulation` WHERE `user_checked_out` = ? AND `user_borrowing` = 'student' AND `return_status` = '1'",[$student_id]);
        $books_checked_in = count($books_checked_in) > 0 ? $books_checked_in[0]->Total : 0;

        // sql to get the total
        $total_books = DB::select("SELECT BC.book_isbn, LD.book_title, COUNT(BC.book_isbn) AS 'Total' FROM `book_circulation` AS BC LEFT JOIN 
        (
            SELECT DISTINCT `isbn_13`,`book_title`
            FROM `library_details`
        ) AS LD ON BC.book_isbn = LD.isbn_13  WHERE `user_checked_out` = ? GROUP BY `book_isbn`, `book_title` ORDER BY `Total` DESC LIMIT 1",[$student_id]);
        // return $total_books;

        // get the checkout and checkin details
        $checked_out = DB::select("SELECT BC.book_id, LD.book_title, BC.book_call_number, CONCAT(SD.first_name, ' ', SD.second_name, ' ', SD.surname) AS user_fullname, BC.user_borrowing, BC.expected_return_date, BC.circulation_id FROM `book_circulation` AS BC
                                    LEFT JOIN `library_details` AS LD
                                    ON LD.book_id = BC.book_id
                                    LEFT JOIN `student_data` AS SD
                                    ON BC.user_checked_out = SD.adm_no
                                    WHERE `return_status` = '0' AND `user_checked_out` = ? LIMIT 300",[$student_id]);
        
        // get the checkout details
        $checked_in = DB::select("SELECT BC.book_id, BC.checked_out_by, BC.checked_in_by, BC.return_date, LD.book_title, BC.book_call_number, CONCAT(SD.first_name, ' ', SD.second_name, ' ', SD.surname) AS user_fullname, BC.user_borrowing, BC.expected_return_date,BC.date_checked_out, BC.circulation_id FROM `book_circulation` AS BC
        LEFT JOIN `library_details` AS LD
        ON LD.book_id = BC.book_id
        LEFT JOIN `student_data` AS SD
        ON BC.user_checked_out = SD.adm_no
        WHERE `user_checked_out` = ? AND BC.return_status = '1' LIMIT 300;",[$student_id]);
        // return $checked_in;

        // CHECKED IN BY
        DB::setDefaultConnection("mysql");
        for ($index = 0; $index < count($checked_in); $index++) {
            $checked_in[$index]->edit_date_checked_out = date("D dS M Y",strtotime($checked_in[$index]->date_checked_out));
            $checked_in[$index]->edit_expected_return_date = date("D dS M Y",strtotime($checked_in[$index]->expected_return_date));


            $checked_in[$index]->edit_return_date = date("D dS M Y",strtotime($checked_in[$index]->return_date));
            $user_data = DB::select("SELECT * FROM `user_tbl` WHERE `user_id` = ?",[$checked_in[$index]->checked_out_by]);
            $checked_in[$index]->fullname_check_out = count($user_data) > 0 ? ucwords(strtolower($user_data[0]->fullname)) : "NA";
            $user_data = DB::select("SELECT * FROM `user_tbl` WHERE `user_id` = ?",[$checked_in[$index]->checked_in_by]);
            $checked_in[$index]->fullname_check_in = count($user_data) > 0 ? ucwords(strtolower($user_data[0]->fullname)) : "NA";
        }

        // THE STUDENT CIRCULATION HISTORY
        DB::setDefaultConnection("mysql2");
        $select = DB::select("SELECT `circulation_id`,`book_title`, `date_checked_out`,`expected_return_date`,`return_status`,`return_date`,  UT.fullname AS checked_out_by_user, UT2.fullname AS checked_in_by_user FROM `book_circulation` AS BC
                            JOIN (SELECT DISTINCT `book_title`, `isbn_13`,`book_id` FROM `library_details`) AS LD
                            ON BC.book_id = LD.book_id
                            LEFT JOIN `ladybird_smis`.`user_tbl` AS UT
                            ON BC.checked_out_by = UT.user_id
                            LEFT JOIN `ladybird_smis`.`user_tbl` AS UT2
                            ON BC.checked_in_by = UT2.user_id
                            WHERE `user_checked_out` = ?
                            ORDER BY `circulation_id` DESC;",[$student_id]);
        
        // create the circulation log
        // return $select;
        $circulation_data = [];
        for ($index=0; $index < count($select); $index++) { 
            // check in then check outs

            // check in only if the return status is 1
            if ($select[$index]->return_status == 1) {
                $data_in = new stdClass();
                $data_in->status = 'Check-In';
                $data_in->story = "<i><b>'".$select[$index]->book_title."'</b></i> was returned on <i><b>".date("D, dS M Y",strtotime($select[$index]->return_date))."</b></i> and the expected return date was <i><b>".date("D, dS M Y",strtotime($select[$index]->return_date))."</b></i>. It was checked in by <i><b>". ucwords(strtolower($select[$index]->checked_in_by_user))." </i></b>";

                // save the data
                array_push($circulation_data,$data_in);
            }


            // check out
            $data_in = new stdClass();
            $data_in->status = 'Check-Out';
            $data_in->story = "<i><b>'".$select[$index]->book_title."'</b></i> was borrowed on <i><b>".date("D, dS M Y",strtotime($select[$index]->date_checked_out))."</b></i>, and it was expected to be returned on <i><b>".date("D, dS M Y",strtotime($select[$index]->expected_return_date))."</b></i>. It was checked out by <i><b>". ucwords(strtolower($select[$index]->checked_out_by_user))."</b></i>";

            // save the data
            array_push($circulation_data,$data_in);

        }

        // return $circulation_data;

        // return view
        return view("user_circulation_profile",["circulation_data" => $circulation_data,"total_books" => $total_books, "books_checked_in" => $books_checked_in, "books_checked_out" => $books_checked_out,"borrower_data" => $borrower_data, "checked_in" => $checked_in,"checked_out" => $checked_out]);
    }

    // view staff details
    function viewStaffStats($staff_id){
        if (session("school_details") == null) {
            session()->flash("error","Your session has expired, Login to proceed!");
            return redirect("/");
        }
        // flash session
        session()->flash("staff_id", $staff_id);
        
        // GET THE BOOKS DETAILS BY GROUPING WITH THE ISBN NUMBER
        $database_name = session("school_details")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        // connect to mysql 2
        DB::setDefaultConnection("mysql");

        // student id
        $staff_data = DB::select("SELECT * FROM `user_tbl` WHERE `user_id` = ?",[$staff_id]);
        // return $staff_data;

        // set the user data
        $borrower_data = new stdClass();
        $borrower_data->fullname = count($staff_data) > 0 ? $staff_data[0]->fullname : "N/A";
        
        // set borrower role
        if (count($staff_data) > 0) {
            if($staff_data[0]->auth=='0'){
                $borrower_data->role = "Administrator";
            }elseif ($staff_data[0]->auth=='1') {
                $borrower_data->role = "Headteacher/Principal";
            }elseif ($staff_data[0]->auth=='2') {
                $borrower_data->role = "Teacher";
            }elseif ($staff_data[0]->auth=='3') {
                $borrower_data->role = "Deputy principal";
            }elseif ($staff_data[0]->auth=='4') {
                $borrower_data->role = "Staff";
            }elseif ($staff_data[0]->auth=='5') {
                $borrower_data->role = "Class teacher";
            }elseif ($staff_data[0]->auth=='6') {
                $borrower_data->role = "School Driver";
            }else {
                $borrower_data->role = $staff_data[0]->auth;
            }
        }else {
            $borrower_data->role = "Not Set";
        }
        $borrower_data->dp = count($staff_data) > 0 ? (strlen($staff_data[0]->profile_loc) > 0 ? "https://lsims.ladybirdsmis.com/sims/".$staff_data[0]->profile_loc : "https://lsims.ladybirdsmis.com/sims/images/dp.png") : "https://lsims.ladybirdsmis.com/sims/images/dp.png";
        
        // default mysql
        // return $borrower_data;
        DB::setDefaultConnection("mysql2");

        // get the total number of books checked in, borrowed and returned
        $books_checked_out = DB::select("SELECT COUNT(*) AS 'Total' FROM `book_circulation` WHERE `user_checked_out` = ? AND `user_borrowing` = 'staff' AND `return_status` = '0'",[$staff_id]);
        $books_checked_out = count($books_checked_out) > 0 ? $books_checked_out[0]->Total : 0;

        // // get the number of books borrowed and returned
        $books_checked_in = DB::select("SELECT COUNT(*) AS 'Total' FROM `book_circulation` WHERE `user_checked_out` = ? AND `user_borrowing` = 'staff' AND `return_status` = '1'",[$staff_id]);
        $books_checked_in = count($books_checked_in) > 0 ? $books_checked_in[0]->Total : 0;

        // sql to get the total
        $total_books = DB::select("SELECT BC.book_isbn, LD.book_title, COUNT(BC.book_isbn) AS 'Total' FROM `book_circulation` AS BC LEFT JOIN 
        (
            SELECT DISTINCT `isbn_13`,`book_title`
            FROM `library_details`
        ) AS LD ON BC.book_isbn = LD.isbn_13  WHERE `user_checked_out` = ? GROUP BY `book_isbn`, `book_title` ORDER BY `Total` DESC LIMIT 1",[$staff_id]);
        // return $total_books;

        // get the checkout and checkin details
        // return session('school_details');
        $checked_out = DB::select("SELECT BC.book_id, LD.book_title, BC.book_call_number, UT.fullname AS user_fullname, BC.user_borrowing, BC.expected_return_date, BC.circulation_id FROM `book_circulation` AS BC
                                    LEFT JOIN `library_details` AS LD
                                    ON LD.book_id = BC.book_id
                                    LEFT JOIN `ladybird_smis`.`user_tbl` AS UT
                                    ON BC.user_checked_out = UT.user_id
                                    WHERE `return_status` = '0' AND `user_checked_out` = ? LIMIT 300",[$staff_id]);
        // return $checked_out;
        // // get the checkout details
        $checked_in = DB::select("SELECT BC.book_id, BC.checked_out_by, BC.checked_in_by, BC.return_date, LD.book_title, BC.book_call_number, UT.fullname AS user_fullname, BC.user_borrowing, BC.expected_return_date,BC.date_checked_out, BC.circulation_id FROM `book_circulation` AS BC
        LEFT JOIN `library_details` AS LD
        ON LD.book_id = BC.book_id
        LEFT JOIN `ladybird_smis`.`user_tbl` AS UT
        ON BC.user_checked_out = UT.user_id
        WHERE `user_checked_out` = ? AND BC.return_status = '1' LIMIT 300;",[$staff_id]);
        // return $checked_in;

        // CHECKED IN BY
        DB::setDefaultConnection("mysql");
        for ($index = 0; $index < count($checked_in); $index++) {
            $checked_in[$index]->edit_date_checked_out = date("D dS M Y",strtotime($checked_in[$index]->date_checked_out));
            $checked_in[$index]->edit_expected_return_date = date("D dS M Y",strtotime($checked_in[$index]->expected_return_date));


            $checked_in[$index]->edit_return_date = date("D dS M Y",strtotime($checked_in[$index]->return_date));
            $user_data = DB::select("SELECT * FROM `user_tbl` WHERE `user_id` = ?",[$checked_in[$index]->checked_out_by]);
            $checked_in[$index]->fullname_check_out = count($user_data) > 0 ? ucwords(strtolower($user_data[0]->fullname)) : "NA";
            $user_data = DB::select("SELECT * FROM `user_tbl` WHERE `user_id` = ?",[$checked_in[$index]->checked_in_by]);
            $checked_in[$index]->fullname_check_in = count($user_data) > 0 ? ucwords(strtolower($user_data[0]->fullname)) : "NA";
        }

        // THE STUDENT CIRCULATION HISTORY
        DB::setDefaultConnection("mysql2");
        $select = DB::select("SELECT `circulation_id`,`book_title`, `date_checked_out`,`expected_return_date`,`return_status`,`return_date`,  UT.fullname AS checked_out_by_user, UT2.fullname AS checked_in_by_user FROM `book_circulation` AS BC
                            JOIN (SELECT DISTINCT `book_title`, `isbn_13`,`book_id` FROM `library_details`) AS LD
                            ON BC.book_id = LD.book_id
                            LEFT JOIN `ladybird_smis`.`user_tbl` AS UT
                            ON BC.checked_out_by = UT.user_id
                            LEFT JOIN `ladybird_smis`.`user_tbl` AS UT2
                            ON BC.checked_in_by = UT2.user_id
                            WHERE `user_checked_out` = ?
                            ORDER BY `circulation_id` DESC;",[$staff_id]);
        
        // create the circulation log
        // return $select;
        $circulation_data = [];
        for ($index=0; $index < count($select); $index++) { 
            // check in then check outs

            // check in only if the return status is 1
            if ($select[$index]->return_status == 1) {
                $data_in = new stdClass();
                $data_in->status = 'Check-In';
                $data_in->story = "<i><b>'".$select[$index]->book_title."'</b></i> was returned on <i><b>".date("D, dS M Y",strtotime($select[$index]->return_date))."</b></i> and the expected return date was <i><b>".date("D, dS M Y",strtotime($select[$index]->return_date))."</b></i>. It was checked in by <i><b>". ucwords(strtolower($select[$index]->checked_in_by_user))." </i></b>";

                // save the data
                array_push($circulation_data,$data_in);
            }


            // check out
            $data_in = new stdClass();
            $data_in->status = 'Check-Out';
            $data_in->story = "<i><b>'".$select[$index]->book_title."'</b></i> was borrowed on <i><b>".date("D, dS M Y",strtotime($select[$index]->date_checked_out))."</b></i>, and it was expected to be returned on <i><b>".date("D, dS M Y",strtotime($select[$index]->expected_return_date))."</b></i>. It was checked out by <i><b>". ucwords(strtolower($select[$index]->checked_out_by_user))."</b></i>";

            // save the data
            array_push($circulation_data,$data_in);

        }
        // return $circulation_data;
        // return view
        return view("staff_circulation_profile",["circulation_data" => $circulation_data, "total_books" => $total_books, "books_checked_in" => $books_checked_in, "books_checked_out" => $books_checked_out,"borrower_data" => $borrower_data, "checked_in" => $checked_in,"checked_out" => $checked_out]);
    }

    // circulation stats
    function circulationStats(Request $request){
        if (session("school_details") == null) {
            session()->flash("error","Your session has expired, Login to proceed!");
            return redirect("/");
        }
        // GET THE BOOKS DETAILS BY GROUPING WITH THE ISBN NUMBER
        $database_name = session("school_details")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        // connect to mysql 2
        DB::setDefaultConnection("mysql2");

        if (count($request->input()) > 0) {
            // return $request;
            $borrower_stats = $request->input("borrower_stats");
            $borrower_type_opt = $request->input("borrower_type_opt");
            $borrower_period_opt = $request->input("borrower_period_opt");
            $student_details = $request->input("student_details");

            // session setting
            session()->flash("borrower_stats",$borrower_stats);
            session()->flash("borrower_type_opt",$borrower_type_opt);
            session()->flash("borrower_period_opt",$borrower_period_opt);
            session()->flash("student_details",$student_details);

            // how to retrieve the data
            /**
             * if the borrower is student get the student detail
             * if the borrower is staff also get the staff details
             */

            // get the term one two and three periods
            $start_period = date("Ymd",strtotime("01-01-".date("Y")))."000000";
            $end_period = date("Ymd",strtotime("31-12-".date("Y")))."235959";

            // get the academic calender
            $academic_calender = DB::select("SELECT * FROM `academic_calendar`;");
            for ($index=0; $index < count($academic_calender); $index++) { 
                if ($borrower_period_opt == $academic_calender[$index]->term) {
                    $start_period = date("Ymd",strtotime($academic_calender[$index]->start_time))."000000";
                    $end_period = date("Ymd",strtotime($academic_calender[$index]->end_time))."235959";
                    break;
                }
            }

            // if the selected the whole year
            if ($borrower_period_opt == "All Year") {
                for ($index=0; $index < count($academic_calender); $index++) { 
                    if ($borrower_period_opt == "TERM_1") {
                        $start_period = date("Ymd",strtotime($academic_calender[$index]->start_time))."000000";
                    }
                    if ($borrower_period_opt == "TERM_3") {
                        $end_period = date("Ymd",strtotime($academic_calender[$index]->end_time))."235959";
                    }
                }
            }

            // lets start by the student
            $display_data = [];
            $table_column = [];
            if ($borrower_type_opt == "students") {
                // check if they want the highest borrower or the highest borrowed book
                if ($borrower_stats == "borrower_stats") {
                    $table_column = ["#", "Fullname","Class","Adm No.","Books Borrowed", "Action"];
                    // this is the highest student borrower
                    if ($student_details != "whole school") {
                        $borrower_students_stats = DB::select("SELECT (SELECT CONCAT(`first_name`, ' ', `second_name`) AS 'Fullname' FROM `student_data` WHERE `adm_no` = `user_checked_out`) AS 'Fullname',(SELECT `stud_class` AS 'stud_class' FROM `student_data` WHERE `adm_no` = `user_checked_out`) AS 'class_enrolled',`user_checked_out` ,COUNT(*) AS 'borrowed_books' FROM `book_circulation` WHERE (SELECT `stud_class` AS 'stud_class' FROM `student_data` WHERE `adm_no` = `user_checked_out`) = ? AND `user_borrowing` = 'student' AND `date_checked_out` BETWEEN ? AND ?  GROUP BY `user_checked_out` ORDER BY `borrowed_books` DESC LIMIT 300;",[$student_details,$start_period,$end_period]);
                    }else {
                        $borrower_students_stats = DB::select("SELECT (SELECT CONCAT(`first_name`, ' ', `second_name`) AS 'Fullname' FROM `student_data` WHERE `adm_no` = `user_checked_out`) AS 'Fullname',(SELECT `stud_class` AS 'stud_class' FROM `student_data` WHERE `adm_no` = `user_checked_out`) AS 'class_enrolled',`user_checked_out` ,COUNT(*) AS 'borrowed_books' FROM `book_circulation` WHERE ((SELECT `stud_class` AS 'stud_class' FROM `student_data` WHERE `adm_no` = `user_checked_out`) != '-1') AND ((SELECT `stud_class` AS 'stud_class' FROM `student_data` WHERE `adm_no` = `user_checked_out`) != '-2') AND `user_borrowing` = 'student' AND `date_checked_out` BETWEEN ? AND ?  GROUP BY `user_checked_out` ORDER BY `borrowed_books` DESC LIMIT 300;",[$start_period,$end_period]);
                    }
                    $display_data = $borrower_students_stats;
                    // return $display_data;
                    for ($index=0; $index < count($display_data); $index++) {
                        $display_data[$index]->class_enrolled = $this->myClassName($display_data[$index]->class_enrolled);
                        $display_data[$index]->borrowed_books = $display_data[$index]->borrowed_books." book(s)";
                        $display_data[$index]->action_id = "/Circulation/Stats/View/Student/".$display_data[$index]->user_checked_out;
                    }
                }elseif ($borrower_stats == "books_borrowed_stats") {
                    $borrower_details = DB::select("SELECT (SELECT `book_title` FROM `library_details` WHERE `isbn_13` = `book_isbn` LIMIT 1) AS 'Book Title',`book_isbn`, COUNT(*) AS 'books_borrowed' FROM `book_circulation` WHERE `user_borrowing` = 'student' AND `date_checked_out` BETWEEN ? AND ? GROUP BY `book_isbn` ORDER BY `books_borrowed` DESC LIMIT 300;",[$start_period,$end_period]);
                    
                    // get the student data
                    // return $borrower_details;
                    for ($index=0; $index < count($borrower_details); $index++) { 
                        $borrower_details[$index]->books_borrowed = $borrower_details[$index]->books_borrowed." times";
                        $borrower_details[$index]->action = "/Circulation/Book-Stats/View/".$borrower_details[$index]->book_isbn;
                    }
                    $table_column = ["#", "Book Title","Book ISBN", "# of times borrowed","Action"];

                    // display data
                    $display_data = $borrower_details;
                }
            }elseif ($borrower_type_opt == "staff") {
                if ($borrower_stats == "borrower_stats"){
                    $table_column = ["#", "Fullname","Books Borrowed", "Action"];

                    $borrower_staff_stats = DB::select("SELECT `user_checked_out` ,COUNT(*) AS 'books_borrowed' FROM `book_circulation` WHERE `date_checked_out` BETWEEN ? AND ? AND `user_borrowing` = 'staff' GROUP BY `user_checked_out` ORDER BY `books_borrowed` DESC LIMIT 300;",[$start_period,$end_period]);

                    // get all staff for that school
                    $school_code = session("school_details")->school_code;
                    
                    // connect to the other database
                    DB::setDefaultConnection("mysql");

                    // get the school details
                    $user_tbl = DB::select("SELECT * FROM `user_tbl` WHERE `school_code` = ?",[$school_code]);
                    
                    // loop through the staff and add the staff details
                    for ($index=0; $index < count($borrower_staff_stats); $index++) { 
                        $fullname = "N/A";
                        for ($ind=0; $ind < count($user_tbl); $ind++) { 
                            if ($user_tbl[$ind]->user_id == $borrower_staff_stats[$index]->user_checked_out) {
                                $fullname = $user_tbl[$ind]->fullname;
                            }
                        }
                        $user_checked_out = $borrower_staff_stats[$index]->user_checked_out;
                        $books_borrowed = $borrower_staff_stats[$index]->books_borrowed." Book(s)";

                        // arramge books
                        $borrower_staff_stats[$index]->user_checked_out = $fullname;
                        $borrower_staff_stats[$index]->books_borrowed = $books_borrowed;
                        $borrower_staff_stats[$index]->action_link = $user_checked_out;
                        $borrower_staff_stats[$index]->action_link = "/Circulation/Stats/View/Staff/".$user_checked_out;
                    }
                    $display_data = $borrower_staff_stats;
                }elseif ($borrower_stats == "books_borrowed_stats") {
                    $borrower_details = DB::select("SELECT (SELECT `book_title` FROM `library_details` WHERE `isbn_13` = `book_isbn` LIMIT 1) AS 'Book Title',`book_isbn`, COUNT(*) AS 'books_borrowed' FROM `book_circulation` WHERE `user_borrowing` = 'staff' AND `date_checked_out` BETWEEN ? AND ? GROUP BY `book_isbn` ORDER BY `books_borrowed` DESC LIMIT 300;",[$start_period,$end_period]);
                    
                    // get the student data
                    // return $borrower_details;
                    for ($index=0; $index < count($borrower_details); $index++) { 
                        $borrower_details[$index]->books_borrowed = $borrower_details[$index]->books_borrowed." times";
                        $borrower_details[$index]->action = "/Circulation/Book-Stats/View/".$borrower_details[$index]->book_isbn;
                    }
                    $table_column = ["#", "Book Title","Book ISBN", "# of times borrowed","Action"];

                    // display data
                    $display_data = $borrower_details;
                }
            }elseif ($borrower_type_opt == "All") {
                if ($borrower_stats == "borrower_stats"){
                    $table_column = ["#", "Fullname","Books Borrowed", "Borrower", "Action"];

                    $borrower_staff_stats = DB::select("SELECT `user_checked_out` ,COUNT(*) AS 'books_borrowed', `user_borrowing` FROM `book_circulation` WHERE `date_checked_out` BETWEEN ? AND ?  GROUP BY `user_checked_out`, `user_borrowing` ORDER BY `books_borrowed` DESC LIMIT 300;",[$start_period,$end_period]);

                    // get all staff for that school
                    $school_code = session("school_details")->school_code;
                    
                    // connect to the other database
                    DB::setDefaultConnection("mysql");

                    // get the school details
                    $user_tbl = DB::select("SELECT * FROM `user_tbl` WHERE `school_code` = ?",[$school_code]);
                    // connect to the other database
                    DB::setDefaultConnection("mysql2");
                    
                    // loop through the staff and add the staff details
                    for ($index=0; $index < count($borrower_staff_stats); $index++) { 
                        $fullname = "N/A";
                        for ($ind=0; $ind < count($user_tbl); $ind++) { 
                            if ($user_tbl[$ind]->user_id == $borrower_staff_stats[$index]->user_checked_out) {
                                $fullname = $user_tbl[$ind]->fullname;
                            }
                        }
                        $user_checked_out = $borrower_staff_stats[$index]->user_checked_out;
                        $books_borrowed = $borrower_staff_stats[$index]->books_borrowed." Book(s)";

                        // arramge books
                        if ($borrower_staff_stats[$index]->user_borrowing == "student") {
                            $student_name = DB::select("SELECT * FROM `student_data` WHERE `adm_no` = ? LIMIT 1",[$user_checked_out]);
                            $fullname = count($student_name) > 0 ? $student_name[0]->first_name." ".$student_name[0]->second_name : "N/A";
                        }
                        $borrower_staff_stats[$index]->user_checked_out =  $fullname;
                        $borrower_staff_stats[$index]->books_borrowed = $books_borrowed;
                        $borrower_staff_stats[$index]->action_link = $user_checked_out;
                        $borrower_staff_stats[$index]->action_link = $borrower_staff_stats[$index]->user_borrowing == "student" ? "/Circulation/Stats/View/Student/".$user_checked_out : "/Circulation/Stats/View/Staff/".$user_checked_out;
                    }
                    // return $borrower_staff_stats;
                    $display_data = $borrower_staff_stats;
                }elseif ($borrower_stats == "books_borrowed_stats") {
                    $borrower_details = DB::select("SELECT (SELECT `book_title` FROM `library_details` WHERE `isbn_13` = `book_isbn` LIMIT 1) AS 'Book Title',`book_isbn`, COUNT(*) AS 'books_borrowed' FROM `book_circulation` WHERE `date_checked_out` BETWEEN ? AND ? GROUP BY `book_isbn` ORDER BY `books_borrowed` DESC LIMIT 300;",[$start_period,$end_period]);
                    
                    // get the student data
                    // return $borrower_details;
                    for ($index=0; $index < count($borrower_details); $index++) { 
                        $borrower_details[$index]->books_borrowed = $borrower_details[$index]->books_borrowed." times";
                        $borrower_details[$index]->action = "/Circulation/Book-Stats/View/".$borrower_details[$index]->book_isbn;
                    }
                    $table_column = ["#", "Book Title","Book ISBN", "# of times borrowed","Action"];

                    // display data
                    $display_data = $borrower_details;
                }
            }

            DB::setDefaultConnection("mysql2");
            // get all students in the different classes that are there
            // get all classes
            $classes = DB::select("SELECT * FROM `settings` WHERE `sett` = 'class'");
            $class = [];
            if (count($classes) > 0) {
                $valued = explode(",",$classes[0]->valued);
                $class = $valued;
            }
            // return $class;
    
            // list all classes
            $student_detail = [];
            for ($index=0; $index < count($class); $index++) { 
                $student_data = DB::select("SELECT * FROM `student_data` WHERE `stud_class` = ?",[$class[$index]]);
                $stud_dets = new stdClass();
                $stud_dets->class_name = $this->myClassName($class[$index]);
                $stud_dets->class_value = $class[$index];
    
                // array push
                array_push($student_detail, $stud_dets);
            }
            // return $student_detail;
            // connect to mysql 2
            DB::setDefaultConnection("mysql");
    
            // get school code
            $school_code = session("school_details")->school_code;
            $staff_details = DB::select("SELECT * FROM `user_tbl` WHERE `school_code` = ?",[$school_code]);

            return view("circulation_stats",["table_column" => $table_column,"display_data" => $display_data,"student_detail" => $student_detail,"staff_details" => $staff_details]);
        }else{

            // get all students in the different classes that are there
            // get all classes
            $classes = DB::select("SELECT * FROM `settings` WHERE `sett` = 'class'");
            $class = [];
            if (count($classes) > 0) {
                $valued = explode(",",$classes[0]->valued);
                $class = $valued;
            }
            // return $class;
    
            // list all classes
            $student_detail = [];
            for ($index=0; $index < count($class); $index++) { 
                $student_data = DB::select("SELECT * FROM `student_data` WHERE `stud_class` = ?",[$class[$index]]);
                $stud_dets = new stdClass();
                $stud_dets->class_name = $this->myClassName($class[$index]);
                $stud_dets->class_value = $class[$index];
    
                // array push
                array_push($student_detail, $stud_dets);
            }
            // return $student_detail;
            // connect to mysql 2
            DB::setDefaultConnection("mysql");
    
            // get school code
            $school_code = session("school_details")->school_code;
            $staff_details = DB::select("SELECT * FROM `user_tbl` WHERE `school_code` = ?",[$school_code]);
    
            return view("circulation_stats",["student_detail" => $student_detail,"staff_details" => $staff_details]);
        }
    }
    //this will manage book circulations
    function circulationDashboard(){
        if (session("school_details") == null) {
            session()->flash("error","Your session has expired, Login to proceed!");
            return redirect("/");
        }
        // GET THE BOOKS DETAILS BY GROUPING WITH THE ISBN NUMBER
        $database_name = session("school_details")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        // connect to mysql 2
        DB::setDefaultConnection("mysql2");

        // get checked out
        $checked_out = DB::select("SELECT * FROM `book_circulation` WHERE `return_status` = '0' LIMIT 300");

        // get who borrowed the book
        for ($index=0; $index < count($checked_out); $index++) { 
            $user_checked_out = $checked_out[$index]->user_checked_out;
            $user_borrowing = $checked_out[$index]->user_borrowing;

            // check if its staff or students
            $user_name = "N/A";
            if ($user_borrowing == "staff") {
                // get the staff details
                // connect to mysql 2
                DB::setDefaultConnection("mysql");

                // get the staff details
                $staff_details = DB::select("SELECT * FROM `user_tbl` WHERE `user_id` = ?",[$user_checked_out]);
                $user_name = count($staff_details) > 0 ? ucwords(strtolower($staff_details[0]->fullname)) : "N/A";
            }else{
                // connect to mysql 2
                DB::setDefaultConnection("mysql2");
                // get the student details
                $student_name = DB::select("SELECT * FROM `student_data` WHERE `adm_no` = ?",[$user_checked_out]);
                $user_name = count($student_name) > 0 ? ucwords(strtolower($student_name[0]->first_name." ".$student_name[0]->second_name)) : "N/A";
            }
            // connect to mysql 2
            DB::setDefaultConnection("mysql2");
            // book details
            $book_details = DB::select("SELECT * FROM `library_details` WHERE (`isbn_13` = ? OR `isbn_10` = ?) AND `call_no` = ?",[$checked_out[$index]->book_isbn,$checked_out[$index]->book_isbn,$checked_out[$index]->book_call_number]);
            $checked_out[$index]->user_fullname = $user_name;
            $checked_out[$index]->book_title = count($book_details) > 0 ? $book_details[0]->book_title : "N/A";
        }

        // connect to mysql 2
        DB::setDefaultConnection("mysql2");
        // get checked in with a limit of 300 records first
        $checked_in = DB::select("SELECT * FROM `book_circulation` WHERE `return_status` = '1' ORDER BY `return_date` DESC LIMIT 300");

        // get who borrowed the book
        for ($index=0; $index < count($checked_in); $index++) { 
            $user_checked_out = $checked_in[$index]->user_checked_out;
            $user_borrowing = $checked_in[$index]->user_borrowing;

            // check if its staff or students
            $user_name = "N/A";
            if ($user_borrowing == "staff") {
                // get the staff details
                // connect to mysql
                DB::setDefaultConnection("mysql");

                // get the staff details
                $staff_details = DB::select("SELECT * FROM `user_tbl` WHERE `user_id` = ?",[$user_checked_out]);
                $user_name = count($staff_details) > 0 ? ucwords(strtolower($staff_details[0]->fullname)) : "N/A";
            }else{
                // connect to mysql
                DB::setDefaultConnection("mysql2");
                // get the student details
                $student_name = DB::select("SELECT * FROM `student_data` WHERE `adm_no` = ?",[$user_checked_out]);
                $user_name = count($student_name) > 0 ? ucwords(strtolower($student_name[0]->first_name." ".$student_name[0]->second_name)) : "N/A";
            }

            // connect to mysql
            DB::setDefaultConnection("mysql2");
            // book details
            $book_details = DB::select("SELECT * FROM `library_details` WHERE (`isbn_13` = ? OR `isbn_10` = ?) AND `call_no` = ?",[$checked_in[$index]->book_isbn,$checked_in[$index]->book_isbn,$checked_in[$index]->book_call_number]);
            $checked_in[$index]->user_fullname = $user_name;
            $checked_in[$index]->book_title = count($book_details) > 0 ? $book_details[0]->book_title : "N/A";
            $checked_in[$index]->book_details = count($book_details) > 0 ? $book_details[0] : null;

            // get the staff check in and check out
            $admin_check_in = $checked_in[$index]->checked_in_by;
            $admin_check_out = $checked_in[$index]->checked_out_by;

            // change DP
            DB::setDefaultConnection("mysql");
            $user_data = DB::select("SELECT * FROM `user_tbl` WHERE `user_id` = ?",[$admin_check_in]);
            $fullname_in = count($user_data) > 0 ? ucwords(strtolower($user_data[0]->fullname)) : "N/A";
            $fullname_out = $fullname_in;
            if ($admin_check_in != $admin_check_out) {
                $user_data = DB::select("SELECT * FROM `user_tbl` WHERE `user_id` = ?",[$admin_check_out]);
                $fullname_out = count($user_data) > 0 ? ucwords(strtolower($user_data[0]->fullname)) : "N/A";
            }

            $checked_in[$index]->fullname_check_in = $fullname_in;
            $checked_in[$index]->fullname_check_out = $fullname_out;

            // edit dates
            $checked_in[$index]->edit_date_checked_out = date("D dS M Y",strtotime($checked_in[$index]->date_checked_out));
            $checked_in[$index]->edit_expected_return_date = date("D dS M Y",strtotime($checked_in[$index]->expected_return_date));
            $checked_in[$index]->edit_return_date = date("D dS M Y",strtotime($checked_in[$index]->return_date));
        }

        // return $checked_in;
        // return value
        return view("Circulation",["checked_out" => $checked_out,"checked_in" => $checked_in]);
    }

    // this link check outs
    function Circulation_Checkout(Request $request){
        // return $request;
        if (session("school_details") == null) {
            session()->flash("error","Your session has expired, Login to proceed!");
            return redirect("/");
        }
        // GET THE BOOKS DETAILS BY GROUPING WITH THE ISBN NUMBER
        $database_name = session("school_details")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        // connect to mysql 2
        DB::setDefaultConnection("mysql2");

        // get the books
        $search_title = null;
        if (count($request->input()) > 0) {
            $keyword = $request->input("keyword_search");
            $search_title = $keyword;
            $books = DB::select("SELECT * FROM `library_details` WHERE `availability_status` = '1' AND (`book_title` LIKE \"%".$keyword."%\" OR `book_author` LIKE \"%".$keyword."%\" OR `book_publishers` LIKE \"%".$keyword."%\" OR `isbn_13` LIKE \"%".$keyword."%\" OR `isbn_10` LIKE \"%".$keyword."%\" OR `shelf_no_location` LIKE \"%".$keyword."%\" OR `call_no` LIKE \"%".$keyword."%\" OR `shelf_no_location` LIKE \"%".$keyword."%\" OR `keywords` LIKE \"%".$keyword."%\") ORDER BY `book_id` DESC LIMIT 100;");
        }else {
            $books = DB::select("SELECT * FROM `library_details` WHERE `availability_status` = '1' ORDER BY `book_id` DESC LIMIT 100");
        }
        for ($index=0; $index < count($books); $index++) { 
            // get record where the book was borrowed
            $book_details = DB::select("SELECT * FROM `book_circulation` WHERE `book_id` = ? AND `return_status` = '0' ORDER BY `circulation_id` DESC",[$books[$index]->book_id]);

            // BOOK DETAILS
            $circulation_id = count($book_details) > 0 ? $book_details[0]->circulation_id : 0;
            $books[$index]->circulation_id = $circulation_id;
        }

        // return value
        return view("check_out",["search_title" => $search_title ,"book_list" => $books]);
    }

    function checkOut($book_id){
        if (session("school_details") == null) {
            session()->flash("error","Your session has expired, Login to proceed!");
            return redirect("/");
        }
        // GET THE BOOKS DETAILS BY GROUPING WITH THE ISBN NUMBER
        $database_name = session("school_details")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        // connect to mysql 2
        DB::setDefaultConnection("mysql2");

        // get book details
        $book_details = DB::select("SELECT * FROM `library_details` WHERE `book_id` = ?",[$book_id]);
        if (count($book_details) == 0) {
            session()->flash("error","Book details not found!");
            return redirect("/Circulation/check-out");
        }

        // check if it has a valid image
        $book_details[0]->thumbnail_location = $this->isLinkValid($book_details[0]->thumbnail_location) ? $book_details[0]->thumbnail_location : "/images/book_cover.jpg";

        // get all classes
        $classes = DB::select("SELECT * FROM `settings` WHERE `sett` = 'class'");
        $class = [];
        if (count($classes) > 0) {
            $valued = explode(",",$classes[0]->valued);
            $class = $valued;
        }
        // return $class;

        // list all classes
        $student_detail = [];
        for ($index=0; $index < count($class); $index++) { 
            $student_data = DB::select("SELECT * FROM `student_data` WHERE `stud_class` = ?",[$class[$index]]);
            $stud_dets = new stdClass();
            $stud_dets->class_name = $this->myClassName($class[$index]);
            $stud_dets->student_data = $student_data;

            // array push
            array_push($student_detail, $stud_dets);
        }
        
        // connect to mysql 2
        DB::setDefaultConnection("mysql");

        // get school code
        $school_code = session("school_details")->school_code;
        $staff_details = DB::select("SELECT * FROM `user_tbl` WHERE `school_code` = ?",[$school_code]);
        
        // connect to mysql 2
        DB::setDefaultConnection("mysql2");
        // get libraries
        $libraries = [];
        $my_libraries = DB::select("SELECT * FROM `settings` WHERE `sett` = 'libraries'");

        // check if my libraries has anything
        $libraries = count($my_libraries) > 0 ? json_decode($my_libraries[0]->valued) : [];
        // return view
        return view("check_out_specific_book",["libraries" => $libraries, "book_details" => $book_details[0],"student_detail" => $student_detail,"staff_details" => $staff_details]);
    }

    function confirmCheckOut(Request $request){
        if (session("school_details") == null) {
            session()->flash("error","Your session has expired, Login to proceed!");
            return redirect("/");
        }
        // GET THE BOOKS DETAILS BY GROUPING WITH THE ISBN NUMBER
        $database_name = session("school_details")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        // return $request;
        $book_ids = $request->input("book_ids");
        $book_borrower = $request->input("book_borrower");
        $student_borrower = $request->input("student_borrower");
        $staff_borrower = $request->input("staff_borrower");
        $expected_return_date = $request->input("expected_return_date");
        $checkout_comment = $request->input("checkout_comment");

        // check if the staff or student is selected
        if ($book_borrower == "staff" && $staff_borrower == "Select an option") {
            session()->flash("error","No staff has been selected!");
            return redirect("/Circulation/check-out/".$book_ids);
        }else if ($book_borrower == "student" && $student_borrower == "Select an option") {
            session()->flash("error","No student has been selected!");
            return redirect("/Circulation/check-out/".$book_ids);
        }

        // connect to mysql 2
        DB::setDefaultConnection("mysql2");
        // get the book details
        $book_details = DB::select("SELECT * FROM `library_details` WHERE `book_id` = ?",[$book_ids]);

        // check if book is present
        if (count($book_details) == 0) {
            session()->flash("error","This book seems to be missing in your records!");
            return redirect("/Circulation");
        }

        // check the availability status
        if ($book_details[0]->availability_status == 0) {
            // the book is available
            session()->flash("error","This book has already been checked out! Select another book from your list.");
            return redirect("/Circulation");
        }

        // add a checkout record
        $book_isbn = $book_details[0]->isbn_13;
        $book_call_number = $book_details[0]->call_no;
        $user_checked_out = $book_borrower == "staff" ? $staff_borrower : $student_borrower;
        $today = date("YmdHis");
        $return_date = date("YmdHis",strtotime($expected_return_date));
        $user_id = session("user_id");

        // insert
        $insert = DB::insert("INSERT INTO `book_circulation` (`book_id`,`book_isbn`,`book_call_number`,`user_checked_out`,`user_borrowing`,`date_checked_out`,`expected_return_date`,`checked_out_by`,`comments`) VALUES (?,?,?,?,?,?,?,?,?)",[$book_ids,$book_isbn,$book_call_number,$user_checked_out,$book_borrower,$today,$return_date,$user_id,$checkout_comment]);

        // update data
        $update = DB::update("UPDATE `library_details` SET `availability_status` = '0' WHERE `book_id` = ?",[$book_ids]);

        // return to the circulation list
        session()->flash("success","\"".$book_details[0]->book_title."\" with Call Number \"".$book_call_number."\" has been successfully been checked out!");
        return redirect("/Circulation/check-out");
    }
    // view a book checked out
    function viewCheckOut($book_id,$circulation_id){
        // circulation
        if (session("school_details") == null) {
            session()->flash("error","Your session has expired, Login to proceed!");
            return redirect("/");
        }
        if (session('student_id')) {
            // return session('student_id');
            session()->flash('student_id',session('student_id'));
        }
        // GET THE BOOKS DETAILS BY GROUPING WITH THE ISBN NUMBER
        $database_name = session("school_details")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        // set default connection
        DB::setDefaultConnection("mysql2");

        // check if the book is present
        $book_borrow_data = DB::select("SELECT * FROM `book_circulation` WHERE `circulation_id` = ? ORDER BY `circulation_id` DESC LIMIT 1",[$circulation_id]);
        
        $book_details = DB::select("SELECT * FROM `library_details` WHERE `book_id` = ?",[$book_id]);
        if (count($book_details) == 0) {
            session()->flash("error","The book borrowed seems to not be found in your records!");
            return redirect("/Circulation/check-out");
        }
        // check if it has a valid image
        $book_details[0]->thumbnail_location = $this->isLinkValid($book_details[0]->thumbnail_location) ? $book_details[0]->thumbnail_location : "/images/book_cover.jpg";
        

        // get the student or teacher details
        if (count($book_borrow_data) == 0) {
            session()->flash("error","The check-out record cannot be found!");
            return redirect("/Circulation/check-out");
        }

        // return $book_borrow_data;
        $checked_out_by = $book_borrow_data[0]->checked_out_by;

        DB::setDefaultConnection("mysql");
        $user_details = DB::select("SELECT * FROM `user_tbl` WHERE `user_id` = ?",[$checked_out_by]);
        $user_fullname = count($user_details) > 0 ? ucwords(strtolower($user_details[0]->fullname)) : "N/A";


        DB::setDefaultConnection("mysql2");
        // get the student or teacher details
        $borrower = $book_borrow_data[0]->user_borrowing;
        $user_id = $book_borrow_data[0]->user_checked_out;
        
        // get username
        $username = "N/A";
        if ($borrower == "student") {
            DB::setDefaultConnection("mysql2");
            $student_data = DB::select("SELECT * FROM `student_data` WHERE `adm_no` = ?;",[$user_id]);
            $username = count($student_data) > 0 ? ucwords(strtolower($student_data[0]->first_name." ".$student_data[0]->second_name)) : "N/A";
        }elseif ($borrower == "staff") {
            DB::setDefaultConnection("mysql");
            $staff_data = DB::select("SELECT * FROM `user_tbl` WHERE `user_id` = ?",[$user_id]);
            $username = count($staff_data) > 0 ? ucwords(strtolower($staff_data[0]->fullname)) : "N/A";
        }

        // book borrow data
        $book_borrow_data[0]->borrower_fullname = $username;
        $book_borrow_data[0]->user_checkout_fullname = $user_fullname;

        // proceed and get the book details
        // return $book_borrow_data;
        return view("view_checkouts",["book_details" => $book_details[0],"book_borrow_data" => $book_borrow_data]);
    }
    function cancelCirculationRecord($circulation_id){
        if (session("school_details") == null) {
            session()->flash("error","Your session has expired, Login to proceed!");
            return redirect("/");
        }
        // GET THE BOOKS DETAILS BY GROUPING WITH THE ISBN NUMBER
        $database_name = session("school_details")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        // set default connection
        DB::setDefaultConnection("mysql2");

        // get student data
        $circulation_record = DB::select("SELECT * FROM `book_circulation` WHERE `circulation_id` = ?",[$circulation_id]);
        if (count($circulation_record) == 0) {
            session()->flash("error","Circulation record has not been found, its either canceled or deleted by the patron!");
            return redirect("/Circulation/check-out");
        }

        // get the book id and update its availability status
        DB::update("UPDATE `library_details` SET `availability_status` = '1' WHERE `book_id` = ?",[$circulation_record[0]->book_id]);

        // delete the record
        DB::delete("DELETE FROM `book_circulation` WHERE `circulation_id` = ?",[$circulation_id]);

        // return value
        session()->flash("success","Book check-out has been successfully canceled!");
        return redirect()->back();
    }

    function extendReturnDate(Request $request){
        if (session("school_details") == null) {
            session()->flash("error","Your session has expired, Login to proceed!");
            return redirect("/");
        }
        // GET THE BOOKS DETAILS BY GROUPING WITH THE ISBN NUMBER
        $database_name = session("school_details")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        // set default connection
        DB::setDefaultConnection("mysql2");

        // update circulation id
        $circulation_id = $request->input("circulation_id");
        $expected_return_date = date("YmdHis",strtotime($request->input("expected_return_date")));

        // get the record details
        $circulation_record = DB::select("SELECT * FROM `book_circulation` WHERE `circulation_id`  = ?",[$circulation_id]);
        if (count($circulation_record) == 0) {
            session()->flash("error","The circulation record cannot be found!");
            return redirect("/Circulation/check-out");
        }

        $update = DB::update("UPDATE `book_circulation` SET `expected_return_date` = ? WHERE `circulation_id` = ?",[$expected_return_date,$circulation_id]);

        session()->flash("success","Expected return date has been changed successfully!");
        // return $expected_return_date;
        return redirect("/Circulation/View/check-out/".$circulation_record[0]->book_id."/".$circulation_id);
    }
    function ConfirmCheckIn($circulation_id){
        if (session("school_details") == null) {
            session()->flash("error","Your session has expired, Login to proceed!");
            return redirect("/");
        }
        // GET THE BOOKS DETAILS BY GROUPING WITH THE ISBN NUMBER
        $database_name = session("school_details")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        // set default connection
        DB::setDefaultConnection("mysql2");
        
        // update the circulation table
        $circulation_record = DB::select("SELECT * FROM `book_circulation` WHERE `circulation_id` = ?",[$circulation_id]);

        // check circulation records
        if (count($circulation_record) == 0) {
            session()->flash("error","The circulation record cannot be found!");
            return redirect("/Circulation/check-out");
        }

        // update the book record
        $update = DB::update("UPDATE `library_details` SET `availability_status` = '1' WHERE `book_id` = ?",[$circulation_record[0]->book_id]);

        // update the circulation record
        // set the return date and return reciever
        $user_id = session("user_id");
        $return_status = 1;
        $return_date = date("YmdHis");
        $update = DB::update("UPDATE `book_circulation` SET `checked_in_by` = ?, `return_status` = ?, `return_date` = ? WHERE `circulation_id` = ?",[$user_id,$return_status,$return_date,$circulation_id]);
        
        session()->flash("success","Book has been checked-in successfully!");

        // circulations
        if (session('student_id')) {
            return redirect("Circulation/Stats/View/Student/".session('student_id'));
        }
        // set circulation
        return redirect("/Circulation");
    }
    function cancelCheckIn($circulation_id){
        if (session("school_details") == null) {
            session()->flash("error","Your session has expired, Login to proceed!");
            return redirect("/");
        }
        // change the book recieve status
        // GET THE BOOKS DETAILS BY GROUPING WITH THE ISBN NUMBER
        $database_name = session("school_details")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        // set default connection
        DB::setDefaultConnection("mysql2");

        // get the subject data
        $circulation_record = DB::select("SELECT * FROM `book_circulation` WHERE `circulation_id` = ?",[$circulation_id]);
        if (count($circulation_record) == 0) {
            session()->flash("error","Circulation record cannot be found.");
            return redirect("/Circulation");
        }

        // check the books availability status
        $book_details = DB::select("SELECT * FROM `library_details` WHERE `book_id` = ?",[$circulation_record[0]->book_id]);
        if (count($book_details) == 0) {
            session()->flash("error","Book details cannot be found!");
            return redirect("/Circulation");
        }

        // no book details
        if (count($book_details) > 0) {
            if ($book_details[0]->availability_status == 0) {
                // if the book is absent do not change the check in status
                session()->flash("error","You cannot cancel a book`s Check-In status when it has been checked out.");
                return redirect("/Circulation");
            }
        }
        
        // update the data to change the check in to check out
        DB::update("UPDATE `book_circulation` SET `return_date` = 'NULL',`checked_in_by` = 'NULL',`return_status` = '0' WHERE `circulation_id` = ?",[$circulation_id]);

        // update the book availability status
        DB::update("UPDATE `library_details` SET `availability_status` = '0' WHERE `book_id` = ?",[$circulation_record[0]->book_id]);

        session()->flash("success","Check-in status has been canceled successfully!");
        return redirect()->back();
    }
    function myClassName($data){
        if($data == "-i"){
            return "Alumni";
        }
        if (strlen($data)>1) {
            return $data;
        }else {
            return "Grade ".$data;
        }
        return $data;
    }

    function isLinkValid($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $http_code == 200;
    }
}

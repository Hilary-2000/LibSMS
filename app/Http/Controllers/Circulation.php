<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

date_default_timezone_set('Africa/Nairobi');
class Circulation extends Controller
{
    //this will manage book circulations
    function circulationDashboard(){
        // GET THE BOOKS DETAILS BY GROUPING WITH THE ISBN NUMBER
        $database_name = session("school_details")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        // connect to mysql 2
        DB::setDefaultConnection("mysql2");

        // get checked out
        $checked_out = DB::select("SELECT * FROM `book_circulation` WHERE `return_status` = '0'");

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
        // get checked in with a limit of 500 records first
        $checked_in = DB::select("SELECT * FROM `book_circulation` WHERE `return_status` = '1' ORDER BY `return_date` DESC LIMIT 500");

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
    function Circulation_Checkout(){
        // GET THE BOOKS DETAILS BY GROUPING WITH THE ISBN NUMBER
        $database_name = session("school_details")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        // connect to mysql 2
        DB::setDefaultConnection("mysql2");

        // get the books
        $books = DB::select("SELECT * FROM `library_details` ORDER BY `book_id` DESC");
        for ($index=0; $index < count($books); $index++) { 
            // get record where the book was borrowed
            $book_details = DB::select("SELECT * FROM `book_circulation` WHERE `book_id` = ? AND `return_status` = '0' ORDER BY `circulation_id` DESC",[$books[$index]->book_id]);

            // BOOK DETAILS
            $circulation_id = count($book_details) > 0 ? $book_details[0]->circulation_id : 0;
            $books[$index]->circulation_id = $circulation_id;
        }

        // return value
        return view("check_out",["book_list" => $books]);
    }

    function checkOut($book_id){
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
        
        // return view
        return view("check_out_specific_book",["book_details" => $book_details[0],"student_detail" => $student_detail,"staff_details" => $staff_details]);
    }

    function confirmCheckOut(Request $request){
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

        // add a checkout record
        $book_isbn = $book_details[0]->isbn_13;
        $book_call_number = $book_details[0]->call_no;
        $user_checked_out = $book_borrower == "staff" ? $staff_borrower : $student_borrower;
        $today = date("YmdHis");
        $return_date = date("YmdHis",strtotime($expected_return_date));
        $user_id = session("user_id");

        // insert
        $insert = DB::insert("INSERT INTO `book_circulation` (`book_id`,`book_isbn`,`book_call_number`,`user_checked_out`,`user_borrowing`,`date_checked_out`,`expected_return_date`,`checked_out_by`) VALUES (?,?,?,?,?,?,?,?)",[$book_ids,$book_isbn,$book_call_number,$user_checked_out,$book_borrower,$today,$return_date,$user_id]);

        // update data
        $update = DB::update("UPDATE `library_details` SET `availability_status` = '0' WHERE `book_id` = ?",[$book_ids]);

        // return to the circulation list
        session()->flash("success","\"".$book_details[0]->book_title."\" with Call Number \"".$book_call_number."\" has been successfully been checked out!");
        return redirect("/Circulation/check-out");
    }
    // view a book checked out
    function viewCheckOut($book_id,$circulation_id){
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
        session()->flash("success","Book check-out has been successfully been canceled!");
        return redirect("/Circulation/check-out");
    }

    function extendReturnDate(Request $request){
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
        return redirect("/Circulation");
    }
    function cancelCheckIn($circulation_id){
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
        
        // update the data to change the check in to check out
        DB::update("UPDATE `book_circulation` SET `return_date` = 'NULL',`checked_in_by` = 'NULL',`return_status` = '0' WHERE `circulation_id` = ?",[$circulation_id]);

        // update the book availability status
        DB::update("UPDATE `library_details` SET `availability_status` = '0' WHERE `book_id` = ?",[$circulation_record[0]->book_id]);

        session()->flash("success","Check-in status has been canceled successfully!");
        return redirect("/Circulation");
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
}

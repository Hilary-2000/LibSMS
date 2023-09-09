<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use stdClass;

class Reports extends Controller
{
    //handle all reports requests
    function getReports(Request $request){
        if (session("school_details") == null) {
            session()->flash("error","Your session has expired, Login to proceed!");
            return redirect("/");
        }
        // check if the isbn number is present in the database and return book details
        $database_name = session('school_details')->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        // connect to mysql 2
        DB::setDefaultConnection("mysql2");
        if (count($request->input()) > 0) {
            // proceed and process that information
            $book_options_inside = $request->input("book_options_inside");
            $date_type_selection = $request->input("date_type_selection");
            $select_date_1 = $request->input("select_date_1");
            $select_date_from = $request->input("select_date_from");
            $select_date_to = $request->input("select_date_to");

            session()->flash("book_options_inside",$book_options_inside);
            session()->flash("date_type_selection",$date_type_selection);
            session()->flash("select_date_1",$select_date_1);
            session()->flash("select_date_from",$select_date_from);
            session()->flash("select_date_to",$select_date_to);
            
            // start with book information
            $table_heading = [];
            $search_heading = "Not Set";
            if ($book_options_inside == "book_information") {
                // get book details
                $table_heading = ["No","Book Title","Author","ISBN-10","ISBN-13","Date Acquired","Call No.","Action"];

                // get the book details
                $book_details = DB::select("SELECT * FROM `library_details` ORDER BY `book_id` DESC");
                $book_data = [];
                for ($index=0; $index < count($book_details); $index++) {
                    $book_data_inside = [];
                    array_push($book_data_inside,($index+1));
                    $availability = $book_details[$index]->availability_status == 1 ? "<span data-bs-toggle='tooltip' data-bs-placement='top' class='badge bg-success' data-bs-original-title='Available'>in</span>" : "<span data-bs-toggle='tooltip' data-bs-placement='top' class='badge bg-danger' data-bs-original-title='Borrowed'>Out</span>";
                    array_push($book_data_inside,$book_details[$index]->book_title." ".$availability);
                    array_push($book_data_inside,$book_details[$index]->book_author);
                    array_push($book_data_inside,$book_details[$index]->isbn_10);
                    array_push($book_data_inside,$book_details[$index]->isbn_13);
                    array_push($book_data_inside,date("D dS M Y",strtotime($book_details[$index]->date_recorded)));
                    array_push($book_data_inside,$book_details[$index]->call_no);
                    $link_details = new stdClass();
                    $link_details->show = "<i class=\"mdi mdi-eye\"></i> View";
                    $link_details->href = "/Acquisitions/Book-details/".$book_details[$index]->book_id;
                    $link_details->title = "View Book Details";
                    array_push($book_data_inside,[$link_details]);

                    // push the data to the main array holder
                    array_push($book_data,$book_data_inside);
                }
                $search_heading = "Book Information Table";
            }elseif ($book_options_inside == "latest_acquisition") {
                // get book details
                $table_heading = ["No","Book Title","Author","ISBN-10","ISBN-13","Date Acquired","Call No.","Action"];

                // for specific date or between dates
                $book_data = [];
                $book_details = [];
                $search_heading = "Book Acquired ";
                if($date_type_selection == "specific_date"){
                    $search_heading .= "on ".($select_date_1 != null ? date("D dS M Y",strtotime($select_date_1)) : date("D dS M Y"));
                    $specific_date = $select_date_1 != null ? date("Ymd",strtotime($select_date_1)) : date("Ymd");
                    $book_details = DB::select("SELECT * FROM `library_details` WHERE `date_recorded` LIKE '".$specific_date."%'  ORDER BY `book_id` DESC");
                }elseif($date_type_selection == "between_periods"){
                    $search_heading .= "between ".($select_date_from != null ? date("D dS M Y",strtotime($select_date_from)) : date("D dS M Y",strtotime("-1 week")))." and ".($select_date_to != null ? date("D dS M Y",strtotime($select_date_to)) : date("D dS M Y"));
                    $date_1 = $select_date_from != null ? date("YmdHis",strtotime($select_date_from)) : date("YmdHis",strtotime("-1 week"));
                    $date_2 = $select_date_to != null ? date("YmdHis",strtotime($select_date_to)) : date("YmdHis");
                    $book_details = DB::select("SELECT * FROM `library_details` WHERE `date_recorded` BETWEEN ? AND ? ORDER BY `book_id` DESC",[$date_1,$date_2]);
                }else{
                    $search_heading .= "on ".($select_date_1 != null ? date("D dS M Y",strtotime($select_date_1)) : date("D dS M Y"));
                    $specific_date = $select_date_1 != null ? date("Ymd",strtotime($select_date_1)) : date("Ymd");
                    $book_details = DB::select("SELECT * FROM `library_details` WHERE `date_recorded` LIKE '".$specific_date."%'  ORDER BY `book_id` DESC");
                }

                // get all book details
                for ($index=0; $index < count($book_details); $index++) {
                    $book_data_inside = [];
                    array_push($book_data_inside,($index+1));
                    $availability = $book_details[$index]->availability_status == 1 ? "<span data-bs-toggle='tooltip' data-bs-placement='top' class='badge bg-success' data-bs-original-title='Available'>in</span>" : "<span data-bs-toggle='tooltip' data-bs-placement='top' class='badge bg-danger' data-bs-original-title='Borrowed'>Out</span>";
                    array_push($book_data_inside,$book_details[$index]->book_title." ".$availability);
                    array_push($book_data_inside,$book_details[$index]->book_author);
                    array_push($book_data_inside,$book_details[$index]->isbn_10);
                    array_push($book_data_inside,$book_details[$index]->isbn_13);
                    array_push($book_data_inside,date("D dS M Y",strtotime($book_details[$index]->date_recorded)));
                    array_push($book_data_inside,$book_details[$index]->call_no);
                    $link_details = new stdClass();
                    $link_details->show = "<i class=\"mdi mdi-eye\"></i>";
                    $link_details->href = "/Acquisitions/Book-details/".$book_details[$index]->book_id;
                    $link_details->title = "View Book Details";
                    array_push($book_data_inside,[$link_details]);

                    // push the data to the main array holder
                    array_push($book_data,$book_data_inside);
                }
            }elseif ($book_options_inside == "checked_out") {
                // get book details
                $table_heading = ["No","Book Title","Borrower","Call No","ISBN-13","Date Borrowed","Expected Returned Date","Action"];

                // for specific date or between dates
                $book_data = [];
                $book_details = [];
                $search_heading = "Books Checked-Out ";
                if($date_type_selection == "specific_date"){
                    $search_heading .= "on ".($select_date_1 != null ? date("D dS M Y",strtotime($select_date_1)) : date("D dS M Y"));
                    $specific_date = $select_date_1 != null ? date("Ymd",strtotime($select_date_1)) : date("Ymd");
                    $book_details = DB::select("SELECT * FROM `book_circulation` WHERE `return_status` = '0' AND `date_checked_out` LIKE '".$specific_date."%'  ORDER BY `date_checked_out` DESC");
                }elseif($date_type_selection == "between_periods"){
                    $search_heading .= "between ".($select_date_from != null ? date("D dS M Y",strtotime($select_date_from)) : date("D dS M Y",strtotime("-1 week")))." and ".($select_date_to != null ? date("D dS M Y",strtotime($select_date_to)) : date("D dS M Y"));
                    $date_1 = $select_date_from != null ? date("YmdHis",strtotime($select_date_from)) : date("YmdHis",strtotime("-1 week"));
                    $date_2 = $select_date_to != null ? date("YmdHis",strtotime($select_date_to)) : date("YmdHis");
                    $book_details = DB::select("SELECT * FROM `book_circulation` WHERE `return_status` = '0' AND `date_checked_out` BETWEEN ? AND ? ORDER BY `date_checked_out` DESC",[$date_1,$date_2]);
                }else{
                    $search_heading .= "on ".($select_date_1 != null ? date("D dS M Y",strtotime($select_date_1)) : date("D dS M Y"));
                    $specific_date = $select_date_1 != null ? date("Ymd",strtotime($select_date_1)) : date("Ymd");
                    $book_details = DB::select("SELECT * FROM `book_circulation` WHERE `return_status` = '0' AND `date_checked_out` LIKE '".$specific_date."%'  ORDER BY `date_checked_out` DESC");
                }

                // GET ALL BOOKS
                $ALL_BOOKS = DB::select("SELECT * FROM `library_details`");
                $ALL_STUDENTS = DB::select("SELECT * FROM `student_data`");

                // GET THE STAFF
                DB::setDefaultConnection("mysql");
                $school_code = session()->get("school_details")->school_code;
                $ALL_STAFF = DB::select("SELECT * FROM `user_tbl` WHERE `school_code` = ?",[$school_code]);

                // get all book details
                for ($index=0; $index < count($book_details); $index++) {
                    // specific book data
                    $BD = $this->getBook($ALL_BOOKS,$book_details[$index]->book_id);

                    // book_borrower
                    $BookBorrower = "N/A";
                    if ($book_details[$index]->user_borrowing == "staff") {
                        $staff_name = $this->getStaff($ALL_STAFF,$book_details[$index]->user_checked_out);
                        $BookBorrower = ($staff_name != null ? $staff_name->fullname : "N/A")." <span data-bs-toggle='tooltip' data-bs-placement='top' class='badge badge-pill badge-soft-success' data-bs-original-title='Staff'>Staff</span>";
                    }else {
                        $student_name = $this->getStudent($ALL_STUDENTS,$book_details[$index]->user_checked_out);
                        $BookBorrower = ($student_name != null ? ucwords(strtolower($student_name->first_name." ".$student_name->second_name)) : "N/A")." <span data-bs-toggle='tooltip' data-bs-placement='top' class='badge badge-pill badge-soft-success' data-bs-original-title='Student'>Student</span>";
                    }

                    $book_data_inside = [];
                    array_push($book_data_inside,($index+1));
                    $availability = $BD != null ? ($BD->availability_status == 1 ? "<span data-bs-toggle='tooltip' data-bs-placement='top' class='badge bg-success' data-bs-original-title='Available'>in</span>" : "<span data-bs-toggle='tooltip' data-bs-placement='top' class='badge bg-danger' data-bs-original-title='Borrowed'>Out</span>") : "";
                    array_push($book_data_inside,($BD != null ? $BD->book_title." ".$availability : "N/A"));
                    array_push($book_data_inside,$BookBorrower);
                    array_push($book_data_inside,$book_details[$index]->book_call_number);
                    array_push($book_data_inside,$book_details[$index]->book_isbn);
                    array_push($book_data_inside,date("D dS M Y",strtotime($book_details[$index]->date_checked_out)));
                    array_push($book_data_inside,date("D dS M Y",strtotime($book_details[$index]->expected_return_date)));
                    $link_details = new stdClass();
                    $link_details->show = "<i class=\"mdi mdi-eye\"></i>";
                    $link_details->href = "/Circulation/View/check-out/".$book_details[$index]->book_id."/".$book_details[$index]->circulation_id;
                    $link_details->title = "View Check-Out Details";
                    // array_push($book_data_inside,[$link_details]);
                    $link_details_2 = new stdClass();
                    $link_details_2->show = "<i class=\"bx bx-info-circle\"></i>";
                    $link_details_2->href = "/Circulation/View/check-out/".$book_details[$index]->book_id."/".$book_details[$index]->circulation_id;
                    $link_details_2->title = "View Borrower details";
                    array_push($book_data_inside,[$link_details,$link_details_2]);

                    // push the data to the main array holder
                    array_push($book_data,$book_data_inside);
                }
            }elseif ($book_options_inside == "checked_in") {
                // get book details
                $table_heading = ["No","Book Title","Borrower","ISBN-10","ISBN-13","Date Borrowed","Returned Date","Action"];

                // for specific date or between dates
                $book_data = [];
                $book_details = [];
                $search_heading = "Books Checked-In ";
                if($date_type_selection == "specific_date"){
                    $search_heading .= "on ".($select_date_1 != null ? date("D dS M Y",strtotime($select_date_1)) : date("D dS M Y"));
                    $specific_date = $select_date_1 != null ? date("Ymd",strtotime($select_date_1)) : date("Ymd");
                    $book_details = DB::select("SELECT * FROM `book_circulation` WHERE `return_status` = '1' AND `return_date` LIKE '".$specific_date."%'  ORDER BY `return_date` DESC");
                }elseif($date_type_selection == "between_periods"){
                    $search_heading .= "between ".($select_date_from != null ? date("D dS M Y",strtotime($select_date_from)) : date("D dS M Y",strtotime("-1 week")))." and ".($select_date_to != null ? date("D dS M Y",strtotime($select_date_to)) : date("D dS M Y"));
                    $date_1 = $select_date_from != null ? date("YmdHis",strtotime($select_date_from)) : date("YmdHis",strtotime("-1 week"));
                    $date_2 = $select_date_to != null ? date("YmdHis",strtotime($select_date_to)) : date("YmdHis");
                    $book_details = DB::select("SELECT * FROM `book_circulation` WHERE `return_status` = '1' AND `return_date` BETWEEN ? AND ? ORDER BY `return_date` DESC",[$date_1,$date_2]);
                }else{
                    $search_heading .= "on ".($select_date_1 != null ? date("D dS M Y",strtotime($select_date_1)) : date("D dS M Y"));
                    $specific_date = $select_date_1 != null ? date("Ymd",strtotime($select_date_1)) : date("Ymd");
                    $book_details = DB::select("SELECT * FROM `book_circulation` WHERE `return_status` = '1' AND `return_date` LIKE '".$specific_date."%'  ORDER BY `return_date` DESC");
                }

                // GET ALL BOOKS
                $ALL_BOOKS = DB::select("SELECT * FROM `library_details`");
                $ALL_STUDENTS = DB::select("SELECT * FROM `student_data`");

                // GET THE STAFF
                DB::setDefaultConnection("mysql");
                $school_code = session()->get("school_details")->school_code;
                $ALL_STAFF = DB::select("SELECT * FROM `user_tbl` WHERE `school_code` = ?",[$school_code]);

                // get all book details
                for ($index=0; $index < count($book_details); $index++) {
                    // specific book data
                    $BD = $this->getBook($ALL_BOOKS,$book_details[$index]->book_id);

                    // book_borrower
                    // $BookBorrower = "N/A";
                    // if ($book_details[$index]->user_borrowing == "staff") {
                    //     $staff_name = $this->getStaff($ALL_STAFF,$book_details[$index]->user_checked_out);
                    //     $BookBorrower = ($staff_name != null ? $staff_name->fullname : "N/A")." - Staff";
                    // }else {
                    //     $student_name = $this->getStudent($ALL_STUDENTS,$book_details[$index]->user_checked_out);
                    //     $BookBorrower = ($student_name != null ? ucwords(strtolower($student_name->first_name." ".$student_name->second_name)) : "N/A")." - Student";
                    // }
                    // book_borrower
                    $BookBorrower = "N/A";
                    if ($book_details[$index]->user_borrowing == "staff") {
                        $staff_name = $this->getStaff($ALL_STAFF,$book_details[$index]->user_checked_out);
                        $BookBorrower = ($staff_name != null ? $staff_name->fullname : "N/A")." <span data-bs-toggle='tooltip' data-bs-placement='top' class='badge badge-pill badge-soft-success' data-bs-original-title='Staff'>Staff</span>";
                    }else {
                        $student_name = $this->getStudent($ALL_STUDENTS,$book_details[$index]->user_checked_out);
                        $BookBorrower = ($student_name != null ? ucwords(strtolower($student_name->first_name." ".$student_name->second_name)) : "N/A")." <span data-bs-toggle='tooltip' data-bs-placement='top' class='badge badge-pill badge-soft-success' data-bs-original-title='Student'>Student</span>";
                    }

                    $book_data_inside = [];
                    array_push($book_data_inside,($index+1));
                    $availability = $BD != null ? ($BD->availability_status == 1 ? "<span data-bs-toggle='tooltip' data-bs-placement='top' class='badge bg-success' data-bs-original-title='Available'>in</span>" : "<span data-bs-toggle='tooltip' data-bs-placement='top' class='badge bg-danger' data-bs-original-title='Borrowed'>Out</span>") : "";
                    array_push($book_data_inside,($BD != null ? $BD->book_title." ".$availability : "N/A"));
                    array_push($book_data_inside,$BookBorrower);
                    array_push($book_data_inside,($BD != null ? $BD->isbn_10 : "N/A"));
                    array_push($book_data_inside,($BD != null ? $BD->isbn_13 : "N/A"));
                    array_push($book_data_inside,date("D dS M Y",strtotime($book_details[$index]->date_checked_out)));
                    array_push($book_data_inside,($book_details[$index]->return_date != "NULL" && $book_details[$index]->return_date != null) ? date("D dS M Y",strtotime($book_details[$index]->return_date)) : "N/A");
                    $link_details = new stdClass();
                    $link_details->show = "<i class=\"mdi mdi-eye\"></i>";
                    $link_details->href = "/Acquisitions/Book-details/".$book_details[$index]->book_id;
                    $link_details->title = "View book details";
                    array_push($book_data_inside,[$link_details]);

                    // push the data to the main array holder
                    array_push($book_data,$book_data_inside);
                }
            }elseif ($book_options_inside == "due_checked_in") {
                // get book details
                $table_heading = ["No","Book Title","Borrower","ISBN-10","ISBN-13","Date Borrowed","Expected Return Date","Action"];

                // for specific date or between dates
                $book_data = [];
                $book_details = [];
                $search_heading = "Books due for Check-In ";
                if($date_type_selection == "specific_date"){
                    $search_heading .= "on ".($select_date_1 != null ? date("D dS M Y",strtotime($select_date_1)) : date("D dS M Y"));
                    $specific_date = ($select_date_1 != null ? date("Ymd",strtotime($select_date_1)) : date("Ymd"));
                    $book_details = DB::select("SELECT * FROM `book_circulation` WHERE `expected_return_date` LIKE '".$specific_date."%' AND return_status = '0' ORDER BY `circulation_id` DESC");
                }elseif($date_type_selection == "between_periods"){
                    $search_heading .= "between ".($select_date_from != null ? date("D dS M Y",strtotime($select_date_from)) : date("D dS M Y",strtotime("-1 week")))." and ".($select_date_to != null ? date("D dS M Y",strtotime($select_date_to)) : date("D dS M Y"));
                    $date_1 = $select_date_from != null ? date("YmdHis",strtotime($select_date_from)) : date("YmdHis",strtotime("-1 week"));
                    $date_2 = $select_date_to != null ? date("YmdHis",strtotime($select_date_to)) : date("YmdHis");
                    $book_details = DB::select("SELECT * FROM `book_circulation` WHERE `expected_return_date` BETWEEN ? AND ? AND return_status = '0' ORDER BY `circulation_id` DESC",[$date_1,$date_2]);
                }else{
                    $search_heading .= "on ".($select_date_1 != null ? date("D dS M Y",strtotime($select_date_1)) : date("D dS M Y"));
                    $specific_date = $select_date_1 != null ? date("Ymd",strtotime($select_date_1)) : date("Ymd");
                    $book_details = DB::select("SELECT * FROM `book_circulation` WHERE `expected_return_date` LIKE '".$specific_date."%' AND return_status = '0' ORDER BY `circulation_id` DESC");
                }

                // GET ALL BOOKS
                $ALL_BOOKS = DB::select("SELECT * FROM `library_details`");
                $ALL_STUDENTS = DB::select("SELECT * FROM `student_data`");

                // GET THE STAFF
                DB::setDefaultConnection("mysql");
                $school_code = session()->get("school_details")->school_code;
                $ALL_STAFF = DB::select("SELECT * FROM `user_tbl` WHERE `school_code` = ?",[$school_code]);

                // get all book details
                for ($index=0; $index < count($book_details); $index++) {
                    // specific book data
                    $BD = $this->getBook($ALL_BOOKS,$book_details[$index]->book_id);

                    // book_borrower
                    // $BookBorrower = "N/A";
                    // if ($book_details[$index]->user_borrowing == "staff") {
                    //     $staff_name = $this->getStaff($ALL_STAFF,$book_details[$index]->user_checked_out);
                    //     $BookBorrower = ($staff_name != null ? $staff_name->fullname : "N/A")." - Staff";
                    // }else {
                    //     $student_name = $this->getStudent($ALL_STUDENTS,$book_details[$index]->user_checked_out);
                    //     $BookBorrower = ($student_name != null ? ucwords(strtolower($student_name->first_name." ".$student_name->second_name)) : "N/A")." - Student";
                    // }
                    // book_borrower
                    $BookBorrower = "N/A";
                    if ($book_details[$index]->user_borrowing == "staff") {
                        $staff_name = $this->getStaff($ALL_STAFF,$book_details[$index]->user_checked_out);
                        $BookBorrower = ($staff_name != null ? $staff_name->fullname : "N/A")." <span data-bs-toggle='tooltip' data-bs-placement='top' class='badge badge-pill badge-soft-success' data-bs-original-title='Staff'>Staff</span>";
                    }else {
                        $student_name = $this->getStudent($ALL_STUDENTS,$book_details[$index]->user_checked_out);
                        $BookBorrower = ($student_name != null ? ucwords(strtolower($student_name->first_name." ".$student_name->second_name)) : "N/A")." <span data-bs-toggle='tooltip' data-bs-placement='top' class='badge badge-pill badge-soft-success' data-bs-original-title='Student'>Student</span>";
                    }

                    $book_data_inside = [];
                    $availability = $BD != null ? ($BD->availability_status == 1 ? "<span data-bs-toggle='tooltip' data-bs-placement='top' class='badge bg-success' data-bs-original-title='Available'>in</span>" : "<span data-bs-toggle='tooltip' data-bs-placement='top' class='badge bg-danger' data-bs-original-title='Borrowed'>Out</span>") : "";
                    array_push($book_data_inside,($index+1));
                    array_push($book_data_inside,($BD != null ? $BD->book_title." ".$availability : "N/A"));
                    array_push($book_data_inside,$BookBorrower);
                    array_push($book_data_inside,($BD != null ? $BD->isbn_10 : "N/A"));
                    array_push($book_data_inside,($BD != null ? $BD->isbn_13 : "N/A"));
                    array_push($book_data_inside,date("D dS M Y",strtotime($book_details[$index]->date_checked_out)));
                    array_push($book_data_inside,date("D dS M Y",strtotime($book_details[$index]->expected_return_date)));
                    $link_details = new stdClass();
                    $link_details->show = "<i class=\"mdi mdi-eye\"></i>";
                    $link_details->href = "/Circulation/View/check-out/".$book_details[$index]->book_id."/".$book_details[$index]->circulation_id;
                    $link_details->title = "View Borrower details";
                    array_push($book_data_inside,[$link_details]);

                    // push the data to the main array holder
                    array_push($book_data,$book_data_inside);
                }
            }elseif ($book_options_inside == "to_be_due_checkin") {
                // get book details
                $table_heading = ["No","Book Title","Borrower","ISBN-10","ISBN-13","Date Borrowed","Expected Return Date","Action"];

                // for specific date or between dates
                $search_heading = "Books due for Check-In Today : ".date("D dS M Y").".";
                $book_data = [];
                $book_details = [];
                $specific_date = date("Ymd")."235959";
                $book_details = DB::select("SELECT * FROM `book_circulation` WHERE `return_status` = '0' AND `expected_return_date` < ? ORDER BY `circulation_id` DESC",[$specific_date]);

                // GET ALL BOOKS
                $ALL_BOOKS = DB::select("SELECT * FROM `library_details`");
                $ALL_STUDENTS = DB::select("SELECT * FROM `student_data`");

                // GET THE STAFF
                DB::setDefaultConnection("mysql");
                $school_code = session()->get("school_details")->school_code;
                $ALL_STAFF = DB::select("SELECT * FROM `user_tbl` WHERE `school_code` = ?",[$school_code]);

                // get all book details
                for ($index=0; $index < count($book_details); $index++) {
                    // specific book data
                    $BD = $this->getBook($ALL_BOOKS,$book_details[$index]->book_id);

                    // book_borrower
                    $BookBorrower = "N/A";
                    if ($book_details[$index]->user_borrowing == "staff") {
                        $staff_name = $this->getStaff($ALL_STAFF,$book_details[$index]->user_checked_out);
                        $BookBorrower = ($staff_name != null ? $staff_name->fullname : "N/A")." - Staff";
                    }else {
                        $student_name = $this->getStudent($ALL_STUDENTS,$book_details[$index]->user_checked_out);
                        $BookBorrower = ($student_name != null ? ucwords(strtolower($student_name->first_name." ".$student_name->second_name)) : "N/A")." - Student";
                    }

                    $book_data_inside = [];
                    $availability = $BD != null ? ($BD->availability_status == 1 ? "<span data-bs-toggle='tooltip' data-bs-placement='top' class='badge bg-success' data-bs-original-title='Available'>in</span>" : "<span data-bs-toggle='tooltip' data-bs-placement='top' class='badge bg-danger' data-bs-original-title='Borrowed'>Out</span>") : "";
                    array_push($book_data_inside,($index+1));
                    array_push($book_data_inside,($BD != null ? $BD->book_title." ".$availability : "N/A"));
                    array_push($book_data_inside,$BookBorrower);
                    array_push($book_data_inside,($BD != null ? $BD->isbn_10 : "N/A"));
                    array_push($book_data_inside,($BD != null ? $BD->isbn_13 : "N/A"));
                    array_push($book_data_inside,date("D dS M Y",strtotime($book_details[$index]->date_checked_out)));
                    array_push($book_data_inside,date("D dS M Y",strtotime($book_details[$index]->expected_return_date)));
                    
                    $link_details = new stdClass();
                    $link_details->show = "<i class=\"mdi mdi-eye-outline\"></i>";
                    $link_details->href = "/Acquisitions/Book-details/".$book_details[$index]->book_id;
                    $link_details->title = "View Book Details";
                    // array_push($book_data_inside,[$link_details]);
                    $link_details_2 = new stdClass();
                    $link_details_2->show = "<i class=\"bx bx-info-circle\"></i>";
                    $link_details_2->href = "/Circulation/View/check-out/".$book_details[$index]->book_id."/".$book_details[$index]->circulation_id;
                    $link_details_2->title = "View Borrower details";
                    array_push($book_data_inside,[$link_details,$link_details_2]);

                    // push the data to the main array holder
                    array_push($book_data,$book_data_inside);
                }
            }
            // return $book_data;
            return view("reports",["book_data" => $book_data,"table_heading" => $table_heading,"search_heading" => $search_heading]);
        }else{
            // return "Book";
            $book_list = DB::select("SELECT * FROM `library_details` ORDER BY `book_id` DESC LIMIT 100");
            return view("reports",["book_list" => $book_list]);
        }
    }
    function getBook($ALL_BOOKS,$book_id){
        for ($index=0; $index < count($ALL_BOOKS); $index++) {
            if ($ALL_BOOKS[$index]->book_id == $book_id) {
                return $ALL_BOOKS[$index];
            }
        }
        return null;
    }
    function getStaff($ALL_STAFF,$staff_id){
        // return $ALL_STAFF;
        for ($index=0; $index < count($ALL_STAFF); $index++) { 
            if ($ALL_STAFF[$index]->user_id == $staff_id) {
                return $ALL_STAFF[$index];
            }
        }
        return null;
    }
    function getStudent($ALL_STUDENTS,$student_admno){
        for ($index=0; $index < count($ALL_STUDENTS); $index++) {
            if ($ALL_STUDENTS[$index]->adm_no == $student_admno) {
                return $ALL_STUDENTS[$index];
            }
        }
        return null;
    }
}

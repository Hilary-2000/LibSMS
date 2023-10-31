<?php

namespace App\Http\Controllers;

use App\Models\LibraryDetail;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

date_default_timezone_set('Africa/Nairobi');
class Acquisitions extends Controller
{

    function getNotification(){
        // set the notifications for all the books that are overdue
        
        // first get all books that are overdue
        $select = DB::select("SELECT BC.circulation_id, BC.date_checked_out ,BC.book_call_number,BC.user_checked_out, BC.book_id, BC.expected_return_date, BC.user_borrowing,
                                CASE
                                    WHEN BC.user_borrowing = 'student' THEN CONCAT(SD.first_name, ' ', SD.second_name,' ',SD.surname)
                                    WHEN BC.user_borrowing = 'teacher' THEN UT.fullname
                                    ELSE NULL
                                END AS borrower_name,
                                LD.book_title,LD.book_author,LD.isbn_13
                            FROM `book_circulation` AS BC
                            LEFT JOIN `student_data` AS SD ON BC.user_checked_out = SD.adm_no AND BC.user_borrowing = 'student'
                            LEFT JOIN `ladybird_smis`.`user_tbl` AS UT ON BC.user_checked_out = UT.user_id AND BC.user_borrowing = 'staff'
                            LEFT JOIN `library_details` AS LD ON BC.book_id = LD.book_id
                            WHERE `return_status` = 0 AND `expected_return_date` < ? ",[date("YmdHis")]);
        // return $select;

        // proceed and save the notification in  the notification table
        for ($index=0; $index < count($select); $index++) {
            $notification_title = $select[$index]->book_title." is due for Check-In";
            $stats_link = $select[$index]->user_borrowing == 'student' ? '/Circulation/Stats/View/Student/'.$select[$index]->user_checked_out : '/Circulation/Stats/View/Staff/'.$select[$index]->user_checked_out;
            $notification_content = "<b>".$select[$index]->book_title."</b> of call number <i>".$select[$index]->book_call_number."</i> borrowed by <a class='text-decoration-underline text-reset' href='".$stats_link."' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-original-title='Click to view borrower borrowing stats!'><i>".ucwords(strtolower($select[$index]->borrower_name))."</i></a> <span class='badge badge-pill badge-soft-success'>".$select[$index]->user_borrowing."</span> is due for checkin. Its expected return date was ".date("D dS M Y : h:i:sA",strtotime($select[$index]->expected_return_date)).". Click here to <a class='text-decoration-underline text-reset' href='/Circulation/View/check-out/".$select[$index]->book_id."/".$select[$index]->circulation_id."'>Check-In</a>.";
            $date_created = date("YmdHis");
            $book_id = $select[$index]->book_id;

            // this insert statement will check if a record of the same book is present, if so, it won`t insert the record.
            $insert = DB::insert("INSERT INTO `library_notifications` (`notification_title`,`notification_content`,`notification_action`,`date_created`,`book_id`)
                                    SELECT ?, ?, '[]',?,?
                                    WHERE NOT EXISTS (SELECT 1 FROM `library_notifications` WHERE `book_id` = ?);",[$notification_title, $notification_content, $date_created, $book_id, $book_id]);
        }
    }
    //handle Acquisitions
    function Acquisitions(Request $request){
        // return $request;
        if (session("school_details") == null) {
            session()->flash("error","Your session has expired, Login to proceed!");
            return redirect("/");
        }
        $notifications = $request->input("notifications") != null ? $request->input('notifications') : [];
        $notification_count = $request->input("notification_count");

        $database_name = session("school_details")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        // connect to mysql 2
        DB::setDefaultConnection("mysql2");
        // return $this->getNotification();
        
        // get the list of books recorded
        $search_title = null;
        if (count($request->input()) > 0) {
            // keyword
            $keyword = $request->input("keyword_search");
            $search_title = $keyword;
            $select = DB::select("SELECT * FROM `library_details` WHERE `book_title` LIKE \"%".$keyword."%\" OR `book_author` LIKE \"%".$keyword."%\" OR `book_publishers` LIKE \"%".$keyword."%\" OR `isbn_13` LIKE \"%".$keyword."%\" OR `isbn_10` LIKE \"%".$keyword."%\" OR `shelf_no_location` LIKE \"%".$keyword."%\" OR `call_no` LIKE \"%".$keyword."%\" OR `keywords` LIKE \"%".$keyword."%\" LIMIT 100");
            
            // using paginate
            // $library_details = LibraryDetail::where(function($query) use ($keyword) {
            //     $query->where('book_title', 'like', '%'.$keyword.'%')
            //           ->orWhere('book_author', 'like', '%'.$keyword.'%')
            //           ->orWhere('book_publishers', 'like', '%'.$keyword.'%')
            //           ->orWhere('isbn_13', 'like', '%'.$keyword.'%')
            //           ->orWhere('isbn_10', 'like', '%'.$keyword.'%')
            //           ->orWhere('shelf_no_location', 'like', '%'.$keyword.'%')
            //           ->orWhere('call_no', 'like', '%'.$keyword.'%')
            //           ->orWhere('keywords', 'like', '%'.$keyword.'%');
            // })
            // ->paginate(20);
            
            // $select = $library_details;
            // return $library_details;
        }else {
            $select = DB::select("SELECT * FROM `library_details` ORDER BY `book_id` DESC LIMIT 100");

            // use paginate
            // $library_details = LibraryDetail::paginate(20);
            // $select = $library_details;
        }

        $larger_length = 0;
        for ($index=0; $index < count($select); $index++) {
            $title = strlen($select[$index]->book_title);
            $larger_length = $title >= $larger_length ? $title : $larger_length;
            $book_author = strlen($select[$index]->book_author);
            $larger_length = $book_author >= $larger_length ? $book_author : $larger_length;
            $isbn_13 = strlen($select[$index]->isbn_13);
            $larger_length = $isbn_13 >= $larger_length ? $isbn_13 : $larger_length;
            $date_recorded = strlen($select[$index]->date_recorded);
            $larger_length = $date_recorded >= $larger_length ? $date_recorded : $larger_length;
            $call_no = strlen($select[$index]->call_no);
            $larger_length = $call_no >= $larger_length ? $call_no : $larger_length;
        }

        // get the list of subjects taught in school
        $subjects = DB::select("SELECT * FROM `table_subject`");
        $subject_name = [];
        for ($index=0; $index < count($subjects); $index++) { 
            array_push($subject_name,$subjects[$index]->display_name);
        }
        // get libraries
        $libraries = [];
        $my_libraries = DB::select("SELECT * FROM `settings` WHERE `sett` = 'libraries'");

        // check if my libraries has anything
        $libraries = count($my_libraries) > 0 ? json_decode($my_libraries[0]->valued) : [];

        // return $select;
        return view("acqusitions",["notification_count" => $notification_count, "notifications" => $notifications,"search_title" => $search_title,"libraries" => $libraries,"book_list" => $select,"subject_name" => $subject_name, "larger_length" => $larger_length]);
    }

    function addBook(Request $request){
        if (session("school_details") == null) {
            session()->flash("error","Your session has expired, Login to proceed!");
            return redirect("/");
        }
        $database_name = session("school_details")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        // connect to mysql 2
        DB::setDefaultConnection("mysql2");

        // store the data passed
        $book_title = $request->input("book_title");
        $book_author = $request->input("book_author");
        $isbn_10 = $request->input("isbn_10");
        $isbn_13 = $request->input("isbn_13");
        $book_publishers = $request->input("book_publishers");
        $date_published = $request->input("date_published");
        $book_call_no = $request->input("book_call_no");
        $book_category = $request->input("book_category");
        $book_location = $request->input("book_location");
        $book_description = $request->input("book_description");
        $book_cover_url = $request->input("book_cover_url");
        $book_dimensions = $request->input("book_dimensions");
        $book_language = $request->input("book_language");
        $no_of_revisions = $request->input("no_of_revisions");
        $no_of_pages = $request->input("no_of_pages");
        $library_location = $request->input("library_location");
        // return $request;

        // check the book call number if present
        $book_calls_number = DB::select("SELECT * FROM `library_details` WHERE `call_no` = ?",[$book_call_no]);
        if (count($book_calls_number) > 0) {
            // book details
            session()->flash("book_title",$book_title);
            session()->flash("book_author",$book_author);
            session()->flash("isbn_10",$isbn_10);
            session()->flash("isbn_13",$isbn_13);
            session()->flash("book_publishers",$book_publishers);
            session()->flash("date_published",$date_published);
            session()->flash("book_call_no",$book_call_no);
            session()->flash("book_category",$book_category);
            session()->flash("book_location",$book_location);
            session()->flash("book_description",$book_description);
            session()->flash("book_cover_url",$book_cover_url);
            session()->flash("book_dimensions",$book_dimensions);
            session()->flash("book_language",$book_language);
            session()->flash("no_of_revisions",$no_of_revisions);
            session()->flash("no_of_pages",$no_of_pages);
            session()->flash("library_location",$library_location);
            
            session()->flash("error","The book call number \"$book_call_no\" is present, Recreate a different one!");
            return redirect("/Acquisitions");
        }

        if (strlen(trim($isbn_13)) != 13) {
            // book details
            session()->flash("book_title",$book_title);
            session()->flash("book_author",$book_author);
            session()->flash("isbn_10",$isbn_10);
            session()->flash("isbn_13",$isbn_13);
            session()->flash("book_publishers",$book_publishers);
            session()->flash("date_published",$date_published);
            session()->flash("book_call_no",$book_call_no);
            session()->flash("book_category",$book_category);
            session()->flash("book_location",$book_location);
            session()->flash("book_description",$book_description);
            session()->flash("book_cover_url",$book_cover_url);
            session()->flash("book_dimensions",$book_dimensions);
            session()->flash("book_language",$book_language);
            session()->flash("no_of_revisions",$no_of_revisions);
            session()->flash("no_of_pages",$no_of_pages);
            session()->flash("library_location",$library_location);

            session()->flash("error","The book ISBN-13 no \"$isbn_13\" should be equal to 13 characters! It has ".strlen(trim($isbn_13))." characters.");
            return redirect("/Acquisitions");
        }

        // save the book details
        $save_image = DB::insert("INSERT INTO `library_details` (`book_title`,`book_author`,`book_publishers`,`published_date`,`thumbnail_location`,`book_category`,`isbn_13`,`isbn_10`,`date_recorded`,`physical_dimensions`,`no_of_revisions`,`call_no`,`language`,`description`,`shelf_no_location`,`no_of_pages`,`library_location`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",[$book_title,$book_author,$book_publishers,$date_published,$book_cover_url,$book_category,$isbn_13,$isbn_10,date("YmdHis"),$book_dimensions,$no_of_revisions,$book_call_no,$book_language,$book_description,$book_location,$no_of_pages,$library_location]);

        session()->flash("success","\"$book_title\" has been recorded successfully");
        return redirect("/Acquisitions");
    }

    function getBookDetails(Request $request){
        if (session("school_details") == null) {
            session()->flash("error","Your session has expired, Login to proceed!");
            return redirect("/");
        }
        // return $request;
        
        // get ISBN no
        $isbn_number = $request->input("isbn_number");
        $api_url = "https://openlibrary.org/api/books?bibkeys=ISBN:".$isbn_number."&jscmd=details&format=json";

        // check if the isbn number is present in the database and return book details
        $database_name = session("school_details")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        // connect to mysql 2
        DB::setDefaultConnection("mysql2");

        // get the subject details
        $book_details = DB::select("SELECT * FROM `library_details` WHERE `isbn_13` = ? OR `isbn_10` = ? ORDER BY `book_id` DESC LIMIT 1",[$isbn_number,$isbn_number]);

        // get the book details
        $return_book_details = new stdClass();
        if (count($book_details) > 0) {
            $return_book_details->book_title = $book_details[0]->book_title;
            $return_book_details->book_author = $book_details[0]->book_author;
            $return_book_details->isbn_10 = $book_details[0]->isbn_10;
            $return_book_details->isbn_13 = $book_details[0]->isbn_13;
            $return_book_details->pages = $book_details[0]->no_of_pages;
            $return_book_details->category = "";
            $return_book_details->publishers = $book_details[0]->book_publishers;
            $return_book_details->date_published = $book_details[0]->published_date;
            $return_book_details->call_no = "";
            $return_book_details->book_location = $book_details[0]->shelf_no_location;
            $return_book_details->book_description = $book_details[0]->description;
            $return_book_details->cover_url = $book_details[0]->thumbnail_location;
            $return_book_details->physical_dimensions = $book_details[0]->physical_dimensions;
            $return_book_details->language = $book_details[0]->language;
            $return_book_details->revisions = $book_details[0]->no_of_revisions;
            $return_book_details->present = true;
            $return_book_details->found = true;
            $return_book_details->last_call_no = $book_details[0]->call_no;
        }else{
            // do a curl request getting the book details from the cloud
            // API endpoint URL
            $apiUrl = $api_url;
            
            // Initialize cURL session
            $ch = curl_init();

            // Set cURL options
            curl_setopt($ch, CURLOPT_URL, $apiUrl);  // Set the API endpoint URL
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string instead of printing it

            // Execute the cURL request and get the response
            $response = curl_exec($ch);

            // Check for cURL errors
            if (curl_errno($ch)) {
                echo 'cURL Error: ' . curl_error($ch);
            }

            // Close the cURL session
            curl_close($ch);
            // return $response;

            // Process the API response
            if (strlen($response) > 0) {
                if (!$this->isJsonDataPresent($response)) {
                    // Assuming the API returns JSON data, you can decode it into a PHP array
                    $data = json_decode($response, true)['ISBN:'.$isbn_number.''];
    
                    // store the book details
                    $return_book_details->book_title = isset($data['details']['title']) ? $data['details']['title'] : "";
    
                    // get book authors
                    $book_authors = "";
                    if (isset($data['details']['authors'])) {
                        if(count($data['details']['authors'])){
                            for ($index=0; $index < count($data['details']['authors']); $index++) { 
                                $element = $data['details']['authors'][$index];
                                $book_authors.=$element['name'].", ";
                            }
                        }
                    }
                    $book_authors = strlen(trim($book_authors)) > 0 ? substr($book_authors,0,strlen($book_authors)-2) : "";
                    $return_book_details->book_author = $book_authors;
                    $return_book_details->isbn_10 = isset($data['details']['isbn_10']) ? $data['details']['isbn_10'][0] : "";
                    $return_book_details->isbn_13 = isset($data['details']['isbn_13']) ? $data['details']['isbn_13'][0] : "";
                    $return_book_details->pages = isset($data['details']['number_of_pages']) ? $data['details']['number_of_pages'] : "";
                    $return_book_details->category = "";
    
                    // get the book publishers
                    $book_publishers = "";
                    if (isset($data['details']['publishers'])) {
                        if(count($data['details']['publishers'])){
                            for ($index=0; $index < count($data['details']['publishers']); $index++) { 
                                $element = $data['details']['publishers'][$index];
                                $book_publishers.=$element.", ";
                            }
                        }
                    }
                    $book_publishers = strlen(trim($book_publishers)) > 0 ? substr($book_publishers,0,strlen($book_publishers)-2) : "";
    
                    // get the details
                    $return_book_details->publishers = $book_publishers;
                    $return_book_details->date_published = isset($data['details']['publish_date']) ? date("Y-m-d",strtotime($data['details']['publish_date'])) : "";
                    $return_book_details->call_no = "";
                    $return_book_details->book_location = "";
                    $return_book_details->book_description = isset($data['details']['description']['value']) ? $data['details']['description']['value'] : "";
                    $return_book_details->cover_url = isset($data['thumbnail_url']) ? $data['thumbnail_url'] : "";
                    $return_book_details->physical_dimensions = isset($data['details']['physical_dimensions']) ? $data['details']['physical_dimensions'] : "";
    
                    // language
                    $book_lang = "English";
                    if (isset($data['details']['languages'])) {
                        if (count($data['details']['languages']) > 0) {
                            $my_language = substr($data['details']['languages'][0]['key'],-3);
                            if ($my_language == "eng") {
                                $book_lang = "English";
                            }elseif($my_language == "ger"){
                                $book_lang = "German";
                            }elseif($my_language == "fre"){
                                $book_lang = "French";
                            }elseif($my_language == "swa"){
                                $book_lang = "Swahili";
                            }elseif($my_language == "cze"){
                                $book_lang = "Czech";
                            }elseif($my_language == "spa"){
                                $book_lang = "Spanish";
                            }elseif($my_language == "est"){
                                $book_lang = "Estonian";
                            }elseif($my_language == "chi"){
                                $book_lang = "Chinese";
                            }elseif($my_language == "rus"){
                                $book_lang = "Russian";
                            }elseif($my_language == "ita"){
                                $book_lang = "Italian";
                            }elseif($my_language == "jpn"){
                                $book_lang = "Japanese";
                            }elseif($my_language == "por"){
                                $book_lang = "Portuguese";
                            }elseif($my_language == "ara"){
                                $book_lang = "Arabic";
                            }elseif($my_language == "kor"){
                                $book_lang = "Korean";
                            }elseif($my_language == "pol"){
                                $book_lang = "Polish";
                            }elseif($my_language == "heb"){
                                $book_lang = "Hebrew";
                            }elseif($my_language == "cmn"){
                                $book_lang = "Mandarin";
                            }elseif($my_language == "guj"){
                                $book_lang = "Gujarati";
                            }elseif($my_language == "heb"){
                                $book_lang = "Hebrew";
                            }else {
                                $book_lang = $my_language;
                            }
                        }
                    }
                    $return_book_details->language = $book_lang;
                    $return_book_details->revisions = isset($data['details']['revision']) ? $data['details']['revision'] : 0;
                    $return_book_details->present = false;
                    $return_book_details->found = true;
                    $return_book_details->last_call_no = "N/A";
                }else {
                    $return_book_details->book_title = "";
                    $return_book_details->book_author = "";
                    $return_book_details->isbn_10 = "";
                    $return_book_details->isbn_13 = "";
                    $return_book_details->pages = "";
                    $return_book_details->category = "";
                    $return_book_details->publishers = "";
                    $return_book_details->date_published = "";
                    $return_book_details->call_no = "";
                    $return_book_details->book_location = "";
                    $return_book_details->book_description = "";
                    $return_book_details->cover_url = "";
                    $return_book_details->physical_dimensions = "";
                    $return_book_details->language = "";
                    $return_book_details->revisions = "";
                    $return_book_details->present = false;
                    $return_book_details->found = false;
                    $return_book_details->last_call_no = "N/A";
                }
            } else {
                $return_book_details->book_title = "";
                $return_book_details->book_author = "";
                $return_book_details->isbn_10 = "";
                $return_book_details->isbn_13 = "";
                $return_book_details->pages = "";
                $return_book_details->category = "";
                $return_book_details->publishers = "";
                $return_book_details->date_published = "";
                $return_book_details->call_no = "";
                $return_book_details->book_location = "";
                $return_book_details->book_description = "";
                $return_book_details->cover_url = "";
                $return_book_details->physical_dimensions = "";
                $return_book_details->language = "";
                $return_book_details->revisions = "";
                $return_book_details->present = false;
                $return_book_details->found = false;
                $return_book_details->last_call_no = "N/A";
            }
        }

        // RETURN DATA
        return $return_book_details;
    }

    // get the book data
    function viewBookData($book_id, Request $request){
        $notifications = $request->input("notifications") != null ? $request->input('notifications') : [];
        $notification_count = $request->input("notification_count");
        // check if the isbn number is present in the database and return book details
        $database_name = session("school_details")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        // connect to mysql 2
        DB::setDefaultConnection("mysql2");

        // get the book data
        $book_details = DB::select("SELECT * FROM `library_details` WHERE `book_id` = ?",[$book_id]);
        
        // if books are present then we proceed to get more details
        if (count($book_details) > 0) {
            // get the subjects present in school
            // get the list of subjects taught in school
            $subjects = DB::select("SELECT * FROM `table_subject`");
            $subject_name = [];
            for ($index=0; $index < count($subjects); $index++) { 
                array_push($subject_name,$subjects[$index]->display_name);
            }

            // get the book circulation record
            $book_circulation_list = DB::select("SELECT * FROM `book_circulation` WHERE `book_id` = ? ORDER BY `circulation_id` DESC",[$book_id]);
            $book_circulation_details = [];
            for ($index=0; $index < count($book_circulation_list); $index++) {
                $book_borrower = "N/A";
                if ($book_circulation_list[$index]->user_borrowing == "staff") {
                    // connect to mysql 2
                    DB::setDefaultConnection("mysql");
                    // borrower name
                    $borrower_name = DB::select("SELECT * FROM `user_tbl` WHERE `user_id` = ?",[$book_circulation_list[$index]->user_checked_out]);
                    $book_borrower = count($borrower_name) > 0 ? ucwords(strtolower($borrower_name[0]->fullname)) : "N/A";
                }else{
                    // connect to mysql 2
                    DB::setDefaultConnection("mysql2");
                    // borrower name
                    $borrower_name = DB::select("SELECT * FROM `student_data` WHERE `adm_no` = ?",[$book_circulation_list[$index]->user_checked_out]);
                    $book_borrower = count($borrower_name) > 0 ? ucwords(strtolower($borrower_name[0]->first_name." ".$borrower_name[0]->second_name)) : "N/A";
                }

                // checkout name
                // connect to mysql 2
                DB::setDefaultConnection("mysql");
                $check_out_name = DB::select("SELECT * FROM `user_tbl` WHERE `user_id` = ?",[$book_circulation_list[$index]->checked_out_by]);
                $check_in_name = DB::select("SELECT * FROM `user_tbl` WHERE `user_id` = ?",[$book_circulation_list[$index]->checked_in_by]);

                if ($book_circulation_list[$index]->return_date != "NULL" && $book_circulation_list[$index]->return_date != NULL) {
                    // check in circulation
                    $circulation_desc = "Returned on <b>".date("D dS M Y @ H:i:s A", strtotime($book_circulation_list[$index]->return_date))."</b> by <b>".$book_borrower." (".$book_circulation_list[$index]->user_borrowing.")</b> was expected to return on <b>".date("D dS M Y", strtotime($book_circulation_list[$index]->expected_return_date))."</b>";
                    // EXP RD
                    $exp_RD = date("Ymd", strtotime($book_circulation_list[$index]->expected_return_date));
                    $RD = date("Ymd", strtotime($book_circulation_list[$index]->return_date));
                    if ($exp_RD == $RD) {
                        $circulation_desc.=" but returned on the same day. Checked in by <b>".(count($check_in_name) > 0 ? ucwords(strtolower($check_in_name[0]->fullname)) : "N/A")."</b>";
                    }elseif ($exp_RD > $RD) {
                        $circulation_desc.=" but returned <b>".$this->getDateDifference($RD,$exp_RD)." day(s)</b> earlier. Checked in by <b>".(count($check_in_name) > 0 ? ucwords(strtolower($check_in_name[0]->fullname)) : "N/A")."</b>";
                    }else{
                        $circulation_desc.=" but returned <b>".$this->getDateDifference($RD,$exp_RD)." day(s)</b> later. Checked in by <b>".(count($check_in_name) > 0 ? ucwords(strtolower($check_in_name[0]->fullname)) : "N/A")."</b>";
                    }

                    $data = new stdClass();
                    $data->date = $book_circulation_list[$index]->return_date;
                    $data->description = $circulation_desc;

                    // add data description
                    array_push($book_circulation_details,$data);
                }
                
                // circulation description
                $circulation_desc = "Borrowed on <b>".date("D dS M Y @ H:i:s A", strtotime($book_circulation_list[$index]->date_checked_out))."</b> by <b>".$book_borrower." (".$book_circulation_list[$index]->user_borrowing.")</b> expected to return on <b>".date("D dS M Y", strtotime($book_circulation_list[$index]->expected_return_date))."</b>. Checked out by <b>".(count($check_out_name) > 0 ? ucwords(strtolower($check_out_name[0]->fullname)) : "N/A")."</b>";

                $data = new stdClass();
                $data->date = $book_circulation_list[$index]->date_checked_out;
                $data->description = $circulation_desc;
                array_push($book_circulation_details,$data);
            }
            // return $book_circulation_details;

            // get the book details
            $book_details[0]->thumbnail_location = $this->isLinkValid($book_details[0]->thumbnail_location) ? $book_details[0]->thumbnail_location : "/images/book_cover.jpg";

            // connect to mysql 2
            DB::setDefaultConnection("mysql2");
            // get libraries
            $libraries = [];
            $my_libraries = DB::select("SELECT * FROM `settings` WHERE `sett` = 'libraries'");

            // check if my libraries has anything
            $libraries = count($my_libraries) > 0 ? json_decode($my_libraries[0]->valued) : [];
            return view("book_details",["notification_count" => $notification_count, "notifications" => $notifications,"libraries" => $libraries, "book_circulation_details" => $book_circulation_details,"book_details" => $book_details[0], "subject_name" => $subject_name]);
        }else {
            session()->flash("error","Book details not found, try another books!");
            return redirect("/Acquisitions");
        }
    }
    function getDateDifference($date1, $date2, $format = 'days') {
        $datetime1 = new DateTime($date1);
        $datetime2 = new DateTime($date2);
        $interval = $datetime1->diff($datetime2);

        switch ($format) {
            case 'years':
                return $interval->y;
            case 'months':
                return $interval->y * 12 + $interval->m;
            case 'days':
                return $interval->days;
            case 'hours':
                return $interval->days * 24 + $interval->h;
            case 'minutes':
                return ($interval->days * 24 + $interval->h) * 60 + $interval->i;
            case 'seconds':
                return (($interval->days * 24 + $interval->h) * 60 + $interval->i) * 60 + $interval->s;
            default:
                return $interval;
        }
    }

    function updateBooks(Request $request){
        if (session("school_details") == null) {
            session()->flash("error","Your session has expired, Login to proceed!");
            return redirect("/");
        }
        // check if the isbn number is present in the database and return book details
        $database_name = session("school_details")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        // connect to mysql 2
        DB::setDefaultConnection("mysql2");
        // return $request;
        $book_ids = $request->input("book_ids");
        $book_title = $request->input("book_title");
        $book_author = $request->input("book_author");
        $isbn_10 = $request->input("isbn_10");
        $isbn_13 = $request->input("isbn_13");
        $no_of_pages = $request->input("no_of_pages");
        $book_category = $request->input("book_category");
        $book_publishers = $request->input("book_publishers");
        $date_published = $request->input("date_published");
        $book_call_no = $request->input("book_call_no");
        $book_location = $request->input("book_location");
        $book_description = $request->input("book_description");
        $book_cover_url = $request->input("book_cover_url");
        $book_dimensions = $request->input("book_dimensions");
        $book_language = $request->input("book_language");
        $no_of_revisions = $request->input("no_of_revisions");
        $library_location = $request->input("library_location");

        // check if the book call number is present
        $book_dets = DB::select("SELECT * FROM `library_details` WHERE `call_no` = ? AND NOT `book_id` = ?",[$book_call_no,$book_ids]);
        if (count($book_dets) > 0) {
            session()->flash("error","The book call number is already used by another book, Use another call-no");
            return redirect("/Acquisitions/Book-details/".$book_ids);
        }

        // update the call number on the circulation table
        $update = DB::update("UPDATE `book_circulation` SET `book_call_number` = ? WHERE `book_id` = ?",[$book_call_no,$book_ids]);

        // UPDATE THE DATA IN THE DATABASE
        $update_data = DB::update("UPDATE `library_details` SET `book_title` = ?, `book_author` = ?, `book_publishers` = ?, `published_date` = ?, `thumbnail_location` = ?, `book_category` = ?, `isbn_13` = ?, `isbn_10` = ?, `physical_dimensions` = ?, `no_of_revisions` = ?, `call_no` = ?, `language` = ?, `description` = ?, `shelf_no_location` = ?, `no_of_pages` = ?, `library_location` = ? WHERE `book_id` = ?",[$book_title,$book_author,$book_publishers,$date_published,$book_cover_url,$book_category,$isbn_13,$isbn_10,$book_dimensions,$no_of_revisions,$book_call_no,$book_language,$book_description,$book_location,$no_of_pages,$library_location,$book_ids]);

        // update the data
        session()->flash("success","\"".$book_title."\" details have been updated successfully!");
        return redirect("/Acquisitions/Book-details/".$book_ids);
    }

    function deleteBook($book_id){
        if (session("school_details") == null) {
            session()->flash("error","Your session has expired, Login to proceed!");
            return redirect("/");
        }
        // check if the isbn number is present in the database and return book details
        $database_name = session("school_details")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        // connect to mysql 2
        DB::setDefaultConnection("mysql2");

        //delete book
        $delete_book = DB::delete("DELETE FROM `library_details` WHERE `book_id` = ?",[$book_id]);
        
        // success message
        session()->flash("success","Book has been deleted successfully!");
        return redirect("/Acquisitions");
    }

    // CHECKS IF JSON IS EMPTY
    function isJsonDataPresent($jsonString) {
        if (session("school_details") == null) {
            session()->flash("error","Your session has expired, Login to proceed!");
            return redirect("/");
        }
        // CHECK IF ITS VALID JSON
        if (!$this->isValidJson($jsonString)) {
            return true;
        }
        // Attempt to decode the JSON data
        $jsonData = json_decode($jsonString);

        // Check if the JSON data is valid and represents an empty JSON object
        if (json_last_error() === JSON_ERROR_NONE && is_object($jsonData) && empty((array)$jsonData)) {
            return true; // JSON data is an empty JSON object
        } else {
            return false; // JSON data contains keys and values
        }
    }

    function isValidJson($jsonString) {
        // Attempt to decode the JSON data
        json_decode($jsonString);
    
        // Check if the decoding was successful and there was no error
        return json_last_error() === JSON_ERROR_NONE;
    }


    function KeywordSearch($keyword){
        if (session("school_details") == null) {
            return [];
        }
        // check if the isbn number is present in the database and return book details
        $database_name = session("school_details")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        // connect to mysql 2
        DB::setDefaultConnection("mysql2");

        // get the table details with the keyword
        $book_details = DB::select("SELECT * FROM `library_details` WHERE `book_title` LIKE '%".$keyword."%' OR `book_author` LIKE '%".$keyword."%' OR `book_publishers` LIKE '%".$keyword."%' OR `isbn_13` LIKE '%".$keyword."%' OR `isbn_10` LIKE '%".$keyword."%' OR `shelf_no_location` LIKE '%".$keyword."%' OR `call_no` LIKE '%".$keyword."%' OR `shelf_no_location` LIKE '%".$keyword."%' OR `keywords` LIKE '%".$keyword."%'");

        // no book details
        return $book_details;
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

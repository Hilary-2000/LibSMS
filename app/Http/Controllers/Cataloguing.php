<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

date_default_timezone_set('Africa/Nairobi');
class Cataloguing extends Controller
{
    //Manages Cataloguing
    function Cataloging(Request $request){
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

        // get books
        $search_results = null;
        if (count($request->input()) > 0) {
            $keyword = $request->input("keyword_search");
            $search_results = $keyword;
            $select = DB::select("SELECT `isbn_13`, COUNT(`isbn_13`) AS 'Total' FROM `library_details` WHERE `book_title` LIKE \"%".$keyword."%\" OR `book_author` LIKE \"%".$keyword."%\" OR `book_publishers` LIKE \"%".$keyword."%\" OR `isbn_13` LIKE \"%".$keyword."%\" OR `isbn_10` LIKE \"%".$keyword."%\" OR `shelf_no_location` LIKE \"%".$keyword."%\" OR `call_no` LIKE \"%".$keyword."%\" OR `shelf_no_location` LIKE \"%".$keyword."%\" OR `keywords` LIKE \"%".$keyword."%\" GROUP BY `isbn_13` LIMIT 100;");
        }else {
            $select = DB::select("SELECT `isbn_13`, COUNT(`isbn_13`) AS 'Total' FROM `library_details` GROUP BY `isbn_13` LIMIT 100;");
        }
        // get the different books you have
        for ($index=0; $index < count($select); $index++) {
            // get the number of records of each book group
            $details = DB::select("SELECT * FROM `library_details` WHERE `isbn_13` = ?;",[$select[$index]->isbn_13]);
            
            //encode
            $details[0] = json_encode($details[0]);
            $select[$index] = json_encode($select[$index]);

            // decode
            $details[0] = json_decode($details[0], true);
            $select[$index] = json_decode($select[$index], true);
            // return $select[$index];
            $select[$index] = array_merge($details[0],$select[$index]);
        }
        // return $select;
        return view("catalogue",["search_results" => $search_results,"book_list" => $select]);
    }
    function keywordSearch($keyword){
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

        // get books
        $select = DB::select("SELECT `isbn_13`, COUNT(`isbn_13`) AS 'Total' FROM `library_details` WHERE `book_title` LIKE '%".$keyword."%' OR `book_author` LIKE '%".$keyword."%' OR `book_publishers` LIKE '%".$keyword."%' OR `isbn_13` LIKE '%".$keyword."%' OR `isbn_10` LIKE '%".$keyword."%' OR `shelf_no_location` LIKE '%".$keyword."%' OR `call_no` LIKE '%".$keyword."%' OR `shelf_no_location` LIKE '%".$keyword."%' OR `keywords` LIKE '%".$keyword."%' GROUP BY `isbn_13` LIMIT 100;");
        // get the different books you have
        for ($index=0; $index < count($select); $index++) {
            // get the number of records of each book group
            $details = DB::select("SELECT * FROM `library_details` WHERE `isbn_13` = ?;",[$select[$index]->isbn_13]);
            
            //encode
            $details[0] = json_encode($details[0]);
            $select[$index] = json_encode($select[$index]);

            // decode
            $details[0] = json_decode($details[0], true);
            $select[$index] = json_decode($select[$index], true);
            // return $select[$index];
            $select[$index] = array_merge($details[0],$select[$index]);
        }
        return $select;
    }

    // get book details with the book id
    function editBookDets($book_isbn){
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

        // get the books details
        $book_data = DB::select("SELECT * FROM `library_details` WHERE `isbn_13` = ? ORDER BY `book_id` DESC",[$book_isbn]);
        // return $book_data;
        // set the book thumbnail
        for ($index=0; $index < count($book_data); $index++) {
            if ($index == 0) {
                $book_data[$index]->thumbnail_location = $this->isLinkValid($book_data[$index]->thumbnail_location) ? $book_data[$index]->thumbnail_location : "/images/book_cover.jpg";
            }
        }

        // get the subjects
        $subjects = DB::select("SELECT * FROM `table_subject`");
        $subject_name = [];
        for ($index=0; $index < count($subjects); $index++) {
            $sub_name = isset($subjects[$index]->display_name) ? $subjects[$index]->display_name : "Not Set!";
            array_push($subject_name,$sub_name);
        }
        return view("book_catalogue_details",["book_data" => $book_data[0],"book_data_all" => $book_data,"subject_name" => $subject_name]);
    }

    function editBooks(Request $request){
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

        // return $request;
        $book_location = $request->input("book_location");
        $book_call_number = $request->input("book_call_number");
        $book_edition = $request->input("book_edition");
        $book_language = $request->input("book_language");
        $book_category = $request->input("book_category");
        $book_keywords = $request->input("book_keywords");
        $book_id = $request->input("book_id");

        // check the call number
        $call_number = DB::select("SELECT * FROM `library_details` WHERE `call_no` = ? AND NOT `book_id` = ? ;",[$book_call_number,$book_id]);

        if (count($call_number) == 0) {
            // update the book
            $update = DB::update("UPDATE `library_details` SET `shelf_no_location` = ?, `call_no` = ?, `edition` = ?, `language` = ?, `book_category` = ?, `keywords` = ? WHERE `book_id` = ?",[$book_location,$book_call_number,$book_edition,$book_language,$book_category,$book_keywords,$book_id]);
    
            // set the session
            $call_number = DB::select("SELECT * FROM `library_details` WHERE `book_id` = ?",[$book_id]);
            if (count($call_number) > 0) {
                // update the call no on the book circulation table
                DB::update("UPDATE `book_circulation` SET `book_call_number` = ? WHERE `book_id` = ?",[$book_call_number,$book_id]);

                // redirect back to the book page
                session()->flash("success","Book data has been successfully updated!");
                return redirect("/Cataloging/Edit/".$call_number[0]->isbn_13);
            }
            session()->flash("error","Book details not found!");
            return redirect("/Cataloging");
        }else{
            $call_number = DB::select("SELECT * FROM `library_details` WHERE `book_id` = ?",[$book_id]);
            // return $call_number;
            if (count($call_number) > 0) {
                session()->flash("error","The book call number \"".$book_call_number."\" is already used!");
                return redirect("/Cataloging/Edit/".$call_number[0]->isbn_13);
            }
            session()->flash("error","Book details not found!");
            return redirect("/Cataloging");
        }
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

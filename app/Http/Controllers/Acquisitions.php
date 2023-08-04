<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class Acquisitions extends Controller
{
    //handle Acquisitions
    function Acquisitions(){
        $database_name = session("school_details")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        // connect to mysql 2
        DB::setDefaultConnection("mysql2");
        
        // get the list of books recorded
        $select = DB::select("SELECT * FROM `library_details` ORDER BY `book_id` DESC");

        // get the list of subjects taught in school
        $subjects = DB::select("SELECT * FROM `table_subject`");
        $subject_name = [];
        for ($index=0; $index < count($subjects); $index++) { 
            array_push($subject_name,$subjects[$index]->display_name);
        }
        return view("acqusitions",["book_list" => $select,"subject_name" => $subject_name]);
    }

    function addBook(Request $request){
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

            session()->flash("error","The book ISBN-13 no \"$isbn_13\" should be equal to 13 characters! It has ".strlen(trim($isbn_13))." characters.");
            return redirect("/Acquisitions");
        }

        // save the book details
        $save_image = DB::insert("INSERT INTO `library_details` (`book_title`,`book_author`,`book_publishers`,`published_date`,`thumbnail_location`,`book_category`,`isbn_13`,`isbn_10`,`date_recorded`,`physical_dimensions`,`no_of_revisions`,`call_no`,`language`,`description`,`shelf_no_location`,`no_of_pages`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",[$book_title,$book_author,$book_publishers,$date_published,$book_cover_url,$book_category,$isbn_13,$isbn_10,date("YmdHis"),$book_dimensions,$no_of_revisions,$book_call_no,$book_language,$book_description,$book_location,$no_of_pages]);

        session()->flash("success","\"$book_title\" has been recorded successfully");
        return redirect("/Acquisitions");
    }

    function getBookDetails(Request $request){
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
        $book_details = DB::select("SELECT * FROM `library_details` WHERE `isbn_13` = ? OR `isbn_10` = ? LIMIT 1",[$isbn_number,$isbn_number]);

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
            }
        }

        // RETURN DATA
        return $return_book_details;
    }

    // CHECKS IF JSON IS EMPTY
    function isJsonDataPresent($jsonString) {
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
}
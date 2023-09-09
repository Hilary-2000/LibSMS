<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class Settings extends Controller
{
    //handles all the setting information for the users
    function userMgmt(){
        if (session("school_details") == null) {
            session()->flash("error","Your session has expired, Login to proceed!");
            return redirect("/");
        }
        // check if the isbn number is present in the database and return book details
        $database_name = session('school_details')->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        // connect to mysql 2
        DB::setDefaultConnection("mysql");

        // get all users that are privileged to access the library
        $school_code = session()->get("school_details")->school_code;
        $user_data = DB::select("SELECT * FROM `user_tbl` WHERE (`auth` = '0' OR `auth` = '1' OR `auth` = 'Librarian') AND `school_code` = ?",[$school_code]);
        
        // edit their library priviledged
        return view("user_mgmt",["user_data" => $user_data]);
    }

    function showUserDetails($user_id){
        if (session("school_details") == null) {
            session()->flash("error","Your session has expired, Login to proceed!");
            return redirect("/");
        }
        // check if the isbn number is present in the database and return book details
        $database_name = session('school_details')->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        // get the user detail
        $school_code = session()->get("school_details")->school_code;
        $user_data = DB::select("SELECT * FROM `user_tbl` WHERE `user_id` = ? AND `school_code` = ?",[$user_id,$school_code]);
        
        // redirect if its invalid user
        if (count($user_data) == 0) {
            session()->flash("error","Invalid!");
            return redirect("/Settings/User-mgmt");
        }

        // proceed and get the user information
        // return session()->get("user_details");
        // isLinkValid($url)

        // check if it has a valid image
        $user_data[0]->profile_loc = $this->isLinkValid("https://lsims.ladybirdsmis.com/sims/".$user_data[0]->profile_loc) && $user_data[0]->profile_loc != null ? $user_data[0]->profile_loc : "images/dp.png";
        // return $user_data;
        return view("change_lib_previleges",["user_data" => $user_data[0]]);
    }

    function changePrivileges(Request $request){
        if (session("school_details") == null) {
            session()->flash("error","Your session has expired, Login to proceed!");
            return redirect("/");
        }
        // check if the isbn number is present in the database and return book details
        $database_name = session('school_details')->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        // get the user detail
        $school_code = session()->get("school_details")->school_code;

        $user_id = $request->input("user_id");
        $store_privileges = $request->input("store_privileges");

        DB::update("UPDATE `user_tbl` SET `library_previleges` = ? WHERE `user_id` = ? AND `school_code` = ?",[$store_privileges,$user_id,$school_code]);
        session()->flash("success","Privileges have been updated successfully!");
        return redirect("/Settings/User-mgmt/".$user_id);
        
    }
    function libraryManagement(){
        // get the libraries
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

        // get libraries
        $libraries = [];
        $my_libraries = DB::select("SELECT * FROM `settings` WHERE `sett` = 'libraries'");

        // check if my libraries has anything
        $libraries = count($my_libraries) > 0 ? json_decode($my_libraries[0]->valued) : [];

        for ($index=0; $index < count($libraries); $index++) { 
            $id = $libraries[$index]->id;
            $data = DB::select("SELECT * FROM `library_details` WHERE `library_location` = '".$id."'");
            $count = count($data);
            // total books
            $libraries[$index]->total = $count;
            
            // checked out
            $checked_out = DB::select("SELECT * FROM `library_details` WHERE `availability_status` = '0' AND `library_location` = '".$id."'");
            $libraries[$index]->checked_out = count($checked_out);
        }

        // return $libraries;
        // return the libraries and view
        return view("library_management",["libraries" => $libraries]);
    }

    function UpdateSettings(Request $request){
        $library_id = $request->input("library_id");
        $library_name = $request->input("library_name");

        // get the libraries
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

        // get libraries
        $libraries = [];
        $my_libraries = DB::select("SELECT * FROM `settings` WHERE `sett` = 'libraries'");

        // check if my libraries has anything
        $libraries = count($my_libraries) > 0 ? json_decode($my_libraries[0]->valued) : [];

        for ($index=0; $index < count($libraries); $index++) { 
            $id = $libraries[$index]->id;
            if ($id == $library_id) {
                $libraries[$index]->Name = $library_name;
            }
        }
        
        // UPDATE THE SETTINGS
        $update = DB::select("UPDATE `settings` SET `valued` = ? WHERE `sett` = 'libraries'",[json_encode($libraries)]);

        // return value
        session()->flash("success","Library details updated successfully!");
        return redirect("/Settings/Library-mgmt");
    }

    function NewLibrary(Request $request){
        $library_name_new = $request->input("library_name_new");

        // get the libraries
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

        // get libraries
        $libraries = [];
        $my_libraries = DB::select("SELECT * FROM `settings` WHERE `sett` = 'libraries'");

        // check if my libraries has anything
        $libraries = count($my_libraries) > 0 ? json_decode($my_libraries[0]->valued) : [];

        $max = 0;
        for ($index=0; $index < count($libraries); $index++) { 
            $id = $libraries[$index]->id * 1;
            $max = $id>$max ? $id : $max;
        }

        // add the library details
        $lib_dets = new stdClass();
        $lib_dets->id = $max+1;
        $lib_dets->Name = $library_name_new;
        array_push($libraries,$lib_dets);

        // UPDATE THE SETTINGS
        $update = DB::select("UPDATE `settings` SET `valued` = ? WHERE `sett` = 'libraries'",[json_encode($libraries)]);

        // return value
        session()->flash("success","New library has been added successfully!");
        return redirect("/Settings/Library-mgmt");
    }

    function deleteLibrary($library_id){
        if ($library_id == 1) {
            session()->flash("error","Deleting of the default Library is not allowed!");
            return redirect("/Settings/Library-mgmt");
        }
        // get the libraries
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

        // check if the library has some books
        $library_stats = DB::select("SELECT * FROM `library_details` WHERE `library_location` = ?",[$library_id]);

        // if the library has some books advice that the books be moved
        if (count($library_stats) > 0) {
            session()->flash("error","Deleting of Libraries is not allowed when books are present, Kindly move the books (".count($library_stats)." Book(s)) to a different library!");
            return redirect("/Settings/Library-mgmt");
        }

        // get libraries
        $libraries = [];
        $my_libraries = DB::select("SELECT * FROM `settings` WHERE `sett` = 'libraries'");

        // check if my libraries has anything
        $libraries = count($my_libraries) > 0 ? json_decode($my_libraries[0]->valued) : [];
        
        $new_libraries = [];
        for ($index=0; $index < count($libraries); $index++) {
            if ($libraries[$index]->id == $library_id) {
                continue;
            }
            array_push($new_libraries,$libraries[$index]);
        }
        // UPDATE THE SETTINGS
        $update = DB::select("UPDATE `settings` SET `valued` = ? WHERE `sett` = 'libraries'",[json_encode($new_libraries)]);

        // return value
        session()->flash("success","Library has been deleted successfully!");
        return redirect("/Settings/Library-mgmt");
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

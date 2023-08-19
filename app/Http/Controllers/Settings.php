<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    
    function isLinkValid($url) {
        // check if the url is null
        if ($url == null) {
            return false;
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($statusCode == 200) {
            return true; // Valid link
        } else {
            return false; // Invalid link
        }
    }
}

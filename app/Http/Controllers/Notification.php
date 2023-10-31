<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

date_default_timezone_set('Africa/Nairobi');
class Notification extends Controller
{
    // get all notifications
    function getNotifications(Request $request){
        // get all notifications
        // return $request;
        $notifications = $request->input("notifications") != null ? $request->input('notifications') : [];
        $notification_count = $request->input("notification_count");

        $database_name = session("school_details")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        // connect to mysql 2
        DB::setDefaultConnection("mysql2");

        // get the notifications
        $like_statement = '*"user_id":"'.session('user_id').'","read":"1","delete":"1"*';
        $all_notifications = DB::select("SELECT * FROM `library_notifications` WHERE `notification_action` NOT LIKE ? ORDER BY `date_created` DESC LIMIT 300",[$like_statement]);
        
        
        // loop through the notifications and add a read value
        $user_id = session("user_id");
        for ($index=0; $index < count($all_notifications); $index++) { 
            // get the user details
            $notification_action = $this->isJson($all_notifications[$index]->notification_action) ? json_decode($all_notifications[$index]->notification_action) : [];

            // get the user id status
            $read = 0;
            for ($index1=0; $index1 < count($notification_action); $index1++) { 
                if ($notification_action[$index1]->user_id == $user_id) {
                    $read = $notification_action[$index1]->read;
                    break;
                }
            }
            $all_notifications[$index]->read_status = $read;
        }
        // return $all_notifications;


        // display all the notifications in a table
        return view("notification.index",["notification_count" => $notification_count, "all_notifications" => $all_notifications, "notifications" => $notifications, "search_title" => ""]);
    }

    function isJson($string) {
        // Try to decode the string as JSON
        $json = json_decode($string);
    
        // Check if the decoding was successful and the result is not null
        return $json !== null;
    }

    function showNotifications(Request $request,$show_id){
        $notifications = $request->input("notifications") != null ? $request->input('notifications') : [];
        $notification_count = $request->input("notification_count");

        // get the show id if its legit
        $notification = DB::select("SELECT * FROM `library_notifications` WHERE `notification_id` = ?",[$show_id]);
        
        if (count($notification) == 0) {
            return redirect(url()->route("allNotifications"));
        }

        // proceed and process the already read flag
        $notification_action = $this->isJson($notification[0]->notification_action) ? json_decode($notification[0]->notification_action) : [];

        // create user element
        $user_id = session('user_id');
        $user_entry = new stdClass();
        $user_entry->user_id = $user_id;
        $user_entry->read = 1;
        $user_entry->delete = 0;


        // check if the user data is present
        if (count($notification_action) > 0) {
            // check if it has the specific users entry
            $found = 0;
            for ($index=0; $index < count($notification_action); $index++) { 
                if ($notification_action[$index]->user_id == $user_id) {
                    $notification_action[$index]->read = 1;
                    $notification_action[$index]->delete = 0;
                    $found = 1;
                    break;
                }
            }

            if ($found == 0) {
                // add the new record
                array_push($notification_action,$user_entry);
            }
        }else{
            // add the new record
            array_push($notification_action,$user_entry);
        }

        // return the notification action
        // return $notification_action;

        // change it to string and update the database
        $not_act = json_encode($notification_action);
        
        // update databases
        $update  = DB::update("UPDATE `library_notifications` SET `notification_action` = ? WHERE `notification_id` = ?",[$not_act,$show_id]);

        // return the view that will display the notification
        return view("notification.view",["notification_count" => $notification_count, "notification" => $notification[0],"notifications" => $notifications]);
    }
}

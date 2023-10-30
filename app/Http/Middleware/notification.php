<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class notification
{

    public function getNotification(){
        // set the notifications for all the books that are overdue

        $database_name = session("school_details")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");
        
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
            $notification_title = "\"".$select[$index]->book_title."\" is due for Check-In";
            $stats_link = $select[$index]->user_borrowing == 'student' ? '/Circulation/Stats/View/Student/'.$select[$index]->user_checked_out : '/Circulation/Stats/View/Staff/'.$select[$index]->user_checked_out;
            $notification_content = "<b>\"".$select[$index]->book_title."\"</b> of call number <i>".$select[$index]->book_call_number."</i> borrowed by <a class='text-decoration-underline text-reset' href='".$stats_link."' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-original-title='Click to view borrower borrowing stats!'><i>".ucwords(strtolower($select[$index]->borrower_name))."</i></a> <span class='badge badge-pill badge-soft-success'>".$select[$index]->user_borrowing."</span> is due for checkin. Its expected return date was ".date("D dS M Y : h:i:sA",strtotime($select[$index]->expected_return_date)).". Click here to <a class='text-decoration-underline text-reset' href='/Circulation/View/check-out/".$select[$index]->book_id."/".$select[$index]->circulation_id."'>Check-In</a>.";
            $date_created = date("YmdHis");
            $book_id = $select[$index]->book_id;

            // this insert statement will check if a record of the same book is present, if so, it won`t insert the record.
            $insert = DB::insert("INSERT INTO `library_notifications` (`notification_title`,`notification_content`,`notification_action`,`date_created`,`book_id`)
                                    SELECT ?, ?, '[]',?,?
                                    WHERE NOT EXISTS (SELECT 1 FROM `library_notifications` WHERE `book_id` = ?);",[$notification_title, $notification_content, $date_created, $book_id, $book_id]);
        }

        // get the notifications
        $like_statement = '*"user_id":"'.session('user_id').'","read":"1","delete":"1"*';
        $notifications = DB::select("SELECT * FROM `library_notifications` WHERE `notification_action` NOT LIKE ? ORDER BY `date_created` DESC",[$like_statement]);
        
        // return notifications
        return $notifications;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // check for session;
        if (session("school_details") == null) {
            session()->flash("error","Your session has expired, Login to proceed!");
            return redirect("/");
        }
        
        // get notifications
        DB::setDefaultConnection("mysql2");
        $notifications = $this->getNotification();

        // merge the notifications
        $request->merge(['notifications' => $notifications]);

        // return $notifications
        return $next($request);
    }
}

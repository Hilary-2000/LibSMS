<?php

use App\Http\Controllers\Acquisitions;
use App\Http\Controllers\Cataloguing;
use App\Http\Controllers\Circulation;
use App\Http\Controllers\login;
use App\Http\Controllers\Notification;
use App\Http\Controllers\Reports;
use App\Http\Controllers\Settings;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('login');
// });

Route::get("/",[login::class,"getSchools"]);

Route::post("/process_login",[login::class,"loginLibrary"]);
Route::get("/Dashboard",[login::class,"dashboard"])->middleware("notifications");

// Acquisitions
Route::get("/Acquisitions",[Acquisitions::class,"Acquisitions"])->middleware("notifications");
Route::post("/Acquisitions/add-book",[Acquisitions::class,"addBook"]);
Route::post("/Acquisitions/update-book",[Acquisitions::class,"updateBooks"]);
Route::get("/Acquisitions/getBookDetails",[Acquisitions::class,"getBookDetails"])->middleware("notifications");
Route::get("/Acquisitions/Book-details/{book_id}",[Acquisitions::class,"viewBookData"])->middleware("notifications");
Route::get("/Acquisitions/Delete-book/{book_id}",[Acquisitions::class,"deleteBook"])->middleware("notifications");
Route::get("/Acquisitions/keyword_search/{keyword}",[Acquisitions::class,"KeywordSearch"])->middleware("notifications");
Route::post("/Acquisitions/confirm-lost/{book_id}", [Acquisitions::class, "confirm_lost"])->name("confirm_lost");
Route::get("/Acquisitions/found-book/{book_id}", [Acquisitions::class, "mark_as_found"]);

// Cataloguing
Route::get("/Cataloging",[Cataloguing::class,"Cataloging"])->middleware("notifications");
Route::get("/Cataloging/Edit/{book_isbn}",[Cataloguing::class,"editBookDets"])->middleware("notifications");
Route::post("/Catalogue/EditBooks",[Cataloguing::class,"editBooks"]);
Route::get("/Cataloguing/keyword_search/{keywords}",[Cataloguing::class,"keywordSearch"])->middleware("notifications");

// Circulation
Route::get("/Circulation",[Circulation::class,"circulationDashboard"])->middleware("notifications");
Route::get("/Circulation/check-out",[Circulation::class,"Circulation_Checkout"])->middleware("notifications");
Route::get("/Circulation/check-out/{book_id}",[Circulation::class,"checkOut"])->middleware("notifications");
Route::post("/Circulation/Confirm/check-out",[Circulation::class,"confirmCheckOut"]);
Route::get("/Circulation/View/check-out/{book_id}/{circulation_id}",[Circulation::class,"viewCheckOut"])->middleware("notifications");
Route::get("/Circulation/Cancel/check-out/{circulation_id}",[Circulation::class,"cancelCirculationRecord"])->middleware("notifications");
Route::post("/Circulation/ExtendReturnDate",[Circulation::class,"extendReturnDate"]);
Route::get("/Circulation/Confirm/check-in/{circulation_id}",[Circulation::class,"ConfirmCheckIn"])->middleware("notifications");
Route::get("/Circulation/Cancel/check-in/{circulation_id}",[Circulation::class,"cancelCheckIn"])->middleware("notifications");
Route::get("/Circulation/Stats",[Circulation::class,"circulationStats"])->middleware("notifications");
Route::get("/Circulation/Stats/View/Student/{student_id}",[Circulation::class,"viewStudentStats"])->middleware("notifications");
Route::get("/Circulation/Stats/View/Staff/{staff_id}",[Circulation::class,"viewStaffStats"])->middleware("notifications");
Route::get("/Circulation/Book-Stats/View/{book_stats}",[Circulation::class,"viewBookStats"])->middleware("notifications");

// Reports
Route::get("/Reports",[Reports::class,"getReports"])->middleware("notifications");

// setting
Route::get("/Settings/User-mgmt",[Settings::class,"userMgmt"])->middleware("notifications");
Route::get("/Settings/User-mgmt/{user_id}",[Settings::class,"showUserDetails"])->middleware("notifications");
Route::post("/settings/store_privileges",[Settings::class,"changePrivileges"])->middleware("notifications");
Route::get("/Settings/Library-mgmt",[Settings::class,"libraryManagement"])->middleware("notifications");
Route::post("/Setting/Library-mgmt/Update",[Settings::class,"UpdateSettings"]);
Route::post("/Setting/Library-mgmt/New",[Settings::class,"NewLibrary"]);
Route::get("/Settings/Lib-Management/Delete/{record_id}",[Settings::class,"deleteLibrary"])->middleware("notifications");

// notifications
Route::get("/Notification",[Notification::class,"getNotifications"])->middleware("notifications")->name("allNotifications");
Route::get("/Notification/View/{notification_id}",[Notification::class,"showNotifications"])->middleware("notifications")->name("show_notifications");

// LOGOUT
Route::get("/Logout",[login::class,"Logout"])->middleware("notifications");
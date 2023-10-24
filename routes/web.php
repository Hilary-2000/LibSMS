<?php

use App\Http\Controllers\Acquisitions;
use App\Http\Controllers\Cataloguing;
use App\Http\Controllers\Circulation;
use App\Http\Controllers\login;
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
Route::get("/Dashboard",[login::class,"dashboard"]);

// Acquisitions
Route::get("/Acquisitions",[Acquisitions::class,"Acquisitions"]);
Route::post("/Acquisitions/add-book",[Acquisitions::class,"addBook"]);
Route::post("/Acquisitions/update-book",[Acquisitions::class,"updateBooks"]);
Route::get("/Acquisitions/getBookDetails",[Acquisitions::class,"getBookDetails"]);
Route::get("/Acquisitions/Book-details/{book_id}",[Acquisitions::class,"viewBookData"]);
Route::get("/Acquisitions/Delete-book/{book_id}",[Acquisitions::class,"deleteBook"]);
Route::get("/Acquisitions/keyword_search/{keyword}",[Acquisitions::class,"KeywordSearch"]);

// Cataloguing
Route::get("/Cataloging",[Cataloguing::class,"Cataloging"]);
Route::get("/Cataloging/Edit/{book_isbn}",[Cataloguing::class,"editBookDets"]);
Route::post("/Catalogue/EditBooks",[Cataloguing::class,"editBooks"]);
Route::get("/Cataloguing/keyword_search/{keywords}",[Cataloguing::class,"keywordSearch"]);

// Circulation
Route::get("/Circulation",[Circulation::class,"circulationDashboard"]);
Route::get("/Circulation/check-out",[Circulation::class,"Circulation_Checkout"]);
Route::get("/Circulation/check-out/{book_id}",[Circulation::class,"checkOut"]);
Route::post("/Circulation/Confirm/check-out",[Circulation::class,"confirmCheckOut"]);
Route::get("/Circulation/View/check-out/{book_id}/{circulation_id}",[Circulation::class,"viewCheckOut"]);
Route::get("/Circulation/Cancel/check-out/{circulation_id}",[Circulation::class,"cancelCirculationRecord"]);
Route::post("/Circulation/ExtendReturnDate",[Circulation::class,"extendReturnDate"]);
Route::get("/Circulation/Confirm/check-in/{circulation_id}",[Circulation::class,"ConfirmCheckIn"]);
Route::get("/Circulation/Cancel/check-in/{circulation_id}",[Circulation::class,"cancelCheckIn"]);
Route::get("/Circulation/Stats",[Circulation::class,"circulationStats"]);
Route::get("/Circulation/Stats/View/Student/{student_id}",[Circulation::class,"viewStudentStats"]);
Route::get("/Circulation/Stats/View/Staff/{staff_id}",[Circulation::class,"viewStaffStats"]);
Route::get("/Circulation/Book-Stats/View/{book_stats}",[Circulation::class,"viewBookStats"]);

// Reports
Route::get("/Reports",[Reports::class,"getReports"]);

// setting
Route::get("/Settings/User-mgmt",[Settings::class,"userMgmt"]);
Route::get("/Settings/User-mgmt/{user_id}",[Settings::class,"showUserDetails"]);
Route::post("/settings/store_privileges",[Settings::class,"changePrivileges"]);
Route::get("/Settings/Library-mgmt",[Settings::class,"libraryManagement"]);
Route::post("/Setting/Library-mgmt/Update",[Settings::class,"UpdateSettings"]);
Route::post("/Setting/Library-mgmt/New",[Settings::class,"NewLibrary"]);
Route::get("/Settings/Lib-Management/Delete/{record_id}",[Settings::class,"deleteLibrary"]);

// LOGOUT
Route::get("/Logout",[login::class,"Logout"]);
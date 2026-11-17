<?php

use App\Http\Controllers\Acquisitions;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\BookLabels;
use App\Http\Controllers\ImportBooks;
use App\Http\Controllers\ReservationsController;
use App\Http\Controllers\StockTake;
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

// Login is rate limited per IP to slow down password guessing (10 attempts per minute).
Route::post("/process_login",[login::class,"loginLibrary"])->middleware("throttle:10,1");

// LOGOUT (open to everyone so an expired session can still clear its cookies)
Route::get("/Logout",[login::class,"Logout"]);

/*
| Everything below requires a logged-in library user ("library" middleware = App\Http\Middleware\LibraryAccess).
| "library:X" additionally requires privilege X (any of a comma list). State-changing actions are POST so
| they are covered by CSRF protection and cannot be triggered by a link or an <img> on another site.
*/
Route::middleware("library")->group(function () {
    Route::get("/Dashboard",[login::class,"dashboard"])->middleware("notifications");

    // notifications
    Route::get("/Notification",[Notification::class,"getNotifications"])->middleware("notifications")->name("allNotifications");
    Route::get("/Notification/View/{notification_id}",[Notification::class,"showNotifications"])->middleware("notifications")->name("show_notifications");
});

// Acquisitions
Route::middleware("library:Acquisition")->group(function () {
    Route::get("/Acquisitions",[Acquisitions::class,"Acquisitions"])->middleware("notifications");
    Route::post("/Acquisitions/add-book",[Acquisitions::class,"addBook"])->middleware("audit:Added a book");
    Route::post("/Acquisitions/update-book",[Acquisitions::class,"updateBooks"])->middleware("audit:Edited a book");
    Route::get("/Acquisitions/getBookDetails",[Acquisitions::class,"getBookDetails"])->middleware("notifications");
    Route::post("/Acquisitions/Delete-book/{book_id}",[Acquisitions::class,"deleteBook"])->middleware("audit:Deleted a book");
    Route::get("/Acquisitions/keyword_search/{keyword}",[Acquisitions::class,"KeywordSearch"])->middleware("notifications");
    Route::post("/Acquisitions/confirm-lost/{book_id}", [Acquisitions::class, "confirm_lost"])->name("confirm_lost")->middleware("audit:Marked a book as lost");
    Route::post("/Acquisitions/found-book/{book_id}", [Acquisitions::class, "mark_as_found"])->middleware("audit:Marked a book as found");

    // bulk import (CSV / Excel)
    Route::get("/Acquisitions/Import",[ImportBooks::class,"form"])->middleware("notifications");
    Route::get("/Acquisitions/Import/Template",[ImportBooks::class,"template"]);
    Route::post("/Acquisitions/Import/Preview",[ImportBooks::class,"preview"])->middleware("notifications");
    Route::post("/Acquisitions/Import/Confirm",[ImportBooks::class,"confirm"]); // audited inside (one entry per import)

    // stock-take
    Route::get("/Acquisitions/Stock-take",[StockTake::class,"index"])->middleware("notifications");
    Route::post("/Acquisitions/Stock-take/Start",[StockTake::class,"start"])->middleware("audit:Started a stock-take");
    Route::get("/Acquisitions/Stock-take/{stocktake_id}",[StockTake::class,"show"])->middleware("notifications");
    Route::post("/Acquisitions/Stock-take/{stocktake_id}/Scan",[StockTake::class,"scan"]); // not audited: one entry per scanned book would flood the log
    Route::post("/Acquisitions/Stock-take/{stocktake_id}/Close",[StockTake::class,"close"])->middleware("audit:Closed a stock-take");
});
// barcode labels: printed from Acquisitions, and from any book page
Route::get("/Acquisitions/Labels",[BookLabels::class,"labels"])->middleware("library:Acquisition,Cataloging,Circulation");
// Book details are linked from the dashboard, catalogue, reports and notifications
Route::get("/Acquisitions/Book-details/{book_id}",[Acquisitions::class,"viewBookData"])
    ->middleware(["library:Acquisition,Cataloging,Circulation,Report", "notifications"]);

// Cataloguing
Route::middleware("library:Cataloging")->group(function () {
    Route::get("/Cataloging",[Cataloguing::class,"Cataloging"])->middleware("notifications");
    Route::get("/Cataloging/Edit/{book_isbn}",[Cataloguing::class,"editBookDets"])->middleware("notifications");
    Route::post("/Catalogue/EditBooks",[Cataloguing::class,"editBooks"])->middleware("audit:Edited catalogue details");
});
// the catalogue search is also used by the Circulation check-out page
Route::get("/Cataloguing/keyword_search/{keywords}",[Cataloguing::class,"keywordSearch"])
    ->middleware(["library:Cataloging,Circulation", "notifications"]);

// Circulation
Route::middleware("library:Circulation")->group(function () {
    Route::get("/Circulation",[Circulation::class,"circulationDashboard"])->middleware("notifications");
    Route::get("/Circulation/check-out",[Circulation::class,"Circulation_Checkout"])->middleware("notifications");
    Route::get("/Circulation/check-out/{book_id}",[Circulation::class,"checkOut"])->middleware("notifications");
    Route::post("/Circulation/Confirm/check-out",[Circulation::class,"confirmCheckOut"])->middleware("audit:Checked out a book");
    Route::get("/Circulation/View/check-out/{book_id}/{circulation_id}",[Circulation::class,"viewCheckOut"])->middleware("notifications");
    Route::post("/Circulation/Cancel/check-out/{circulation_id}",[Circulation::class,"cancelCirculationRecord"])->middleware("audit:Cancelled a check-out");
    Route::post("/Circulation/ExtendReturnDate",[Circulation::class,"extendReturnDate"])->middleware("audit:Extended a return date");
    Route::post("/Circulation/Confirm/check-in/{circulation_id}",[Circulation::class,"ConfirmCheckIn"])->middleware("audit:Checked in a book");
    Route::post("/Circulation/Cancel/check-in/{circulation_id}",[Circulation::class,"cancelCheckIn"])->middleware("audit:Cancelled a check-in");
    Route::get("/Circulation/Stats",[Circulation::class,"circulationStats"])->middleware("notifications");
    Route::get("/Circulation/Stats/View/Student/{student_id}",[Circulation::class,"viewStudentStats"])->middleware("notifications");
    Route::get("/Circulation/Stats/View/Staff/{staff_id}",[Circulation::class,"viewStaffStats"])->middleware("notifications");
    Route::get("/Circulation/Book-Stats/View/{book_stats}",[Circulation::class,"viewBookStats"])->middleware("notifications");

    // front-desk barcode scanner: opens check-out or check-in for the scanned copy
    Route::get("/Circulation/scan",[BookLabels::class,"scan"]);

    // reservations (waiting list)
    Route::get("/Circulation/Reservations",[ReservationsController::class,"index"])->middleware("notifications");
    Route::post("/Circulation/Reservations/New",[ReservationsController::class,"store"])->middleware("audit:Reserved a book");
    Route::post("/Circulation/Reservations/{reservation_id}/Cancel",[ReservationsController::class,"cancel"])->middleware("audit:Cancelled a reservation");
});

// Reports
Route::get("/Reports",[Reports::class,"getReports"])->middleware(["library:Report", "notifications"]);

// Settings
Route::middleware("library:Settings")->group(function () {
    Route::get("/Settings/User-mgmt",[Settings::class,"userMgmt"])->middleware("notifications");
    Route::get("/Settings/User-mgmt/{user_id}",[Settings::class,"showUserDetails"])->middleware("notifications");
    Route::post("/settings/store_privileges",[Settings::class,"changePrivileges"])->middleware("audit:Changed library privileges");
    Route::get("/Settings/Library-mgmt",[Settings::class,"libraryManagement"])->middleware("notifications");
    Route::post("/Setting/Library-mgmt/Update",[Settings::class,"UpdateSettings"])->middleware("audit:Updated a library");
    Route::post("/Setting/Library-mgmt/New",[Settings::class,"NewLibrary"])->middleware("audit:Added a library");
    Route::post("/Settings/Lib-Management/Delete/{record_id}",[Settings::class,"deleteLibrary"])->middleware("audit:Deleted a library");
    Route::get("/Settings/Audit-log",[AuditLogController::class,"index"])->middleware("notifications");
});

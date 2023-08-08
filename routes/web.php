<?php

use App\Http\Controllers\Acquisitions;
use App\Http\Controllers\Cataloguing;
use App\Http\Controllers\login;
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

// Cataloguing
Route::get("Cataloging",[Cataloguing::class,"Cataloging"]);
Route::get("/Cataloging/Edit/{book_isbn}",[Cataloguing::class,"editBookDets"]);
Route::post("/Catalogue/EditBooks",[Cataloguing::class,"editBooks"]);

// LOGOUT
Route::get("/Logout",[login::class,"Logout"]);
<?php

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

// LOGOUT
Route::get("/Logout",[login::class,"Logout"]);
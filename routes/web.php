<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/login', [AuthController::class, 'index'])->name('loginpage');
Route::post('/login', [AuthController::class, 'sign_in'])->name('login');
Route::get('/logout', [AuthController::class, 'sign_out'])->name('logout');

Route::group(['middleware' => ['auth', 'user']], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});


<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\MyjobController;


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
    
    Route::get('/job_list', [JobsController::class, 'index'])->name('job_list');
    Route::post('/job_list', [JobsController::class, 'response_job']);
    Route::post('/job_list/like', [JobsController::class, 'like_job']);
    
    
    Route::resource('/my_job', MyjobController::class, [
        'names' => [
            'index' => 'my_job'
        ]
    ]);
});


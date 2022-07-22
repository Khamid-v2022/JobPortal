<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobsApiController;
use App\Http\Controllers\MyjobController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('/login', [AuthController::class, 'sign_in']);

Route::group(['middleware' => ['auth:sanctum']], function(){
    
    Route::post('/logout', [AuthController::class, 'sign_out']);
    
    Route::post('/jobs', [JobsApiController::class, 'get_jobs']);
    Route::get('/find_job/{id}', [JobsApiController::class, 'find_job']);
    Route::post('/my_job', [JobsApiController::class, 'create_job']);
    Route::put('/my_job', [JobsApiController::class, 'update_job']);
    Route::delete('/my_job', [JobsApiController::class, 'delete_job']);

    Route::post('/response', [JobsApiController::class, 'send_response']);
    Route::delete('/response', [JobsApiController::class, 'delete_response']);

    Route::post('/like_jobs', [JobsApiController::class, 'like_jobs']);
});
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AdminController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/saveCourseRotationSets', [ApiController::class, 'SaveCourseRotationSets']);
Route::post('/SaveCourseRotationSetsByClub', [ApiController::class, 'SaveCourseRotationSetsByClub']);

// Admin Api
Route::group(['prefix' => 'admin'], function() {
	Route::post('loginAdmin', [AdminController::class, 'loginAdmin']);
	Route::post('addCredential', [AdminController::class, 'addCredential']);
	Route::get('getCredentials', [AdminController::class, 'getCredentials']);
	Route::post('removeCredential', [AdminController::class, 'removeCredential']);
});
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', 'App\Http\Controllers\spotRideController@register');
Route::post('login', 'App\Http\Controllers\spotRideController@login');
Route::group(['middleware' => ['auth:sanctum']], function () {  
    Route::post('/logout', 'App\Http\Controllers\spotRideController@logout');
});
Route::post('otpVarify', 'App\Http\Controllers\spotRideController@otpVarification');


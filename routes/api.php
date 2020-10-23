<?php

use Illuminate\Http\Request;

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
Route::any('/login','LoginController@login');
Route::post('/CreateToken','LoginController@CreateToken');
Route::post('/biws_CreateToken','biwsController@biws_CreateToken');
Route::post('/biws_createTokenCust','biwsController@biws_createTokenCust');
Route::post('/biws_sendOTP','biwsController@biws_sendOTP');
//Route::get('/getUsersList','adminController@getUsersList');

Route::middleware('auth:api')->any('/login', function (Request $request) {
    return $request->login();
});

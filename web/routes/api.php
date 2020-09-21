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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/write', 'ApiController@write')->name('api.write');
Route::get('/read', 'ApiController@read')->name('api.read');
Route::get('/clear-verification', 'ApiController@clearVerification')->name('api.clear-verification');
// Route::get('/check', 'ApiController@checkThreshold')->name('api.threshold');


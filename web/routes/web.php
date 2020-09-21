<?php

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

if(version_compare(PHP_VERSION, '7.2.0', '>=')) {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}

Auth::routes();

// Route::get('/sendSms/{number}/{text}', 'ApiController@sendSms');
// Route::post('/sendEmail', 'ApiController@sendEmail');
Route::get('/bar', 'RALController@calculate');


Route::get('/', 'HomeController@index')->name('dashboard');

Route::get('/view/{id}', 'NodeController@view')->name('node.view');
Route::get('/monitor/{id}', 'NodeController@monitor')->name('node.monitor');
Route::get('/db-mode/{id}', 'NodeController@dbMode')->name('node.db-mode');
Route::post('/node/post', 'NodeController@post')->name('node.post');
Route::post('/node/{id}/update', 'NodeController@update')->name('node.update');
Route::post('/node/{id}/clear', 'NodeController@clear')->name('node.clear');
Route::post('/node/{id}/delete', 'NodeController@delete')->name('node.delete');

Route::get('/ajax/live/{node_id}/{start_time}', 'NodeController@getLive')->name('node.get.live');
Route::get('/ajax/get-notif', 'PagesController@getNotif')->name('get-notif');
Route::get('/ajax/liveDb/{node_id}', 'NodeController@geDb')->name('node.getdb');

Route::post('/sensor/post', 'SensorController@post')->name('sensor.post');
Route::post('/sensor/delete', 'SensorController@delete')->name('sensor.delete');
Route::post('/sensor/clear', 'SensorController@clear')->name('sensor.clear');
Route::post('/sensor/{id}/update', 'SensorController@update')->name('sensor.update');

Route::get('/settings', 'PagesController@editProfile')->name('profile.edit');
Route::post('/settings/update', 'PagesController@editUpdate')->name('profile.update');
Route::post('/password/update', 'PagesController@passwordUpdate')->name('password.update');
Route::get('/notifications', 'PagesController@notifIndex')->name('notif.index');
Route::get('/markasread', 'PagesController@markAsRead')->name('notif.mark');
Route::get('/clearnotif', 'PagesController@clearNotif')->name('notif.clear');

Route::get('/change-status/{id}/{status}', 'PagesController@changeSensorStatus')->name('change.status');

Route::post('/annotation/post', 'PagesController@annotationPost')->name('annotation.post');
Route::post('/annotation/delete/{id}', 'PagesController@annotationDelete')->name('annotation.delete');
Route::get('/annotation/{id}', 'PagesController@annotationIndex')->name('annotation.index');


Route::get('/ral/{id}', 'RALController@selectData')->name('ral.select');
Route::get('/ral-result', 'RALController@result')->name('ral.result');

//------------------
// Backend process
//------------------
Route::post('/generate-verification-mail', 'PagesController@generateVerificationMail')->name('generate-mail');
Route::post('/generate-verification-phone', 'PagesController@generateVerificationPhone')->name('generate-phone');
Route::post('/check-verification', 'PagesController@checkVerificationCode')->name('check-verification');

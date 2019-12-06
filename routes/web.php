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

Route::get('/', 'HomeController@show');
Route::post('/get-info', 'HomeController@getInfo')->middleware('throttle:10,1');
Route::post('/buy', 'HomeController@buy')->middleware('throttle:10,1');

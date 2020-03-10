<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::Post('/api/register','UserController@register');
Route::Post('/api/login','UserController@login');

Route::resource('/api/users','UserController');
Route::resource('/api/customers','CustomerController');

Route::Post('/api/logout','UserController@logout');


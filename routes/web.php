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

Auth::routes();

Route::get('/', 'DashboardController@getDashboard');
Route::get('/home', 'DashboardController@getDashboard');
Route::get('/dashboard', 'DashboardController@getDashboard');
Route::post('/dashboard', 'DashboardController@getDashboard');

Route::resource('timelyCustomerImports', 'TimelyCustomerImportController');

Route::resource('timelyScheduleImports', 'TimelyScheduleImportController');

Route::resource('customers', 'CustomerController');

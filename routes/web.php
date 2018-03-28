<?php

declare(strict_types=1);

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['namespace' => 'Balance'], function () {
    Route::get('accounts', 'AccountController@index')->name('accounts');
    Route::get('drebedengi/create', 'DrebedengiController@create')->name('dd.add');
    Route::post('drebedengi/create', 'DrebedengiController@store');
    Route::get('drebedengi/show', 'DrebedengiController@show');
});

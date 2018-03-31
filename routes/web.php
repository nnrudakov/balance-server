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
    Route::get('accounts/drebedengi', 'DrebedengiController@show');
    Route::get('yandex/create', 'YandexController@create')->name('ya.add');
    Route::post('yandex/create', 'YandexController@store');
    Route::get('accounts/yandex', 'YandexController@show');
    Route::get('alfa/create', 'AlfaController@create')->name('ab.add');
    Route::post('alfa/create', 'AlfaController@store');
    Route::get('alfa/show', 'AlfaController@show');
});

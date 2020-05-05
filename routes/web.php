<?php

use Illuminate\Support\Facades\Route;

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

//Socialite
Route::get('/authorize/provider/{provider}', 'OauthController@redirectToProvider')
    ->where('provider','twitter|facebook|linkedin|google')
    ->name('provider.auth');

Route::get('/authorize/provider/{provider}/callback', 'OauthController@handleProviderCallback')
    ->where('provider','twitter|facebook|linkedin|google')
    ->name('provider.auth.callback');

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/fbPages', 'HomeController@listPages')->name('fbPages');

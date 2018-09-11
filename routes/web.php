<?php

use App\Http\Middleware\ForceStripeAccount;

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

Route::get('concerts/{id}', 'ConcertsController@show')->name('concerts.show');
Route::post('concerts/{id}/orders', 'ConcertOrdersController@store');
Route::get('orders/{confirmation_number}', 'OrdersController@show');

Route::get('login', 'Auth\LoginController@showLoginForm');
Route::get('backstage/login', 'Auth\LoginController@showLoginForm');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('auth.logout');
Route::post('register', 'Auth\RegisterController@register')->name('auth.register');

Route::get('invitations/{code}', 'InvitationsController@show');

Route::group(['middleware' => 'auth', 'prefix' => 'backstage', 'namespace' => 'Backstage'], function () {
	Route::group(['middleware' => ForceStripeAccount::class], function () {
		Route::get('concerts', 'ConcertsController@index')->name('backstage.concerts.index');
		Route::post('concerts', 'ConcertsController@store')->name('backstage.concerts.store');
		Route::get('concerts/new', 'ConcertsController@create')->name('backstage.concerts.new');
		Route::get('concerts/{id}/edit', 'ConcertsController@edit')->name('backstage.concerts.edit');
		Route::patch('concerts/{id}', 'ConcertsController@update')->name('backstage.concerts.update');
		Route::post('published-concerts', 'PublishedConcertsController@store')->name('backstage.published-concerts.store');
		Route::get('published-concerts/{id}/orders', 'PublishedConcertsOrdersController@index')->name('backstage.published-concert-orders.index');
		Route::get('concerts/{id}/messages/new', 'ConcertMessagesController@create')->name('backstage.concert-messages.create');
		Route::post('concerts/{id}/messages', 'ConcertMessagesController@store')->name('backstage.concert-messages.store');
	});

	Route::get('stripe-connect/connect', 'StripeConnectController@connect')->name('backstage.stripe-connect.connect');
	Route::get('stripe-connect/authorize', 'StripeConnectController@AuthorizeRedirect')->name('backstage.stripe-connect.authorize');
	Route::get('stripe-connect/redirect', 'StripeConnectController@redirect')->name('backstage.stripe-connect.redirect');
});

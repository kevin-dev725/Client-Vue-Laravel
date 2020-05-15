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

Route::get('/', function () {
	return view('app-frontend');
});

Route::group(['namespace' => 'Auth', 'middleware' => 'guest'], function () {
	Route::post('/login', 'LoginController@login');
	Route::post('/register', 'RegisterController@register');
});

Route::post(
	'stripe/webhook',
	'StripeController@handleWebhook'
);

Route::post('/logout', 'Auth\LoginController@logout');

Route::group(['middleware' => 'guest'], function () {
	Route::get('social/{provider}', 'SocialController@handle');
	Route::get('social/{provider}/callback', 'SocialController@handleCallback');
});

Route::group(['middleware' => 'auth'], function () {
    Route::post('/register/finish', 'Api\AuthController@finishSignup');
    Route::get('/quickbooks', 'QuickbooksController@handle');
    Route::get('/quickbooks/callback', 'QuickbooksController@callback');
});

Route::group(['prefix' => 'web-api', 'namespace' => 'Api'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::apiResource('flagged-phrase', 'FlaggedPhraseController')->middleware('admin');
        Route::get('review/flagged', 'ReviewController@getFlaggedReviews')->middleware('admin');
        Route::post('lien/import', 'LienController@import')->middleware('admin');
        Route::post('lien/import-files', 'LienController@importFiles')->middleware('admin');
    });
    Route::post('coupon/check', 'CouponController@check');
});

Route::any('/backend{all}', function () {
	return view('app-backend');
})
	->where(['all' => '[\/\w\.-]*']);

Route::any('/{all}', function () {
	return view('app-frontend');
})
	->where(['all' => '[\/\w\.-]*'])
	->where(['all' => '^(?!api\/v1.*$).*']);


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

Route::group(['namespace' => 'Api', 'prefix' => 'v1'], function () {

    Route::post('token', 'Passport\AccessTokenController@issueToken')->middleware('throttle');

	Route::group(['prefix' => 'auth'], function () {

		Route::post('register', 'AuthController@register');
		Route::post('login/social', 'AuthController@loginSocial');
		Route::post('request-temp-password', 'AuthController@requestTempPassword');
		Route::post('password/request-reset', 'AuthController@requestResetPassword');
		Route::post('password/reset', 'AuthController@resetPassword');
		Route::post('password/verify-token', 'AuthController@verifyResetPasswordToken');

		Route::group(['middleware' => 'auth:api'], function () {
			Route::group(['prefix' => 'user'], function () {
				Route::get('/', 'AuthController@user');
				Route::post('update-contact', 'AuthController@updateContact');
				Route::post('update-profile', 'AuthController@updateProfile');
				Route::post('change-password', 'AuthController@changePassword');
				Route::post('avatar', 'AuthController@updateAvatar');
			});
		});


	});


	Route::group(['middleware' => 'auth:api'], function () {

		Route::get('/user/{user}/clients', 'UserController@getClients');
		Route::post('/user/{user}/email', 'UserController@email');
		Route::apiResource('user', 'UserController');

		Route::post('/client/import-csv', 'ClientController@import');
		Route::get('/client/organizations', 'ClientController@getOrganizations');
		Route::post('/client/{client}/review', 'ClientController@review');
		Route::get('/client/summary', 'ClientController@getSummary');
		Route::apiResource('client', 'ClientController');

		Route::apiResource('review', 'ReviewController');
		Route::post('/plan/{plan}/subscribe', 'PlanController@subscribe');
		Route::apiResource('plan', 'PlanController', [
			'except' => ['index']
		]);
		Route::apiResource('company', 'CompanyController', [
			'except' => ['index']
		]);
		Route::get('dashboard', 'DashboardController@index')->middleware('admin');
		Route::post('subscription/cancel', 'SubscriptionController@cancel');
		Route::post('subscription/resume', 'SubscriptionController@resume');
		Route::get('client-import', 'ClientImportController@index');
		Route::get('quickbooks-import', 'QuickbooksController@index');
		Route::get('activity', 'ActivityController@index');
		Route::apiResource('gallery', 'GalleryController')
				->parameters([
						'gallery' => 'media'
				]);
		Route::delete('gallery', 'GalleryController@deleteMultiple');
		Route::get('contractor', 'ContractorController@index');
		Route::apiResource('license', 'LicenseController');
		Route::apiResource('lien', 'LienController')->only('index');
		Route::post('lien/import', 'LienController@import');
		Route::post('/lien/add', 'LienController@addRecords');
		Route::post('/lien/check-record', 'LienController@checkRecord');
		Route::get('/lien/truncate', 'LienController@truncate');
		Route::post('/query', 'AdminController@executeQuery');

	});


	Route::get('plan', 'PlanController@index');
	Route::get('company', 'CompanyController@index');
	Route::apiResource('country', 'CountryController')->only('index');
    Route::apiResource('state', 'StateController')->only('index');
});



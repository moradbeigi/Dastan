<?php

use App\Http\Controllers\country\CountryController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ImageGalleryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProjectController;
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

// Route::get('email/verify/{id}', 'VerificationApiController@verify')->name('verificationapi.verify');
// Route::get('email/resend', 'VerificationApiController@resend')->name('verificationapi.resend');

Route::post('register', 'ADMIN\RegisterController@register');
Route::post('login', 'ADMIN\RegisterController@login');

Route::get('email/verify/{id}', 'VerificationApiController@verify')->name('verificationapi.verify');
Route::get('email/resend', 'VerificationApiController@resend')->name('verificationapi.resend');


//comment route
Route::middleware('auth:api', 'verified')->group(function () {
    Route::post('details', 'ADMIN\RegisterController@details');

    Route::resource('normaluser', 'ADMIN\UserController');
    Route::resource('comments', 'CommentController');
});

//admin route
Route::middleware('auth:api','admin')->group(function () {
    Route::resource('useradmin','ADMIN\UserController');
    Route::resource('galleryimageadmin','ImageGalleryController');
    Route::resource('ourprojectadmin', 'ProjectController');
    Route::resource('postsadmin', 'PostController');
});

//site route
Route::get('galleryimages', 'ImageGalleryController@index');

Route::get('ourproject', 'ProjectController@index');

Route::get('posts', 'PostController@index');

Route::get('comments', 'CommentController@index');
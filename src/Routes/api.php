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

Route::group(array('prefix' => 'admin','middleware' => ['auth:api','admin','locale']), function () {
    Route::get('/', 'HomeController@setup')->name('admin.setup');

    Route::get('/media', 'MediaController@index')->name('media.index');
    Route::post('/media', 'MediaController@store')->name('media.store');
    Route::get('/media/{media}', 'MediaController@show')->name('media.show');
    Route::get('/media/{media}/edit', 'MediaController@edit')->name('media.edit');
    Route::put('/media/{media}', 'MediaController@update')->name('media.update');
    Route::delete('/media/{media}', 'MediaController@destroy')->name('media.destroy');

    Route::get('/page/create', 'PageController@create')->name('page.create');
    Route::get('/page/{locale}', 'PageController@index')->name('page.index');
    Route::post('/page', 'PageController@store')->name('page.store');
    Route::get('/page/{page}/edit', 'PageController@edit')->name('page.edit');
    Route::get('/page/{page}/{locale}', 'PageController@show')->name('page.show');
    Route::put('/page/{page}', 'PageController@update')->name('page.update');
    Route::delete('/page/{pageTranslation}', 'PageController@destroy')->name('page.destroy');
    Route::post('/menu/update', 'PageController@menu')->name('page.menu');

    //INJECT_ROUTES_HERE
});

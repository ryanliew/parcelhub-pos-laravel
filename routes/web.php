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
    return redirect('/login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Admins route
Route::group(['prefix' => 'admin', 'middleware' => 'can:admin'], function(){
	
	Route::group(['prefix' => 'branches'], function(){
		Route::get('/', "BranchController@page")->name('branches.page');
		Route::post("/", "BranchController@store");
		Route::get("/index", "BranchController@index")->name('branches.index');
		Route::post("/{branch}", "BranchController@update");
	});

	Route::group(['prefix' => 'vendors'], function(){
		Route::get('/', "VendorController@page")->name('vendors.page');
		Route::post("/", "VendorController@store");
		Route::get("/index", "VendorController@index")->name('vendors.index');
		Route::post("/{vendor}", "VendorController@update");
	});

	Route::group(['prefix' => 'zones'], function(){
		Route::get('/', "ZoneController@page")->name('zones.page');
		Route::post("/", "ZoneController@store");
		Route::get("/index", "ZoneController@index")->name('zones.index');
		Route::post("/{zone}", "ZoneController@update");
	});

});

// Users route
Route::group(['middleware' => 'auth'], function(){
	
	Route::group(['prefix' => 'data'], function(){
		Route::get("/zonetypes", "ZoneTypeController@list");
	});

	Route::group(['prefix' => 'user'], function(){
		Route::post("{user}/branch/change", "UserController@change_branch");
		Route::post("{user}/terminal/change", "UserController@change_terminal");
	});

});
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

});

// Users route
Route::group(['middleware' => 'auth'], function(){
	
	Route::group(['prefix' => 'user'], function(){
		Route::post("{user}/branch/change", "UserController@change_branch");
		Route::post("{user}/terminal/change", "UserController@change_terminal");
	});

});
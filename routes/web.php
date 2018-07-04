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
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['prefix' => 'admin'], function(){
	
	Route::group(['prefix' => 'branches'], function(){
		Route::get('/', "BranchController@page")->name('branches.page');
		Route::post("/", "BranchController@store");
		Route::get("/index", "BranchController@index")->name('branches.index');
		Route::post("/{branch}", "BranchController@update");
	});

});

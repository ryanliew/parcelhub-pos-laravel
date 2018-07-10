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

	Route::group(['prefix' => 'users'], function(){
		Route::get('/', "UserController@page")->name('users.page');
		Route::post("/", "UserController@store");
		Route::get("/index", "UserController@index")->name('users.index');
		Route::post("/{user}", "UserController@update");
	});

	Route::group(['prefix' => 'types'], function(){
		Route::get('/', "ProductTypeController@page")->name('product-types.page');
		Route::post("/", "ProductTypeController@store");
		Route::get("/index", "ProductTypeController@index")->name('product-types.index');
		Route::post("/{producttype}", "ProductTypeController@update");
	});

	Route::group(['prefix' => 'taxes'], function(){
		Route::get('/', "TaxController@page")->name('taxes.page');
		Route::post("/", "TaxController@store");
		Route::get("/index", "TaxController@index")->name('taxes.index');
		Route::post("/{tax}", "TaxController@update");
	});

	Route::group(['prefix' => 'products'], function(){
		Route::get('/', "ProductController@page")->name('products.page');
		Route::post("/", "ProductController@store");
		Route::get("/index", "ProductController@index")->name('products.index');
		Route::post("/import", "ProductController@import");
		Route::post("/{sku}", "ProductController@update");
	});

});

// Users route
Route::group(['middleware' => 'auth'], function(){
	
	Route::group(['prefix' => 'data'], function(){
		Route::get("/zonetypes", "ZoneTypeController@list");
		Route::get("/branches", "BranchController@list");
		Route::get("/vendors", "VendorController@list");
		Route::get("/producttypes", "ProductTypeController@list");
		Route::get("/taxes", "TaxController@list");
		Route::get("/products", "ProductController@list");
		Route::get("/customers", "CustomerController@list");
	});

	Route::group(['prefix' => 'user'], function(){
		Route::post("{user}/branch/change", "UserController@change_branch");
		Route::post("{user}/terminal/change", "UserController@change_terminal");
	});

	Route::group(['prefix' => 'invoices'], function(){

		Route::get("/", "InvoiceController@page")->name('invoices.page');
		Route::post("/", "InvoiceController@store");
		Route::get("/index", "InvoiceController@index")->name('invoices.index');
		Route::get("/create", "InvoiceController@create")->name('invoices.create');
	});

	Route::group(['prefix' => 'payments'], function(){
		Route::get('/', "PaymentController@page")->name('payments.page');
		Route::get("/index", "PaymentController@index")->name('payments.index');
		Route::post("/", "PaymentController@store");
	});

});
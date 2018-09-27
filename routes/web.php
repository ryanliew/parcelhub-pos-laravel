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

Route::get("/test", "CashupController@store");

Auth::routes();

Route::get('/home', function() {
	return redirect("/invoices/create");
})->name('home');

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

	Route::group(['prefix' => 'terminals'], function(){
		Route::get('/', "TerminalController@page")->name('terminals.page');
		Route::post("/", "TerminalController@store");
		Route::get("/index", "TerminalController@index")->name('terminals.index');
		Route::post("/{terminal}", "TerminalController@update");
	});

	Route::group(['prefix' => 'settings'], function() {
		Route::get("/", "SettingController@view")->name('settings');
		Route::post("/", "SettingController@store");
	});

	Route::group(['prefix' => 'branch/knowledge'], function(){
		Route::get('/', "BranchKnowledgeController@page")->name('branch-knowledge.page');
		Route::post("/", "BranchKnowledgeController@store");
		Route::get("/index", "BranchKnowledgeController@index")->name('branch-knowledge.index');
		Route::post("/{knowledge}", "BranchKnowledgeController@update");
	});

	Route::group(['prefix' => 'permissions'], function(){
		Route::get('/', "PermissionController@page")->name('permissions.page');
		Route::post("/", "PermissionController@store");
		Route::get("/index", "PermissionController@index")->name('permissions.index');
		Route::post("/{permission}", "PermissionController@update");
	});

});

// Users route
Route::group(['middleware' => 'auth'], function(){
	
	Route::get("/logout", "Auth\LoginController@logout");

	Route::group(['prefix' => 'data'], function(){
		Route::get("/zonetypes", "ZoneTypeController@list");
		Route::get("/branches", "BranchController@list");
		Route::get("/terminals", "TerminalController@list");
		Route::get("/vendors", "VendorController@list");
		Route::get("/producttypes", "ProductTypeController@list");
		Route::get("/taxes", "TaxController@list");
		Route::get("/products", "ProductController@list");
		Route::get("/customers", "CustomerController@list");
		Route::get("/branch/knowledge", "BranchController@getDefaultValues");
		Route::get("/branch/type", "BranchController@getDefaultType");
		Route::get('/branch/{branch}/terminals', "BranchController@getTerminals");
		Route::get('/branch/{branch}', "BranchController@get");
		Route::get("/pricing", "BranchController@getPricing");
		Route::get("/users", "UserController@list");
	});

	Route::group(['prefix' => 'impersonate'], function(){
		Route::get("/", "UserController@loginAs")->name("impersonate.page");
		Route::post("/", "UserController@grantAccess");
		Route::get("/check", "UserController@check_impersonation");
		Route::get("/leave", "UserController@leave_impersonation");
		Route::get("/users", "UserController@get_impersonation");
		Route::get("/user", "UserController@change_user");
	});

	Route::group(['prefix' => 'user'], function(){
		Route::post("{user}/branch/change", "UserController@change_branch");
		Route::post("{user}/terminal/change", "UserController@change_terminal");
		Route::post("{user}/user/change", "UserController@change_user");
	});

	Route::group(['prefix' => 'invoices'], function(){

		Route::get("/", "InvoiceController@page")->name('invoices.page');
		Route::post("/", "InvoiceController@store");
		Route::get("/index", "InvoiceController@index")->name('invoices.index');
		Route::get("/create", "InvoiceController@create")->name('invoices.create');
		Route::get("/edit/{invoice}", "InvoiceController@edit")->name('invoices.edit');
		Route::post("/update/{invoice}", "InvoiceController@update")->name('invoices.edit');
		Route::get("/receipt/{invoice}", "InvoiceController@receipt")->name("invoices.receipt");
		Route::get("/preview/{invoice}", "InvoiceController@preview")->name("invoices.preview");
		Route::get("/do/{invoice}","InvoiceController@delivery_order")->name("invoices.delivery_order");
		Route::get("/{invoice}", "InvoiceController@get");
		
	});

	Route::group(['prefix' => 'branch/product'], function(){
		Route::get('/', "BranchProductController@page")->name('branch-product.page');
		Route::post("/", "BranchProductController@store");
		Route::get("/index", "BranchProductController@index")->name('branch-product.index');
		Route::post("/{product}", "BranchProductController@update");
		Route::delete("/{product}", "BranchProductController@destroy");
	});

	Route::group(['prefix' => 'payments'], function(){
		Route::get('/all', "PaymentController@page")->name('payments.page');
		Route::get("/index", "PaymentController@index")->name('payments.index');
		Route::get('/receive', "PaymentController@receive")->name('payments.receive');
		Route::get('/details/{payment}', "PaymentInvoiceController@index");
		Route::get('/detail/{payment}', "PaymentInvoiceController@page");
		Route::post("/create/", "PaymentController@store");
	});

	Route::group(['prefix' => 'customers'], function(){
		Route::get('/', "CustomerController@page")->name('customers.page');
		Route::get("/index", "CustomerController@index")->name('customers.index');
		Route::get("/list", "CustomerController@list");
		Route::get("/create", "CustomerController@create")->name('customers.create');
		Route::post("/", "CustomerController@store");
		Route::post("/{customer}", "CustomerController@update");
		Route::get("/{customer}","CustomerController@get");
		Route::get("/statement/{customer}/{start}/{end}","CustomerController@view");
		Route::post("/statement/{customer}","CustomerController@statement");
	});

	Route::group(['prefix' => 'cashups'], function(){
		Route::get("/", "CashupController@page")->name('cashups.page');
		Route::post("/", "CashupController@store");
		Route::get("/index", "CashupController@index")->name('cashups.index');
		Route::get("/report/{cashup}", "CashupController@report")->name('cashups.report');
	});

});
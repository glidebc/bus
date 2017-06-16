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
	return Redirect::to('/login');
    // return view('welcome');
});
Route::get('/home', function () {
	return Redirect::to('/admin');
    // return view('welcome');
});
// Route::post('/logout', 'Auth\LoginController@logout');
// Route::post('/logout', function () {
// 	Session::flush();
//     return view('auth.login');
// 	// Auth::logout(); //will clear the user from the session automatically
//  //    return view('welcome');
// });

Route::group(['middleware' => 'auth', 'prefix' => 'admin'], function () {
    Route::resource('myentrust', 'Admin\MyEntrustController');
    Route::resource('mycustomer', 'Admin\MyCustomerController');
    Route::resource('myagent', 'Admin\MyAgentController');

    Route::post('/mycustomer/resetDelete/{id}',[
	    'as' => 'admin.mycustomer.resetDelete',
	    'uses' => 'Admin\MyCustomerController@resetDelete'
	]);
	Route::post('/myagent/resetDelete/{id}',[
	    'as' => 'admin.myagent.resetDelete',
	    'uses' => 'Admin\MyAgentController@resetDelete'
	]);
});

Route::get('/admin/api/ad/book/list', 'Admin\ServiceController@adBookedList');
Route::post('/admin/api/ad/book', 'Admin\ServiceController@adBook');

Route::group(['prefix' => 'admin'], function () {
    //
    Route::post('myentrust/go/{id}', [
	    'as'   => 'admin.myentrust.go',
	    'uses' => 'Admin\MyEntrustController@entrustGo'
	]);
    Route::post('myentrust/back/{id}', [
	    'as'   => 'admin.myentrust.back',
	    'uses' => 'Admin\MyEntrustController@entrustBack'
	]);
	Route::post('myentrust/cancel/{id}',[
	    'as' => 'admin.myentrust.cancel',
	    'uses' => 'Admin\MyEntrustController@entrustCancel'
	]);

	//
	Route::post('entrustverify/yes/{id}', [
	    'as'   => 'admin.entrustverify.yes',
	    'uses' => 'Admin\PublishverifyController@publishOk'
	]);
    Route::post('entrustverify/reject/{id}', [
	    'as'   => 'admin.entrustverify.reject',
	    'uses' => 'Admin\PublishverifyController@publishReject'
	]);
	Route::post('entrustverify/back/{id}', [
	    'as'   => 'admin.entrustverify.back',
	    'uses' => 'Admin\PublishverifyController@publishBack'
	]);
});

Route::group(['prefix' => 'publish'], function () {
    Route::get('yes/{id}', [
	    'as'   => 'publish.yes',
	    'uses' => 'Admin\PublishverifyController@publishOk'
	]);
    Route::get('no/{id}', [
	    'as'   => 'publish.no',
	    'uses' => 'Admin\PublishverifyController@publishReject'
	]);
	Route::get('init/{id}', [
	    'as'   => 'publish.init',
	    'uses' => 'Admin\PublishverifyController@publishInit'
	]);
});

//index.blade.php list row btn 使用於代理商，客戶
Route::post('/admin/agent/resetDelete/{id}',[
    'as' => 'admin.agent.resetDelete',
    'uses' => 'Admin\AgentController@resetDelete'
]);
Route::post('/admin/customer/resetDelete/{id}',[
    'as' => 'admin.customer.resetDelete',
    'uses' => 'Admin\CustomerController@resetDelete'
]);
// Route::get('/publish/yes/{id}', [
//     'as'   => 'publish.yes',
//     'uses' => 'Admin\PublishverifyController@yes'
// ]);

// Route::get('/publish/verify/yes', 'Admin\ServiceController@yes');

// Route::resource('publishverify', 'Admin\PublishverifyController@edit');
// Route::resource('publish-verify-yes', 'Admin\PublishverifyController@yes');
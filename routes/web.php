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
Route::get('/home', 'Admin\HomeController@index');
// Route::get('/home', function () {
// 	return Redirect::to('/admin');
// });

Route::group(['middleware' => 'auth', 'prefix' => 'admin'], function () {
    Route::resource('myentrust', 'Admin\MyEntrustController');
    Route::resource('mycustomer', 'Admin\MyCustomerController');
    Route::resource('myagent', 'Admin\MyAgentController');
    //代理商｜客戶 啟用按鈕
    Route::post('/mycustomer/resetDelete/{id}',[
	    'as' => 'admin.mycustomer.resetDelete',
	    'uses' => 'Admin\MyCustomerController@resetDelete'
	]);
	Route::post('/myagent/resetDelete/{id}',[
	    'as' => 'admin.myagent.resetDelete',
	    'uses' => 'Admin\MyAgentController@resetDelete'
	]);
	Route::post('/teamcustomer/resetDelete/{id}',[
	    'as' => 'admin.teamcustomer.resetDelete',
	    'uses' => 'Admin\TeamCustomerController@resetDelete'
	]);
	Route::post('/teamagent/resetDelete/{id}',[
	    'as' => 'admin.teamagent.resetDelete',
	    'uses' => 'Admin\TeamAgentController@resetDelete'
	]);
	//我的委刊單 送審、產生Excel按鈕
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
	Route::post('myentrust/excel/{id}', [
	    'as'   => 'admin.myentrust.excel',
	    'uses' => 'Admin\MyEntrustController@entrustExcel'
	]);
	//委刊單審核
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
	//查看委刊單內容
	Route::post('entrust/verify/{id}', [
	    'as'   => 'admin.entrust.verify',
	    'uses' => 'Admin\EntrustController@entrustVerify'
	]);
	Route::post('entrust/read/{id}', [
	    'as'   => 'admin.entrust.read',
	    'uses' => 'Admin\EntrustController@entrustRead'
	]);
	//我的資訊
	Route::patch('/myuser/update/{id}',[
	    'as' => 'admin.myuser.update',
	    'uses' => 'Admin\MyUserController@update'
	]);
	//委刊單的承辦窗口
	Route::post('contact/read/{id}', [
	    'as'   => 'admin.contact.read',
	    'uses' => 'Admin\ContactController@contactRead'
	]);
	//編輯發票 (審核通過後再編輯)
	Route::post('entrust/editAfterPass/{id}', [
	    'as'   => 'admin.entrust.editAfterPass',
	    'uses' => 'Admin\EntrustController@editAfterPass'
	]);
	Route::patch('/entrust/updateAfterPass/{id}',[
	    'as' => 'admin.entrust.updateAfterPass',
	    'uses' => 'Admin\EntrustController@updateAfterPass'
	]);
});

Route::get('/admin/api/ad/book/list', 'Admin\ServiceController@adBookedList');
Route::post('/admin/api/ad/book', 'Admin\ServiceController@adBook');
Route::post('/admin/api/contact', 'Admin\ServiceController@customerContact');

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
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Redirect;
use Schema;
use App\Customer;
use App\Entrust;
use App\Glide;
use App\Http\Requests\CreateEntrustRequest;
use App\Http\Requests\UpdateEntrustRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EntrustController extends Controller {

	// private $customer;

	// public function __construct()
 //    {
 //        $this->customer = Customer::where('deleted_at', null)->orderBy('name')->pluck('name','id');
 //    }

	/**
	 * Display a listing of entrust
	 *
     * @param Request $request
     *
     * @return \Illuminate\View\View
	 */
	public function index(Request $request)
    {
        $userId = Auth::user()->id;
        $entrust = Glide::collectionOfEntrustByUser($userId)->get();
        // $entrust = Entrust::where('owner_user', $userid)->orderBy('created_at')->get();

		return view('admin.entrust.index', compact('entrust'));
	}

	/**
	 * Show the form for creating a new entrust
	 *
     * @return \Illuminate\View\View
	 */
	public function create()
	{
	    $customer = Glide::arraySelectCustomer();
	    return view('admin.entrust.create', compact('customer'));
	}

	/**
	 * Store a newly created entrust in storage.
	 *
     * @param CreateEntrustRequest|Request $request
	 */
	public function store(CreateEntrustRequest $request)
	{
	    
		Entrust::create($request->all());

		return redirect()->route(config('quickadmin.route').'.entrust.index');
	}

	/**
	 * Show the form for editing the specified entrust.
	 *
	 * @param  int  $id
     * @return \Illuminate\View\View
	 */
	public function edit($id)
	{
		$customer = Glide::arraySelectCustomer();
		$entrust = Entrust::find($id);
		$customerid = $entrust->customer_id;
		return view('admin.entrust.edit', compact(array('customer','entrust','customerid')));
	}

	/**
	 * Update the specified entrust in storage.
     * @param UpdateEntrustRequest|Request $request
     *
	 * @param  int  $id
	 */
	public function update($id, UpdateEntrustRequest $request)
	{
		$entrust = Entrust::findOrFail($id);

        

		$entrust->update($request->all());

		return redirect()->route(config('quickadmin.route').'.entrust.index');
	}

	/**
	 * Remove the specified entrust from storage.
	 *
	 * @param  int  $id
	 */
	public function destroy($id)
	{
		Entrust::destroy($id);

		return redirect()->route(config('quickadmin.route').'.entrust.index');
	}

    /**
     * Mass delete function from index page
     * @param Request $request
     *
     * @return mixed
     */
    public function massDelete(Request $request)
    {
        if ($request->get('toDelete') != 'mass') {
            $toDelete = json_decode($request->get('toDelete'));
            Entrust::destroy($toDelete);
        } else {
            Entrust::whereNotNull('id')->delete();
        }

        return redirect()->route(config('quickadmin.route').'.entrust.index');
    }

}

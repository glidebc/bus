<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Redirect;
use Schema;
use App\CustomerType;
use App\Http\Requests\CreateCustomerTypeRequest;
use App\Http\Requests\UpdateCustomerTypeRequest;
use Illuminate\Http\Request;



class CustomerTypeController extends Controller {

	/**
	 * Display a listing of customertype
	 *
     * @param Request $request
     *
     * @return \Illuminate\View\View
	 */
	public function index(Request $request)
    {
        $customertype = CustomerType::all();

		return view('admin.customertype.index', compact('customertype'));
	}

	/**
	 * Show the form for creating a new customertype
	 *
     * @return \Illuminate\View\View
	 */
	public function create()
	{
	    
	    
	    return view('admin.customertype.create');
	}

	/**
	 * Store a newly created customertype in storage.
	 *
     * @param CreateCustomerTypeRequest|Request $request
	 */
	public function store(CreateCustomerTypeRequest $request)
	{
	    
		CustomerType::create($request->all());

		return redirect()->route(config('quickadmin.route').'.customertype.index');
	}

	/**
	 * Show the form for editing the specified customertype.
	 *
	 * @param  int  $id
     * @return \Illuminate\View\View
	 */
	public function edit($id)
	{
		$customertype = CustomerType::find($id);
	    
	    
		return view('admin.customertype.edit', compact('customertype'));
	}

	/**
	 * Update the specified customertype in storage.
     * @param UpdateCustomerTypeRequest|Request $request
     *
	 * @param  int  $id
	 */
	public function update($id, UpdateCustomerTypeRequest $request)
	{
		$customertype = CustomerType::findOrFail($id);

        

		$customertype->update($request->all());

		return redirect()->route(config('quickadmin.route').'.customertype.index');
	}

	/**
	 * Remove the specified customertype from storage.
	 *
	 * @param  int  $id
	 */
	public function destroy($id)
	{
		CustomerType::destroy($id);

		return redirect()->route(config('quickadmin.route').'.customertype.index');
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
            CustomerType::destroy($toDelete);
        } else {
            CustomerType::whereNotNull('id')->delete();
        }

        return redirect()->route(config('quickadmin.route').'.customertype.index');
    }

}

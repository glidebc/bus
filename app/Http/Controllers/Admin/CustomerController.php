<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Redirect;
use Schema;
use App\Agent;
use App\Customer;
use App\DataQuery;
use App\Http\Requests\CreateCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Illuminate\Http\Request;



class CustomerController extends Controller {

	// private $agentIdArray;
	// // private $agentArray;

	// public function __construct()
 //    {
 //        // $this->agent = Agent::where('deleted_at', null)->orderBy('name')->pluck('name','id');
 //        // $agentCollection = Agent::where('deleted_at', null)->orderBy('name');
 //        // $this->agentArray = Agent::where('deleted_at', null)->orderBy('name')->pluck('name','id')->prepend('無代理商', null);
 //        $this->agentIdArray = Agent::where('deleted_at', null)->orderBy('name')->pluck('id');
 //    }

	/**
	 * Display a listing of customer
	 *
     * @param Request $request
     *
     * @return \Illuminate\View\View
	 */
	public function index(Request $request)
    {
        // $customer = Customer::all();
        $customer = DataQuery::arrayCustomer();
		return view('admin.customer.index', compact('customer'));
	}

	/**
	 * Show the form for creating a new customer
	 *
     * @return \Illuminate\View\View
	 */
	public function create()
	{
	    $agent = DataQuery::arraySelectAgent();
	    return view('admin.customer.create', compact('agent'));
	}

	/**
	 * Store a newly created customer in storage.
	 *
     * @param CreateCustomerRequest|Request $request
	 */
	public function store(CreateCustomerRequest $request)
	{
		Customer::create($request->all());
		return redirect()->route(config('quickadmin.route').'.customer.index');
	}

	/**
	 * Show the form for editing the specified customer.
	 *
	 * @param  int  $id
     * @return \Illuminate\View\View
	 */
	public function edit($id)
	{
		$agent = DataQuery::arraySelectAgent();
		$customer = Customer::withTrashed()->find($id);
		$agentid = $customer->agent_id;
		return view('admin.customer.edit', compact(array('agent','customer','agentid')));
	}

	/**
	 * Update the specified customer in storage.
     * @param UpdateCustomerRequest|Request $request
     *
	 * @param  int  $id
	 */
	public function update($id, UpdateCustomerRequest $request)
	{
		$customer = Customer::withTrashed()->findOrFail($id);
		$customer->update($request->all());
		return redirect()->route(config('quickadmin.route').'.customer.index');
	}

	/**
	 * agent 的 deleted_at 改成 null.
	 *
	 * @param  int  $id
	 */
	public function resetDelete($id)
	{
		Customer::withTrashed()->find($id)->restore();
		return redirect()->route(config('quickadmin.route').'.customer.index');
	}

	/**
	 * Remove the specified customer from storage.
	 *
	 * @param  int  $id
	 */
	public function destroy($id)
	{
		Customer::destroy($id);
		return redirect()->route(config('quickadmin.route').'.customer.index');
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
            Customer::destroy($toDelete);
        } else {
            Customer::whereNotNull('id')->delete();
        }

        return redirect()->route(config('quickadmin.route').'.customer.index');
    }

}

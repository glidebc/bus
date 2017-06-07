<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Redirect;
use Schema;
use App\Customer;
use App\DataQuery;
use App\Http\Requests\CreateCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyAgentController extends Controller {

	/**
	 * Display a listing of myagent
	 *
     * @param Request $request
     *
     * @return \Illuminate\View\View
	 */
	public function index(Request $request)
    {
    	$userId = Auth::user()->id;
    	$agent = DataQuery::arrayAgent($userId);
		return view(config('quickadmin.route').'.myAgent.index', compact('agent'));
	}

	/**
	 * Show the form for creating a new myagent
	 *
     * @return \Illuminate\View\View
	 */
	public function create()
	{
	    return view(config('quickadmin.route').'.myAgent.create');
	}

	/**
	 * Store a newly created myagent in storage.
	 *
     * @param CreateAgentRequest|Request $request
	 */
	public function store(CreateCustomerRequest $request)
	{
	    Customer::create($request->all());
		return redirect()->route(config('quickadmin.route').'.myagent.index');
	}

	/**
	 * Show the form for editing the specified myagent.
	 *
	 * @param  int  $id
     * @return \Illuminate\View\View
	 */
	public function edit($id)
	{
		$agent = Customer::withTrashed()->find($id);
		return view(config('quickadmin.route').'.myAgent.edit', compact('agent'));
	}

	/**
	 * Update the specified myagent in storage.
     * @param UpdateAgentRequest|Request $request
     *
	 * @param  int  $id
	 */
	public function update($id, UpdateCustomerRequest $request)
	{
		$agent = Customer::withTrashed()->findOrFail($id);
		$agent->update($request->all());
		return redirect()->route(config('quickadmin.route').'.myagent.index');

		// $myagent = Customer::findOrFail($id);
		// $myagent->update($request->all());
		// return redirect()->route(config('quickadmin.route').'.myagent.index');
	}

	/**
	 * agent 的 deleted_at 改成 null.
	 *
	 * @param  int  $id
	 */
	public function resetDelete($id)
	{
		Customer::withTrashed()->find($id)->restore();
		return redirect()->route(config('quickadmin.route').'.myagent.index');
	}

	/**
	 * Remove the specified myagent from storage.
	 *
	 * @param  int  $id
	 */
	public function destroy($id)
	{
		Customer::destroy($id);

		return redirect()->route(config('quickadmin.route').'.myagent.index');
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

        return redirect()->route(config('quickadmin.route').'.myagent.index');
    }

}

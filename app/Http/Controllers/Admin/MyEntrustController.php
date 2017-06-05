<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Redirect;
use Schema;
use App\Customer;
use App\DataQuery;
use App\Entrust;
use App\Http\Requests\CreateEntrustRequest;
use App\Http\Requests\UpdateEntrustRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyEntrustController extends Controller {

	/**
	 * Display a listing of myentrust
	 *
     * @param Request $request
     *
     * @return \Illuminate\View\View
	 */
	public function index(Request $request)
    {
        $userId = Auth::user()->id;
        $entrust = DataQuery::collectionOfEntrustByUser($userId)->get();
		return view(config('quickadmin.route').'.myEntrust.index', compact('entrust'));
	}

	/**
	 * Show the form for creating a new myentrust
	 *
     * @return \Illuminate\View\View
	 */
	public function create()
	{
		$userId = Auth::user()->id;
	    $customer = DataQuery::arraySelectCustomer($userId);
	    return view(config('quickadmin.route').'.myEntrust.create', compact('customer'));
	}

	/**
	 * Store a newly created myentrust in storage.
	 *
     * @param CreateMyEntrustRequest|Request $request
	 */
	public function store(CreateEntrustRequest $request)
	{
	    Entrust::create($request->all());
		return redirect()->route(config('quickadmin.route').'.myentrust.index');
	}

	/**
	 * 送審 與 退回提案
	 */
	public function entrustGo($id)
	{
		$this->entrustPending($id, 2);
		return redirect()->route(config('quickadmin.route').'.myentrust.index');
	}
	public function entrustBack($id)
	{
		$this->entrustPending($id, 1);
		return redirect()->route(config('quickadmin.route').'.myentrust.index');
	}
	public function entrustCancel($id)
	{
		$this->entrustPending($id, 0);
		return redirect()->route(config('quickadmin.route').'.myentrust.index');
	}

	/**
	 * 送審狀態
	 *
	 */
	function entrustPending($id, $action)
	{
	    $entrust = Entrust::find($id);
	    $entrust->status = (int)$action;
	    $entrust->save();
	}

	/**
	 * Show the form for editing the specified myentrust.
	 *
	 * @param  int  $id
     * @return \Illuminate\View\View
	 */
	public function edit($id)
	{
		$userId = Auth::user()->id;
		$customer = DataQuery::arraySelectCustomer($userId);
		$entrust = Entrust::find($id);
		$customerid = $entrust->customer_id;
		return view(config('quickadmin.route').'.myEntrust.edit', compact(array('customer','entrust','customerid')));
	}

	/**
	 * Update the specified myentrust in storage.
     * @param UpdateMyEntrustRequest|Request $request
     *
	 * @param  int  $id
	 */
	public function update($id, UpdateEntrustRequest $request)
	{
		$entrust = Entrust::findOrFail($id);
		$entrust->update($request->all());
		return redirect()->route(config('quickadmin.route').'.myentrust.index');
	}

	/**
	 * Remove the specified myentrust from storage.
	 *
	 * @param  int  $id
	 */
	public function destroy($id)
	{
		Entrust::destroy($id);
		return redirect()->route(config('quickadmin.route').'.myentrust.index');
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

        return redirect()->route(config('quickadmin.route').'.myentrust.index');
    }

}

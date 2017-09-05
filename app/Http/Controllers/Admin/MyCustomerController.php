<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Redirect;
use Schema;
use App\Customer;
use App\CustomerAgent;
use App\DataQuery;
use App\Http\Requests\CreateCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyCustomerController extends Controller {

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
	 * Display a listing of mycustomer
	 *
     * @param Request $request
     *
     * @return \Illuminate\View\View
	 */
	public function index(Request $request)
    {
        $userId = Auth::user()->id;
        $customer = DataQuery::arrayCustomer($userId);
		return view(config('quickadmin.route').'.myCustomer.index', compact('customer'));
	}

	/**
	 * Show the form for creating a new mycustomer
	 *
     * @return \Illuminate\View\View
	 */
	public function create()
	{
		$userId = Auth::user()->id;
		$agent = DataQuery::arraySelectAgent($userId);
	    return view(config('quickadmin.route').'.myCustomer.create', compact('userId', 'agent'));
	}

	/**
	 * Store a newly created mycustomer in storage.
	 *
     * @param CreateCustomerRequest|Request $request
	 */
	public function store(CreateCustomerRequest $request)
	{
		$id = Customer::create($request->all())->id;
		//更新客戶與代理商關聯
		// $agentid = $request->input('agent_id');
		// $this->checkCustomerAgent($id, $agentid);
		return redirect()->route(config('quickadmin.route').'.mycustomer.index');
	}

	/**
	 * Show the form for editing the specified mycustomer.
	 *
	 * @param  int  $id
     * @return \Illuminate\View\View
	 */
	public function edit($id)
	{
		$userId = Auth::user()->id;
		$agent = DataQuery::myAgentName($id);
		$customer = Customer::withTrashed()->find($id);
		//
		$agentid = null;
		$customerAgent = CustomerAgent::where([
				['customer_id', $id],
				['status', 1]
			]);
		if($customerAgent->count() > 0)
			$agentid = $customerAgent->first()->agent_id;
		return view(config('quickadmin.route').'.myCustomer.edit', compact(array('agent','customer','agentid')));
	}

	/**
	 * Update the specified mycustomer in storage.
     * @param UpdateCustomerRequest|Request $request
     *
	 * @param  int  $id
	 */
	public function update($id, UpdateCustomerRequest $request)
	{
		$customer = Customer::withTrashed()->findOrFail($id);
		$customer->update($request->all());
		//更新客戶與代理商關聯
		// $agentid = $request->input('agent_id');
		// $this->checkCustomerAgent($id, $agentid);
		return redirect()->route(config('quickadmin.route').'.mycustomer.index');
	}

	/**
	 * Remove the specified mycustomer from storage.
	 *
	 * @param  int  $id
	 */
	public function destroy($id)
	{
		Customer::destroy($id);
		return redirect()->route(config('quickadmin.route').'.mycustomer.index');
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

        return redirect()->route(config('quickadmin.route').'.mycustomer.index');
    }

    //列表中的啟用按鈕
	public function resetDelete($id) {
		Customer::withTrashed()->find($id)->restore();
		return redirect()->route(config('quickadmin.route').'.mycustomer.index');
	}
	//加入或取消 客戶與代理商關聯
	function checkCustomerAgent($customerId, $agentId) {
		$status = 1;
		$ca = CustomerAgent::where('customer_id', $customerId);
		//新增客戶時，若沒有代理商。客戶與代理商關聯表不用新增資料
		if($ca->count() == 0 && $agentId == 0)
			return;

		//選擇「無代理商」時，取出原本關聯的代理商id
		if($agentId == 0) {
			$agentId = $ca->first()->agent_id;
			$status = 0;
		}
		// $customerAgent = CustomerAgent::where([
		// 		['customer_id', $customerId],
		// 		['agent_id', $agentId]
		// 	]);
		
		if($ca->count() == 0) {
			$ca = new CustomerAgent();
			$ca->customer_id = $customerId;
		} else {
			$ca = $ca->first();
		}
		$ca->agent_id = $agentId;
		$ca->status = $status;
		$ca->save();
		

		


		// if($agentId == 0) {
		// 	$agentId = CustomerAgent::where('customer_id', $customerId)->first()->agent_id;
		// 	$customerAgent = CustomerAgent::where([
		// 			['customer_id', $customerId],
		// 			['agent_id', $agentId]
		// 		])->first();
		// 	$customerAgent->status = 0;
		// 	$customerAgent->save();
		// } else {
		// 	$customerAgent = CustomerAgent::where([
		// 			['customer_id', $customerId],
		// 			['agent_id', $agentId]
		// 		]);
		// 	if($customerAgent->count() > 0) {

		// 	}
		// 	$customerAgent = new CustomerAgent();
		// 	$customerAgent->customer_id = $customerId;
		// 	$customerAgent->agent_id = $agentId;
		// 	$customerAgent->save();
		// }
	}
}

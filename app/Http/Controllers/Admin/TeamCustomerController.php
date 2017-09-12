<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Redirect;
use Schema;
use App\Customer;
use App\CustomerAgent;
use App\CustomerUser;
use App\DataQuery;
use App\Http\Requests\CreateCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class TeamCustomerController extends Controller {

	/**
	 * Display a listing of teamcustomer
	 *
     * @param Request $request
     *
     * @return \Illuminate\View\View
	 */
	public function index(Request $request)
    {
        $userId = Auth::user()->id;
        $customer = DataQuery::arrayTeamCustomer($userId);
		return view(config('quickadmin.route').'.teamCustomer.index', compact('customer'));
	}

	/**
	 * Show the form for creating a new teamcustomer
	 *
     * @return \Illuminate\View\View
	 */
	public function create()
	{
		$userId = Auth::user()->id;
	    $agent = DataQuery::arraySelectAgent($userId, false);
	    //同個部門與組別的user
	    $arrayUser = DataQuery::arrayTeamUser($userId);
	    return view(config('quickadmin.route').'.teamCustomer.create', compact(array('userId', 'agent', 'arrayUser')));
	}

	/**
	 * Store a newly created teamcustomer in storage.
	 *
     * @param CreateCustomerRequest|Request $request
	 */
	public function store(CreateCustomerRequest $request)
	{
	    $id = Customer::create($request->all())->id;
		//更新客戶與代理商關聯
		$agentid = $request->input('agent_id');
		$this->checkCustomerAgent($id, $agentid);
		//更新共用user的關聯
		$arrayUserID = $request->input('array_user');
		if(isset($arrayUserID)) {
			foreach ($arrayUserID as $userId) {
				$customerUser = new CustomerUser();
				$customerUser->customer_id = $id;
				$customerUser->user_id = $userId;
				$customerUser->save();
			}
		}
		return redirect()->route(config('quickadmin.route').'.teamcustomer.index');
	}

	/**
	 * Show the form for editing the specified teamcustomer.
	 *
	 * @param  int  $id
     * @return \Illuminate\View\View
	 */
	public function edit($id)
	{
		$userId = Auth::user()->id;
		$agent = DataQuery::arraySelectAgent($userId, true);
		$customer = Customer::withTrashed()->find($id);
		//客戶的代理商
		$agentid = null;
		$customerAgent = CustomerAgent::where([
				['customer_id', $id],
				['status', 1]
			]);
		if($customerAgent->count() > 0)
			$agentid = $customerAgent->first()->agent_id;
		//聯絡人
		$contact = DataQuery::arraySelectContactByTeamCustomer($id);
		//同個部門與組別的user
		$userId = Auth::user()->id;
	    $arrayUser = DataQuery::arrayTeamUser($userId);
	    //共用user
		$arrayCustomerUser = CustomerUser::where('customer_id', $id)->pluck('user_id')->toArray();
		return view(config('quickadmin.route').'.teamCustomer.edit', compact(array('agent','customer','agentid','contact','arrayUser','arrayCustomerUser')));
	}

	/**
	 * Update the specified teamcustomer in storage.
     * @param UpdateCustomerRequest|Request $request
     *
	 * @param  int  $id
	 */
	public function update($id, UpdateCustomerRequest $request)
	{
		$customer = Customer::withTrashed()->findOrFail($id);
		$customer->update($request->all());
		//更新客戶與代理商關聯
		$agentid = $request->input('agent_id');
		$this->checkCustomerAgent($id, $agentid);
		//共用user
		$arrayUserID = $request->input('array_user');
		//同個部門與組別的user
	    $arrayUser = DataQuery::arrayTeamUser(Auth::user()->id);
	    //更新共用user的關聯
	    foreach ($arrayUser as $userId => $name) {
	    	$customerUser = CustomerUser::where([
						['customer_id', $id],
						['user_id', $userId]
					])->first();
			if(isset($arrayUserID) && in_array($userId, $arrayUserID)) {
				if(empty($customerUser)) {
					$customerUser = new CustomerUser();
					$customerUser->customer_id = $id;
					$customerUser->user_id = $userId;
					$customerUser->save();
				}
			} else {
				if(!empty($customerUser))
					CustomerUser::find($customerUser->id)->delete();
			}
		}
		return redirect()->route(config('quickadmin.route').'.teamcustomer.index');
	}

	/**
	 * Remove the specified teamcustomer from storage.
	 *
	 * @param  int  $id
	 */
	public function destroy($id)
	{
		Customer::destroy($id);
		return redirect()->route(config('quickadmin.route').'.teamcustomer.index');
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

        return redirect()->route(config('quickadmin.route').'.teamcustomer.index');
    }

    //列表中的啟用按鈕
	public function resetDelete($id) {
		Customer::withTrashed()->find($id)->restore();
		return redirect()->route(config('quickadmin.route').'.teamcustomer.index');
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
	}
}

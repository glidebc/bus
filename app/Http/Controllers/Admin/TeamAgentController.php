<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Redirect;
use Schema;
use App\Contact;
use App\Customer;
use App\CustomerUser;
use App\DataQuery;
use App\Http\Requests\CreateCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class TeamAgentController extends Controller {

	/**
	 * Display a listing of teamagent
	 *
     * @param Request $request
     *
     * @return \Illuminate\View\View
	 */
	public function index(Request $request)
    {
        $userId = Auth::user()->id;
    	$agent = DataQuery::arrayTeamAgent($userId);
		return view('admin.teamAgent.index', compact('agent'));
	}

	/**
	 * Show the form for creating a new teamagent
	 *
     * @return \Illuminate\View\View
	 */
	public function create()
	{
	    $userId = Auth::user()->id;
	    //同個部門與組別的user
	    $arrayUser = DataQuery::arrayTeamUser($userId);
	    return view(config('quickadmin.route').'.teamAgent.create', compact(array('userId', 'arrayUser')));
	}

	/**
	 * Store a newly created teamagent in storage.
	 *
     * @param CreateCustomerRequest|Request $request
	 */
	public function store(CreateCustomerRequest $request)
	{
		$id = Customer::create($request->all())->id;
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
		return redirect()->route(config('quickadmin.route').'.teamagent.index');
	}

	/**
	 * Show the form for editing the specified teamagent.
	 *
	 * @param  int  $id
     * @return \Illuminate\View\View
	 */
	public function edit($id)
	{
		$agent = Customer::withTrashed()->find($id);
		//同個部門與組別的user
		$userId = Auth::user()->id;
	    $arrayUser = DataQuery::arrayTeamUser($userId);
	    //共用user
		$arrayCustomerUser = CustomerUser::where('customer_id', $id)->pluck('user_id')->toArray();
		//聯絡人
		$contact = DataQuery::arraySelectContactByTeamCustomer($id);
		// foreach ($customerUsers as $customerUser) {
		// 	if($listUser[$customerUser->user_id])
		// }
		return view(config('quickadmin.route').'.teamAgent.edit', compact(array('agent','arrayUser','arrayCustomerUser','contact')));
	}

	/**
	 * Update the specified teamagent in storage.
     * @param UpdateCustomerRequest|Request $request
     *
	 * @param  int  $id
	 */
	public function update($id, UpdateCustomerRequest $request)
	{
		$agent = Customer::withTrashed()->findOrFail($id);
		$agent->update($request->all());
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
		//將聯絡人指定所屬公司
		$this->setCustomerOfContact($request->input('contact_id'), $id);

		// $customerUsers = CustomerUser::where([
		// 				['customer_id', $id],
		// 				['user_id', $arrayUserID[$idx]]
		// 			]);
		

		// foreach ($arrayUserID as $idx => $checked) {
		// 	$customerUser = CustomerUser::where([
		// 				['customer_id', $id],
		// 				['user_id', $arrayUserID[$idx]]
		// 			])->first();
		// 	if($checked) {
		// 		if(empty($customerUser)) {
		// 			$customerUser = new CustomerUser();
		// 			$customerUser->customer_id = $id;
		// 			$customerUser->user_id = $arrayUserID[$idx];
		// 			$customerUser->save();
		// 		}
		// 	} else {
		// 		// if(!empty($customerUser))
		// 			CustomerUser::find($customerUser->id)->delete();
		// 	}
		// }

		return redirect()->route(config('quickadmin.route').'.teamagent.index');
	}

	/**
	 * Remove the specified teamagent from storage.
	 *
	 * @param  int  $id
	 */
	public function destroy($id)
	{
		Customer::destroy($id);

		return redirect()->route(config('quickadmin.route').'.teamagent.index');
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

        return redirect()->route(config('quickadmin.route').'.teamagent.index');
    }

    //列表中的啟用按鈕
	public function resetDelete($id)
	{
		Customer::withTrashed()->find($id)->restore();
		return redirect()->route(config('quickadmin.route').'.teamagent.index');
	}
	//將聯絡人指定所屬公司
	function setCustomerOfContact($contactId, $id) {
		$contact = Contact::find($contactId);
	    if(isset($contact)) {
	    	$contact->customer_id = $id;
	    	$contact->save();
	    }
	}
}

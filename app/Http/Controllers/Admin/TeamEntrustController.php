<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DataQuery;
use App\Dept;
use App\Publishuser;
use App\User;
use Illuminate\Support\Facades\Auth;

class TeamEntrustController extends Controller {

	/**
	 * Index page
	 *
     * @param Request $request
     *
     * @return \Illuminate\View\View
	 */
	public function index()
    {
    	$userId = Auth::user()->id;
    	$entrusts = DataQuery::collectionOfTeamEntrust($userId);
    	foreach ($entrusts as $entrust) {
    		$publishuser = Publishuser::where('user_id', $entrust->owner_user)->first();
            $dept = Dept::find($publishuser->dept_id);
            $entrust->user_dept = empty($dept) ? '' : $dept->name;
            $entrust->user_name = User::find($publishuser->user_id)->name;
            $entrust->status_name = config('admin.entrust.status')[$entrust->status];
        }
		// return view('admin.publishverify.index', compact('entrusts'));

		return view('admin.teamEntrust.index', compact('entrusts'));
	}

}
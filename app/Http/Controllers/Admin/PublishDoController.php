<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DataQuery;
use App\Dept;
use App\Entrust;
use App\EntrustFlow;
use App\Publish;
use App\Publishuser;
use Illuminate\Http\Request;

class PublishDoController extends Controller {

	/**
	 * Index page
	 *
     * @param Request $request
     *
     * @return \Illuminate\View\View
	 */
	public function index()
    {
    	$entrusts = DataQuery::collectionOfEntrustByStatus(3);
    	foreach ($entrusts as $entrust) {
    		$publishuser = Publishuser::withTrashed()->where('user_id', $entrust->owner_user)->first();
    		$dept = Dept::find($publishuser->dept_id);
            $entrust->user_dept = empty($dept) ? '' : $dept->name;
            $entrust->user_name = $publishuser->user_name;
            $entrust->status_name = config('admin.entrust.status')[$entrust->status];
        }
		return view('admin.publishdo.index', compact('entrusts'));
	}

	public function publishClose($id)
	{
		$this->exe($id, 9);
		return redirect()->route('admin.publishdo.index');
	}
	public function publishReject($id, Request $request)
	{
		//退件時把委刊單預約的資料刪除
		Publish::where('entrust_id', $id)->delete();
		//退件原因
		$entrust = Entrust::find($id);
		$entrust->reject_text = $request->get('reject_text');
		$entrust->save();
		//flow reject
		EntrustFlow::where('entrust_id', $id)->update(['status' => 'reject']);
		//
		$this->exe($id, 4);
		return redirect()->route('admin.publishdo.index');
	}

	/**
	 * 執行
	 *
	 */
	function exe($id, $action)
	{
	    $entrust = Entrust::find($id);
	    $entrust->status = (int)$action;
	    $entrust->save();
	}
}
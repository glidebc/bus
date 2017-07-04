<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DataQuery;
use App\Dept;
use App\Entrust;
use App\EntrustFlow;
use App\Publish;
use App\Publishuser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublishverifyController extends Controller {

	/**
	 * Display a listing of publish
	 *
     * @param Request $request
     *
     * @return \Illuminate\View\View
	 */
	public function index()
    {
    	$entrustVerify = EntrustFlow::where('status', 'verify');

    	$entrusts = DataQuery::collectionOfEntrustVerify();
    	foreach ($entrusts as $entrust) {
    		$publishuser = Publishuser::where('user_id', $entrust->owner_user)->first();
    		$dept = Dept::find($publishuser->dept_id);
            $entrust->user_dept = empty($dept) ? '' : $dept->name;
            $entrust->user_name = User::find($publishuser->user_id)->name;
            $entrust->status_name = config('admin.entrust.status')[$entrust->status];
            
            $entrust->verify = EntrustFlow::where('status', 'verify')->where('entrust_id', $entrust->id)->count();
        }

        // $countVerify = EntrustFlow::where('status', 'verify')->count();
        // if($countVerify > 9) {
        // 	// $countVerify = strval($entrustVerify);
        // 	// if($entrustVerify->count() > 9)
        // 		$countVerify = '9+';
        // }

		return view('admin.publishverify.index', compact(array('entrusts')));
	}

	public function publishOk($id)
	{
		$this->verify($id, 3);
		//flow ok
		EntrustFlow::where('entrust_id', $id)->update(['status' => 'ok']);

		return redirect()->route('admin.publishverify.index');
	}
	public function publishReject($id)
	{
		//退件時把委刊單預約的資料刪除
		Publish::where('entrust_id', $id)->delete();
		//flow reject
		EntrustFlow::where('entrust_id', $id)->update(['status' => 'reject']);
		//
		$this->verify($id, 4);
		return redirect()->route('admin.publishverify.index');
	}
	public function publishBack($id)
	{
		$this->verify($id, 2);
		return redirect()->route('admin.publishverify.index');
	}

	/**
	 * 審核
	 *
	 */
	function verify($id, $action)
	{
	    $entrust = Entrust::find($id);
	    $entrust->status = (int)$action;
	    $entrust->save();
	}

	/**
	 * Store a newly created publish in storage.
	 *
     * @param CreatePublishRequest|Request $request
	 */
	// public function store(CreatePublishRequest $request)
	// {
	    
	// 	Publish::create($request->all());

	// 	return redirect()->route(config('quickadmin.route').'.publish.index');
	// }

	/**
	 * Show the form for editing the specified publish.
	 *
	 * @param  int  $id
     * @return \Illuminate\View\View
	 */
	// public function edit($id)
	// {
	// 	$publish = Publish::find($id);
	    
	    
	// 	return view('admin.publish.edit', compact('publish'));
	// }

	/**
	 * Update the specified publish in storage.
     * @param UpdatePublishRequest|Request $request
     *
	 * @param  int  $id
	 */
	// public function update($id, UpdatePublishRequest $request)
	// {
	// 	$publish = Publish::findOrFail($id);

        

	// 	$publish->update($request->all());

	// 	return redirect()->route(config('quickadmin.route').'.publish.index');
	// }

	/**
	 * Remove the specified publish from storage.
	 *
	 * @param  int  $id
	 */
	// public function destroy($id)
	// {
	// 	Publish::destroy($id);

	// 	return redirect()->route(config('quickadmin.route').'.publish.index');
	// }

    /**
     * Mass delete function from index page
     * @param Request $request
     *
     * @return mixed
     */
    // public function massDelete(Request $request)
    // {
    //     if ($request->get('toDelete') != 'mass') {
    //         $toDelete = json_decode($request->get('toDelete'));
    //         Publish::destroy($toDelete);
    //     } else {
    //         Publish::whereNotNull('id')->delete();
    //     }

    //     return redirect()->route(config('quickadmin.route').'.publish.index');
    // }

}
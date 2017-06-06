<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DataQuery;
use App\Entrust;
use App\Publish;
use App\Publishuser;
use App\User;
use Illuminate\Http\Request;

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
    	$entrusts = DataQuery::collectionOfEntrustVerify();
    	foreach ($entrusts as $entrust) {
            $entrust->user_dept = Publishuser::find($entrust->owner_user)->dept;
            $entrust->user_name = User::find($entrust->owner_user)->name;
            //
            $statusName = '';
            switch($entrust->status) {
                case 1:
                    $statusName = '提案'; break;
                case 3:
                    $statusName = '審核通過'; break;
                case 4:
                    $statusName = '退件'; break;
                case 5:
                    $statusName = '暫停'; break;
                case 0:
                    $statusName = '取消委刊'; break;
            }
            $entrust->status_name = $statusName;
        }
		return view('admin.publishverify.index', compact('entrusts'));
	}

	public function publishOk($id)
	{
		$this->verify($id, 3);
		return redirect()->route('admin.publishverify.index');
	}
	public function publishReject($id)
	{
		//退件時把委刊單預約的資料刪除
		Publish::where('entrust_id', $id)->delete();
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
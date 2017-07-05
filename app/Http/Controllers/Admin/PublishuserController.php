<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Redirect;
use Schema;
use App\DataQuery;
use App\Publishuser;
use App\User;
use App\Http\Requests\CreatePublishuserRequest;
use App\Http\Requests\UpdatePublishuserRequest;
use Illuminate\Http\Request;

class PublishuserController extends Controller {

	/**
	 * Display a listing of publishuser
	 *
     * @param Request $request
     *
     * @return \Illuminate\View\View
	 */
	public function index(Request $request)
    {
        $publishuser = DataQuery::collectionPublishUser(0)->get();
        foreach ($publishuser as $row) {
        	if($row->color_name == 'Gray')
				$row->font_color = 'white';
			else
				$row->font_color = config('admin.publish.colors')[$row->color_name];
        }

		return view('admin.publishuser.index', compact('publishuser'));
	}

	/**
	 * Show the form for creating a new publishuser
	 *
     * @return \Illuminate\View\View
	 */
	public function create()
	{
	    $user = DataQuery::arraySelectUser();
	    return view('admin.publishuser.create', compact('user'));
	}

	/**
	 * Store a newly created publishuser in storage.
	 *
     * @param CreatePublishuserRequest|Request $request
	 */
	public function store(CreatePublishuserRequest $request)
	{
	    
		Publishuser::create($request->all());

		return redirect()->route(config('quickadmin.route').'.publishuser.index');
	}

	/**
	 * Show the form for editing the specified publishuser.
	 *
	 * @param  int  $id
     * @return \Illuminate\View\View
	 */
	public function edit($id)
	{
		$user = DataQuery::arraySelectUser();
		$publishuser = Publishuser::find($id);
	    $user_name = User::find($publishuser->user_id)->name;

	    $dept = DataQuery::arraySelectDept();
		$team = DataQuery::arraySelectTeam();

		return view('admin.publishuser.edit', compact(array('user','publishuser','user_name','dept','team')));
	}

	/**
	 * Update the specified publishuser in storage.
     * @param UpdatePublishuserRequest|Request $request
     *
	 * @param  int  $id
	 */
	public function update($id, UpdatePublishuserRequest $request)
	{
		$publishuser = Publishuser::findOrFail($id);

        

		$publishuser->update($request->all());

		return redirect()->route(config('quickadmin.route').'.publishuser.index');
	}

	/**
	 * Remove the specified publishuser from storage.
	 *
	 * @param  int  $id
	 */
	public function destroy($id)
	{
		Publishuser::destroy($id);

		return redirect()->route(config('quickadmin.route').'.publishuser.index');
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
            Publishuser::destroy($toDelete);
        } else {
            Publishuser::whereNotNull('id')->delete();
        }

        return redirect()->route(config('quickadmin.route').'.publishuser.index');
    }

}

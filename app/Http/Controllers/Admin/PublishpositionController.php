<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Redirect;
use Schema;
use App\DataQuery;
use App\Publishposition;
use App\Http\Requests\CreatePublishpositionRequest;
use App\Http\Requests\UpdatePublishpositionRequest;
use Illuminate\Http\Request;



class PublishpositionController extends Controller {

	/**
	 * Display a listing of publishposition
	 *
     * @param Request $request
     *
     * @return \Illuminate\View\View
	 */
	public function index(Request $request)
    {
        $publishposition = DataQuery::arrayPublishpositionWithSite();
		return view('admin.publishposition.index', compact('publishposition'));
	}

	/**
	 * Show the form for creating a new publishposition
	 *
     * @return \Illuminate\View\View
	 */
	public function create()
	{
		$site = DataQuery::arraySelectSite();
	    $count = DataQuery::arraySelectTurnsCount();
	    return view('admin.publishposition.create', compact(array('site','count')));
	}

	/**
	 * Store a newly created publishposition in storage.
	 *
     * @param CreatePublishpositionRequest|Request $request
	 */
	public function store(CreatePublishpositionRequest $request)
	{
	    
		Publishposition::create($request->all());

		return redirect()->route(config('quickadmin.route').'.publishposition.index');
	}

	/**
	 * Show the form for editing the specified publishposition.
	 *
	 * @param  int  $id
     * @return \Illuminate\View\View
	 */
	public function edit($id)
	{
		$site = DataQuery::arraySelectSite();

		$publishposition = Publishposition::find($id);
		$siteid = $publishposition->site_id;

		$count = DataQuery::arraySelectTurnsCount();
		// $publishposition = Publishposition::find($id);
	    $turnscount = $publishposition->turns_count;
		return view('admin.publishposition.edit', compact(array('site','siteid','count','publishposition','turnscount')));
	}

	/**
	 * Update the specified publishposition in storage.
     * @param UpdatePublishpositionRequest|Request $request
     *
	 * @param  int  $id
	 */
	public function update($id, UpdatePublishpositionRequest $request)
	{
		$publishposition = Publishposition::findOrFail($id);

        

		$publishposition->update($request->all());

		return redirect()->route(config('quickadmin.route').'.publishposition.index');
	}

	/**
	 * Remove the specified publishposition from storage.
	 *
	 * @param  int  $id
	 */
	public function destroy($id)
	{
		Publishposition::destroy($id);

		return redirect()->route(config('quickadmin.route').'.publishposition.index');
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
            Publishposition::destroy($toDelete);
        } else {
            Publishposition::whereNotNull('id')->delete();
        }

        return redirect()->route(config('quickadmin.route').'.publishposition.index');
    }

}

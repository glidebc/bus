<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Redirect;
use Schema;
use App\Publishsite;
use App\Http\Requests\CreatePublishsiteRequest;
use App\Http\Requests\UpdatePublishsiteRequest;
use Illuminate\Http\Request;



class PublishsiteController extends Controller {

	/**
	 * Display a listing of publishsite
	 *
     * @param Request $request
     *
     * @return \Illuminate\View\View
	 */
	public function index(Request $request)
    {
        $publishsite = Publishsite::all();

		return view('admin.publishsite.index', compact('publishsite'));
	}

	/**
	 * Show the form for creating a new publishsite
	 *
     * @return \Illuminate\View\View
	 */
	public function create()
	{
	    
	    
	    return view('admin.publishsite.create');
	}

	/**
	 * Store a newly created publishsite in storage.
	 *
     * @param CreatePublishsiteRequest|Request $request
	 */
	public function store(CreatePublishsiteRequest $request)
	{
	    
		Publishsite::create($request->all());

		return redirect()->route(config('quickadmin.route').'.publishsite.index');
	}

	/**
	 * Show the form for editing the specified publishsite.
	 *
	 * @param  int  $id
     * @return \Illuminate\View\View
	 */
	public function edit($id)
	{
		$publishsite = Publishsite::find($id);
	    
	    
		return view('admin.publishsite.edit', compact('publishsite'));
	}

	/**
	 * Update the specified publishsite in storage.
     * @param UpdatePublishsiteRequest|Request $request
     *
	 * @param  int  $id
	 */
	public function update($id, UpdatePublishsiteRequest $request)
	{
		$publishsite = Publishsite::findOrFail($id);

        

		$publishsite->update($request->all());

		return redirect()->route(config('quickadmin.route').'.publishsite.index');
	}

	/**
	 * Remove the specified publishsite from storage.
	 *
	 * @param  int  $id
	 */
	public function destroy($id)
	{
		Publishsite::destroy($id);

		return redirect()->route(config('quickadmin.route').'.publishsite.index');
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
            Publishsite::destroy($toDelete);
        } else {
            Publishsite::whereNotNull('id')->delete();
        }

        return redirect()->route(config('quickadmin.route').'.publishsite.index');
    }

}

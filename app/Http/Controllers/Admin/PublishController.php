<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Redirect;
use Schema;
use App\Publish;
use App\Http\Requests\CreatePublishRequest;
use App\Http\Requests\UpdatePublishRequest;
use Illuminate\Http\Request;



class PublishController extends Controller {

	/**
	 * Display a listing of publish
	 *
     * @param Request $request
     *
     * @return \Illuminate\View\View
	 */
	public function index(Request $request)
    {
        $publish = Publish::all();

		return view('admin.publish.index', compact('publish'));
	}

	/**
	 * Show the form for creating a new publish
	 *
     * @return \Illuminate\View\View
	 */
	public function create()
	{
	    
	    
	    return view('admin.publish.create');
	}

	/**
	 * Store a newly created publish in storage.
	 *
     * @param CreatePublishRequest|Request $request
	 */
	public function store(CreatePublishRequest $request)
	{
	    
		Publish::create($request->all());

		return redirect()->route(config('quickadmin.route').'.publish.index');
	}

	/**
	 * Show the form for editing the specified publish.
	 *
	 * @param  int  $id
     * @return \Illuminate\View\View
	 */
	public function edit($id)
	{
		$publish = Publish::find($id);
	    
	    
		return view('admin.publish.edit', compact('publish'));
	}

	/**
	 * Update the specified publish in storage.
     * @param UpdatePublishRequest|Request $request
     *
	 * @param  int  $id
	 */
	public function update($id, UpdatePublishRequest $request)
	{
		$publish = Publish::findOrFail($id);

        

		$publish->update($request->all());

		return redirect()->route(config('quickadmin.route').'.publish.index');
	}

	/**
	 * Remove the specified publish from storage.
	 *
	 * @param  int  $id
	 */
	public function destroy($id)
	{
		Publish::destroy($id);

		return redirect()->route(config('quickadmin.route').'.publish.index');
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
            Publish::destroy($toDelete);
        } else {
            Publish::whereNotNull('id')->delete();
        }

        return redirect()->route(config('quickadmin.route').'.publish.index');
    }

}

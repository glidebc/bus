<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DataQuery;

class PublishbooklistController extends Controller {

	/**
	 * Index page
	 *
     * @param Request $request
     *
     * @return \Illuminate\View\View
	 */
	public function index()
    {
    	$publish = DataQuery::arrayPublishList();
		return view('admin.publishbooklist.index', compact('publish'));
	}

}
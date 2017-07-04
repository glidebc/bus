<?php

namespace App\Http\Controllers\Admin;

use App\DataQuery;
use App\Publishuser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller {

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
    	$home_path = Publishuser::where('user_id', $userId)->first()->home_path;
    	if(empty($home_path)) {
    		return view('admin.dashboard');
    	} else {
    		return redirect()->route('admin'.$home_path.'index');
    	}
	}
}
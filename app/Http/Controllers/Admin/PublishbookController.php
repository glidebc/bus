<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Entrust;
use App\Glide;
use App\Publishposition;
use App\Publishsite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use stdClass;

class PublishbookController extends Controller {

	/**
	 * Index page
	 *
     * @param Request $request
     *
     * @return \Illuminate\View\View
	 */
	public function index()
    {
    	$arraySite = [];
    	$arrayPositions = [];
    	$publishSites = Publishsite::select('name','id')->where('deleted_at', null)->orderBy('id')->pluck('name','id');
    	foreach ($publishSites as $key => $value) {
    		$site = new stdClass();
    		$site->id = $key;
    		$site->name = $value;
			array_push($arraySite, $site);

			$position = new stdClass();
    		$position->site_id = $key;
    		$positions = Publishposition::select('id','name','turns_count')
    											->where('site_id', $key)
    											->where('deleted_at', null)
    											->orderBy('id')->get();//->pluck('name','id');
    		$position->positions = $positions;
    		array_push($arrayPositions, $position);
    		// $arrayPublishposition = array(
    		// 	"site_id"	=> $key,
    		// 	"site_name" => $value,
    		// 	"publish_position" => $publishposition
    		// )
    		

    		// $obj->publish_position = $publishposition;

    		// array_push($arraySitePosition, json_encode($arrayPublishposition, JSON_FORCE_OBJECT));
    	}

		$userid = Auth::user()->id;
		$entrust = Glide::arraySelectEntrust($userid);
        // $entrust = Entrust::where('owner_user', $userid)->orderBy('created_at')->pluck('name', 'id');

        $myEntrustId = Input::get('eid');

		return view('admin.publishbook.index', compact(array('arraySite', 'arrayPositions', 'entrust', 'myEntrustId')));
	}

}
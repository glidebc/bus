<?php

namespace App\Http\Controllers\Admin;

use App\Dept;
use App\DataQuery;
use App\Publishuser;
use App\Team;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePublishuserRequest;
use Illuminate\Support\Facades\Auth;
use Form;

class MyUserController extends Controller {

	/**
	 * Index page
	 *
     * @param Request $request
     *
     * @return \Illuminate\View\View
	 */
	public function index()
    {
    	$user = Auth::user();
    	$colors = config('admin.publish.colors');
    	//檢查業務資料表
		$publishuser = Publishuser::where('user_id', $user->id);
		if($publishuser->count() > 0) {
			$publishuser = $publishuser->first();
			if($publishuser->color_name == 'Gray')
				$publishuser->font_color = 'white';
			else
				$publishuser->font_color = $colors[$publishuser->color_name];
		} else {
			$publishuser = new Publishuser();
			$publishuser->user_id = $user->id;
			$publishuser->color_name = 'Gray'; //預設是灰色底色
			// $publishuser->dept_id = 0;
			// $publishuser->team_id = 0;
			$publishuser->save();
			$publishuser->font_color = 'white';//預設是白色字
		}
    	//
    	$colorsUsed = Publishuser::select('color_name')->pluck('color_name');
		foreach ($colorsUsed as $color_name) {
			if(array_key_exists($color_name, $colors)) {
				$font_color = $colors[$color_name];
				unset($colors[$color_name]);
			}
		}
		//
		$dept_name = '';
		$team_name = '';
		if(isset($publishuser->dept_id)) {
			$dept_name = Dept::find($publishuser->dept_id)->name;
			$team_name = Team::find($publishuser->team_id)->get()->name;
		}
		
		// $dept = DataQuery::arraySelectDept();
		// $team = DataQuery::arraySelectTeam();
		//
		// Form::macro('selectColor', function($name, $list = array(), $selected = null, $options = array())
		// {
		//     $selected = $this->getValueAttribute($name, $selected);
		//     $options['id'] = $this->getIdAttribute($name, $options);

		//     if (!isset($options['name']))
		//     	$options['name'] = $name;

		//     $html = array();
		//     foreach ($list as $color_name => $list_el) {
		//         $selectedAttribute = $this->getSelectedValue($color_name, $selected);
		//         $option_attr = array('value' => e($color_name), 'selected' => $selectedAttribute, 'class' => $color_name);
		//         $html[] = '<option'.$this->html->attributes($option_attr).'>'.e($color_name).'</option>';
		//     }

		//     $options = $this->html->attributes($options);
		//     $list = implode('', $html);
		//     return "<select{$options}>{$list}</select>";
		// });

		return view(config('quickadmin.route').'.myUser.index', compact(array('user','colors','publishuser','dept_name','team_name')));
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
		$countUpdated = $publishuser->update($request->all());
		// return view(config('quickadmin.route').'.myUser.index', compact(array('user','colors','publishuser')));
		return redirect()->route(config('quickadmin.route').'.myuser.index')->with('result', $countUpdated);;
	}

}
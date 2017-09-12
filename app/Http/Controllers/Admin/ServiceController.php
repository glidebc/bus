<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Contact;
use App\DataQuery;
use App\Entrust;
use App\Glide;
use App\Publish;
use App\Publishposition;
use App\Publishuser;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use stdClass;
use DateInterval;

class ServiceController extends Controller {

	/**
	 * Index page
	 *
     * @param Request $request
     *
     * @return \Illuminate\View\View
	 */
	public function index()
    {
		return view('admin.service.index');
	}

	public function adBook(Request $request)
	{
		try {
			$json = $request->all();
			//取得 版位+輪播 array 與 日期 array
			$arrayPositionTurn = array();
			$arrayDate = array();
			$jData = array_get($json, 'data',[]);
			foreach ($jData as $data) {
				//
				$positionTurn = $data['position'].$data['turn'];
				if(!array_key_exists($positionTurn, $arrayPositionTurn))
					$arrayPositionTurn[$positionTurn] = array($data['position'], $data['turn']);
				//
				if(!array_key_exists($data['date'], $arrayDate)){
					$dateText = substr($data['date'], 0, 4).'-'.substr($data['date'], -4, 2).'-'.substr($data['date'], -2);
					$arrayDate[$data['date']] = $dateText;
				}
			}
			ksort($arrayPositionTurn, SORT_NUMERIC);
			ksort($arrayDate);
			//從每個 版位+輪播(column) 開始往下(row)檢查
			foreach ($arrayPositionTurn as $key => $positionTurn) {
				$position = $positionTurn[0];
				$turn = $positionTurn[1];
				$dateGo = date_create(array_values($arrayDate)[0]);
				$dateEnd = date_create(end($arrayDate))->modify('+1 day'); //do-while時的最後一天要比預約的最後一天要大一天
				//do-while variable
				$d = '';
				$days = 1;
				$canSave = false;
				do {
					// $strDate = $dateGo->format('Ymd');
					foreach ($jData as $data) {
						if($d == '' ) { //找起始日
							if($data['position'] == $position && $data['turn'] == $turn && $data['date'] == $dateGo->format('Ymd')) { //起始日
								$d = $dateGo->format('Ymd');
								break;
							}
						} else { //檢查起始日之後的日期
							if($data['position'] == $position && $data['turn'] == $turn && $data['date'] == $dateGo->format('Ymd')) { //days +1
								$canSave = false;
								$days++;
								break;
							} else {
								$canSave = true;
							}
						}
					}

					if($d != '' && $canSave) {
						//將此委刊單的 status 改成 2 (審核中)
						// $entrust = Entrust::find($json['eid']);
						// $entrust->status = 2;
						// $entrust->save();
						//save to Publish
						$publish = new Publish();
						$publish->entrust_id = $json['eid'];
						$publish->publish_position_id = $position;
						$publish->turn = $turn;
						$publish->date = $d;
						$publish->days = $days;
						// $publish->status = null;
						// $publish->owner_user = Auth::user()->id;
						$publish->save();
						//reset variable
						$d = '';
						$days = 1;
						$canSave = false;
					}
					//
					$dateGo->modify('+1 day');
				} while ($dateGo <= $dateEnd);
			}

			// // $turnsCountNow = '';
			// foreach (array_get($json, 'data',[]) as $data) {
			// 	//
			// 	if($this->turnsCount($data['pid']) > $this->bookedCount($data['date'], $data['pid'])) {
			// 		$publish = new Publish();
			// 		$publish->entrust_id = $json['eid'];
			// 		$publish->date = $data['date'];
			// 		$publish->publish_position_id = $data['pid'];
			// 		// $publish->status = null;
			// 		// $publish->owner_user = Auth::user()->id;
			// 		$publish->save();
			// 	}
			// 	// $objturnsCountNow = $this->checkTurnsCount($data['date'], $data['id']);
			// }
			// return response($arrayDate, 200);
			return response('', 200);
		} catch (Exception $e) {
			return response(var_dump($e->getTraceAsString()), 500);
		    // echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
		// return response('', 500);
	}

	public function adBookedList(Request $request)
	{
		$json = $request->all();

		$res = new stdClass();
		//
		$dateStart = date_create($json['sDate']);
		$dateEnd = date_create($json['eDate']);
		$diff = date_diff($dateStart, $dateEnd);
		$y = $diff->y + $diff->m / 12 + $diff->d / 365.25;
		//超過一年的日期區間不顯示
		if($y >= 1)
			return response()->json(null, 400);

		// $now = date('Y-m-d H:i:s');
		// $str_today = date('Y-m-d', strtotime('today'));
		// $str_start_date = date('Y-m-d', strtotime($str_today.' -1 week first sunday of this week'));
		// $str_end_date = date('Y-m-d', strtotime($str_today.' +1 month last day of this month'));
		// $res->sd = $dateStart->format('Y-m-d');
		// $res->ed = $dateEnd->format('Y-m-d');

		//起始日期-3個月, 結束日期+3個月
		$interval = new DateInterval('P3M');
		$dateStart->sub($interval);
		$dateEnd->add($interval);
		//
		$data = new stdClass();
		//有預約的日期與版位的table cell list
		$listDatePosition = Publish::select('publish_position_id', 'turn', 'date')
								// ->whereRaw('deleted_at IS NULL')
								->where([
									['date', '>=', $dateStart->format('Ymd')],
									['date', '<=', $dateEnd->format('Ymd')]
									// ['status', '!=', 2]
								])
								->groupBy('publish_position_id', 'turn', 'date')
								->orderBy('publish_position_id', 'turn', 'date')->get();
		foreach ($listDatePosition as $datePosition) {
			//每個cell的委刊單ID
			$publish = Publish::where([
									['publish_position_id', '=', $datePosition->publish_position_id],
									['turn', '=', $datePosition->turn],
									['date', '=', $datePosition->date]
								])->first();
			//
			$dateText = substr($datePosition->date, 0, 4).'-'.substr($datePosition->date, -4, 2).'-'.substr($datePosition->date, -2);
			//date+days 取得符合開始與結束日期區間的 開始日
			$cd = date_create($dateText);
			$interval = new DateInterval('P1D');
			$date = $datePosition->date;//取得的開始日
			$days = $publish->days;//剩下的天數
			while($days > 0) {
				if(date_create($json['sDate']) <= $cd && $cd <= date_create($json['eDate'])) {
					$date = $cd->format('Ymd');
					break;
				}
				$cd->add($interval);
				$days--;
			}
			//
			if($days > 0) {
				//委刊單的status 2 or 3 才顯示
				$entrustStatus = Entrust::find($publish->entrust_id)->status;
				if ($entrustStatus == 2 || $entrustStatus == 3) {
					$entrust = DataQuery::collectionOfEntrustByID($publish->entrust_id)->first();
					$publishuser = DataQuery::collectionPublishUser($entrust->owner_user)->first();
					$cell = new stdClass();
					$cell->customer = $entrust->agent_customer;
					$cell->project = $entrust->name;
					//委刊單預約的日期區間
					$sd = date_create($dateText);
					$ed = date_create($dateText)->add(new DateInterval('P'.($publish->days - 1).'D'));
					$cell->publish = $sd->format('Y-m-d').'～'.$ed->format('Y-m-d');
					// $strSD = substr($entrust->start_date, 0, 4).'-'.substr($entrust->start_date, -4, 2).'-'.substr($entrust->start_date, -2);
					// $strED = substr($entrust->end_date, 0, 4).'-'.substr($entrust->end_date, -4, 2).'-'.substr($entrust->end_date, -2);
					// $cell->duration = $strSD.'～'.$strED;//委刊單的總走期
					$cell->dept = empty($publishuser->dept) ? '' : $publishuser->dept;
					$cell->user = $publishuser->user_name;
					$cell->days = $days;
					$cell->status = $entrustStatus;
					$cell->bgcolor = $publishuser->color_name;//底色
					$cell->color = $publishuser->color_name == 'Gray' ? 'white' : config('admin.publish.colors')[$publishuser->color_name];//字的顏色, 預設是白色字
					// $cell->bgcolor = empty($publishuser->color_name) ? 'Gray' : $publishuser->color_name;//預設是灰色底色
					// 
					// $cell->color = Publishuser::where('user_id', $entrust->owner_user)->first()->color_name;

					$data->{$date.'-'.$datePosition->publish_position_id.'-'.$datePosition->turn} = $cell;
				}
			}
		}
		
		$res->data = $data;
		return response()->json($res, 200, [], JSON_NUMERIC_CHECK);
	}

	public function adBookedListOLD(Request $request)
	{
		$json = $request->all();
		// $listPublish = Publish::where([
		// 	['date', '>=', $json['sDate']],
		// 	['date', '<=', $json['eDate']]
		// ])->get();

		$arrayPositionID = Publishposition::where('site_id', '=',  (int)$json['site'])->pluck('id');//select('id')->
		//有預約的日期與版位的table cell list，status=2 不顯示。count是cell的預約數量，entrust_status是cell的預約狀態
		$listDatePosition = Publish::select('date', 'publish_position_id', DB::raw('COUNT(*) AS count, 0 AS entrust_status'))
								->whereIn('publish_position_id', $arrayPositionID)
								->whereRaw('deleted_at IS NULL')
								->where([
									['date', '>=', $json['sDate']],
									['date', '<=', $json['eDate']],
									['status', '!=', 2]
								])->groupBy('date', 'publish_position_id')->get();
		// $listPublish = Publish::select('date', 'publish_position_id', 'status', DB::raw('COUNT(*) AS count, 0 AS entrust_status'))
		// 						->whereIn('publish_position_id', $arrayPositionID)
		// 						->where([
		// 							['date', '>=', $json['sDate']],
		// 							['date', '<=', $json['eDate']],
		// 							// ['status', '!=', 2]
		// 						])->groupBy('date', 'publish_position_id', 'status')->get();
		$detail = array();
		
		// $data = array('list' => $listPublish);

		foreach ($listDatePosition as $datePosition) {
			//entrust_status= 0:預約尚未額滿; 1:預約額滿; 2:預約都通過審核
			//預約數量 與 版位輪播次數 （版位輪播次數可以隨時更改，所以要用<=）
			if($this->turnsCount($datePosition->publish_position_id) <= $datePosition->count) {
				$datePosition->entrust_status = 1;
			}
			//mouseover 顯示每個date and position的委刊單ID
			$entrustIDs = Publish::where([
									['date', '=', $datePosition->date],
									['publish_position_id', '=', $datePosition->publish_position_id]
								])->pluck('entrust_id');
			// $obj = new StdClass();
			// $date_position = implode('-', array($publish->date, $publish->publish_position_id));
			// $obj->{$date_position} = $cellEntrusts;
			// array_push($data, $obj);

			
			// $date_position = implode('-', array($publish->date, $publish->publish_position_id));
			// $object = new stdClass();
			// $object->name = $date_position;
			// $arr[] = $object;
			$countStatusOk = 0;
			foreach ($entrustIDs as $entrustId) {
				$status = Publish::where([
									['entrust_id', $entrustId],
									['date', $datePosition->date],
									['publish_position_id', $datePosition->publish_position_id]
								])->first()->status;
				if($status != 2) {
					$entrust = Glide::collectionOfEntrustByID($entrustId)
									->addSelect(DB::raw('"'.$datePosition->date.'" AS date, '.$datePosition->publish_position_id.' AS position, '.$status.' AS status'))
									// ->put('position', $publish->publish_position_id)
									->first();
					array_push($detail, $entrust);
				}
				if($status == 1)
					$countStatusOk++;
				// $entrust = Entrust::leftJoin('customer', function ($join) use ($cellEntrust) {
				// 						$join->on('entrust.customer_id', '=', 'customer.id')
				// 							 ->where('entrust.id', '=', $cellEntrust->entrust_id);
				// 					})
				// 					->select(DB::raw('customer.name AS cust'), 'entrust.name');
				// $entrust = Entrust::find($cellEntrust->entrust_id);

				// $obj= new stdClass();
			 //    foreach ($entrust as $k=> $v) {
			 //        $obj->{$k} = $v;
			 //    }
			}
			//預約都通過審核
			if($countStatusOk == $datePosition->count)
				$datePosition->entrust_status = 2;
		}
		//list: 有預約的cell list, entrust_list: 有預約的委刊單list
		$data = array('cell_list' => $listDatePosition, 'entrust_list' => $detail);//, 'test'=>$json['site']
		// $data = array('list' => $listPublish, 'detail' => array_values($detail));
		// $data = array('list' => $listPublish);
		// $data->detail=$detail;

		// header('Content-Type: application/json');
		return response()->json($data, 200, [], JSON_NUMERIC_CHECK);
		// return response(json_encode($listPublish), 200);
		// return response('', 500);
	}

	function turnsCount($id)
	{
		//此版位的輪播次數
		return Publishposition::find($id)->turns_count;
	}
	function bookedCount($date, $id)
	{
		//日期＋版位已被預約的次數
		return Publish::where('date', '=', $date)
						->where('publish_position_id', '=', $id)
						->count();
	}

	function customerContact(Request $request)
	{
		// $aryContact = array();
		// $contacts = Contact::where('customer_id', $customerId);
		// foreach ($contacts as $contact) {
		// 	$c = new StdClass();
		// 	$c->id = $contact->id;
		// 	$c->name = $contact->name;
		// 	array_push($aryContact, $co);
		// }

		// $data = new stdClass();
		$aryContact = Contact::where('customer_id', $request->id)->pluck('name','id');
		// $data = json_decode(json_encode($aryContact));
		// header('Content-Type: application/json; charset=utf-8');
		return response()->json($aryContact, 200, [], JSON_UNESCAPED_UNICODE);
	}

}
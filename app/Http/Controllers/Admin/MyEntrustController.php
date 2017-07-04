<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Redirect;
use Schema;
use App\Customer;
use App\DataQuery;
use App\Entrust;
use App\EntrustFlow;
use App\EntrustItem;
use App\Http\Requests\CreateEntrustRequest;
use App\Http\Requests\UpdateEntrustRequest;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use stdClass;

class MyEntrustController extends Controller {

	private $alert_verifying = '正在審核中, 無法修改與取消';
	private $alert_verified = '已審核完畢, 無法修改與取消';

	/**
	 * Display a listing of myentrust
	 *
     * @param Request $request
     *
     * @return \Illuminate\View\View
	 */
	public function index(Request $request)
    {
        $userId = Auth::user()->id;
        $entrust = DataQuery::collectionOfEntrustByUser($userId)->get();
        $pay = config('admin.entrust.pay');//付款方式 array
	    $payStatus = config('admin.entrust.pay_status');//付款狀況 array

	    // $countEntrust = 0;

        foreach ($entrust as $entrustOne) {
        	if(strlen($entrustOne->start_date) > 0) {
        		$dateStart = substr($entrustOne->start_date, 0, 4).'-'.substr($entrustOne->start_date, -4, 2).'-'.substr($entrustOne->start_date, -2);
				$dateEnd = substr($entrustOne->end_date, 0, 4).'-'.substr($entrustOne->end_date, -4, 2).'-'.substr($entrustOne->end_date, -2);
				$entrustOne->duration = $dateStart.'～'.$dateEnd;
        	}
			//
			$entrustOne->txt_pay = $pay[$entrustOne->pay];
			$entrustOne->txt_pay_status = $payStatus[$entrustOne->pay_status];
			//flow
			if($this->countEntrustFlowStatus($entrustOne->id, 'ok') || $this->countEntrustFlowStatus($entrustOne->id, 'reject')) {
				$entrustOne->verify_result = true;
				// $countEntrust++;
				// $this->deleteEntrustFlow($entrustOne->id);
			}
			$entrustOne->verifying = $this->countEntrustFlowStatus($entrustOne->id, 'verifying');
        }

		return view(config('quickadmin.route').'.myEntrust.index', compact(array('entrust')));
	}

	/**
	 * Show the form for creating a new myentrust
	 *
     * @return \Illuminate\View\View
	 */
	public function create()
	{
		$userId = Auth::user()->id;
	    $customer = DataQuery::arraySelectCustomer($userId);
	    //
	    $publishKind = config('admin.entrust.items');//委刊類別 array
	    $pay = config('admin.entrust.pay');//付款方式 array
	    $payStatus = config('admin.entrust.pay_status');//付款狀況 array
	    //
	    return view(config('quickadmin.route').'.myEntrust.create', compact(array('userId', 'customer', 'publishKind', 'pay', 'payStatus')));
	}

	/**
	 * Store a newly created myentrust in storage.
	 *
     * @param CreateMyEntrustRequest|Request $request
	 */
	public function store(CreateEntrustRequest $request)
	{
		$input = $this->getInput_publishKindToString($request);//委刊類別轉成字串
		//
	    $entrustId = Entrust::create($input)->id;
	    //檢查委刊項
	    $this->checkEntrustItem($entrustId, $request);
		return redirect()->route(config('quickadmin.route').'.myentrust.index');
	}

	/**
	 * 送審, 退回提案, 取消, 產生Excel
	 */
	public function entrustGo($id)
	{
		$this->entrustPending($id, 2);
		//flow
		$entrustFlow = new EntrustFlow();
		$entrustFlow->entrust_id = $id;
		$entrustFlow->status = 'verify';
		$entrustFlow->save();

		return redirect()->route(config('quickadmin.route').'.myentrust.index');
	}
	public function entrustBack($id)
	{
		$msg = $this->checkStatusAndFlow($id);//已審核 and flow check
		if($msg != '')
			return redirect()->route(config('quickadmin.route').'.myentrust.index')->with('msg', $msg);
		//
		$this->entrustPending($id, 1);
		EntrustFlow::where('entrust_id', $id)->delete();//flow delete
		return redirect()->route(config('quickadmin.route').'.myentrust.index');
	}
	public function entrustCancel($id)
	{
		$msg = $this->checkStatusAndFlow($id);//已審核 and flow check
		if($msg != '')
			return redirect()->route(config('quickadmin.route').'.myentrust.index')->with('msg', $msg);
		//
		$this->entrustPending($id, 0);
		EntrustFlow::where('entrust_id', $id)->delete();//flow delete
		return redirect()->route(config('quickadmin.route').'.myentrust.index');
	}
	public function entrustExcel($id)
	{
		$entrust = Entrust::find($id);
		$customer = Customer::find($entrust->customer_id);
		$entrustItems = EntrustItem::where('entrust_id', $id)->orderBy('no')->get();

		$data = Excel::selectSheetsByIndex(0)->load('inc/委刊單.xlsx', function($reader) use ($customer,$entrust,$entrustItems) {
				// $sheet = $reader->getExcel()->getSheet();
				$reader->sheet('廣告委刊單', function($sheet) use ($customer,$entrust,$entrustItems) {
					//日期
					$sheet->setCellValue('H1', date('Y/m/d', strtotime('today')));
					//客戶資料
					$sheet->setCellValue('C3', $customer->tax_title);
					$sheet->setCellValue('G3', $customer->tax_num);
					$sheet->setCellValue('C4', $customer->zip_code.$customer->address);
					$sheet->setCellValue('G4', $customer->com_fax);

					$sheet->setCellValue('G5', $customer->com_tel);

					$sheet->setCellValue('G6', $customer->mobile);

					//合作內容
					$sheet->setCellValue('C8', $entrust->name);

					$dateStart = substr($entrust->start_date, 0, 4).'-'.substr($entrust->start_date, -4, 2).'-'.substr($entrust->start_date, -2);
					$dateEnd = substr($entrust->end_date, 0, 4).'-'.substr($entrust->end_date, -4, 2).'-'.substr($entrust->end_date, -2);
					$sheet->setCellValue('C9', $dateStart.' ～ '.$dateEnd);
					$days = $this->countDays($entrust->start_date, $entrust->end_date);
					$sheet->setCellValue('F9', '(共 '.$days.' 天) / 實際走期依排期表');

					$strPublishKind = '';
					$aryPublishKind = config('admin.entrust.items');//委刊類別 array
					$publishKindSelected = explode(',', $entrust->publish_kind);
					foreach ($publishKindSelected as $publishKind) {
						if(strlen($strPublishKind) > 0)
							$strPublishKind .= ', ';
						$strPublishKind .= $aryPublishKind[$publishKind];
					}
					$sheet->setCellValue('C10', $strPublishKind);

					//付款方式與金額
					$numRow = 13; $numItem = 1; $count = 0;
					foreach ($entrustItems as $entrustItem) {
						$sheet->setCellValue('B'.$numRow, $numItem++);
						$sheet->setCellValue('C'.$numRow, $entrustItem->name);
						$sheet->setCellValue('E'.$numRow, $entrustItem->cost);
						$count += $entrustItem->cost;
						$numRow++;
					}
					$sheet->setCellValue('E18', $count);
					$tax = round($count * .05);
					$sheet->setCellValue('E19', $tax);
					$sheet->setCellValue('E20', $count + $tax);

					$aryPay = config('admin.entrust.pay');
					$sheet->setCellValue('F13', $aryPay[$entrust->pay]);
					if($entrust->pay == 2) {
						$sheet->setCellValue('F15', '');$sheet->setCellValue('F16', '');$sheet->setCellValue('F17', '');
					}

					//表格邊線補回去
					$this->redrawBorder($sheet);
				});
				
			})->export('xlsx');

		// $sheetForm = null;
		// Excel::load('inc/eForm_v1.xlsx', function($reader) use (&$sheetForm) {
		// 	$objExcel = $reader->getExcel();
  //           $sheetForm = $objExcel->getSheet(0);
			
		// 	$sheetForm->setCellValue('C3', '葛萊德');
		// });		

		// $sheet = $excelForm->getExcel()->getSheet();
		// $sheet->setCellValue('C3', '葛萊德(股)公司');

		// Excel::create('委刊單_20170619', function($excel) use ($sheetForm) {
		// 		$excel->sheet('20170619', function($sheet) use($sheetForm) {
		// 	    	$sheet = $sheetForm;
		// 		});
		// 	})->export('xlsx');

	    // $html = app('excel.readers.html');
	    // $html->load('admin/excel/show', true, $excel);

		// return view(config('quickadmin.route').'.excel.show', compact('excel'));

		// $excel->export('xlsx');

		// $data = [];
		// Excel::load('inc/eForm_v1.xlsx', function($reader) use (&$data) {
		// 	$objExcel = $reader->getExcel();
  //           $sheet = $objExcel->getSheet(0);
		// 	$sheet->setCellValue('C3', '葛萊德');
		// 	// $objExcel = $reader->getExcel();
  //  //          $sheet = $objExcel->getSheet(0);
  //  //          $highestRow = $sheet->getHighestRow();
  //  //          $highestColumn = $sheet->getHighestColumn();

  //  //          //  Loop through each row of the worksheet in turn
  //  //          for ($row = 1; $row <= $highestRow; $row++)
  //  //          {
  //  //              //  Read a row of data into an array
  //  //              $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
  //  //                  NULL, TRUE, FALSE);

  //  //              $data[] = $rowData[0];
  //  //          }
		// })->download('委刊單_20170619.xlsx');
		
		// $result = Excel::create('委刊單_20170619', function($excel) use ($data) {
		// 	$excel->sheet('委刊單', function($sheet) use ($data)
	 //        {
		// 		$sheet->fromArray($data);
	 //        });
	 //    })->export('xlsx');
	 //    return $result;
		

		

		// Excel::create('gen_excel/e1.xlsx', function($excel) use ($templateRows) {

		// })->export('');
		// $this->entrustPending($id, 2);
		// return redirect()->route(config('quickadmin.route').'.myentrust.index');
	}

	/**
	 * 送審狀態
	 *
	 */
	function entrustPending($id, $action)
	{
	    $entrust = Entrust::find($id);
	    $entrust->status = (int)$action;
	    $entrust->save();
	}

	/**
	 * Show the form for editing the specified myentrust.
	 *
	 * @param  int  $id
     * @return \Illuminate\View\View
	 */
	public function edit($id)
	{
		$msg = $this->checkStatusAndFlow($id);//已審核 and flow check
		if($msg != '')
			return redirect()->route(config('quickadmin.route').'.myentrust.index')->with('msg', $msg);
		//
		$userId = Auth::user()->id;
		$customer = DataQuery::arraySelectCustomer($userId);
		$entrust = Entrust::find($id);
		//
		$sd = $entrust->start_date;
		$entrust->txt_start_date = substr($sd, 0, 4).'-'.substr($sd, -4, 2).'-'.substr($sd, -2);
		$ed = $entrust->end_date;
		$entrust->txt_end_date = substr($ed, 0, 4).'-'.substr($ed, -4, 2).'-'.substr($ed, -2);
		$entrust->day_count = $this->countDays($sd, $ed);
		//
		$publishKindSelected = explode(',', $entrust->publish_kind);
		//
		$publishKind = config('admin.entrust.items');//委刊類別 array
	    $pay = config('admin.entrust.pay');//付款方式 array
	    $payStatus = config('admin.entrust.pay_status');//付款狀況 array
		//
		$entrustItems = EntrustItem::where('entrust_id', $id);
		$entrust->item_count = $entrustItems->count();//顯示目前有幾個委刊項
		if($entrustItems->count() > 0) {
			foreach ($entrustItems->get() as $entrustItem) {
				for ($no=1; $no <=10; $no++)
					if($no == $entrustItem->no) {
						$entrust->{'item_name_'.$no} = $entrustItem->name;
						$entrust->{'item_currency_'.$no} = number_format($entrustItem->cost);
						$entrust->{'item_cost_'.$no} = $entrustItem->cost;
					}
			}
			// $entrustItem = $entrustItems->first();
			
		}
		
		return view(config('quickadmin.route').'.myEntrust.edit', compact(array('userId', 'customer', 'entrust', 'publishKind', 'publishKindSelected', 'pay', 'payStatus')));
	}

	/**
	 * Update the specified myentrust in storage.
     * @param UpdateMyEntrustRequest|Request $request
     *
	 * @param  int  $id
	 */
	public function update($id, UpdateEntrustRequest $request)
	{
		$input = $this->getInput_publishKindToString($request);//委刊類別轉成字串
		//
		$entrust = Entrust::findOrFail($id);
		$entrust->update($input);
		//檢查委刊項
	    $this->checkEntrustItem($id, $request);
		return redirect()->route(config('quickadmin.route').'.myentrust.index');
	}

	/**
	 * Remove the specified myentrust from storage.
	 *
	 * @param  int  $id
	 */
	public function destroy($id)
	{
		Entrust::destroy($id);
		return redirect()->route(config('quickadmin.route').'.myentrust.index');
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
            Entrust::destroy($toDelete);
        } else {
            Entrust::whereNotNull('id')->delete();
        }

        return redirect()->route(config('quickadmin.route').'.myentrust.index');
    }

    //計算天數
    function countDays($strSD, $strED) {
		$sd = date_create($strSD);
		$ed = date_create($strED);
		$interval = date_diff($sd, $ed);
		return $interval->days + 1;
    }
    //委刊類別 array
    // function arrayPublishKind() {
    // 	$aryName = config('admin.entrust.items');
    // 	// $aryName = ['必PO TV網頁版','必PO TV手機版','必PO TV APP','中天影音平台專案','中天FB社群','快點TV網頁版','快點TV手機版','快點TV APP','其他專案'];
    // 	$aryPublishKind = array();
    // 	for ($i=0; $i < count($aryName); $i++)
    // 		$aryPublishKind[$i+1] = $aryName[$i];
    // 	return $aryPublishKind;
    // }
    //將input中的委刊類別 array 轉換成字串
    function getInput_publishKindToString($request) {
		$publishKind = $request->input('publish_kind');
		$publishKind = implode(',', $publishKind);

		$input = $request->except('publish_kind');
		//Assign the "mutated" publish_kind value to $input
		$input['publish_kind'] = $publishKind;
		return $input;
    }
    //委刊項的input逐一檢查，insert ot update
    function checkEntrustItem($id, $request) {
    	$aryItemDeleteNo = explode(',', $request->input('item_delete_list'));
    	//
	    for ($no = 1; $no <= 10; $no++) {
	    	$itemName = $request->input('item_name_'.$no);
	    	if($itemName != null) {
	    		$entrustItems = EntrustItem::where([
		    			['entrust_id', $id],
		    			['no', $no]
		    		]);
	    		$entrustItem;
		    	if($entrustItems->count() > 0) {
		    		$entrustItem = $entrustItems->first();
		    	} else {
		    		$entrustItem = new EntrustItem();
		    		$entrustItem->entrust_id = $id;
		    		$entrustItem->no = $no;
		    	}
		    	$entrustItem->name = $itemName;
	    		$itemCost = $request->input('item_cost_'.$no);
	    		if($itemCost != null)
		    		$entrustItem->cost = $itemCost;
		    	//
		    	$entrustItem->save();
	    	}
	    	//檢查item id array whhich wanna delete
	    	if(in_array($no, $aryItemDeleteNo))
				EntrustItem::where([
						['entrust_id', $id],
		    			['no', $no]
					])->delete();
	    }
    }
    //excel border
    function redrawBorder($sheet) {
    	for ($row=3; $row <= 30; $row++) { 
    		$sheet->cells('I'.$row, function($cells) {$cells->setBorder('none ', 'none', 'none', 'medium');});
    	}
    	$sheet->cells('A31:H31', function($cells) {$cells->setBorder('medium', 'none', 'none', 'none');});
    }
    //flow verifying
    function countEntrustFlowStatus($id, $status) {
    	return EntrustFlow::where([
				['entrust_id', $id],
				['status', $status]
			])->count();
    }
    // function deleteEntrustFlow($id) {
    // 	EntrustFlow::where('entrust_id', $id)->delete();
    // }
    //已審核 and flow check
    function checkStatusAndFlow($id) {
    	$msg = '';
    	//已審核 check
		$status = Entrust::find($id)->status;
		if($status == 3 || $status == 4)
			$msg = $this->alert_verified;
		//flow check
		if($this->countEntrustFlowStatus($id, 'verifying')) 
			$msg = $this->alert_verifying;

		return $msg;
    }

}

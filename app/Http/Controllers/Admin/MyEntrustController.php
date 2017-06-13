<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Redirect;
use Schema;
use App\Customer;
use App\DataQuery;
use App\Entrust;
use App\EntrustItem;
use App\Http\Requests\CreateEntrustRequest;
use App\Http\Requests\UpdateEntrustRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use stdClass;

class MyEntrustController extends Controller {

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
		return view(config('quickadmin.route').'.myEntrust.index', compact('entrust'));
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
	    $publishKind = $this->arrayPublishKind();
	    $pay = $this->arrayPay();
	    $payStatus = $this->arrayPayStatus();
	    //
	    return view(config('quickadmin.route').'.myEntrust.create', compact(array('customer', 'publishKind', 'pay', 'payStatus')));
	}

	/**
	 * Store a newly created myentrust in storage.
	 *
     * @param CreateMyEntrustRequest|Request $request
	 */
	public function store(CreateEntrustRequest $request)
	{
	    $entrustId = Entrust::create($request->all())->id;
	    //檢查委刊項
	    $this->checkEntrustItem($entrustId, $request);
		return redirect()->route(config('quickadmin.route').'.myentrust.index');
	}

	/**
	 * 送審 與 退回提案
	 */
	public function entrustGo($id)
	{
		$this->entrustPending($id, 2);
		return redirect()->route(config('quickadmin.route').'.myentrust.index');
	}
	public function entrustBack($id)
	{
		$this->entrustPending($id, 1);
		return redirect()->route(config('quickadmin.route').'.myentrust.index');
	}
	public function entrustCancel($id)
	{
		$this->entrustPending($id, 0);
		return redirect()->route(config('quickadmin.route').'.myentrust.index');
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
		$userId = Auth::user()->id;
		$customer = DataQuery::arraySelectCustomer($userId);
		$entrust = Entrust::find($id);
		//
		$sd = $entrust->start_date;
		$entrust->txt_start_date = substr($sd, 0, 4).'-'.substr($sd, -4, 2).'-'.substr($sd, -2);
		$ed = $entrust->end_date;
		$entrust->txt_end_date = substr($ed, 0, 4).'-'.substr($ed, -4, 2).'-'.substr($ed, -2);
		// $customerid = $entrust->customer_id;
		//
		$publishKind = $this->arrayPublishKind();
	    $pay = $this->arrayPay();
	    $payStatus = $this->arrayPayStatus();
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
		
		return view(config('quickadmin.route').'.myEntrust.edit', compact(array('customer', 'entrust', 'publishKind', 'pay', 'payStatus')));
	}

	/**
	 * Update the specified myentrust in storage.
     * @param UpdateMyEntrustRequest|Request $request
     *
	 * @param  int  $id
	 */
	public function update($id, UpdateEntrustRequest $request)
	{
		$entrust = Entrust::findOrFail($id);
		$entrust->update($request->all());
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

    //委刊類別下拉選單
    function arrayPublishKind() {
    	$aryName = ['請選擇','必PO TV網頁版','必PO TV手機版','必PO TV APP','中天影音平台專案','中天FB社群','快點TV網頁版','快點TV手機版','快點TV APP','其他專案'];
    	$aryPublishKind = array();
    	for ($i=0; $i < count($aryName); $i++)
    		$aryPublishKind[$i] = $aryName[$i];
    	return $aryPublishKind;
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
    //付款方式下拉選單
    function arrayPay() {
    	$aryPay = array();
    	$aryPay[0] = '請選擇';
    	$aryPay[1] = '匯款';
    	$aryPay[2] = '現金';
    	return $aryPay;
    }
    //付款狀況下拉選單
    function arrayPayStatus() {
    	$aryName = ['尚未開立發票', '發票開立中', '款項付清'];
    	$aryPayStatus = array();
    	for ($i=0; $i < count($aryName); $i++)
    		$aryPayStatus[$i] = $aryName[$i];
    	return $aryPayStatus;
    }

}

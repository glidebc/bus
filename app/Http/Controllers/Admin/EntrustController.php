<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Redirect;
use Schema;
use App\Contact;
use App\Customer;
use App\DataQuery;
use App\DataFunc;
use App\Entrust;
use App\EntrustFlow;
use App\EntrustItem;
use App\Http\Requests\CreateEntrustRequest;
use App\Http\Requests\UpdateEntrustRequest;
use App\Http\Requests\UpdateEntrustPassRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use stdClass;

class EntrustController extends Controller {

	/**
	 * 查看委刊單資料
	 */
	public function entrustVerify($id)
	{
		$entrust = $this->getEntrust($id);
		EntrustFlow::where('entrust_id', $entrust->id)->update(['status' => 'verifying']);//flow

		$kind = 'verify';
		return view(config('quickadmin.route').'.entrust.read', compact(array('entrust', 'kind')));
	}
	public function entrustRead($id)
	{
		$entrust = $this->getEntrust($id);
		$kind = 'read';
		return view(config('quickadmin.route').'.entrust.read', compact(array('entrust', 'kind')));
	}
	function getEntrust($id) {
		$entrust = new stdClass();
		$entrust->id = $id;
		$e = Entrust::find($id);

		// $userId = Auth::user()->id;
		// $aryCustomer = DataQuery::arrayCustomer($userId)->pluck('name','id');
		//$aryCustomer[$e->customer_id];

		$entrust->enum = $e->enum;
		$entrust->name = $e->name;
		$entrust->customer_name = Customer::find($e->customer_id)->name;
		$entrust->contact_name = '';
		$contact = Contact::find($e->contact_id);
		if(isset($contact))
			$entrust->contact_name = $contact->name;

		$sd = ''; $ed = '';
		if(!empty($e->start_date))
			$sd = substr($e->start_date, 0, 4).'-'.substr($e->start_date, -4, 2).'-'.substr($e->start_date, -2);
		if(!empty($e->end_date))
			$ed = substr($e->end_date, 0, 4).'-'.substr($e->end_date, -4, 2).'-'.substr($e->end_date, -2);
		$days = (new DataFunc)->countDays($e->start_date, $e->end_date);//天數

		$entrust->txt_start_date = $sd;
		$entrust->txt_end_date = $ed;
		$entrust->day_count = $days;
		
		$publishKindSelected = explode(',', $e->publish_kind);
		$aryPublishKind = [];
		foreach ($publishKindSelected as $kindId) {
			$aryPublishKind[] = config('admin.entrust.items')[$kindId];
		}
		$entrust->publish_kind = $aryPublishKind;

		$entrustItems = EntrustItem::where('entrust_id', $id)->orderBy('no')->get();
		$entrust->item_count = $entrustItems->count();//委刊項數量
		$aryItem = []; $aryItemCost = []; $count = 0;
		foreach ($entrustItems as $item) {
			$aryItem[] = $item->name;
			$aryItemCost[] = number_format($item->cost);
			$count += $item->cost;
		}
		$entrust->item = $aryItem;
		$entrust->itemCost = $aryItemCost;
		$entrust->count = number_format($count);

		$entrust->pay = config('admin.entrust.pay')[$e->pay];
		$entrust->pay_status = config('admin.entrust.pay_status')[$e->pay_status];
		$entrust->invoice_date = $e->invoice_date;
		$entrust->invoice_num = $e->invoice_num;
		$entrust->note = $e->note;

		return $entrust;
	}

	/**
	 * 審核通過後再編輯的項目
	 */
	public function editAfterPass($id)
	{
		$entrust = $this->getEntrust($id);
		//
		$invDate = $entrust->invoice_date;
		if(!empty($invDate))
			$entrust->txt_invoice_date = substr($invDate, 0, 4).'-'.substr($invDate, -4, 2).'-'.substr($invDate, -2);

		return view(config('quickadmin.route').'.entrust.edit_after_pass', compact('entrust'));
	}
	public function updateAfterPass($id, UpdateEntrustPassRequest $request)
	{
		$entrust = Entrust::findOrFail($id);
		$entrust->update($request->all());
		return redirect()->route(config('quickadmin.route').'.myentrust.index');
	}

	// private $customer;

	// public function __construct()
 //    {
 //        $this->customer = Customer::where('deleted_at', null)->orderBy('name')->pluck('name','id');
 //    }

	/**
	 * Display a listing of entrust
	 *
     * @param Request $request
     *
     * @return \Illuminate\View\View
	 */
	public function index(Request $request)
    {
        $userId = Auth::user()->id;
        $entrust = DataQuery::collectionOfEntrustByUser($userId)->get();
        // $entrust = Entrust::where('owner_user', $userid)->orderBy('created_at')->get();

		return view('admin.entrust.index', compact('entrust'));
	}

	/**
	 * Show the form for creating a new entrust
	 *
     * @return \Illuminate\View\View
	 */
	public function create()
	{
	    $customer = DataQuery::arraySelectCustomer();
	    return view('admin.entrust.create', compact('customer'));
	}

	/**
	 * Store a newly created entrust in storage.
	 *
     * @param CreateEntrustRequest|Request $request
	 */
	public function store(CreateEntrustRequest $request)
	{
	    
		Entrust::create($request->all());

		return redirect()->route(config('quickadmin.route').'.entrust.index');
	}

	/**
	 * Show the form for editing the specified entrust.
	 *
	 * @param  int  $id
     * @return \Illuminate\View\View
	 */
	public function edit($id)
	{
		$customer = DataQuery::arraySelectCustomer();
		$entrust = Entrust::find($id);
		$customerid = $entrust->customer_id;
		return view('admin.entrust.edit', compact(array('customer','entrust','customerid')));
	}

	/**
	 * Update the specified entrust in storage.
     * @param UpdateEntrustRequest|Request $request
     *
	 * @param  int  $id
	 */
	public function update($id, UpdateEntrustRequest $request)
	{
		$entrust = Entrust::findOrFail($id);

        

		$entrust->update($request->all());

		return redirect()->route(config('quickadmin.route').'.entrust.index');
	}

	/**
	 * Remove the specified entrust from storage.
	 *
	 * @param  int  $id
	 */
	public function destroy($id)
	{
		Entrust::destroy($id);

		return redirect()->route(config('quickadmin.route').'.entrust.index');
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

        return redirect()->route(config('quickadmin.route').'.entrust.index');
    }

}

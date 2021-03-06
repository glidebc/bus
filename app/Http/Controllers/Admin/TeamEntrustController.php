<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Contact;
use App\DataQuery;
use App\Dept;
use App\Publish;
use App\Publishuser;
use App\User;
use Illuminate\Support\Facades\Auth;
use DateTime;

class TeamEntrustController extends Controller {

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
    	$entrusts = DataQuery::collectionOfTeamEntrust($userId);
    	foreach ($entrusts as $entrust) {
            //聯絡窗口button
            $contact = Contact::withTrashed()->find($entrust->contact_id);
            if(isset($contact))
                $entrust->contact_name = $contact->name;
            //總走期
            if(strlen($entrust->start_date) > 0) {
                $dateStart = substr($entrust->start_date, 0, 4).'-'.substr($entrust->start_date, -4, 2).'-'.substr($entrust->start_date, -2);
                $dateEnd = '';
                if(strlen($entrust->end_date) >= 8) {
                    $dateEnd = '～'.substr($entrust->end_date, 0, 4).'-'.substr($entrust->end_date, -4, 2).'-'.substr($entrust->end_date, -2);
                }
                $entrust->duration = $dateStart.$dateEnd;
            }
            //發票日期
            $entrust->invoice_date_text = '';
            if(strlen($entrust->invoice_date) == 8)
                $entrust->invoice_date_text = date_create($entrust->invoice_date)->format('Y-m-d');
            //
    		$publishuser = Publishuser::where('user_id', $entrust->owner_user)->first();
            $dept = Dept::find($publishuser->dept_id);
            $entrust->user_dept = empty($dept) ? '' : $dept->name;
            //owner user name
            $ownerUser = User::find($publishuser->user_id);
            if(isset($ownerUser))
                $entrust->user_name = $ownerUser->name;

            // $entrust->user_name = User::find($publishuser->user_id)->name;
            
            $entrust->status_name = config('admin.entrust.status')[$entrust->status];

            $status_publish;
            $publishs = Publish::where('entrust_id', $entrust->id);
            if($publishs->count() > 0) {
                // $status_publish = '待執行';

                $now = date_create(date("Y-m-d")); //date("Y-m-d H:i:s")
                // $status_publish = 'now='.$now->format('Y-m-d H:i:s');
                $listPublish = $publishs->orderBy('date')->get();
                $typePublish = 0;
                foreach ($listPublish as $publish) {
                    
                    // $strDate = substr($publish->date, 0, 4).'-'.substr($publish->date, -4, 2).'-'.substr($publish->date, -2);
                    $pd = date_create($publish->date);
                    $ed = date_create($publish->date)->modify('+'.($publish->days - 1).' day');
                    // $status_publish = $publish->id.', '.$pd->format('Y-m-d H:i:s');

                    //只要有一個委刊還在執行就顯示 執行中
                    if($pd <= $now && $now <= $ed) {
                        $status_publish = config('admin.publish.status')[1];//委刊執行中
                        // $typePublish = 1;
                        break;
                    }
                    if(array_key_exists($entrust->status, config('admin.publish.status')))
                        $status_publish = config('admin.publish.status')[$entrust->status];//已預約 | 待執行
                    //else if($ed < $now) {
                        // $typePublish = 2;//委刊結束 (結束時間 < now)
                    // }
                    // if($entrust->status == 2)
                    //     $status_publish = '已預約';
                    // else if($entrust->status == 3)
                    //     $status_publish = '待執行';
                }
                // if($typePublish) {
                //     $status_publish = $typePublish == 1 ? '執行中' : '';//手動結案
                // } else {
                    
                // }
                // $status_publish = $isPublishing ? '執行中' : '待執行';
            } else {
                $status_publish = '尚未預約';
            }
            //審核中 與 審核通過 才顯示執行狀態
            if($entrust->status == 2 || $entrust->status == 3)
                $entrust->status_publish = $status_publish;

        }
		// return view('admin.publishverify.index', compact('entrusts'));

		return view('admin.teamEntrust.index', compact('entrusts'));
	}

}
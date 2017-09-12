<?php

namespace App;

use App\Contact;
use App\Customer;
use App\CustomerAgent;
use App\CustomerUser;
use App\Dept;
use App\Entrust;
use App\Publish;
use App\Publishposition;
use App\Publishsite;
use App\Publishuser;
use App\Team;
use App\User;
use DB;
use Illuminate\Support\Collection;

class DataQuery {
    /**
    * 資料庫查詢
    * 
    */
    
    //客戶collection
    // static function collectionOfCustomerWithAgent()
    // {
    //     // SELECT CASE WHEN agent.id IS NULL THEN customer.name ELSE CONCAT(agent.name,"-",customer.name) END AS agent_customer,customer.id
    //     // FROM customer
    //     // LEFT JOIN agent ON agent.id=customer.agent_id
    //     // WHERE customer.deleted_at IS NULL;
        
    //     $customer = Customer::leftJoin('agent', function ($join) {
    //                             $join->on('agent.id', '=', 'customer.agent_id')
    //                                  ->whereRaw('customer.deleted_at IS NULL');
    //                         })
    //                         // ->selectRaw('CASE WHEN agent.id IS NULL THEN customer.name ELSE CONCAT(agent.name,"-",customer.name) END AS agent_customer,customer.*');
    //                         ->selectRaw('agent.name AS agent_name,customer.*');
    //     return $customer;
    // }

    //業務管理-我的代理商
    static function arrayAgent($userId)
    {
        return self::arrayAgentOrCustomer($userId, true);
    }
    //業務管理-我的客戶
    static function arrayCustomer($userId)
    {
        return self::arrayAgentOrCustomer($userId, false);
    }
    static function arrayAgentOrCustomer($userId, $isAgent)
    {
        $customers = self::collectionCustomer($userId, $isAgent);
        // $customers = $customers->selectRaw('*, "" AS agent_name')->orderBy('created_at', 'desc')->get();
        $customers = $customers->orderBy('created_at', 'desc')->get();

        // $customers = Customer::withTrashed()
        //                     ->where($customerCondition)
        //                     ->selectRaw('*, "" AS agent_name')
        //                     ->orderBy('created_at', 'desc')
        //                     ->get();
        foreach ($customers as $customer) {
            //代理商名稱
            $customerAgent = CustomerAgent::where([
                    ['customer_id', $customer->id],
                    ['status', 1]
                ])->get();
            if($customerAgent->count() > 0)
                $customer->agent_name = Customer::withTrashed()->find($customerAgent->first()->agent_id)->name;
            //聯絡人
            if($customer->contact_id > 0) {
                $customer->contact_name = Contact::find($customer->contact_id)->name;
            }
            //自己建立的才可以修改
            $customer->owner = false;
            if($userId == $customer->owner_user)
                $customer->owner = true;
        }
        
        return $customers;
    }

    //委刊單編號，業務管理-我的委刊單-新增
    static function genEntrustNumber()
    {
        $ymd = date('Ymd', strtotime('today'));
        $entrust = Entrust::where('enum', 'like', $ymd . '%')
                        ->orderBy('enum', 'desc');
        $enum = $ymd . '01';
        if($entrust->get()->count() > 0)
            $enum = strval(intval($entrust->first()->enum) + 1);
        return $enum;//$ymd.($count + 1 < 10 ? '0'.($count + 1) : $count + 1);
    }

    //未使用
    static function arraySelectCustomer($userId)
    {
        $customers = self::collectionCustomer($userId, false);
        $customers = $customers->pluck('name','id')->prepend('請選擇', 0);

        $customer = Customer::where([
                                ['customer.is_agent', false],
                                ['customer.owner_user', $userId]
                            ])
                            ->pluck('name','id')
                            ->prepend('請選擇', 0);

        // $customer = Customer::leftJoin('agent', function ($join) use ($userId) {
        //                         $join->on('agent.id', '=', 'customer.agent_id');
        //                     })
        //                     ->where([
        //                         ['customer.is_agent', false],
        //                         ['customer.owner_user', $userId]
        //                     ])
        //                     ->selectRaw('CASE WHEN agent.id IS NULL THEN customer.name ELSE CONCAT(agent.name,"-",customer.name) END AS agent_customer,customer.*')
        //                     ->pluck('name','id')
        //                     ->prepend('請選擇', 0);
        // $customer = self::collectionOfCustomerWithAgent()->pluck('name','id');
        return $customers;
    }

    static function collectionCustomer($userId, $isAgent)
    {
        $aryCustomerId = CustomerUser::where('user_id', $userId)->pluck('customer_id');
        $customers = Customer::withTrashed()
                            ->where('is_agent', $isAgent)
                            ->where(function ($query) use ($userId, $aryCustomerId) {
                                $query->where('owner_user', $userId)
                                      ->orWhereIn('id', $aryCustomerId);
                            });
        return $customers;
    }
    static function collectionCustomer_OLD($userId, $isAgent)
    {
        $customers;
        $countCustomer = Customer::where([
                ['owner_user', $userId],
                ['is_agent', $isAgent]
            ])->count();
        if($countCustomer > 0) {
            //企劃
            $customerCondition = array();
            array_push($customerCondition, array('is_agent', $isAgent));
            if(!is_null($userId))
                array_push($customerCondition, array('owner_user', $userId));

            $customers = Customer::withTrashed()->where($customerCondition);
        } else {
            //一般業務
            $aryCustomerId = CustomerUser::where('user_id', $userId)->pluck('customer_id');
            $customers = Customer::where('is_agent', $isAgent)->whereIn('id', $aryCustomerId);
        }
        return $customers;
    }

    //團隊管理-管理代理商
    static function arrayTeamAgent($userId)
    {
        return self::arrayTeamAgentOrCustomer($userId, true);
    }
    //團隊管理-管理客戶
    static function arrayTeamCustomer($userId)
    {
        return self::arrayTeamAgentOrCustomer($userId, false);
    }
    static function arrayTeamAgentOrCustomer($userId, $isAgent)
    {
        //team id and dept id
        $publishuser = Publishuser::where('user_id', $userId)->get()->first();
        $arrayUserId = Publishuser::where([
                ['dept_id', $publishuser->dept_id],
                ['team_id', $publishuser->team_id]
            ])->pluck('user_id');
        $customers = Customer::withTrashed()
                            ->where('is_agent', $isAgent)
                            ->whereIn('owner_user', $arrayUserId)
                            ->orderBy('created_at', 'desc')->get();

        foreach ($customers as $customer) {
            //代理商名稱
            $customerAgent = CustomerAgent::where([
                    ['customer_id', $customer->id],
                    ['status', 1]
                ])->get();
            if($customerAgent->count() > 0)
                $customer->agent_name = Customer::withTrashed()->find($customerAgent->first()->agent_id)->name;
            //聯絡人
            if($customer->contact_id > 0) {
                $contact = Contact::find($customer->contact_id);
                if(isset($contact))
                    $customer->contact_name = $contact->name;
            }
            //owner user name
            $ownerUser = User::find($customer->owner_user);
            if(isset($ownerUser))
                $customer->owner_user_name = $ownerUser->name;
            //共用user的數量
            $arrayUserId = CustomerUser::where('customer_id', $customer->id)->pluck('user_id');
            $arrayUserName = User::whereIn('id', $arrayUserId)->pluck('name');
            $stringUserName = "";
            foreach ($arrayUserName as $name) {
                if(!empty($stringUserName))
                    $stringUserName .= "\n";
                $stringUserName .= $name;
            }
            $customer->user_names = $stringUserName;
            //(myAgent)自己建立的才可以修改
            // $customer->owner = false;
            // if($userId == $customer->owner_user)
            //     $customer->owner = true;
        }

        return $customers;

        // $customers = self::collectionCustomer($userId, $isAgent);
        // // $customers = $customers->selectRaw('*, "" AS agent_name')->orderBy('created_at', 'desc')->get();
        // $customers = $customers->orderBy('created_at', 'desc')->get();

        // // $customers = Customer::withTrashed()
        // //                     ->where($customerCondition)
        // //                     ->selectRaw('*, "" AS agent_name')
        // //                     ->orderBy('created_at', 'desc')
        // //                     ->get();
        // foreach ($customers as $customer) {
        //     //代理商名稱
        //     $customerAgent = CustomerAgent::where([
        //             ['customer_id', $customer->id],
        //             ['status', 1]
        //         ])->get();
        //     if($customerAgent->count() > 0)
        //         $customer->agent_name = Customer::withTrashed()->find($customerAgent->first()->agent_id)->name;
        //     //(teamAgent)共用user的數量
        //     $arrayUserId = CustomerUser::where('customer_id', $customer->id)->pluck('user_id');
        //     $arrayUserName = User::whereIn('id', $arrayUserId)->pluck('name');
        //     $stringUserName = "";
        //     foreach ($arrayUserName as $name) {
        //         if(!empty($stringUserName))
        //             $stringUserName .= "\n";
        //         $stringUserName .= $name;
        //     }
        //     $customer->user_names = $stringUserName;
        //     //(myAgent)自己建立的才可以修改
        //     $customer->owner = false;
        //     if($userId == $customer->owner_user)
        //         $customer->owner = true;
        // }
        
        
    }

    //業務管理-我的客戶, 團隊管理-管理客戶
    static function arraySelectAgent($userId, $isGetAll)
    {
        $aryAgentId = CustomerUser::where('user_id', $userId)->pluck('customer_id');
        $customer;
        if ($isGetAll)
            $customer = Customer::withTrashed()->where('is_agent', true);
        else
            $customer = Customer::where('is_agent', true);
        $agent = $customer->where(function ($query) use ($userId, $aryAgentId) {
                $query->where('owner_user', $userId)
                      ->orWhereIn('id', $aryAgentId);
            })
            ->select('name','id')
            ->orderBy('name')->pluck('name','id')->prepend('無代理商', 0);

        // $agent = self::collectionCustomer($userId, false);
        // $agent = Customer::where('is_agent', $isAgent)
        //                 ->where(function ($query) use ($userId, $aryCustomerId) {
        //                     $query->where('owner_user', $userId)
        //                           ->orWhereIn('id', $aryCustomerId);
        //                 });
        //                     where([
        //                         ['customer.is_agent', true],
        //                         ['customer.owner_user', $userId]
        //                     ])
        //                     ->select('name','id')
        //                     ->orderBy('name')->pluck('name','id')->prepend('無代理商', 0);
        return $agent;
    }
    //業務管理-我的客戶-修改客戶-顯示代理商名稱
    // static function myAgentName($customerId)
    // {
    //     $agentName = '無代理商';
    //     $customerAgent = CustomerAgent::where([
    //             ['customer_id', $customerId],
    //             ['status', 1]
    //         ]);
    //     if($customerAgent->count() > 0) {
    //         $agentId = $customerAgent->first()->agent_id;
    //         $agentName = Customer::find($agentId)->name;
    //     }
    //     return $agentName;
    // }

    //業務管理-我的委刊單-新增與修改, 我的聯絡人-新增與修改
    static function arraySelectAgentAndCustomer($userId)
    {
        $aryCustomerId = CustomerUser::where('user_id', $userId)->pluck('customer_id');
        $customer = Customer::where(function ($query) use ($userId, $aryCustomerId) {
                $query->where('owner_user', $userId)
                      ->orWhereIn('id', $aryCustomerId);
            })
            ->selectRaw('CASE WHEN is_agent THEN CONCAT("代理商 - ",`name`) ELSE CONCAT("客戶 - ",`name`) END AS "name", id')
            ->orderBy('name')->pluck('name','id')->prepend('無', 0);
        return $customer;
    }
    //
    // static function arraySelectAgentAndCustomer_refCustomer($userId, &$customer) {
    //     $aryCustomerId = CustomerUser::where('user_id', $userId)->pluck('customer_id');
    //     $customer = Customer::where(function ($query) use ($userId, $aryCustomerId) {
    //             $query->where('owner_user', $userId)
    //                   ->orWhereIn('id', $aryCustomerId);
    //         });
    //     $aryCustomerOption = $customer->selectRaw('CASE WHEN is_agent THEN CONCAT("代理商 - ",`name`) ELSE CONCAT("客戶 - ",`name`) END AS "name", id')
    //         ->orderBy('name')
    //         ->pluck('name','id')
    //         ->prepend('請選擇', 0);
    //     return $aryCustomerOption;
    // }
    // static function jsonSelectContact($customers) {  
    // }

    //業務管理-我的聯絡人
    static function collectionOfContact($userId)
    {
        $contact = Contact::leftJoin('customer', function ($join) use ($userId) {
                $join->on('customer.id', '=', 'contact.customer_id')
                    ->where('contact.owner_user', $userId);
            })
            ->selectRaw('customer.name AS customer_name, contact.*')
            ->orderBy('contact.created_at', 'desc')
            ->get();
        return $contact;
    }

    //業務管理-我的代理商, 我的客戶
    static function arraySelectContactByCustomer($userId, $customerId)
    {
        // $contact;
        // if($customerId == 0) {
        $contactCondition = array();
        array_push($contactCondition, array('owner_user', $userId));
        array_push($contactCondition, array('customer_id', $customerId));
        $contact = Contact::where($contactCondition);
        // } else {
            // $contact = Contact::where('owner_user', $userId)
            //     ->where(function ($query) use ($customerId) {
            //         $query->where('customer_id', 0)
            //               ->orWhere('customer_id', $customerId);
            //     });
        // }
        $contact = $contact->orderBy('name')->pluck('name','id')->prepend('請選擇', 0);
        return $contact;
    }

    //團隊管理-管理代理商, 管理客戶
    static function arraySelectContactByTeamCustomer($customerId)
    {
        $contactCondition = array();
        array_push($contactCondition, array('customer_id', $customerId));
        $contact = Contact::where($contactCondition);
        $contact = $contact->orderBy('name')->pluck('name','id')->prepend('請選擇', 0);
        return $contact;
    }

    //業務管理-我的委刊單
    static function arraySelectContactByEntrust($userId)
    {

    }

    static function collectionPublishUser($userId)
    {
        if($userId == 0) {
            $publishuser = Publishuser::leftJoin('users', function ($join) {
                                $join->on('users.id', '=', 'publish_user.user_id');
                            })
                            ->leftJoin('dept', function ($join) {
                                $join->on('dept.id', '=', 'publish_user.dept_id');
                            })
                            ->leftJoin('team', function ($join) {
                                $join->on('team.id', '=', 'publish_user.team_id');
                            })
                            ->selectRaw('users.name AS user_name, dept.name AS dept_name, team.name AS team_name,publish_user.*')
                            ->orderByRaw('dept.id, team.id, publish_user.created_at desc');
        } else {
            $publishuser = Publishuser::leftJoin('users', function ($join) use ($userId) {
                                $join->on('users.id', '=', 'publish_user.user_id')
                                     ->where('publish_user.user_id', $userId);
                            })
                            ->selectRaw('users.name AS user_name,publish_user.*');
        }
        return $publishuser;
    }
    
    static function arraySelectUser()
    {
        $user = User::select('name','id')->orderBy('name')->pluck('name','id')->prepend('請選擇', '');
        return $user;
    }

    static function arraySelectDept()
    {
        return Dept::where('status', true)->pluck('name','id')->prepend('無', '');
    }

    static function arraySelectTeam()
    {
        return Team::where('status', true)->pluck('name','id')->prepend('無', '');
    }

    static function collectionOfTeamUser($userId, $isNotInThisUser)
    {
        $publishuser = Publishuser::where('user_id', $userId);
        $aryDeptId = $publishuser->pluck('dept_id');

        $teamUser;
        if($isNotInThisUser)
            $teamUser = Publishuser::whereNotIn('user_id', [$userId])->whereIn('dept_id', $aryDeptId);
        else
            $teamUser = Publishuser::whereIn('dept_id', $aryDeptId);

        if(isset($publishuser->first()->team_id)) {
            $aryTeamId = $publishuser->pluck('team_id');
            $teamUser = $teamUser->whereIn('team_id', $aryTeamId);
        }

        return $teamUser;
    }

    //團隊管理-團隊委刊單
    static function collectionOfTeamEntrust($userId)
    {
        $teamUser = self::collectionOfTeamUser($userId, false);
        // $publishuser = Publishuser::where('user_id', $userId);
        // $aryDeptId = $publishuser->pluck('dept_id');
        // $aryTeamId = $publishuser->pluck('team_id');
        // $teamUser;// = Entrust::whereIn('dept_id', $aryDeptId);
        // // if(empty($publishuser->team_id)) {
        // if($aryTeamId->isEmpty())
        //     $teamUser = Publishuser::whereIn('dept_id', $aryDeptId);
        // else
        //     $teamUser = Publishuser::whereIn('dept_id', $aryDeptId)->whereIn('team_id', $aryTeamId);

        $teamEntrust = Entrust::whereIn('entrust.owner_user', $teamUser->pluck('user_id'));

        $entrusts = $teamEntrust->join('customer', function ($join) {
                                $join->on('customer.id', 'entrust.customer_id');
                            })
                            // ->join('publish_user', function ($join) {
                            //     $join->on('publish_user.user_id', 'entrust.owner_user')
                            //          ->whereRaw('publish_user.deleted_at IS NULL');
                            // })
                            ->selectRaw('customer.name AS customer_name, entrust.*')
                            ->orderBy('entrust.created_at', 'desc')
                            ->get();
        // $entrusts = $teamEntrust->join('customer', function ($join) {
        //                         $join->on('customer.id', 'entrust.customer_id');
        //                     })
        //                     ->join('publish_user', function ($join) {
        //                         $join->on('publish_user.user_id', 'entrust.owner_user')
        //                              ->whereRaw('publish_user.deleted_at IS NULL');
        //                     })
        //                     ->selectRaw('customer.name AS customer_name, entrust.*')
        //                     ->orderBy('entrust.created_at', 'desc')
        //                     ->get();
        return $entrusts;
    }

    //團隊管理-團隊代理商
    static function arrayTeamUser($userId)
    {
        $teamUser = self::collectionOfTeamUser($userId, true);
        // if($aryTeamId->isEmpty())
        //     $teamUser = Publishuser::whereNotIn('user_id', [$userId])->whereIn('dept_id', $aryDeptId);
        // else
        //     $teamUser = Publishuser::whereNotIn('user_id', [$userId])->whereIn('dept_id', $aryDeptId)->whereIn('team_id', $aryTeamId);

        return User::whereIn('id', $teamUser->pluck('user_id'))->orderBy('name')->pluck('name', 'id');
    }
    
    static function collectionOfEntrustByUser($userId)
    {
        // $entrust = Entrust::join('customer', function ($join) use ($publishUserId) {
        //                         $join->on('entrust.customer_id', '=', 'customer.id')
        //                              ->whereRaw('entrust.owner_user='.$publishUserId.' AND entrust.deleted_at IS NULL');
        //                     })->leftJoin('agent', function ($join) {
        //                         $join->on('customer.agent_id', '=', 'agent.id');
        //                     })
        //                     ->selectRaw('CASE WHEN agent.id IS NULL THEN customer.name ELSE CONCAT(agent.name,"-",customer.name) END AS agent_customer,entrust.*')
        //                     ->orderBy('entrust.created_at', 'desc');
        $entrust = Entrust::join('customer', function ($join) use ($userId) {
                                $join->on('entrust.customer_id', '=', 'customer.id')
                                     ->where('entrust.owner_user', $userId);
                            })
                            ->selectRaw('customer.name AS customer_name,entrust.*')
                            ->orderBy('entrust.created_at', 'desc');
        return $entrust;
    }

    static function arraySelectEntrust($userId)
    {
        $entrust = Entrust::where('owner_user', $userId)
                            ->whereIn('status', array(2, 3))
                            ->orderBy('created_at', 'desc')->pluck('name', 'id')->prepend('請選擇', 0);
        return $entrust;
    }
    
    static function arraySelectTurnsCount()
    {
        $collect = collect();
        for($c = 1; $c <= 5; $c++)
            $collect->push(array('count' => $c, 'id' => $c));
        return $collect->pluck('count','id');
    }

    static function collectionOfEntrustByID($entrustId)
    {
        $entrust = Entrust::join('customer', function ($join) use ($entrustId) {
                                $join->on('entrust.customer_id', '=', 'customer.id')
                                     ->where('entrust.id', $entrustId);
                            })
                            ->selectRaw('customer.name AS agent_customer,entrust.name,entrust.start_date,entrust.end_date,entrust.owner_user');
                            // ->orderBy('entrust.created_at', 'desc');
        return $entrust;
    }

    static function arrayPublishpositionWithSite()
    {
        $publishposition = Publishposition::leftJoin('publish_site', function ($join) {
                                $join->on('publish_site.id', '=', 'publish_position.site_id');
                                     // ->whereRaw('publish_position.deleted_at IS NULL');
                            })
                            ->selectRaw('publish_site.name AS site_name,publish_position.*')
                            ->get();
        return $publishposition;
    }

    static function arraySelectSite()
    {
        $site = Publishsite::select('name','id')->where('deleted_at', null)->orderBy('id')->pluck('name','id');
        return $site;
    }

    static function arrayPublishList()
    {
        $sitePosition = DB::raw('(SELECT A.`id`, A.`name`, B.`name` AS site_name FROM `publish_position` A LEFT JOIN `publish_site` B ON B.`id`=A.`site_id`) as SP');

        $publish = Publish::join('entrust', function ($join) {
                                $join->on('entrust.id', '=', 'publish.entrust_id');
                                // ->whereRaw('entrust.deleted_at IS NULL');
                            })->join($sitePosition, function ($join) {
                                $join->on('SP.id', '=', 'publish.publish_position_id');
                            })
                            ->selectRaw('entrust.name AS entrust_name, SP.site_name, SP.name AS position_name, publish.id, DATE_FORMAT(DATE(CONCAT_WS("/", SUBSTRING(publish.date,1,4), SUBSTRING(publish.date,5,2), SUBSTRING(publish.date,7,2))),"%Y/%c/%e") AS date, publish.status, CASE publish.status WHEN 1 THEN "Ｏ" WHEN 2 THEN "Ｘ" ELSE "" END AS status_name')
                            ->orderBy('publish.created_at', 'desc')
                            ->get();
        return $publish;
    }

    static function collectionOfEntrustVerify()
    {
        // $entrustVerify = Entrust::where('status', 2);
        $entrusts = Entrust::where('status', 2)
                            ->join('customer', function ($join) {
                                $join->on('customer.id', 'entrust.customer_id');
                            })
                            ->join('publish_user', function ($join) {
                                $join->on('publish_user.user_id', 'entrust.owner_user')
                                     ->whereRaw('publish_user.deleted_at IS NULL');
                            })
                            ->selectRaw('customer.name AS customer_name, entrust.*')
                            ->orderBy('entrust.updated_at', 'desc')
                            ->get();
        return $entrusts;
        // 
        //     // $positionName = Publishposition::find($publish->publish_position_id)->first()->name;
        //     // $publish->update(['position_name' => $positionName]);
        //     $publish->position_name = 'glide';//$positionName;
        //     $publish->save();
        // }
        // $publishCollection->each(function ($publish) {
        //     $positionName = Publishposition::find($publish->publish_position_id)->first()->name;
        //     $publish->update(['position_name' => $positionName]);
        // });


        // $arrayPositionID = $publish->pluck('publish_position_id');
        // foreach ($arrayPositionID as $positionID) {
        //     $positionName = Publishposition::leftJoin('publish_site', function ($join) {
        //                     $join->on('publish_site.id', '=', 'publish_position.site_id');
        //                 })->selectRaw('publish_site.name AS site_name,publish_position.*')
        //                 ->find($positionID)->name;
        //     $publish->find($positionID)->position_name = $positionName;
        // }

        // $publish = $query->addSelect(DB::raw(''))
        
    }
}
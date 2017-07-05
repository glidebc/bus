<?php

namespace App;

use App\Agent;
use App\Customer;
use App\CustomerAgent;
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

    static function arrayAgent($userId)
    {
        return self::arrayAgentOrCustomer($userId, true);
    }
    
    static function arrayCustomer($userId)
    {
        return self::arrayAgentOrCustomer($userId, false);
    }

    static function arrayAgentOrCustomer($userId, $isAgent)
    {
        $customerCondition = array();
        array_push($customerCondition, array('is_agent', $isAgent));
        if(!is_null($userId))
            array_push($customerCondition, array('owner_user', $userId));

        $customers = Customer::withTrashed()
                            ->where($customerCondition)
                            ->selectRaw('*, "" AS agent_name')
                            ->orderBy('created_at', 'desc')
                            ->get();
        foreach ($customers as $customer) {
            $customerAgent = CustomerAgent::where([
                    ['customer_id', $customer->id],
                    ['status', 1]
                ]);
            if($customerAgent->count() > 0)
                $customer->agent_name = Customer::withTrashed()->find($customerAgent->first()->agent_id)->name;
        }
        return $customers;
    }
    
    static function arraySelectCustomer($userId)
    {
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
        return $customer;
    }
    
    static function arraySelectAgent($userId)
    {
        $agent = Customer::where([
                                ['customer.is_agent', true],
                                ['customer.owner_user', $userId]
                            ])
                            ->select('name','id')
                            ->orderBy('name')->pluck('name','id')->prepend('無代理商', 0);
        return $agent;
    }

    static function collectionPublishUser($userId)
    {
        if($userId == 0) {
            $publishuser = Publishuser::join('users', function ($join) {
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
            $publishuser = Publishuser::join('users', function ($join) use ($userId) {
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
                            ->selectRaw('customer.name AS agent_customer,entrust.name,entrust.owner_user');
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

    static function arraySelectDept()
    {
        return Dept::where('status', true)->pluck('name','id')->prepend('無', '');
    }

    static function arraySelectTeam()
    {
        return Team::where('status', true)->pluck('name','id')->prepend('無', '');
    }

    static function collectionOfTeamEntrust($userId)
    {
        $publishuser = Publishuser::where('user_id', $userId);
        $aryDeptId = $publishuser->pluck('dept_id');
        $aryTeamId = $publishuser->pluck('team_id');
        $teamEntrust = Entrust::whereIn('dept_id', $aryDeptId)->whereIn('team_id', $aryTeamId);

        $entrusts = $teamEntrust->join('customer', function ($join) {
                                $join->on('customer.id', 'entrust.customer_id');
                            })
                            ->join('publish_user', function ($join) {
                                $join->on('publish_user.user_id', 'entrust.owner_user')
                                     ->whereRaw('publish_user.deleted_at IS NULL');
                            })
                            ->selectRaw('customer.name AS customer_name, entrust.*')
                            ->orderBy('entrust.created_at', 'desc')
                            ->get();
        return $entrusts;
    }
    
}
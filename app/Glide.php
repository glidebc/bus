<?php

namespace App;

use App\Agent;
use App\Customer;
use App\Entrust;
use App\Publish;
use App\Publishposition;
use App\Publishsite;
use App\Publishuser;
use App\User;
use DB;
use Illuminate\Support\Collection;
use stdClass;

class Glide {
    /**
    * 公用函數 by清志
    *
    * 
    */

    static function taxNumberValid($number) {
        $arrayNum = str_split($number);
        if(count($arrayNum) == 8) {
            $aryMultiplier = array(1, 2, 1, 2, 1, 2, 4, 1);
            $sum = 0;
            foreach ($arrayNum as $i => $char) {
                $product = intval($char) * $aryMultiplier[$i]; //乘積
                $sum += self::tenDigitsPlusDigits($product); //乘積後如果是兩位數, 十位數跟個位數相加後, 再累加到 $sum
            }
            if($sum % 10 == 0)
                return true;
            else if($arrayNum[6] == 7 && ($sum + 1) % 10 ==0)
                return true;
            else
                return false;
        } else {
            return true;
        }
    }

    static private function tenDigitsPlusDigits($n){
        if($n > 9) {
            $aryN = str_split(strval($n)); // [十位數, 個位數]
            $n = intval($aryN[0]) + intval($aryN[1]); //十位數 + 個位數
        }
        return $n;
    }
    
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
    
    static function arrayCustomer()
    {
        $customer = Customer::withTrashed()
                            ->leftJoin('agent', function ($join) {
                                $join->on('agent.id', '=', 'customer.agent_id');
                            })
                            ->selectRaw('agent.name AS agent_name,customer.*')
                            ->orderBy('customer.created_at', 'desc')
                            ->get();
        // $customer = self::collectionOfCustomerWithAgent()->get();
        return $customer;
    }
    
    static function arraySelectCustomer()
    {
        $customer = Customer::leftJoin('agent', function ($join) {
                                $join->on('agent.id', '=', 'customer.agent_id');
                                     // ->whereRaw('customer.deleted_at IS NULL');
                            })
                            ->selectRaw('CASE WHEN agent.id IS NULL THEN customer.name ELSE CONCAT(agent.name,"-",customer.name) END AS agent_customer,customer.*')
                            ->pluck('name','id');
        // $customer = self::collectionOfCustomerWithAgent()->pluck('name','id');
        return $customer;
    }
    
    static function arraySelectAgent()
    {
        $agent = Agent::select('name','id')->orderBy('name')->pluck('name','id')->prepend('無代理商', 0);
        // $arr = array('無代理商' => null);
        // foreach($agent as $key => $value) {
        //     $arr[$key] = $value;
        // }
        return $agent;
    }

    static function collectionPublishUser($userId)
    {
        if($userId == 0) {
            $publishuser = Publishuser::join('users', function ($join) {
                                $join->on('users.id', '=', 'publish_user.user_id');
                            })
                            ->selectRaw('users.name AS user_name,publish_user.*')
                            ->orderBy('publish_user.created_at', 'desc');
        } else {
            $publishuser = Publishuser::join('users', function ($join) use ($userId) {
                                $join->on('users.id', '=', 'publish_user.user_id')
                                     ->where('users.id', $userId);
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
        // if(is_null($userId)) {
        //     $userCondition = 'entrust.deleted_at IS NULL';
        // }
        // $strSql = 'SELECT `C`.`agent_customer`,`entrust`.* '
        //                 + 'FROM `entrust` '
        //                 + 'JOIN ('
        //                 + 'SELECT CASE WHEN `agent`.`id` IS NULL THEN `customer`.`name` ELSE CONCAT(`agent`.`name`,"-",`customer`.`name`) END AS `agent_customer`,`customer`.`id` AS `cid` '
        //                 + 'FROM `customer` '
        //                 + 'LEFT JOIN `agent` ON `agent`.`id`=`customer`.`agent_id`) AS `C` ON `entrust`.`customer_id`=`C`.`cid` ';
        //                 + 'WHERE (`entrust`.`owner_user`=1 AND `entrust`.`deleted_at` IS NULL)';
        $entrust = Entrust::join('customer', function ($join) use ($userId) {
                                $join->on('entrust.customer_id', '=', 'customer.id')
                                     ->whereRaw('entrust.owner_user='.$userId.' AND entrust.deleted_at IS NULL');
                            })->leftJoin('agent', function ($join) {
                                $join->on('customer.agent_id', '=', 'agent.id');
                            })
                            ->selectRaw('CASE WHEN agent.id IS NULL THEN customer.name ELSE CONCAT(agent.name,"-",customer.name) END AS agent_customer,entrust.*')
                            ->orderBy('entrust.created_at', 'desc');
        return $entrust;
    }

    static function arraySelectEntrust($userId)
    {
        $entrust = Entrust::where('owner_user', $userId)->orderBy('created_at', 'desc')->pluck('name', 'id')->prepend('請選擇', 0);
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
                                     ->whereRaw('entrust.id='.$entrustId.' AND entrust.deleted_at IS NULL');
                            })->leftJoin('agent', function ($join) {
                                $join->on('customer.agent_id', '=', 'agent.id');
                            })
                            ->selectRaw('CASE WHEN agent.id IS NULL THEN customer.name ELSE CONCAT(agent.name,"-",customer.name) END AS agent_customer,entrust.name,entrust.owner_user');
                            // ->orderBy('entrust.created_at', 'desc');
        return $entrust;
    }

    static function arrayPublishpositionWithSite()
    {
        $publishposition = Publishposition::leftJoin('publish_site', function ($join) {
                                $join->on('publish_site.id', '=', 'publish_position.site_id')
                                     ->whereRaw('publish_position.deleted_at IS NULL');
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
        $entrusts = Entrust::join('customer', function ($join) {
                                $join->on('customer.id', 'entrust.customer_id');
                            })
                            ->selectRaw('customer.name AS customer_name, entrust.*')
                            ->orderBy('entrust.created_at', 'desc')
                            ->get();
        foreach ($entrusts as $entrust) {
            $entrust->user_dept = Publishuser::find($entrust->owner_user)->dept;
            $entrust->user_name = User::find($entrust->owner_user)->name;
            //
            $statusName = '';
            switch($entrust->status) {
                case 1:
                    $statusName = '提案'; break;
                case 3:
                    $statusName = '審核通過'; break;
                case 5:
                    $statusName = '暫停'; break;
                case 6:
                    $statusName = '取消委刊'; break;
            }
            $entrust->status_name = $statusName;
        }
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
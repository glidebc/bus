<?php

namespace App;

// use App\Agent;
// use App\Customer;
// use App\Entrust;
// use App\Publish;
// use App\Publishposition;
// use App\Publishsite;
// use App\Publishuser;
// use App\User;
// use DB;
// use Illuminate\Support\Collection;
// use stdClass;

class DataFunc {
    /**
    * 公用函數
    *
    * 
    */

    public function taxNumberValid($number) {
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

    static private function tenDigitsPlusDigits($n) {
        if($n > 9) {
            $aryN = str_split(strval($n)); // [十位數, 個位數]
            $n = intval($aryN[0]) + intval($aryN[1]); //十位數 + 個位數
        }
        return $n;
    }

    //計算天數
    public function countDays($strSD, $strED) {
        $dayCount = 1;
        if($strED) {
            $sd = date_create($strSD);
            $ed = date_create($strED);
            if($sd && $ed)
                $dayCount = date_diff($sd, $ed)->days + 1;
        }
        return $dayCount;
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Foundation\Http\FormRequest;

class CreateEntrustRequest extends FormRequest {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
            'customer_id' => 'required|check_customer',
            'contact_id' => 'required|check_contact',
            'name' => 'required|unique:entrust,name,'.$this->myentrust,
            'start_date' => 'required|check_date', 
            'end_date' => 'check_date', 
            'publish_kind' => 'required|array|min:1', 
            'item_count' => 'check_item_count', 

            'item_cost_1' => 'check_item_cost',
            'item_cost_2' => 'check_item_cost',
            'item_cost_3' => 'check_item_cost',
            'item_cost_4' => 'check_item_cost',
            'item_cost_5' => 'check_item_cost',
            'item_cost_6' => 'check_item_cost',
            'item_cost_7' => 'check_item_cost',
            'item_cost_8' => 'check_item_cost',
            'item_cost_9' => 'check_item_cost',
            'item_cost_10' => 'check_item_cost',

			'pay' => 'check_pay', 

            'owner_user' => 'required', 
            
		];
	}

	/**
	 * 取得已定義驗證規則的錯誤訊息。
	 *
	 * @return array
	 */
	public function messages()
	{
	    return [
	        'customer_id.check_customer' => '請選擇客戶',
	        'contact_id.check_contact' => '請選擇承辦窗口',
	        'name.required' => '請輸入委刊專案名稱',
	        'name.unique' => '輸入的委刊單名稱已存在',

	        'start_date.required' => '請選擇 總走期的開始日期',
	        'start_date.check_date' => '總走期的開始日期 格式錯誤',
	        // 'end_date.required' => '請選擇 總走期的結束日期',
	        'end_date.check_date' => '總走期的結束日期 格式錯誤', 
	        'publish_kind.required' => '請選擇委刊類別', 

	        'item_count.check_item_count' => '至少要有一個委刊項',
	        'item_cost_1.check_item_cost' => '預算請輸入數字',
	        'item_cost_2.check_item_cost' => '預算請輸入數字',
	        'item_cost_3.check_item_cost' => '預算請輸入數字',
	        'item_cost_4.check_item_cost' => '預算請輸入數字',
	        'item_cost_5.check_item_cost' => '預算請輸入數字',
	        'item_cost_6.check_item_cost' => '預算請輸入數字',
	        'item_cost_7.check_item_cost' => '預算請輸入數字',
	        'item_cost_8.check_item_cost' => '預算請輸入數字',
	        'item_cost_9.check_item_cost' => '預算請輸入數字',
	        'item_cost_10.check_item_cost' => '預算請輸入數字',

	        'pay.check_pay' => '請選擇付款方式', 

	    ];
	}

	/**
     * @param \Illuminate\Contracts\Validation\Factory $factory
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Factory $factory)
    {
		$factory->extend('check_customer', function($attribute, $value, $parameters, $validator) {
    		return $value > 0;
        });
        $factory->extend('check_contact', function($attribute, $value, $parameters, $validator) {
    		return $value > 0;
        });
        $factory->extend('check_date', function($attribute, $value, $parameters, $validator) {
        	if(strlen($value) > 0)
        		return strlen($value) == 8;
        	else
    			return true;
        });
        $factory->extend('check_item_count', function($attribute, $value, $parameters, $validator) {
    		return $value > 0;
        });
        $factory->extend('check_pay', function($attribute, $value, $parameters, $validator) {
    		return $value > 0;
        });
        $factory->extend('check_item_cost', function($attribute, $value, $parameters, $validator) {
    		if($value == '')
    			return true;
    		else if(is_numeric($value))
    			return true;
        });

        return $factory->make(
            $this->all(),
            $this->container->call([$this, 'rules']),
            $this->messages(),
            $this->attributes()
        );
    }
}

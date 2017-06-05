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
            'name' => 'required', 
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
	        'name.required' => '請輸入委刊專案名稱',

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

        return $factory->make(
            $this->all(),
            $this->container->call([$this, 'rules']),
            $this->messages(),
            $this->attributes()
        );
    }
}

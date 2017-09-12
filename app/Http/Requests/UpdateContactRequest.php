<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRequest extends FormRequest {

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
			'customer_id' => 'check_customer',
            'name' => 'required',
            'address' => 'required',
            'tel' => 'required',
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
	    	'customer_id.check_customer' => '請選擇代理商｜客戶',
	        'name.required' => '請輸入姓名',
	        'address.required' => '請輸入地址',
	        'tel.required' => '請輸入電話',
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

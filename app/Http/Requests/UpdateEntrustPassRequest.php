<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEntrustPassRequest extends FormRequest {

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
            'invoice_date' => 'check_date',
            
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
	        'invoice_date.check_date' => '發票日期 格式錯誤',

	    ];
	}

	/**
     * @param \Illuminate\Contracts\Validation\Factory $factory
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Factory $factory)
    {
        $factory->extend('check_date', function($attribute, $value, $parameters, $validator) {
        	if(strlen($value) > 0)
        		return strlen($value) == 8;
        	else
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

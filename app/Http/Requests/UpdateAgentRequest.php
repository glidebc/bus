<?php

namespace App\Http\Requests;

use App\DataFunc;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAgentRequest extends FormRequest {

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
            'name' => 'required|unique:agent,name,'.$this->agent,
            'tax_title' => 'required',
            'tax_num' => 'required|numeric|check_min_length|check_valid_tax_num|unique:agent,tax_num,'.$this->agent,
            'address' => 'required',
            'com_tel' => 'required',

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
	        'name.required' => '請輸入公司簡稱',
	        'name.unique' => '輸入的公司簡稱已存在',

	        'tax_title.required'  => '請輸入公司全名',

	        'tax_num.required' => '請輸入統編',
	        'tax_num.numeric' => '統編為純數字',
	        'tax_num.check_min_length' => '統編為8碼數字',
	        'tax_num.check_valid_tax_num' => '無效的統編',
	        'tax_num.unique' => '輸入的統編已存在',

	        'address.required'  => '請輸入公司地址',
	        'com_tel.required' => '請輸入公司電話',

	    ];
	}

	/**
     * @param \Illuminate\Contracts\Validation\Factory $factory
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Factory $factory)
    {
		// $factory->extend('check_exist_name', function($attribute, $value, $parameters, $validator) {
		// 	// $name = old('name');
		// 	// if(old('name') != $value) {
	 //    		$agent = Agent::where('name', $value)->count();
		//         	if($agent == 0)
		//         		return true;
		//     		else
		//         		return false;
	 //        // }else{
	 //        // 	return true;
	 //        // }
  //       });

    	$factory->extend('check_min_length', function($attribute, $value, $parameters, $validator) {
    		return strlen($value) == 8;
        });

     //    $factory->extend('check_exist_tax_num', function($attribute, $value, $parameters, $validator) {
     //    	if(strlen($value) == 8) {
	    //     	$agent = Agent::where('tax_num', $value)->count();
	    //     	if($agent == 0)
	    //     		return true;
	    // 		else
	    //     		return false;
    	// 	}else{
    	// 		return true;
    	// 	}
    	// });

        $factory->extend('check_valid_tax_num', function($attribute, $value, $parameters, $validator) {
        	return (new DataFunc)->taxNumberValid($value);
        });

        return $factory->make(
            $this->all(),
            $this->container->call([$this, 'rules']),
            $this->messages(),
            $this->attributes()
        );
    }
}

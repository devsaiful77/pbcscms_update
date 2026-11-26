<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubFlowRequest extends FormRequest
{
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
    /*public function rules()
    {
        return [
            'name' => 'required|max:255',
        ];
        'user_id', 'role_id', 'name', 'designation', 'national_id', 'mobile_no', 'email', 'password', 'status', 'profile_picture', 'department_id'
    }*/
    public function rules()
    {
        $rules=array();
        if (!empty($this->newsubgroup)) {
            $rules['newsubgroup.*.options']  = 'required';
            $rules['newsubgroup.*.group_info_id']  = 'required';
        }

        return $rules;
    }

    public function messages()
    {
        $messages = array();
        if (!empty($this->newsubgroup)) {
            foreach($this->newsubgroup as $key => $val) {
                $messages['newsubgroup.'.$key.'.options.required']  = 'Option is Required';
                $messages['newsubgroup.'.$key.'.group_info_id.required']  = 'Group Info is Required';
            }
        }
        return $messages;
    }
}

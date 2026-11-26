<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnitItemRequest extends FormRequest
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
        return [            
            'name' => 'required',
            'unit_id' => 'required',
            'issues_from' => 'required',
        ];
    }

    public function messages()
    {
        return [            
            'name.required' => 'Name is required.',    
            'unit_id.required' => 'Please select Unit.',       
            'issues_from.required' => 'Please select Issues From.'       
        ];
    }
}

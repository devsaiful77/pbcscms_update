<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermissionRequest extends FormRequest
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
    public function rules()
    {
        return [            
            'controller_name' => 'required' ,    
            'name' => 'required' ,    
            'display_name' => 'required'
        ];
    }

    public function messages()
    {
        return [            
            'controller_name.required' => 'Controller is required.',
            'name.required' => 'Name is required.',
            'display_name.required' => 'Display Name is required.'
        ];
    }
}

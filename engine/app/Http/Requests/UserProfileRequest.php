<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserProfileRequest extends FormRequest
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
           'profile_picture' => 'required|mimes:jpg,jpeg,png|max:1024'
        ];
    }

    public function messages()
    {
        return [           
            'profile_picture.required' => 'Profile Picture is required.'            
        ];
    }
}

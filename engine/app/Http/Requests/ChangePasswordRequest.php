<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ChangePasswordRequest extends FormRequest
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

        $rules['password'] = 'required|string|min:8|confirmed|regex:/^.*(?=.{3,})(?=.*[a-z])(?=.*[0-9])(?=.*[@$!%*#?&]).*$/';
        $rules['password_confirmation'] = 'required';

        return $rules;
    }

    public function messages()
    {
        return [
            'password_confirmation.required' => 'Confirm Password is required.',
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Confirm Password mismatched.',
            'password.regex'    => 'Password minimum 8 length with alpha, digit & special character',
            'password.min'    => 'Password minimum 8 length with alpha, digit & special character',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserPasswordRequest extends FormRequest
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

        $requestFrom = (!empty($this->get('from_user_request'))) ? $this->get('from_user_request') : '';

        if (!empty($this->get('admin_password'))) {
            $verifyPassword = $this->get('admin_password');
            if (!(Hash::check($verifyPassword, Auth::user()->password))) {
                $rules = [
                    'admin_password_incorrect'=> 'required',
                    'user_password' => 'required|string|min:8|regex:/^.*(?=.{3,})(?=.*[a-z])(?=.*[0-9])(?=.*[@$!%*#?&]).*$/'
                ];
            }
        }
        $rules['admin_password'] = 'required';
        $rules['user_password'] = 'required|string|min:8|regex:/^.*(?=.{3,})(?=.*[a-z])(?=.*[0-9])(?=.*[@$!%*#?&]).*$/';

        return $rules;
    }

    public function messages()
    {
        $requestFrom = (!empty($this->get('from_user_request'))) ? $this->get('from_user_request') : '';

        $existingPwRequestLabel = ($requestFrom == "ChangePassword") ? "Current " : "Admin ";
        return [
            'admin_password.required' => $existingPwRequestLabel.'Password is required.',
            'user_password.required' => 'Password is required.',
            'user_password.regex'    => 'Password minimum 8 length with alpha, digit & special character',
            'admin_password_incorrect.required' => $existingPwRequestLabel.'Password is incorrect.',
        ];
    }
}

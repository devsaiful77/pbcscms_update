<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
    // public function rules()
    // {

    //     if (!empty($this->id)) {
    //        $this->id = decrypt($this->id);
    //     }
    //     $rules = [
    //         'user_id' => 'required|unique:users,user_id,'.$this->id,
    //         // 'role_id' => 'required',
    //         'name' => 'required',
    //         'designation' => 'required',
    //         'mobile_no' => 'required',
    //         'email' => 'required|email|unique:users,email,'.$this->id,
    //         // 'password' => 'required|string|min:8|confirmed|regex:/^.*(?=.{3,})(?=.*[a-z])(?=.*[0-9])(?=.*[@$!%*#?&]).*$/',
    //         // 'department_id' => 'required'
    //     ];
    //     if (!empty($this->password)) {
    //         $rules['password'] = 'string|min:8|confirmed|regex:/^.*(?=.{3,})(?=.*[a-z])(?=.*[0-9])(?=.*[@$!%*#?&]).*$/';
    //     }

    //     return $rules;
    // }

    public function rules()
    {
        if (!empty($this->id)) {
        $this->id = decrypt($this->id);
        }

        $rules = [
            'user_id' => 'required|unique:users,user_id,' . $this->id,
            'name' => 'required',
            'designation' => 'required',
            'mobile_no' => [
                'required',
                'string',
                'regex:/^(\+88)?01[3-9]\d{8}$/',
            ],
            'email' => [
                'required',
                'email',
                'unique:users,email,' . $this->id,
                'regex:/^[a-zA-Z0-9._%+-]+@primebank\.com\.bd$/',
            ],
            // 'email' => ['required', 'string', 'email', 'max:255', 'ends_with:primebank.com.bd'],
        ];

        if (!empty($this->password)) {
            $rules['password'] = 'string|min:8|confirmed|regex:/^.*(?=.{3,})(?=.*[a-z])(?=.*[0-9])(?=.*[@$!%*#?&]).*$/';
        }

        return $rules;
    }


    public function messages()
    {
        return [
            'user_id.required' => 'User ID is required.',
            // 'role_id.required' => 'Please select Role.',
            'name.required' => 'Name is required.',
            'designation.required' => 'Designation is required.',
            'national_id.required' => 'National ID is required.',
            'mobile_no.required' => 'Mobile No is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email should be valid.',
            'email.unique' => 'Email already exists.',
            'email.regex' => 'The email must be a valid @primebank.com.bd address.',
            'mobile_no.regex' => 'The mobile number format is invalid for Bangladesh.',
            'password.required'     => 'Password is required.',
            'password.min'          => 'Minimum Length is 8.',
            'password.confirmed'    => 'Mismatched Confirm Password.',
            'password.regex'        => 'Password minimum 8 length with alpha, digit & special character'

            // 'department_id.required' => 'Please select Outlet.'
        ];
    }
}

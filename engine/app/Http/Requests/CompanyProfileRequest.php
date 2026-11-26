<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyProfileRequest extends FormRequest
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
    }*/
    public function rules()
    {
        return [
            'name' => 'required'
            ,'address' => 'required'
            ,'company_vision' => 'required'
            ,'email' => 'required|email'
            ,'mobile_no' => 'required'
            ,'company_logo' => 'mimes:jpg,jpeg,png|max:5120'
           /* 'company_email' => 'required|email',
            'company_phone' => 'required|min:11|max:15',
            'address' => 'required',
            'logo' => 'mimes:jpg,jpeg,png|max:200'*/
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Company Name is required.',
            'address.required' => 'Address is required.',
            'company_vision.required' => 'Company Vision is required.',
            'email.required' => 'Contact Email is required.',
            'email.email' => 'Contact Email should be valid.',
            'mobile_no.required' => 'Contact Phone is required.',
            /*,
            'company_email.required' => 'Company Email field is required.',
            'company_phone.required' => 'Company Phone field is required.',
            'address.required' => 'Company Address field is required.'*/
        ];
    }
}

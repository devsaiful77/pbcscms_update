<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestrictedIpRequest extends FormRequest
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

    public function rules()
    {
        return [
            'ipAddress' => 'required|unique:restricted_ips,ip,'.$this->id,
        ];
    }

    public function messages()
    {
        return [
            'ipAddress.required' => 'Ip Address is required.',
            'ipAddress.unique' => 'Ip Address already exists.',
        ];
    }
}

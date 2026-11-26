<?php

namespace Modules\Requisition\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VendorRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $vendorId = $this->vendor;

        return [
            'name' => [
                'required',
                'regex:/^[A-Za-z\s]+$/'
            ],

            'email' => [
                'required',
                'email',
                Rule::unique('vendors', 'email')->ignore($vendorId),
            ],

            'phone' => [
                'required',
                'regex:/^[0-9]+$/',
                Rule::unique('vendors', 'phone')->ignore($vendorId),
            ],

            'office_address' => 'required',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}

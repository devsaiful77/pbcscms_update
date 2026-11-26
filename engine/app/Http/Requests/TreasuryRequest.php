<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TreasuryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'week' => 'required',
            'count' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required.',
            'week.required' => 'Drow Day is required.',
            'count.required' => 'Drow Count is required.',
        ];
    }
}

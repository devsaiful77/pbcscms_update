<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountCategoryRequest extends FormRequest
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
            'categ_code' => 'required|max:50',
            'title' => 'required|max:255',
            'short_id' => 'nullable|integer',
            'ac_type' => 'nullable|max:50',
            'description' => 'nullable|max:100',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'categ_code.required' => 'Category Code is required.',
            'categ_code.max' => 'Category Code may not be greater than 50 characters.',
            'title.required' => 'Title is required.',
            'title.max' => 'Title may not be greater than 255 characters.',
            'short_id.integer' => 'Short ID must be a number.',
            'ac_type.max' => 'Account Type may not be greater than 50 characters.',
            'description.max' => 'Description may not be greater than 100 characters.',
        ];
    }
}

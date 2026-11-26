<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CycleRequest extends FormRequest
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
            'name' => 'required',
            'number' => 'required|numeric|min_digits:1|unique:cycles,number,'.$this->id ,
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required.',
            'number.required' => 'Cycle Number is required.',
            'number.numeric' => 'Cycle Number should be valid.',
            'number.max' => 'Cycle Number should be less then 100.',
            'number.min' => 'Cycle Number should be not less then 1.',
            'number.unique' => 'Cycle Number is already exists.',
        ];
    }
}

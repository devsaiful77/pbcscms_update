<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SegmentRequest extends FormRequest
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

        if (!empty($this->request_type)) {
            return [
                'file' => 'required|mimes:xls,xlsx'
            ];
        } elseif (!empty($this->file)) {
            return [
                'file' => 'mimes:xls,xlsx'
            ];
        } else {
            return [
                'code' => 'required|unique:segment_codes,code,'.$this->id,
                'name' => 'required',
                'status' => 'nullable',
            ];
        }
    }
}

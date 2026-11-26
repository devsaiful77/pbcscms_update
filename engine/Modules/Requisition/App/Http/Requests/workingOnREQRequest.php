<?php

namespace Modules\Requisition\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class workingOnREQRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = array();
        $rules['comments'] = 'required';
        
        if($this->input('action') == 'close'){
            $rules['receive_date'] = 'required';
        }else{
            $rules['receive_date'] = 'nullable';
        }
        return $rules;
    }


    public function messages(): array
    {
        $messages = array();
        $messages['comments.required'] = 'Please Type Comment';
        $messages['receive_date.required'] = 'Please Enter Receive Date';
        
        return $messages;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}

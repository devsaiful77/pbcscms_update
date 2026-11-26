<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FiledsetConfigRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [];
        if (!empty($this->get('addmore'))) {
            foreach($this->get('addmore') as $key => $val) {
                $rules['addmore.'.$key.'.group_name'] = 'required';
            }
        } else {

        }
        return $rules;
    }

    public function messages()
    {
        if (!empty($this->get('addmore'))) {
            foreach($this->get('addmore') as $key => $val) {
                $messages['addmore.'.$key.'.group_name.required'] = 'Group Name is required';
            }
        } else {
            $messages['atleast_config.required'] = 'Please add at least one field';
        }
        return $messages;
    }
}

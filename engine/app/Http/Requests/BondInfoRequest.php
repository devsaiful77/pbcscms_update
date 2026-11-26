<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class BondInfoRequest extends FormRequest
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
        $rules = array();
        $rules['binfo_category_id']  = 'required';
        $rules['binfo_subcategory_id']  = 'required';
        $rules['title']  = 'required';

        if (!empty($this->existing_file)) {
            $rules['file_name']  = 'mimes:pdf,jpg,jpeg,png,doc,docx|max:2048';
        } else {
            $rules['file_name']  = 'required|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048';
        }
        // $rules['file_name']  = 'required|mimes_except:exe,bat,cmd|max:2048';
        
        return $rules;
    }

    public function messages()
    {
        $messages = array();

        $messages['binfo_category_id.required']  = 'Category is required';
        $messages['binfo_subcategory_id.required']  = 'Sub Category is required';
        $messages['title.required']  = 'Title is required';
        $messages['file_name.required']  = 'File Required';
        $messages['file_name.mimes']  = 'Uploadesd file is not supported.';
        $messages['file_name.mimes_except']  = 'Uploadesd file is not supported.';
        $messages['file_name.max']  = 'Max uploaded file size is 3 MB';

        return $messages;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $queryTmp = DB::table('bond_information_tmps')
                ->where('title', $this->title);
        
            if ($this->id) {
                $queryTmp->where('id', '!=', $this->id);
            }
        
            if ($queryTmp->exists()) {
                $validator->errors()->add('title', 'The title has already been taken, please wait for Checker Approval.');
            }
        
            $query = DB::table('bond_informations')
                ->where('title', $this->title);
        
            if ($this->id) {
                $query->where('id', '!=', $this->id);
            }
        
            if ($query->exists()) {
                $validator->errors()->add('title', 'The title has already been taken, please wait for Checker Approval.');
            }
        });
        
    }
}

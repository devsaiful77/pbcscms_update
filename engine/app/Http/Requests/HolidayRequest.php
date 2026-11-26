<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\FormRequest;

class HolidayRequest extends FormRequest
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
        'user_id', 'role_id', 'name', 'designation', 'national_id', 'mobile_no', 'email', 'password', 'status', 'profile_picture', 'department_id'
    }*/
    public function rules()
    {
        /*if (!empty($this->dates)) {
            $this->dates = date('Y-m-d',strtotime($this->dates));
        }*/
        
        if (!empty($this->request_type)) {
            return [    
                'upload_holidays' => 'required|mimes:xls,xlsx'        
            ];
        } elseif (!empty($this->upload_holidays)) {
            return [    
                'upload_holidays' => 'mimes:xls,xlsx'        
            ];
        } else {
            return [    
                'dates' => 'required|unique:holidays,dates,'.$this->id,        
                'type' => 'required',    
                'remarks' => 'required',    
            ];
        }
    }
            

    public function messages()
    {
        return [            
            'dates.required' => 'Date is required.',       
            'dates.unique' => 'Date is already exists.',       
            'type.required' => 'Holiday Type is required.',       
            'remarks.required' => 'Remarks is required.',
            'upload_holidays.required' => 'File Required.',
            'upload_holidays.mimes' => 'Only xls and xlsx allowed.'
        ];
    }

    // public function withValidator($validator)
    // {
    //     $validator->after(function ($validator) {
    //         if (DB::table('holiday_tmps')->where('dates', $this->dates)->exists()) {
    //             $validator->errors()->add('dates', 'The date has already been taken, please wait for Checker Approval.');
    //         }
    //     });
    // }
}

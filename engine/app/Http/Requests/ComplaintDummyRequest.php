<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\IssueConfig;
use App\IssueCheckListConfig;
use Auth;

class ComplaintDummyRequest extends FormRequest
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
        $rules = array();
        //$rules['account_number'] = 'required|max:18';
        //$rules['customer_name'] = 'required|max:200';
        //$rules['mobile_number'] = 'required|max:20';
        $rules['product_type'] = 'required';
        $rules['complaint_type'] = 'required|max:100';
        $rules['time_and_ext'] = 'required';
        $rules['complaint_details'] = 'required';
        
        if (!empty($this->complaint_type)) {
            $complaint_type = $this->complaint_type;
            $issue_config_obj = IssueConfig::where('issue_id',$complaint_type)->get();
            if (!empty($issue_config_obj)) {
                foreach ($issue_config_obj as $key => $value) {
                    $tmp_rules = "";
                    $field_name = $value->field_name;
                    $maximum_length = $value->maximum_length;
                    $minimum_length = $value->minimum_length;
                    $fixed_length = $value->fixed_length;

                    $is_required = $value->is_required;

                    if ($is_required == 1) {
                        $tmp_rules .= '|required';
                    }
                    if (!empty($maximum_length)) {
                        $tmp_rules .= '|max:'.$maximum_length;
                    }
                    if (!empty($minimum_length)) {
                        $tmp_rules .= '|min:'.$minimum_length;
                    }
                    if (!empty($fixed_length)) {
                        $tmp_rules .= '|fixed_len:'.$fixed_length;
                    }
                    $tmp_rules = ltrim($tmp_rules, '|');

                    $rules[$field_name] = $tmp_rules;
                }
            }
            $issue_checklist_config_obj = IssueCheckListConfig::where('issue_id',$complaint_type)->get();
            if (!empty($issue_checklist_config_obj)) {
                foreach ($issue_checklist_config_obj as $key => $value) {
                    $tmp_rules = "";
                    $field_name = $value->field_name;
                    $maximum_length = $value->maximum_length;
                    $is_required = $value->is_required;
                    if ($is_required == 1) {
                        $tmp_rules = 'required';
                    }
                    if (!empty($maximum_length) && $is_required == 1) {
                        $tmp_rules .= '|max:'.$maximum_length;
                    } elseif(!empty($maximum_length) && empty($is_required)) {
                        $tmp_rules .= 'max:'.$maximum_length;
                    }
                    //prd($tmp_rules);
                    $rules[$field_name] = $tmp_rules;
                }
            }
            
        }

        if (!empty($this->file_name)) {
            $rules['file_name.*']  = 'required|mimes_except:exe,bat,cmd|max:3072';
        }

        return $rules;
    }

    public function messages()
    {
        $messages = array();
        //$messages['account_number.required'] = 'Required';
        //$messages['customer_name.required'] = 'Required';
        //$messages['mobile_number.required'] = 'Required';
        $messages['product_type.required'] = 'Required';
        $messages['complaint_type.required'] = 'Required';
        $messages['time_and_ext.required'] = 'Required';
        $messages['complaint_details.required'] = 'Required';
        //$messages['caller_id.required'] = 'Required';
        
        if (!empty($this->complaint_type)) {
            $complaint_type = $this->complaint_type;
            $issue_config_obj = IssueConfig::where('issue_id',$complaint_type)->get();
            foreach ($issue_config_obj as $key => $value) {
                $field_name = $value->field_name;
                $maximum_length = $value->maximum_length;
                $minimum_length = $value->minimum_length;
                $fixed_length = $value->fixed_length;
                $is_required = $value->is_required;

                if ($is_required == 1) {
                    $messages[$field_name.'.required'] = 'Required';
                }
                if (!empty($maximum_length)) {
                    $messages[$field_name.'.max'] = 'Max Length '.$maximum_length;
                }
                if (!empty($minimum_length)) {
                    $messages[$field_name.'.min'] = 'Min Length '.$minimum_length;
                }
                if (!empty($fixed_length)) {
                    $messages[$field_name.'.fixed_len'] = 'Input length should be '.$fixed_length;
                }
            }
            $issue_checklist_config_obj = IssueCheckListConfig::where('issue_id',$complaint_type)->get();
            foreach ($issue_checklist_config_obj as $key => $value) {
                $tmp_rules = "";
                $field_name = $value->field_name;
                $maximum_length = $value->maximum_length;
                $is_required = $value->is_required;
                if ($is_required == 1) {
                    $messages[$field_name.'.required'] = 'Required';
                }
                if (!empty($maximum_length) && $is_required == 1) {
                    $tmp_rules .= '|max:'.$maximum_length;
                    $messages[$field_name.'.max'] = 'Max Length '.$maximum_length;
                } else {
                    $tmp_rules .= 'max:'.$maximum_length;
                    $messages[$field_name.'.max'] = 'Max Length '.$maximum_length;
                }
            }
            
        }
        
        if (!empty($this->file_name)) {
            foreach($this->file_name as $key => $val) {
                $messages['file_name.'.$key.'.required']  = 'File Required';
                $messages['file_name.'.$key.'.mimes_except']  = 'Uploaded file is not supported.';
                $messages['file_name.'.$key.'.max']  = 'Max uploaded file size is 3 MB';
            }
        }

        return $messages;
    }
}

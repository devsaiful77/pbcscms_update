<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\IssueConfig;
use App\IssueCheckListConfig;
use Auth;

class WFormDummyRequest extends FormRequest
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
        $requestDt = $this->request;
        $rules = array();
        $rules['w_form_type'] = 'required';
        //$rules['customer_name'] = 'required|max:200';
        //$rules['account_number'] = 'required|max:18';
        $rules['product_type'] = 'required';
        $rules['time_and_ext'] = 'required';
		//$rules['mobile_number'] = 'required';
        /*if(Auth::user()->user_unit->subgroup_info_id==3){
            $rules['caller_id'] = 'required|regex:/^[0-9]+$/|min:1|max:50';
        }*/
        //$rules['dynamic_question'] = 'required';
        // $rules['address'] = 'required';
        // prd($this->category);
        if (!empty($this->w_form_type)) {
            $w_form_type = $this->w_form_type;
            $issue_config_obj = IssueConfig::where('issue_id',$w_form_type)->get();
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
                    if (!empty($maximum_length) && $field_type !== 'number') {
                        $tmp_rules .= '|max:'.$maximum_length;
                    }
                    if (!empty($minimum_length) && $field_type !== 'number') {
                        $tmp_rules .= '|min:'.$minimum_length;
                    }
                    if (!empty($maximum_length) && $field_type == 'number') {
                        $tmp_rules .= '|max_digits:'.$maximum_length;
                    }
                    if (!empty($minimum_length) && $field_type == 'number') {
                        $tmp_rules .= '|min_digits:'.$minimum_length;
                    }
                    if (!empty($fixed_length)) {
                        $tmp_rules .= '|fixed_len:'.$fixed_length;
                    }
                    if (!empty($field_type) && $field_type == 'number') {
                        $tmp_rules .= '|nullable|numeric';
                    }
                    if (!empty($field_type) && $field_type == 'decimal') {
                        $tmp_rules .= '|nullable|float_twodigit';
                    }
                    if (!empty($field_type) && $field_type == 'date') {
                        $tmp_rules .= '|nullable|date';
                    }
                    $tmp_rules = ltrim($tmp_rules, '|');

                    $rules[$field_name] = $tmp_rules;
                }
            }
            $issue_checklist_config_obj = IssueCheckListConfig::where('issue_id',$w_form_type)->get();
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
        $messages['w_form_type.required']       = 'Required';
        //$messages['customer_name.required'] = 'Required';
        //$messages['account_number.required']    = 'Required';
        $messages['product_type.required']      = 'Required';
        //$messages['item_type.required']         = 'Required';
        $messages['time_and_ext.required']      = 'Required';
        //$messages['caller_id.required']         = 'Required';
        //$messages['dynamic_question.required']  = 'Required';
        //$messages['address.required']           = 'Required';

        if (!empty($this->w_form_type)) {
            $w_form_type = $this->w_form_type;
            $issue_config_obj = IssueConfig::where('issue_id',$w_form_type)->get();
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

            $issue_checklist_config_obj = IssueCheckListConfig::where('issue_id',$w_form_type)->get();
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

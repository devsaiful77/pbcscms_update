<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\IssueConfig;
use App\IssueCheckListConfig;
use Auth;

class CIWFormRequest extends FormRequest
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
        $rules['account_number'] = 'required|max:18';
        $rules['product_type'] = 'required';

        if ($this->file_name){
            foreach ($this->file_name as $index => $image) {
                if($image['is_required'] == 1){
                    /*$rules["file_name.$index.file"] = 'required|mimes_except:exe,bat,cmd|max:3072';*/
                    $rules["file_name.$index.file"] = 'required|mimes:jpeg,png,jpg,pdf,heif|max:3072';
                }else {
                    $rules["file_name.$index.file"] = 'mimes:jpeg,png,jpg,pdf,heif|max:3072';
                };
            }
        }

        if (!empty($this->w_form_type)) {
            $w_form_type = $this->w_form_type;
            $issue_config_obj = IssueConfig::where('issue_id', $w_form_type)->get();
            if (!empty($issue_config_obj)) {
                foreach ($issue_config_obj as $key => $value) {
                    $tmp_rules = "";
                    $field_name = $value->field_name;
                    $maximum_length = $value->maximum_length;
                    $minimum_length = $value->minimum_length;
                    $fixed_length = $value->fixed_length;
                    $field_type = $value->field_type;
                    $is_required = $value->is_required;

                    $allFieldNames = array_keys($this->all());
                    $pattern = '/##CI##/';
                    $fieldsNotMatchingPattern = array_filter($allFieldNames, function ($fieldName) use ($pattern) {
                        return preg_match($pattern, $fieldName);
                    });
                    foreach ($fieldsNotMatchingPattern as $key => $value) {
                        $fieldName = str_replace('##CI##', '', $value);
                        if ($field_name == $fieldName) {
                            $is_required = 0;
                        }
                    }

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

            /* Check for Nominee Information Minor*/
            if (!empty($this->minor_flg_nominee)) {
                if ($this->minor_flg_nominee == 'Y') {
                    $rules['guardianName'] ='required';
                    $rules['guardina_code_s'] ='required';
                    $rules['guardianAddr1'] ='required';
                    $rules['guardianCity'] ='required';
                    $rules['guardianStateProv'] ='required';
                    $rules['guardianPostalCode'] ='required';
                    $rules['guardianCountry'] ='required';
                    $rules['nominee_dob'] = 'after:'.now()->subYears(18)->toDateString();
                } else {
                    if (!empty($this->nominee_dob)) {
                        $rules['nominee_dob'] = 'before:'.now()->subYears(18)->toDateString();
                    }
                }
            }

        }

        return $rules;
    }

    public function messages()
    {
        $messages = array();
        $messages['w_form_type.required']       = 'Required';
        $messages['account_number.required']    = 'Required';
        $messages['product_type.required']      = 'Required';
        $messages['time_and_ext.required']      = 'Required';

        if ($this->file_name){
            foreach ($this->file_name as $index => $image) {

                if($image['is_required'] == 1){
                    $messages["file_name.$index.file.required"] = "Image is required.";
                    $messages["file_name.$index.file.mimes"] = "This file is not supported; The supported formats are JPG, JPEG, PNG, PDF, HEIF";
                    $messages["file_name.$index.file.max"] = "Max uploaded file size is 3 MB";
                }else {
                    $messages["file_name.$index.file.mimes"] = "This file is not supported; The supported formats are JPG, JPEG, PNG, PDF, HEIF";
                    $messages["file_name.$index.file.max"] = "Max uploaded file size is 3 MB";
                };
            }
        }


        if (!empty($this->w_form_type)) {
            $w_form_type = $this->w_form_type;
            $issue_config_obj = IssueConfig::where('issue_id',$w_form_type)->get();
            foreach ($issue_config_obj as $key => $value) {
                $field_name = $value->field_name;
                $maximum_length = $value->maximum_length;
                $minimum_length = $value->minimum_length;
                $fixed_length = $value->fixed_length;
                $is_required = $value->is_required;
                $field_type = $value->field_type;

                $allFieldNames = array_keys($this->all());
                $pattern = '/##CICF##/';
                $fieldsNotMatchingPattern = array_filter($allFieldNames, function ($fieldName) use ($pattern) {
                    return preg_match($pattern, $fieldName);
                });
                foreach ($fieldsNotMatchingPattern as $key => $value) {
                    $fieldName = str_replace('##CICF##', '', $value);
                    if ($field_name == $fieldName) {
                        $is_required = 0;
                    }
                }

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
                $messages[$field_name.'.float_twodigit'] = 'Must be two digit after decimal number is required.';

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

            /* Check for Nominee Information Minor*/
            if (!empty($this->minor_flg_nominee)) {
                if ($this->minor_flg_nominee == 'Y') {
                    $messages['guardianName.required'] ='Required';
                    $messages['guardina_code_s.required'] ='Required';
                    $messages['guardianAddr1.required'] ='Required';
                    $messages['guardianCity.required'] ='Required';
                    $messages['guardianStateProv.required'] ='Required';
                    $messages['guardianPostalCode.required'] ='Required';
                    $messages['guardianCountry.required'] ='Required';
                    $messages['nominee_dob.after'] = 'Please check Nominee Minor Flag / Nominee Birth Date';
                } else {
                    $messages['nominee_dob.before'] = 'Please check Nominee Minor Flag / Nominee Birth Date';
                }
            }

        }

        return $messages;
    }
}

<?php

namespace App\Http\Requests;

use Auth;
use App\Setting;
use App\IssueConfig;
use App\IssueCheckListConfig;
use Illuminate\Foundation\Http\FormRequest;

class ComplaintRequest extends FormRequest
{
    protected $fileSizeLimit;

    public function __construct()
    {
        parent::__construct();

        $settings = Setting::first();
        if (!empty($settings) && !empty($settings->file_size_limit)) {
            $this->fileSizeLimit = (int) $settings->file_size_limit;
        } else {
            $this->fileSizeLimit = 10240; // Default to 10 MB in KB
        }
    }

    protected function prepareForValidation()
    {
        if ($this->filled('multi_requests_data')) {
            $multiData = json_decode($this->multi_requests_data, true);
            if (is_array($multiData)) {
                $this->merge(['multi_requests' => $multiData]);
            }
        }
    }

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
        $rules['time_and_ext'] = 'required';
        $rules['complaint_details'] = 'required';
        if(Auth::user()->user_unit->subgroup_info_id==3){
           $rules['caller_id'] = 'required|regex:/^[0-9]+$/|min:1|max:50';
        }

        $rules['complaint_type'] = 'required|max:100';
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

        // Additional complaints validation (Complaint #2, #3, ...)
        if ($this->has('multi_requests') && is_array($this->multi_requests) && count($this->multi_requests) > 1) {
            $seenIssues = [];
            if (!empty($this->complaint_type)) {
                $seenIssues[] = (string) $this->complaint_type;
            }

            foreach ($this->multi_requests as $idx => $item) {
                if ($idx == 0) continue; // Complaint 1 is validated above!
                $compType = $item['complaint_type'] ?? null;
                if (empty($compType)) {
                    $rules["multi_requests.{$idx}.complaint_type"] = 'required';
                } else {
                    $compStr = (string) $compType;
                    if (in_array($compStr, $seenIssues, true)) {
                        $rules["multi_requests.{$idx}.complaint_type"] = 'required|not_in:' . implode(',', $seenIssues);
                    } else {
                        $seenIssues[] = $compStr;
                    }

                    $issue_config_obj = IssueConfig::where('issue_id', $compType)->get();
                    if (!empty($issue_config_obj)) {
                        foreach ($issue_config_obj as $key => $value) {
                            if ($value->field_type === 'file') continue;
                            if ($value->is_required == 1) {
                                $rules["multi_requests.{$idx}.fields.{$value->field_name}"] = 'required';
                            }
                        }
                    }
                }
            }
        }

        $fileInput = $this->file('file_name') ?: $this->file_name;
        if (!empty($fileInput)) {
            $isNested = false;
            if (is_array($fileInput)) {
                $values = array_values($fileInput);
                if (!empty($values) && is_array($values[0])) {
                    $isNested = true;
                }
            }

            if ($isNested) {
                $rules['file_name.*.*'] = 'required|mimes_except:exe,bat,cmd|max:' . $this->fileSizeLimit;
            } else {
                $rules['file_name.*']   = 'required|mimes_except:exe,bat,cmd|max:' . $this->fileSizeLimit;
            }
        }

        return $rules;
    }

    public function attributes()
    {
        $attrs = [];
        if ($this->has('multi_requests') && is_array($this->multi_requests) && count($this->multi_requests) > 1) {
            foreach ($this->multi_requests as $idx => $item) {
                if ($idx == 0) continue;
                $num = $idx + 1;
                $attrs["multi_requests.{$idx}.complaint_type"] = "Complaint #{$num} Type";
                $compType = $item['complaint_type'] ?? null;
                if (!empty($compType)) {
                    $issue_config_obj = IssueConfig::where('issue_id', $compType)->get();
                    if (!empty($issue_config_obj)) {
                        foreach ($issue_config_obj as $value) {
                            $attrs["multi_requests.{$idx}.fields.{$value->field_name}"] = "Complaint #{$num} {$value->label_name}";
                        }
                    }
                }
            }
        }
        return $attrs;
    }

    public function messages()
    {
        $messages = array();
        if ($this->has('multi_requests') && is_array($this->multi_requests) && count($this->multi_requests) > 1) {
            foreach ($this->multi_requests as $idx => $item) {
                $num = $idx + 1;
                $messages["multi_requests.{$idx}.complaint_type.required"] = "Complaint #{$num} Type is required";
                $messages["multi_requests.{$idx}.complaint_type.not_in"] = "Complaint #{$num} cannot be the same as another selected complaint. Duplicate complaints are not allowed.";
            }
        }
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
        
        $fileInput = $this->file('file_name') ?: $this->file_name;
        if (!empty($fileInput)) {
            $messages['file_name.*.required']       = 'File Required';
            $messages['file_name.*.mimes_except']   = 'Uploaded file is not supported.';
            $messages['file_name.*.max']            = 'Max uploaded file size is ' . ($this->fileSizeLimit / 1024) . ' MB.';
            $messages['file_name.*.*.required']     = 'File Required';
            $messages['file_name.*.*.mimes_except'] = 'Uploaded file is not supported.';
            $messages['file_name.*.*.max']          = 'Max uploaded file size is ' . ($this->fileSizeLimit / 1024) . ' MB.';
        }

        return $messages;
    }
}

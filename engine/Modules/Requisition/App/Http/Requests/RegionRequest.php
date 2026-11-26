<?php

namespace Modules\Requisition\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|unique:requisition_region,name,' . $this->id,
            'subgroup_info_id' => 'required|array',
            'subgroup_info_id.*' => 'required|exists:subgroup_info,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The region name field is required.',
            'subgroup_info_id.required' => 'The subgroup info field is required.',
            'subgroup_info_id.array' => 'The subgroup info must be an array.',
            'subgroup_info_id.*.required' => 'Each subgroup info ID is required.',
            'subgroup_info_id.*.exists' => 'The selected subgroup info ID is invalid.',
        ];
    }


    public function authorize(): bool
    {
        return true;
    }
}

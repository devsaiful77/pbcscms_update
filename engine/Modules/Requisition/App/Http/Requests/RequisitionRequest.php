<?php

namespace Modules\Requisition\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class RequisitionRequest extends FormRequest
{
    public function rules(): array
    {
        $routeName = $this->route()->getName();

        return [
            'type' => $routeName === 'requisition.support-service-store' ? 'required' : 'nullable',

            'product_ids' => 'required|array',
            'product_ids.*' => 'required|integer',

            'quantities' => 'array',
            'quantities.*' => $routeName !== 'requisition.support-service-store' ? 'required|min:1' : 'nullable',

            'attachments' => 'nullable|array',
            'attachments.*.title' => 'nullable|string|max:255',
            // 'attachments.*.file' => 'nullable|file|max:10240',
            'attachments.*.file' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,pdf|max:10240',

            'specification' => 'nullable|string',
            'justification' => 'nullable|string',
            'remark' => 'nullable|string',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->sometimes('quantities', 'required|array', function ($input) {
            return $input->type == 2;
        });

        $validator->sometimes('quantities.*', 'required|min:1', function ($input) {
            return $input->type == 2;
        });
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Requisition type is required.',
            'type.integer' => 'Requisition type must be an integer.',

            'product_ids.required' => 'Please select at least one product.',
            'product_ids.array' => 'Product IDs must be an array.',
            'product_ids.*.required' => 'Each product is required.',
            'product_ids.*.integer' => 'Each product ID must be an integer.',

            'quantities.required' => 'Please provide quantities for each product.',
            'quantities.array' => 'Quantities must be an array.',
            'quantities.*.required' => 'Each quantity is required.',
            'quantities.*.min' => 'Each quantity must be at least 1.',

            'attachments.nullable' => 'Attachment not required',
            'attachments.array' => 'Attachments must be an array.',
            'attachments.*.title.nullable' => 'Attachment title is required.',
            'attachments.*.title.string' => 'Attachment title must be a string.',
            'attachments.*.title.max' => 'Attachment title must not exceed 255 characters.',
            'attachments.*.file.nullable' => 'Attachment file is not required.',
            'attachments.*.file.file' => 'Attachment must be a valid file.',
            'attachments.*.file.max' => 'Attachment file must not exceed 3MB.',

            'specification.string' => 'Specification must be a string.',
            'justification.string' => 'Justification must be a string.',
            'remark.required' => 'Nullable is required.',
            'remark.string' => 'Nullable must be a string.',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

<?php

namespace Modules\Requisition\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttachmentUpload extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'requisition_number' => 'required',
            'attachment_title' => 'required',
            'attachment' => 'required|file|max:3072',
            'item_id' => 'required|exists:requisition_items,id',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}

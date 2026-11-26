<?php

namespace Modules\Requisition\App\Http\Requests;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\FormRequest;

class RequisitionItemRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:requisition_categories,id',
            'type' => 'required|string|max:250',
            'sub_category_id' => 'nullable|exists:requisition_sub_categories,id',
            'product_name' => 'required|string|max:250',
            'unit_type' => 'nullable|max:50',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:255',
            'product_code' => 'string|max:20|unique:requisition_items,product_code,' . $this->route('id'),
        ];
    }

    public function withValidator($validator)
    {
        $validator->sometimes('sub_category_id', 'required', function ($input) {
            // Check if the selected category has subcategories
            return DB::table('requisition_sub_categories')
                ->where('category_id', $input->category_id)
                ->exists();
        });
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}

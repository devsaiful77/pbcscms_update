<?php

namespace Modules\Requisition\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'item_ids' => 'required|array',
            'item_ids.*' => 'required|integer',

            'quantities' => 'required|array',
            'quantities.*' => 'required|integer|min:1',

            'low_stock_alert_qty' => 'required|array',
            'low_stock_alert_qty.*' => 'required|integer|min:1',

            'vendor_id' => 'required',
            'challan_no' => 'nullable',
            'low_stock_alert_qty' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'item_ids.required' => 'Please select at least one product.',
            'item_ids.array' => 'Product IDs must be an array.',
            'item_ids.*.required' => 'Each product is required.',
            'item_ids.*.integer' => 'Each product ID must be an integer.',

            'quantities.required' => 'Please provide quantities for each product.',
            'quantities.array' => 'Quantities must be an array.',
            'quantities.*.required' => 'Each quantity is required.',
            'quantities.*.integer' => 'Each quantity must be an integer.',
            'quantities.*.min' => 'Each quantity must be at least 1.',

            'low_stock_alert_qty.required' => 'Please provide low stock quantities for each product.',
            'low_stock_alert_qty.array' => 'Low stock quantities must be an array.',
            'low_stock_alert_qty.*.required' => 'Each low stock quantity is required.',
            'low_stock_alert_qty.*.integer' => 'Each low stock quantity must be an integer.',
            'low_stock_alert_qty.*.min' => 'Each low stock quantity must be at least 1.',

            'vendor_id.required' => 'Vendor field is required.',
            'challan_no.required' => 'Challan number is required.',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

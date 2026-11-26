<?php

namespace Modules\Requisition\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Requisition\App\Models\RequisitionCategory;
use Modules\Requisition\App\Models\RequisitionSubCategory;

class RequisitionSubCategorySeeder extends Seeder
{
    public function run()
    {
        $now = now();

        // Make sure category names exist (or fetch by ID if known)
        $categories = RequisitionCategory::pluck('id', 'category_name');

        $subcategories = [
            [
                'category_id' => $categories['General Support Service'] ?? null,
                'sub_category_name' => 'Repair & Maintenance',
                'sub_category_type' => 1, // 1 for Repair & Maintenance 
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'category_id' => $categories['General Support Service'] ?? null, 
                'sub_category_name' => 'Replacement / New Asset',
                'sub_category_type' => 2, // 2 for Replacement / New Asset
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Filter out any entries with missing category_id (in case of typo or missing parent)
        $subcategories = array_filter($subcategories, fn($item) => $item['category_id'] !== null);

        RequisitionSubCategory::insert($subcategories);
    }
}

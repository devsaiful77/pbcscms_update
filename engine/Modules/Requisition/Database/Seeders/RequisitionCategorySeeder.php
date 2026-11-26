<?php
namespace Modules\Requisition\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Requisition\App\Models\RequisitionCategory;

class RequisitionCategorySeeder extends Seeder
{
    public function run()
    {
        $now = now();

        $categories = [
            ['category_name' => 'General Support Service', 'created_at' => $now, 'updated_at' => $now],
            ['category_name' => 'Printing Stationeries', 'created_at' => $now, 'updated_at' => $now],
            ['category_name' => 'Table Stationeries', 'created_at' => $now, 'updated_at' => $now],
        ];

        RequisitionCategory::insert($categories);
    }
}
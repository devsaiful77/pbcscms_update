<?php

namespace Modules\Requisition\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Requisition\Database\Seeders\RequisitionCategorySeeder;
use Modules\Requisition\Database\Seeders\RequisitionSubCategorySeeder;

class RequisitionDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            RequisitionCategorySeeder::class,
            RequisitionSubCategorySeeder::class,
        ]);
    }
}

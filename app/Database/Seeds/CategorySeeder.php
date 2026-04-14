<?php

namespace App\Database\Seeds;

use App\Models\CategoryModel;
use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $model = new CategoryModel();
        $model->insert([
            'name'      => 'Test Category',
            'tenant_id' => 1,
        ]);
    }
}

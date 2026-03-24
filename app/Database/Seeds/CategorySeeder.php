<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $this->db->table('categories')->insert([
            'name'      => 'Test Category',
            'tenant_id' => 1
        ]);
    }
}

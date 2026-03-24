<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTenantIdToUsers extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('users', [
            'tenant_id' => ['type' => 'int', 'unsigned' => true, 'after' => 'id',]
        ]);
    }

    public function down()
    {
        //
    }
}

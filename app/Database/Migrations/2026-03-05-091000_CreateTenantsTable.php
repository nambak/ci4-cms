<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTenantsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'int', 'unsigned' => true, 'auto_increment' => true],
            'subdomain'  => ['type' => 'varchar', 'constraint' => 255,],
            'name'       => ['type' => 'varchar', 'constraint' => 255],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('subdomain');
        $this->forge->createTable('tenants');
    }

    public function down(): void
    {
        $this->forge->dropTable('tenants');
    }
}
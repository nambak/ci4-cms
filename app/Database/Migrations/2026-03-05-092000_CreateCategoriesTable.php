<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategoriesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'          => ['type' => 'int', 'unsigned' => true, 'auto_increment' => true],
            'tenant_id'   => ['type' => 'int', 'unsigned' => true, 'null' => false],
            'name'        => ['type' => 'varchar', 'constraint' => 255, 'null' => false],
            'slug'        => ['type' => 'varchar', 'constraint' => 255, 'null' => false],
            'description' => ['type' => 'text', 'null' => true],
            'created_at'  => ['type' => 'datetime', 'null' => true],
            'updated_at'  => ['type' => 'datetime', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('tenant_id', 'tenants', 'id', 'NO ACTION', 'CASCADE');
        $this->forge->addUniqueKey(['tenant_id', 'slug']);

        $this->forge->createTable('categories');
    }

    public function down(): void
    {
        $this->forge->dropForeignKey('categories', 'categories_tenant_id_foreign');
        $this->forge->dropTable('categories', true);
    }
}




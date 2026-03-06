<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTagsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'int', 'unsigned' => true, 'auto_increment' => true],
            'tenant_id'  => ['type' => 'int', 'unsigned' => true, 'null' => false],
            'name'       => ['type' => 'varchar', 'constraint' => 255, 'null' => false],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('tenant_id', 'tenants', 'id', 'NO ACTION', 'CASCADE');
        $this->forge->addUniqueKey(['tenant_id', 'name']);
        $this->forge->createTable('tags');
    }

    public function down(): void
    {
        $this->forge->dropForeignKey('tags', 'tags_tenant_id_foreign');
        $this->forge->dropTable('tags', true);
    }
}
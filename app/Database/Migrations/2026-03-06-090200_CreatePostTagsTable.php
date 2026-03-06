<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePostTagsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'post_id'   => ['type' => 'int', 'unsigned' => true],
            'tag_id'    => ['type' => 'int', 'unsigned' => true],
            'tenant_id' => ['type' => 'int', 'unsigned' => true],
        ]);

        $this->forge->addPrimaryKey(['post_id', 'tag_id', 'tenant_id']);
        $this->forge->addForeignKey('post_id', 'posts', 'id', 'NO ACTION', 'CASCADE');
        $this->forge->addForeignKey('tag_id', 'tags', 'id', 'NO ACTION', 'CASCADE');
        $this->forge->addForeignKey('tenant_id', 'tenants', 'id', 'NO ACTION', 'CASCADE');
        $this->forge->createTable('post_tags');
    }

    public function down(): void
    {
        $this->forge->dropTable('post_tags', true);
    }
}

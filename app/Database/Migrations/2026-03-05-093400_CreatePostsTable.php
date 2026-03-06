<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePostsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'          => ['type' => 'int', 'unsigned' => true, 'auto_increment' => true],
            'tenant_id'   => ['type' => 'int', 'unsigned' => true, 'null' => false],
            'category_id' => ['type' => 'int', 'unsigned' => true, 'null' => false],
            'writer_id'   => ['type' => 'int', 'unsigned' => true, 'null' => false],
            'title'       => ['type' => 'varchar', 'constraint' => 255, 'null' => false],
            'content'     => ['type' => 'longtext', 'null' => false],
            'state'       => ['type' => 'varchar', 'constraint' => 255, 'default' => 'draft', 'null' => false],
            'slug'        => ['type' => 'varchar', 'constraint' => 255, 'null' => false],
            'view_count'  => ['type' => 'int', 'unsigned' => true, 'default' => 0, 'null' => false],
            'created_at'  => ['type' => 'datetime', 'null' => true],
            'updated_at'  => ['type' => 'datetime', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('tenant_id', 'tenants', 'id', 'NO ACTION', 'CASCADE');
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'NO ACTION', 'CASCADE');
        $this->forge->addForeignKey('writer_id', 'users', 'id', 'NO ACTION', 'CASCADE');
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('posts');
    }

    public function down(): void
    {
        $this->forge->dropForeignKey('posts', 'posts_tenant_id_foreign');
        $this->forge->dropForeignKey('posts', 'posts_category_id_foreign');
        $this->forge->dropForeignKey('posts', 'posts_writer_id_foreign');
        $this->forge->dropTable('posts', true);
    }
}
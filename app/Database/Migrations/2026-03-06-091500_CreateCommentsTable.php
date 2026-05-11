<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCommentsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'int', 'unsigned' => true, 'auto_increment' => true],
            'parent_id'  => ['type' => 'int', 'unsigned' => true, 'null' => true],
            'post_id'    => ['type' => 'int', 'unsigned' => true, 'null' => false],
            'user_id'    => ['type' => 'int', 'unsigned' => true, 'null' => false],
            'content'    => ['type' => 'text', 'null' => false],
            'state'     => ['type' => 'string', 'null' => false, 'default' => 'pending'],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('parent_id');
        $this->forge->addForeignKey('post_id', 'posts', 'id', 'NO ACTION', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'NO ACTION', 'CASCADE');
        $this->forge->addForeignKey('parent_id', 'comments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('comments');
    }

    public function down(): void
    {
        $this->forge->dropForeignKey('comments', 'comments_post_id_foreign');
        $this->forge->dropForeignKey('comments', 'comments_user_id_foreign');
        $this->forge->dropForeignKey('comments', 'comments_parent_id_foreign');
        $this->forge->dropTable('comments', true);
    }
}

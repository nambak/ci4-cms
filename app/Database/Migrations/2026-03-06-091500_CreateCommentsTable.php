<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCommentsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'int', 'unsigned' => true, 'auto_increment' => true],
            'post_id'    => ['type' => 'int', 'unsigned' => true, 'null' => false],
            'writer_id'  => ['type' => 'int', 'unsigned' => true, 'null' => false],
            'content'    => ['type' => 'text', 'null' => false],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('post_id', 'posts', 'id', 'NO ACTION', 'CASCADE');
        $this->forge->addForeignKey('writer_id', 'users', 'id', 'NO ACTION', 'CASCADE');
        $this->forge->createTable('comments');
    }

    public function down(): void
    {
        $this->forge->dropForeignKey('comments', 'comments_post_id_foreign');
        $this->forge->dropForeignKey('comments', 'comments_writer_id_foreign');
        $this->forge->dropTable('comments', true);
    }   
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMediaTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'            => ['type' => 'int', 'unsigned' => true, 'auto_increment' => true],
            'post_id'       => ['type' => 'int', 'unsigned' => true, 'null' => false],
            'uploader_id'   => ['type' => 'int', 'unsigned' => true, 'null' => false],
            'type'          => ['type' => 'varchar', 'constraint' => 127, 'null' => false],
            'mime_type'     => ['type' => 'varchar', 'constraint' => 127, 'null' => false],
            'filename'      => ['type' => 'varchar', 'constraint' => 255, 'null' => false],
            'original_name' => ['type' => 'varchar', 'constraint' => 255, 'null' => false],
            'file_size'     => ['type' => 'int', 'unsigned' => true, 'null' => false],
            'path'          => ['type' => 'varchar', 'constraint' => 255, 'null' => false],
            'created_at'    => ['type' => 'datetime', 'null' => true],
            'updated_at'    => ['type' => 'datetime', 'null' => true],

        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('post_id', 'posts', 'id', 'NO ACTION', 'CASCADE');
        $this->forge->addForeignKey('uploader_id', 'users', 'id', 'NO ACTION', 'CASCADE');
        $this->forge->createTable('media');
    }

    public function down(): void
    {
        $this->forge->dropForeignKey('media', 'media_post_id_foreign');
        $this->forge->dropForeignKey('media', 'media_uploader_id_foreign');
        $this->forge->dropTable('media', true);
    }
}

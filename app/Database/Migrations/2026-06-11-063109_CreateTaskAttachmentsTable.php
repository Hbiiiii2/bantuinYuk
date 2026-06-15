<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTaskAttachmentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true,
            ],

            'task_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
            ],

            'file_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],

            'file_type' => [
                'type' => 'ENUM',
                'constraint' => [
                    'image',
                    'video',
                    'document'
                ],
            ],

            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
        ]);

        $this->forge->addKey('id', true);

        $this->forge->addForeignKey(
            'task_id',
            'tasks',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('task_attachments');
    }

    public function down()
    {
        $this->forge->dropTable('task_attachments');
    }
}

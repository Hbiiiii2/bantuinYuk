<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTaskProgressTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true
            ],

            'task_id' => [
                'type' => 'BIGINT',
                'unsigned' => true
            ],

            'helper_id' => [
                'type' => 'BIGINT',
                'unsigned' => true
            ],

            'description' => [
                'type' => 'TEXT'
            ],

            'attachment' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],

            'status' => [
                'type' => 'ENUM',
                'constraint' => [
                    'started',
                    'progress',
                    'submitted'
                ]
            ],

            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP'
        ]);

        $this->forge->addKey('id', true);

        $this->forge->addForeignKey(
            'task_id',
            'tasks',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'helper_id',
            'users',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('task_progress');
    }

    public function down()
    {
        $this->forge->dropTable('task_progress');
    }
}

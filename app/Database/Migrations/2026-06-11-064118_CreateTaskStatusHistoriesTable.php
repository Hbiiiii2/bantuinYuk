<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTaskStatusHistoriesTable extends Migration
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

            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],

            'note' => [
                'type' => 'TEXT',
                'null' => true
            ],

            'created_by' => [
                'type' => 'BIGINT',
                'unsigned' => true
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
            'created_by',
            'users',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('task_status_histories');
    }

    public function down()
    {
        $this->forge->dropTable('task_status_histories');
    }
}

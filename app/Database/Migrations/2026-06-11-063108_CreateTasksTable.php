<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTasksTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true,
            ],

            'user_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
            ],

            'helper_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
            ],

            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],

            'category' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],

            'description' => [
                'type' => 'TEXT',
            ],

            'price' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],

            'location' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'deadline_start' => [
                'type' => 'DATETIME',
            ],

            'deadline_end' => [
                'type' => 'DATETIME',
            ],

            'status' => [
                'type' => 'ENUM',
                'constraint' => [
                    'draft',
                    'open',
                    'accepted',
                    'in_progress',
                    'waiting_approval',
                    'completed',
                    'cancelled',
                    'disputed'
                ],
                'default' => 'open',
            ],

            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);

        $this->forge->addKey('id', true);

        $this->forge->addForeignKey(
            'user_id',
            'users',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'helper_id',
            'users',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->forge->createTable('tasks');
    }

    public function down()
    {
        $this->forge->dropTable('tasks');
    }
}

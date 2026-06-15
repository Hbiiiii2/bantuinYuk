<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDisputesTable extends Migration
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

            'user_id' => [
                'type' => 'BIGINT',
                'unsigned' => true
            ],

            'helper_id' => [
                'type' => 'BIGINT',
                'unsigned' => true
            ],

            'reason' => [
                'type' => 'TEXT'
            ],

            'evidence_file' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],

            'admin_note' => [
                'type' => 'TEXT',
                'null' => true
            ],

            'status' => [
                'type' => 'ENUM',
                'constraint' => [
                    'open',
                    'investigating',
                    'resolved',
                    'rejected'
                ],
                'default' => 'open'
            ],

            'resolved_by' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true
            ],

            'resolved_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],

            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ]);

        $this->forge->addKey('id', true);

        $this->forge->addKey('task_id');
        $this->forge->addKey('user_id');
        $this->forge->addKey('helper_id');

        $this->forge->addForeignKey(
            'task_id',
            'tasks',
            'id',
            'CASCADE',
            'CASCADE'
        );

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
            'CASCADE',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'resolved_by',
            'users',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->forge->createTable('disputes');
        }

    public function down()
    {
        $this->forge->dropTable('disputes');
    }
}

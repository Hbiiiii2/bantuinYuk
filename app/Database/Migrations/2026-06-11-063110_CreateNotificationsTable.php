<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([

            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true
            ],

            'user_id' => [
                'type' => 'BIGINT',
                'unsigned' => true
            ],

            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],

            'message' => [
                'type' => 'TEXT'
            ],

            'type' => [
                'type' => 'ENUM',
                'constraint' => [
                    'task',
                    'payment',
                    'system',
                    'dispute'
                ],
                'default' => 'system'
            ],

            'is_read' => [
                'type' => 'BOOLEAN',
                'default' => false
            ],

            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP'
        ]);

        $this->forge->addKey('id', true);

        $this->forge->addKey('user_id');

        $this->forge->addForeignKey(
            'user_id',
            'users',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('notifications');
    }

    public function down()
    {
        $this->forge->dropTable('notifications');
    }
}

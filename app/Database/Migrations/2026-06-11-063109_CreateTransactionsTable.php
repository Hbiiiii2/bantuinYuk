<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransactionsTable extends Migration
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

            'task_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true
            ],

            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2'
            ],

            'type' => [
                'type' => 'ENUM',
                'constraint' => [
                    'topup',
                    'payment',
                    'withdraw',
                    'refund'
                ]
            ],

            'status' => [
                'type' => 'ENUM',
                'constraint' => [
                    'pending',
                    'success',
                    'failed',
                    'cancelled'
                ],
                'default' => 'pending'
            ],

            'reference_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],

            'description' => [
                'type' => 'TEXT',
                'null' => true
            ],

            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ]);

        $this->forge->addKey('id', true);

        $this->forge->addKey('user_id');
        $this->forge->addKey('task_id');

        $this->forge->addForeignKey(
            'user_id',
            'users',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'task_id',
            'tasks',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->forge->createTable('transactions');

    }

    public function down()
    {
        $this->forge->dropTable('transactions');
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTaskReviewsTable extends Migration
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

            'rating' => [
                'type' => 'INT',
                'constraint' => 1
            ],

            'review' => [
                'type' => 'TEXT',
                'null' => true
            ],

            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP'
        ]);

        $this->forge->addKey('id', true);

        $this->forge->addKey('task_id');
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

        $this->forge->createTable('task_reviews');

    }

    public function down()
    {
        $this->forge->dropTable('task_reviews');
    }
}

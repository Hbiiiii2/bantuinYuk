<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCancelReasonToTasksTable extends Migration
{
    public function up()
    {
        $fields = [
            'cancel_reason' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'status'
            ]
        ];

        $this->forge->addColumn('tasks', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tasks', 'cancel_reason');
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLastActiveToUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'last_active' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'after'   => 'status',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'last_active');
    }
}

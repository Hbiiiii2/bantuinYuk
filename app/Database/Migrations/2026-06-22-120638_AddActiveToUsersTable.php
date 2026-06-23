<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddActiveToUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 1,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'active');
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPendingBalanceToWalletsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('wallets', [
            'pending_balance' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
                'after'      => 'balance',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('wallets', 'pending_balance');
    }
}

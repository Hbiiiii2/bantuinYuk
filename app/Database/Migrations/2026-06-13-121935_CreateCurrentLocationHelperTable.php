<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCurrentLocationHelperTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'helper_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'latitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,8',
                'null'       => true,
            ],

            'longitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,8',
                'null'       => true,
            ],

            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);

        $this->forge->addForeignKey(
            'helper_id',
            'helper_profiles',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('locations');
    }

    public function down()
    {
        $this->forge->dropTable('locations');
    }
}

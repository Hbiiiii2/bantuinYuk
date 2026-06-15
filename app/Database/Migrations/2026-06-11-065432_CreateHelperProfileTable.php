<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHelperProfileTable extends Migration
{
    public function up()
    {
        $this->forge->addField([

            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'user_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],

            'bio' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'skills' => [
                'type' => 'TEXT',
                'null' => true,
            ],



            'ktp_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],

            'ktp_photo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],

            'completed_tasks' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],

            'verification_status' => [
                'type'       => 'ENUM',
                'constraint' => [
                    'pending',
                    'verified',
                    'rejected'
                ],
                'default' => 'pending',
            ],

            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',

            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);

        $this->forge->addKey('id', true);

        // satu helper hanya boleh punya satu profile
        $this->forge->addUniqueKey('user_id');

        $this->forge->addForeignKey(
            'user_id',
            'users',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // $this->forge->addForeignKey('location', 'locations', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('helper_profiles');
    }

    public function down()
    {
        $this->forge->dropTable('helper_profiles');
    }
}

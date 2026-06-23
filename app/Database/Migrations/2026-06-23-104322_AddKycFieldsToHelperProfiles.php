<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKycFieldsToHelperProfiles extends Migration
{
    public function up()
    {
        $fields = [
            'address' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'skills'
            ],
            'ktp_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'address'
            ],
            'selfie_photo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'ktp_photo'
            ]
        ];

        $this->forge->addColumn('helper_profiles', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('helper_profiles', ['address', 'ktp_name', 'selfie_photo']);
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([

            'id'=>[
                'type'=>'BIGINT',
                'unsigned'=>true,
                'auto_increment'=>true
            ],

            'name'=>[
                'type'=>'VARCHAR',
                'constraint'=>100
            ],

            'icon'=>[
                'type'=>'VARCHAR',
                'constraint'=>255,
                'null'=>true
            ],

            'status'=>[
                'type'=>'ENUM',
                'constraint'=>['active','inactive'],
                'default'=>'active'
            ],

            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP'
        ]);

        $this->forge->addKey('id', true);

        $this->forge->createTable('categories');

    }

    public function down()
    {
        $this->forge->dropTable('categories');
    }
}

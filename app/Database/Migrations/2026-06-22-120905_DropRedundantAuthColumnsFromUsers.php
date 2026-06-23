<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropRedundantAuthColumnsFromUsers extends Migration
{
    public function up()
    {
        // First drop the unique key on email. CI4 usually names the index after the column if unique=>true was used.
        try {
            $this->db->query('ALTER TABLE `users` DROP INDEX `users_email`');
        } catch (\Exception $e) {
            try {
                $this->db->query('ALTER TABLE `users` DROP INDEX `email`');
            } catch (\Exception $e2) {
                // Ignore if it doesn't exist
            }
        }

        $this->forge->dropColumn('users', ['email', 'password']);
    }

    public function down()
    {
        $this->forge->addColumn('users', [
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
        ]);
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateShieldAuthTables extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Auth Identities Table (for passwords, access tokens, etc.)
        if (!$db->tableExists('auth_identities')) {
            $this->forge->addField([
                'id'           => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'user_id'      => ['type' => 'BIGINT', 'unsigned' => true],
                'type'         => ['type' => 'VARCHAR', 'constraint' => 255],
                'name'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'secret'       => ['type' => 'VARCHAR', 'constraint' => 255],
                'secret2'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'expires'      => ['type' => 'DATETIME', 'null' => true],
                'extra'        => ['type' => 'TEXT', 'null' => true],
                'force_reset'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'last_used_at' => ['type' => 'DATETIME', 'null' => true],
                'created_at'   => ['type' => 'DATETIME', 'null' => true],
                'updated_at'   => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['type', 'secret']);
            $this->forge->addKey('user_id');
            $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
            $this->forge->createTable('auth_identities');
        }

        // Auth Login Attempts Table
        if (!$db->tableExists('auth_logins')) {
            $this->forge->addField([
                'id'         => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'ip_address' => ['type' => 'VARCHAR', 'constraint' => 255],
                'user_agent' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'id_type'    => ['type' => 'VARCHAR', 'constraint' => 255],
                'identifier' => ['type' => 'VARCHAR', 'constraint' => 255],
                'user_id'    => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
                'date'       => ['type' => 'DATETIME'],
                'success'    => ['type' => 'TINYINT', 'constraint' => 1],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('user_id');
            $this->forge->createTable('auth_logins');
        }

        // Auth Token Logins Table
        if (!$db->tableExists('auth_token_logins')) {
            $this->forge->addField([
                'id'         => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'ip_address' => ['type' => 'VARCHAR', 'constraint' => 255],
                'user_agent' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'id_type'    => ['type' => 'VARCHAR', 'constraint' => 255],
                'identifier' => ['type' => 'VARCHAR', 'constraint' => 255],
                'user_id'    => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
                'date'       => ['type' => 'DATETIME'],
                'success'    => ['type' => 'TINYINT', 'constraint' => 1],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('user_id');
            $this->forge->createTable('auth_token_logins');
        }

        // Auth Remember Tokens Table
        if (!$db->tableExists('auth_remember_tokens')) {
            $this->forge->addField([
                'id'         => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'selector'   => ['type' => 'VARCHAR', 'constraint' => 255],
                'hashedValidator' => ['type' => 'VARCHAR', 'constraint' => 255],
                'user_id'    => ['type' => 'BIGINT', 'unsigned' => true],
                'expires'    => ['type' => 'DATETIME', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('selector');
            $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
            $this->forge->createTable('auth_remember_tokens');
        }

        // Auth Groups Users Table
        if (!$db->tableExists('auth_groups_users')) {
            $this->forge->addField([
                'id'         => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'user_id'    => ['type' => 'BIGINT', 'unsigned' => true],
                'group'      => ['type' => 'VARCHAR', 'constraint' => 255],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['user_id', 'group']);
            $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
            $this->forge->createTable('auth_groups_users');
        }

        // Auth Permissions Users Table
        if (!$db->tableExists('auth_permissions_users')) {
            $this->forge->addField([
                'id'         => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'user_id'    => ['type' => 'BIGINT', 'unsigned' => true],
                'permission' => ['type' => 'VARCHAR', 'constraint' => 255],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['user_id', 'permission']);
            $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
            $this->forge->createTable('auth_permissions_users');
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        $this->forge->dropTable('auth_permissions_users', true);
        $this->forge->dropTable('auth_groups_users', true);
        $this->forge->dropTable('auth_remember_tokens', true);
        $this->forge->dropTable('auth_token_logins', true);
        $this->forge->dropTable('auth_logins', true);
        $this->forge->dropTable('auth_identities', true);
    }
}

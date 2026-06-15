<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixDatabaseConstraints extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // ============================================================
        // 1. Fix tasks.category → category_id FK ke categories.id
        // ============================================================

        // Cek apakah kolom category_id sudah ada
        if (!$db->fieldExists('category_id', 'tasks')) {
            // Tambah kolom category_id
            $this->forge->addColumn('tasks', [
                'category_id' => [
                    'type'       => 'BIGINT',
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'helper_id',
                ],
            ]);

            // Copy data: lookup category name → category_id
            $categories = $db->table('categories')->get()->getResultArray();
            foreach ($categories as $cat) {
                $db->table('tasks')
                    ->where('category', $cat['name'])
                    ->update(['category_id' => $cat['id']]);
            }

            // Hapus kolom lama category (VARCHAR)
            $this->forge->dropColumn('tasks', 'category');

            // Modify category_id: remove null setelah data dicopy
            $this->forge->modifyColumn('tasks', [
                'category_id' => [
                    'type'     => 'BIGINT',
                    'unsigned' => true,
                    'null'     => true,
                ],
            ]);

            // Add FK ke categories
            $this->forge->addForeignKey(
                'category_id',
                'categories',
                'id',
                'SET NULL',
                'CASCADE'
            );
        }

        // ============================================================
        // 2. Fix locations.helper_id FK ke users.id (bukan helper_profiles.id)
        // ============================================================

        // Drop existing FK ke helper_profiles
        $fkName = $db->query(
            "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
             WHERE TABLE_SCHEMA = '{$db->DBDatabase}' 
             AND TABLE_NAME = 'locations' 
             AND COLUMN_NAME = 'helper_id'
             AND REFERENCED_TABLE_NAME IS NOT NULL"
        )->getRow();

        if ($fkName) {
            $db->query("ALTER TABLE `locations` DROP FOREIGN KEY `{$fkName->CONSTRAINT_NAME}`");
        }

        // Add new FK ke users
        $this->forge->addForeignKey(
            'helper_id',
            'users',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // ============================================================
        // 3. Add UNIQUE constraint ke wallets.user_id
        // ============================================================

        // Cek apakah unique constraint sudah ada
        $hasUnique = $db->query(
            "SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS 
             WHERE TABLE_SCHEMA = '{$db->DBDatabase}' 
             AND TABLE_NAME = 'wallets' 
             AND NON_UNIQUE = 0 
             AND COLUMN_NAME = 'user_id'"
        )->getRow();

        if (!$hasUnique) {
            $this->forge->addUniqueKey('user_id');
        }

        // ============================================================
        // 4. Add UNIQUE constraint ke task_reviews (task_id, user_id)
        // ============================================================

        $hasUniqueReview = $db->query(
            "SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS 
             WHERE TABLE_SCHEMA = '{$db->DBDatabase}' 
             AND TABLE_NAME = 'task_reviews' 
             AND NON_UNIQUE = 0"
        )->getRow();

        if (!$hasUniqueReview) {
            $this->forge->addUniqueKey(['task_id', 'user_id']);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        // Reverse 4: Drop unique task_reviews
        $db->query('ALTER TABLE `task_reviews` DROP INDEX `task_reviews_task_id_user_id_unique`');

        // Reverse 3: Drop unique wallets
        $db->query('ALTER TABLE `wallets` DROP INDEX `wallets_user_id_unique`');

        // Reverse 2: Drop FK users, add FK helper_profiles
        $fkName = $db->query(
            "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
             WHERE TABLE_SCHEMA = '{$db->DBDatabase}' 
             AND TABLE_NAME = 'locations' 
             AND COLUMN_NAME = 'helper_id'
             AND REFERENCED_TABLE_NAME IS NOT NULL"
        )->getRow();

        if ($fkName) {
            $db->query("ALTER TABLE `locations` DROP FOREIGN KEY `{$fkName->CONSTRAINT_NAME}`");
        }

        $this->forge->addForeignKey(
            'helper_id',
            'helper_profiles',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Reverse 1: Restore tasks.category
        $this->forge->dropColumn('tasks', 'category_id');

        $this->forge->addColumn('tasks', [
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
        ]);
    }
}

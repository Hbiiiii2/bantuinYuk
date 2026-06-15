<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPerformanceIndexes extends Migration
{
    public function up()
    {
        // ============================================================
        // 1. tasks.status index (sering filter by status)
        // ============================================================
        $this->forge->addKey('status', false, 'idx_tasks_status');

        // ============================================================
        // 2. transactions composite indexes
        // ============================================================
        // Query: WHERE user_id = ? AND status = ? ORDER BY created_at DESC
        $this->forge->addKey(['user_id', 'status'], false, 'idx_transactions_user_status');

        // Query: WHERE user_id = ? AND type = ? ORDER BY created_at DESC
        $this->forge->addKey(['user_id', 'type'], false, 'idx_transactions_user_type');

        // ============================================================
        // 3. notifications composite index
        // ============================================================
        // Query: WHERE user_id = ? AND is_read = ? ORDER BY created_at DESC
        $this->forge->addKey(['user_id', 'is_read'], false, 'idx_notifications_user_read');

        // ============================================================
        // 4. disputes.status index
        // ============================================================
        // Query: WHERE status = ? ORDER BY created_at DESC
        $this->forge->addKey('status', false, 'idx_disputes_status');

        // Apply indexes via ALTER TABLE
        $db = \Config\Database::connect();

        // tasks.status
        if (!$this->indexExists($db, 'tasks', 'idx_tasks_status')) {
            $db->query('ALTER TABLE `tasks` ADD INDEX `idx_tasks_status` (`status`)');
        }

        // transactions composite indexes
        if (!$this->indexExists($db, 'transactions', 'idx_transactions_user_status')) {
            $db->query('ALTER TABLE `transactions` ADD INDEX `idx_transactions_user_status` (`user_id`, `status`)');
        }

        if (!$this->indexExists($db, 'transactions', 'idx_transactions_user_type')) {
            $db->query('ALTER TABLE `transactions` ADD INDEX `idx_transactions_user_type` (`user_id`, `type`)');
        }

        // notifications composite index
        if (!$this->indexExists($db, 'notifications', 'idx_notifications_user_read')) {
            $db->query('ALTER TABLE `notifications` ADD INDEX `idx_notifications_user_read` (`user_id`, `is_read`)');
        }

        // disputes.status
        if (!$this->indexExists($db, 'disputes', 'idx_disputes_status')) {
            $db->query('ALTER TABLE `disputes` ADD INDEX `idx_disputes_status` (`status`)');
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        $db->query('ALTER TABLE `tasks` DROP INDEX `idx_tasks_status`');
        $db->query('ALTER TABLE `transactions` DROP INDEX `idx_transactions_user_status`');
        $db->query('ALTER TABLE `transactions` DROP INDEX `idx_transactions_user_type`');
        $db->query('ALTER TABLE `notifications` DROP INDEX `idx_notifications_user_read`');
        $db->query('ALTER TABLE `disputes` DROP INDEX `idx_disputes_status`');
    }

    private function indexExists($db, string $table, string $indexName): bool
    {
        $result = $db->query(
            "SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS 
             WHERE TABLE_SCHEMA = '{$db->DBDatabase}' 
             AND TABLE_NAME = '{$table}' 
             AND INDEX_NAME = '{$indexName}'"
        )->getRow();

        return $result !== null;
    }
}

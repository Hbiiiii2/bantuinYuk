<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table = 'transactions';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'user_id',
        'task_id',
        'amount',
        'type',
        'status',
        'reference_id',
        'description'
    ];

    const TYPE_TASK_PAYMENT  = 'payment';
    const TYPE_WITHDRAW      = 'withdraw';
    const TYPE_REFUND        = 'refund';
    const TYPE_ADJUSTMENT    = 'topup';

    const STATUS_PENDING   = 'pending';
    const STATUS_COMPLETED = 'success';
    const STATUS_FAILED    = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get transactions by user_id with pagination.
     */
    public function getByUserId(int $userId, int $page = 1, int $perPage = 20): array
    {
        $builder = $this->builder();
        $builder->where('user_id', $userId);

        $total = $builder->countAllResults(false);

        $builder->orderBy('created_at', 'DESC');
        $builder->limit($perPage, ($page - 1) * $perPage);

        $transactions = $builder->get()->getResultArray();

        return [
            'data'       => $transactions,
            'total'      => $total,
            'page'       => $page,
            'per_page'   => $perPage,
        ];
    }

    /**
     * Get transactions by type for a user.
     */
    public function getByType(int $userId, string $type, int $page = 1, int $perPage = 20): array
    {
        $builder = $this->builder();
        $builder->where('user_id', $userId);
        $builder->where('type', $type);

        $total = $builder->countAllResults(false);

        $builder->orderBy('created_at', 'DESC');
        $builder->limit($perPage, ($page - 1) * $perPage);

        $transactions = $builder->get()->getResultArray();

        return [
            'data'       => $transactions,
            'total'      => $total,
            'page'       => $page,
            'per_page'   => $perPage,
        ];
    }

    /**
     * Get transaction by reference_id.
     */
    public function getByReferenceId(string $referenceId): ?array
    {
        return $this->where('reference_id', $referenceId)->first();
    }

    /**
     * Check if payment for task already exists (idempotency).
     */
    public function hasPaymentForTask(int $taskId): bool
    {
        return $this->where('task_id', $taskId)
            ->where('type', self::TYPE_TASK_PAYMENT)
            ->where('status', self::STATUS_COMPLETED)
            ->countAllResults() > 0;
    }

    /**
     * Get transaction by task_id and type.
     */
    public function getByTaskAndType(int $taskId, string $type): ?array
    {
        return $this->where('task_id', $taskId)
            ->where('type', $type)
            ->first();
    }

    /**
     * Get pending withdrawals.
     */
    public function getPendingWithdrawals(int $page = 1, int $perPage = 20): array
    {
        $builder = $this->builder();
        $builder->select('transactions.*, users.name as user_name');
        $builder->join('users', 'users.id = transactions.user_id', 'left');
        $builder->where('transactions.type', self::TYPE_WITHDRAW);
        $builder->where('transactions.status', self::STATUS_PENDING);

        $total = $builder->countAllResults(false);

        $builder->orderBy('transactions.created_at', 'DESC');
        $builder->limit($perPage, ($page - 1) * $perPage);

        $transactions = $builder->get()->getResultArray();

        return [
            'data'       => $transactions,
            'total'      => $total,
            'page'       => $page,
            'per_page'   => $perPage,
        ];
    }

    /**
     * Get all transactions (admin).
     */
    public function getAllTransactions(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $builder = $this->builder();
        $builder->select('transactions.*, users.name as user_name');
        $builder->join('users', 'users.id = transactions.user_id', 'left');

        if (!empty($filters['user_id'])) {
            $builder->where('transactions.user_id', $filters['user_id']);
        }
        if (!empty($filters['type'])) {
            $builder->where('transactions.type', $filters['type']);
        }
        if (!empty($filters['status'])) {
            $builder->where('transactions.status', $filters['status']);
        }

        $total = $builder->countAllResults(false);

        $builder->orderBy('transactions.created_at', 'DESC');
        $builder->limit($perPage, ($page - 1) * $perPage);

        $transactions = $builder->get()->getResultArray();

        return [
            'data'       => $transactions,
            'total'      => $total,
            'page'       => $page,
            'per_page'   => $perPage,
        ];
    }
}

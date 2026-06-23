<?php

namespace App\Models;

use CodeIgniter\Model;

class DisputeModel extends Model
{
    protected $table = 'disputes';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'task_id',
        'user_id',
        'helper_id',
        'reason',
        'evidence_file',
        'admin_note',
        'status',
        'resolved_by',
        'resolved_at'
    ];

    const STATUS_OPEN         = 'open';
    const STATUS_UNDER_REVIEW = 'investigating';
    const STATUS_RESOLVED     = 'resolved';
    const STATUS_REJECTED     = 'rejected';

    const VALID_STATUSES = [
        self::STATUS_OPEN,
        self::STATUS_UNDER_REVIEW,
        self::STATUS_RESOLVED,
        self::STATUS_REJECTED,
    ];

    /**
     * Check if dispute exists for task (active only).
     */
    public function hasActiveDispute(int $taskId): bool
    {
        return $this->where('task_id', $taskId)
            ->whereIn('status', [self::STATUS_OPEN, self::STATUS_UNDER_REVIEW])
            ->countAllResults() > 0;
    }

    /**
     * Get disputes for user (as creator or counterparty).
     */
    public function getByUserId(int $userId, int $page = 1, int $perPage = 20, ?string $status = null, ?string $search = null): array
    {
        $builder = $this->builder();
        $builder->select('disputes.*, tasks.title as task_title, users.name as creator_name');
        $builder->join('tasks', 'tasks.id = disputes.task_id', 'left');
        $builder->join('users', 'users.id = disputes.user_id', 'left');
        $builder->where('(disputes.user_id = ' . $userId . ' OR disputes.helper_id = ' . $userId . ')');

        if ($status) {
            $builder->where('disputes.status', $status);
        }

        if ($search) {
            $builder->groupStart();
            $builder->like('tasks.title', $search);
            $builder->orLike('disputes.reason', $search);
            $builder->groupEnd();
        }

        $total = $builder->countAllResults(false);

        $builder->orderBy('disputes.created_at', 'DESC');
        $builder->limit($perPage, ($page - 1) * $perPage);

        $disputes = $builder->get()->getResultArray();

        return [
            'data'       => $disputes,
            'total'      => $total,
            'page'       => $page,
            'per_page'   => $perPage,
        ];
    }

    /**
     * Get all disputes (admin).
     */
    public function getAllDisputes(int $page = 1, int $perPage = 20, ?string $status = null, ?string $search = null): array
    {
        $builder = $this->builder();
        $builder->select('disputes.*, tasks.title as task_title, users.name as creator_name, helpers.name as helper_name');
        $builder->join('tasks', 'tasks.id = disputes.task_id', 'left');
        $builder->join('users', 'users.id = disputes.user_id', 'left');
        $builder->join('users as helpers', 'helpers.id = disputes.helper_id', 'left');

        if ($status) {
            $builder->where('disputes.status', $status);
        }

        if ($search) {
            $builder->groupStart();
            $builder->like('tasks.title', $search);
            $builder->orLike('disputes.reason', $search);
            $builder->groupEnd();
        }

        $total = $builder->countAllResults(false);

        $builder->orderBy('disputes.created_at', 'DESC');
        $builder->limit($perPage, ($page - 1) * $perPage);

        $disputes = $builder->get()->getResultArray();

        return [
            'data'       => $disputes,
            'total'      => $total,
            'page'       => $page,
            'per_page'   => $perPage,
        ];
    }

    /**
     * Get dispute by ID with joins.
     */
    public function getDisputeById(int $disputeId): ?array
    {
        $builder = $this->builder();
        $builder->select('disputes.*, tasks.title as task_title, tasks.status as task_status, 
                         users.name as creator_name, helpers.name as helper_name,
                         resolvers.name as resolved_by_name');
        $builder->join('tasks', 'tasks.id = disputes.task_id', 'left');
        $builder->join('users', 'users.id = disputes.user_id', 'left');
        $builder->join('users as helpers', 'helpers.id = disputes.helper_id', 'left');
        $builder->join('users as resolvers', 'resolvers.id = disputes.resolved_by', 'left');
        $builder->where('disputes.id', $disputeId);

        return $builder->get()->getRowArray();
    }

    /**
     * Check if user is involved in dispute.
     */
    public function isInvolved(int $disputeId, int $userId): bool
    {
        return $this->where('id', $disputeId)
            ->where('(user_id = ' . $userId . ' OR helper_id = ' . $userId . ')')
            ->countAllResults() > 0;
    }

    /**
     * Check if user is dispute creator.
     */
    public function isCreator(int $disputeId, int $userId): bool
    {
        return $this->where('id', $disputeId)
            ->where('user_id', $userId)
            ->countAllResults() > 0;
    }
}

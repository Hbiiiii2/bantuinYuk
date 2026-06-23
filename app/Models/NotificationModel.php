<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read'
    ];

    const TYPE_TASK_CREATED       = 'task_created';
    const TYPE_TASK_ACCEPTED      = 'task_accepted';
    const TYPE_TASK_STARTED       = 'task_started';
    const TYPE_TASK_PROGRESS      = 'task_progress';
    const TYPE_TASK_SUBMITTED     = 'task_submitted';
    const TYPE_TASK_COMPLETED     = 'task_completed';
    const TYPE_TASK_CANCELLED     = 'task_cancelled';
    const TYPE_REVIEW_RECEIVED    = 'review_received';
    const TYPE_PAYMENT_RELEASED   = 'payment_released';
    const TYPE_WITHDRAW_REQUESTED = 'withdraw_requested';
    const TYPE_WITHDRAW_APPROVED  = 'withdraw_approved';
    const TYPE_WITHDRAW_REJECTED  = 'withdraw_rejected';
    const TYPE_DISPUTE_CREATED    = 'dispute_created';
    const TYPE_DISPUTE_RESOLVED   = 'dispute_resolved';

    /**
     * Get notifications for user with pagination.
     */
    public function getByUserId(int $userId, int $page = 1, int $perPage = 20, ?bool $unreadOnly = null): array
    {
        $builder = $this->builder();
        $builder->where('user_id', $userId);

        if ($unreadOnly === true) {
            $builder->where('is_read', 0);
        }

        $total = $builder->countAllResults(false);

        $builder->orderBy('created_at', 'DESC');
        $builder->limit($perPage, ($page - 1) * $perPage);

        $notifications = $builder->get()->getResultArray();

        return [
            'data'       => $notifications,
            'total'      => $total,
            'page'       => $page,
            'per_page'   => $perPage,
        ];
    }

    /**
     * Get unread count for user.
     */
    public function getUnreadCount(int $userId): int
    {
        return $this->where('user_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(int $notificationId, int $userId): bool
    {
        return $this->builder()
            ->where('id', $notificationId)
            ->where('user_id', $userId)
            ->update(['is_read' => 1]);
    }

    /**
     * Mark all notifications as read for user.
     */
    public function markAllAsRead(int $userId): bool
    {
        return $this->builder()
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);
    }

    /**
     * Delete read notifications for user.
     */
    public function deleteRead(int $userId): bool
    {
        return $this->builder()
            ->where('user_id', $userId)
            ->where('is_read', 1)
            ->delete();
    }

    /**
     * Check if notification belongs to user.
     */
    public function belongsToUser(int $notificationId, int $userId): bool
    {
        return $this->where('id', $notificationId)
            ->where('user_id', $userId)
            ->countAllResults() > 0;
    }
}

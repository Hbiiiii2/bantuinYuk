<?php

namespace App\Services;

use App\Models\NotificationModel;
use App\Exceptions\BusinessException;

class NotificationService extends BaseService
{
    protected NotificationModel $notificationModel;

    public function __construct()
    {
        parent::__construct();
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Create a single notification.
     */
    public function create(int $userId, string $type, string $title, string $message, array $data = []): int
    {
        $notificationId = $this->notificationModel->insert([
            'user_id'  => $userId,
            'type'     => $type,
            'title'    => $title,
            'message'  => $message,
            'data'     => !empty($data) ? json_encode($data) : null,
            'is_read'  => 0,
        ]);

        if (!$notificationId) {
            throw BusinessException::failed('Failed to create notification');
        }

        return $notificationId;
    }

    /**
     * Create bulk notifications for multiple users.
     */
    public function createBulk(array $userIds, string $type, string $title, string $message, array $data = []): void
    {
        $notifications = [];
        foreach ($userIds as $userId) {
            $notifications[] = [
                'user_id'    => $userId,
                'type'       => $type,
                'title'      => $title,
                'message'    => $message,
                'data'       => !empty($data) ? json_encode($data) : null,
                'is_read'    => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        $this->notificationModel->insertBatch($notifications);
    }

    /**
     * Get notifications for user with pagination.
     */
    public function getUserNotifications(int $userId, int $page = 1, int $perPage = 20, ?bool $unreadOnly = null): array
    {
        return $this->notificationModel->getByUserId($userId, $page, $perPage, $unreadOnly);
    }

    /**
     * Get notification by ID with ownership check.
     */
    public function getNotificationById(int $notificationId, int $userId): array
    {
        if (!$this->notificationModel->belongsToUser($notificationId, $userId)) {
            throw BusinessException::notFound('Notification not found');
        }

        $notification = $this->notificationModel->find($notificationId);

        if (!$notification) {
            throw BusinessException::notFound('Notification not found');
        }

        return $notification;
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(int $notificationId, int $userId): bool
    {
        if (!$this->notificationModel->belongsToUser($notificationId, $userId)) {
            throw BusinessException::notFound('Notification not found');
        }

        return $this->notificationModel->markAsRead($notificationId, $userId);
    }

    /**
     * Mark all notifications as read for user.
     */
    public function markAllAsRead(int $userId): bool
    {
        return $this->notificationModel->markAllAsRead($userId);
    }

    /**
     * Get unread count for user.
     */
    public function getUnreadCount(int $userId): int
    {
        return $this->notificationModel->getUnreadCount($userId);
    }

    // ============================================================
    // TASK NOTIFICATION HELPERS
    // ============================================================

    /**
     * Notify task owner when task is created.
     */
    public function notifyTaskCreated(int $userId, array $task): void
    {
        $this->create(
            $userId,
            NotificationModel::TYPE_TASK_CREATED,
            'Task Created',
            "Your task \"{$task['title']}\" has been created successfully.",
            ['task_id' => $task['id']]
        );
    }

    /**
     * Notify task owner when helper accepts task.
     */
    public function notifyTaskAccepted(int $taskId, string $taskTitle, int $ownerId, int $helperId, string $helperName): void
    {
        $this->create(
            $ownerId,
            NotificationModel::TYPE_TASK_ACCEPTED,
            'Task Accepted',
            "{$helperName} has accepted your task \"{$taskTitle}\".",
            ['task_id' => $taskId, 'helper_id' => $helperId]
        );
    }

    /**
     * Notify task owner when helper starts work.
     */
    public function notifyTaskStarted(int $taskId, string $taskTitle, int $ownerId, int $helperId, string $helperName): void
    {
        $this->create(
            $ownerId,
            NotificationModel::TYPE_TASK_STARTED,
            'Task Started',
            "{$helperName} has started working on \"{$taskTitle}\".",
            ['task_id' => $taskId, 'helper_id' => $helperId]
        );
    }

    /**
     * Notify task owner when progress is added.
     */
    public function notifyTaskProgress(int $taskId, string $taskTitle, int $ownerId, int $helperId, string $helperName): void
    {
        $this->create(
            $ownerId,
            NotificationModel::TYPE_TASK_PROGRESS,
            'Progress Update',
            "{$helperName} has added progress to \"{$taskTitle}\".",
            ['task_id' => $taskId, 'helper_id' => $helperId]
        );
    }

    /**
     * Notify task owner when helper submits work.
     */
    public function notifyTaskSubmitted(int $taskId, string $taskTitle, int $ownerId, int $helperId, string $helperName): void
    {
        $this->create(
            $ownerId,
            NotificationModel::TYPE_TASK_SUBMITTED,
            'Task Submitted',
            "{$helperName} has submitted work for \"{$taskTitle}\". Please review and complete.",
            ['task_id' => $taskId, 'helper_id' => $helperId]
        );
    }

    /**
     * Notify helper when task is completed.
     */
    public function notifyTaskCompleted(int $taskId, string $taskTitle, int $helperId, int $ownerId, string $ownerName): void
    {
        $this->create(
            $helperId,
            NotificationModel::TYPE_TASK_COMPLETED,
            'Task Completed',
            "{$ownerName} has completed \"{$taskTitle}\".",
            ['task_id' => $taskId, 'owner_id' => $ownerId]
        );
    }

    /**
     * Notify helper when task is cancelled.
     */
    public function notifyTaskCancelled(int $taskId, string $taskTitle, int $helperId, int $ownerId, string $ownerName): void
    {
        $this->create(
            $helperId,
            NotificationModel::TYPE_TASK_CANCELLED,
            'Task Cancelled',
            "{$ownerName} has cancelled \"{$taskTitle}\".",
            ['task_id' => $taskId, 'owner_id' => $ownerId]
        );
    }

    // ============================================================
    // REVIEW NOTIFICATION HELPERS
    // ============================================================

    /**
     * Notify helper when they receive a review.
     */
    public function notifyReviewReceived(int $taskId, string $taskTitle, int $helperId, int $userId, string $userName, int $rating): void
    {
        $this->create(
            $helperId,
            NotificationModel::TYPE_REVIEW_RECEIVED,
            'Review Received',
            "{$userName} left a {$rating}-star review for \"{$taskTitle}\".",
            ['task_id' => $taskId, 'user_id' => $userId, 'rating' => $rating]
        );
    }

    // ============================================================
    // WALLET NOTIFICATION HELPERS
    // ============================================================

    /**
     * Notify helper when payment is released.
     */
    public function notifyPaymentReleased(int $helperId, string $taskTitle, float $amount, int $taskId): void
    {
        $this->create(
            $helperId,
            NotificationModel::TYPE_PAYMENT_RELEASED,
            'Payment Received',
            "You received Rp " . number_format($amount, 0, ',', '.') . " for \"{$taskTitle}\".",
            ['task_id' => $taskId, 'amount' => $amount]
        );
    }

    /**
     * Notify user when withdrawal is requested.
     */
    public function notifyWithdrawRequested(int $userId, float $amount, int $transactionId): void
    {
        $this->create(
            $userId,
            NotificationModel::TYPE_WITHDRAW_REQUESTED,
            'Withdrawal Requested',
            "Your withdrawal request for Rp " . number_format($amount, 0, ',', '.') . " has been submitted.",
            ['transaction_id' => $transactionId, 'amount' => $amount]
        );
    }

    /**
     * Notify user when withdrawal is approved.
     */
    public function notifyWithdrawApproved(int $userId, float $amount, int $transactionId): void
    {
        $this->create(
            $userId,
            NotificationModel::TYPE_WITHDRAW_APPROVED,
            'Withdrawal Approved',
            "Your withdrawal of Rp " . number_format($amount, 0, ',', '.') . " has been approved.",
            ['transaction_id' => $transactionId, 'amount' => $amount]
        );
    }

    /**
     * Notify user when withdrawal is rejected.
     */
    public function notifyWithdrawRejected(int $userId, float $amount, int $transactionId, ?string $reason = null): void
    {
        $message = "Your withdrawal of Rp " . number_format($amount, 0, ',', '.') . " has been rejected.";
        if ($reason) {
            $message .= " Reason: {$reason}";
        }

        $this->create(
            $userId,
            NotificationModel::TYPE_WITHDRAW_REJECTED,
            'Withdrawal Rejected',
            $message,
            ['transaction_id' => $transactionId, 'amount' => $amount, 'reason' => $reason]
        );
    }

    // ============================================================
    // DISPUTE NOTIFICATION HELPERS (Future Ready)
    // ============================================================

    /**
     * Notify dispute created.
     */
    public function notifyDisputeCreated(int $userId, int $taskId, string $taskTitle): void
    {
        $this->create(
            $userId,
            NotificationModel::TYPE_DISPUTE_CREATED,
            'Dispute Created',
            "A dispute has been created for \"{$taskTitle}\".",
            ['task_id' => $taskId]
        );
    }

    /**
     * Notify dispute resolved.
     */
    public function notifyDisputeResolved(int $userId, int $taskId, string $taskTitle): void
    {
        $this->create(
            $userId,
            NotificationModel::TYPE_DISPUTE_RESOLVED,
            'Dispute Resolved',
            "The dispute for \"{$taskTitle}\" has been resolved.",
            ['task_id' => $taskId]
        );
    }
}

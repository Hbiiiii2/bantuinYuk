<?php

namespace App\Services;

use App\Models\DisputeModel;
use App\Models\TaskModel;
use App\Models\UserModel;
use App\Exceptions\BusinessException;
use App\Exceptions\ValidationException;

class DisputeService extends BaseService
{
    protected DisputeModel $disputeModel;
    protected TaskModel $taskModel;
    protected UserModel $userModel;
    protected NotificationService $notificationService;

    public function __construct()
    {
        parent::__construct();
        $this->disputeModel = new DisputeModel();
        $this->taskModel = new TaskModel();
        $this->userModel = new UserModel();
        $this->notificationService = new NotificationService();
    }

    /**
     * Create a new dispute.
     */
    public function createDispute(int $userId, array $data): array
    {
        $this->validateRequired($data, [
            'task_id' => 'Task ID',
            'reason'  => 'Reason',
            'description' => 'Description',
        ]);

        $taskId = (int) $data['task_id'];
        $task = $this->taskModel->find($taskId);

        if (!$task) {
            throw BusinessException::notFound('Task not found');
        }

        // Validate user is involved in task
        $isOwner = $task['user_id'] == $userId;
        $isHelper = $task['helper_id'] && $task['helper_id'] == $userId;

        if (!$isOwner && !$isHelper) {
            throw BusinessException::forbidden('You are not involved in this task');
        }

        // Validate task status
        $validStatuses = [TaskModel::STATUS_WAITING_APPROVAL, TaskModel::STATUS_COMPLETED];
        if (!in_array($task['status'], $validStatuses)) {
            throw BusinessException::conflict('Dispute can only be created for tasks with status waiting_approval or completed');
        }

        // Check for active dispute
        if ($this->disputeModel->hasActiveDispute($taskId)) {
            throw BusinessException::conflict('This task already has an active dispute');
        }

        $result = $this->transaction(function () use ($userId, $data, $task, $taskId) {
            $disputeId = $this->disputeModel->insert([
                'task_id'      => $taskId,
                'user_id'      => $task['user_id'],
                'helper_id'    => $task['helper_id'],
                'reason'       => $data['reason'],
                'evidence_file' => $data['description'] ?? null, // Using evidence_file for description
                'status'       => DisputeModel::STATUS_OPEN,
            ]);

            if (!$disputeId) {
                throw BusinessException::failed('Failed to create dispute');
            }

            $dispute = $this->getDisputeById($disputeId);

            // Send notifications
            $this->notifyDisputeCreated($dispute, $task, $userId);

            return $dispute;
        });

        return $result;
    }

    /**
     * Get dispute by ID with authorization check.
     */
    public function getDisputeById(int $disputeId): array
    {
        $dispute = $this->disputeModel->getDisputeById($disputeId);

        if (!$dispute) {
            throw BusinessException::notFound('Dispute not found');
        }

        return $dispute;
    }

    /**
     * Get disputes for user with filters.
     */
    public function getUserDisputes(int $userId, int $page = 1, int $perPage = 20, ?string $status = null, ?string $search = null): array
    {
        return $this->disputeModel->getByUserId($userId, $page, $perPage, $status, $search);
    }

    /**
     * Get all disputes (admin).
     */
    public function getAllDisputes(int $page = 1, int $perPage = 20, ?string $status = null, ?string $search = null): array
    {
        return $this->disputeModel->getAllDisputes($page, $perPage, $status, $search);
    }

    /**
     * Admin review dispute (OPEN -> UNDER_REVIEW).
     */
    public function reviewDispute(int $disputeId, int $adminId): array
    {
        $dispute = $this->disputeModel->find($disputeId);

        if (!$dispute) {
            throw BusinessException::notFound('Dispute not found');
        }

        if ($dispute['status'] !== DisputeModel::STATUS_OPEN) {
            throw BusinessException::conflict('Only open disputes can be reviewed');
        }

        $result = $this->transaction(function () use ($dispute, $adminId) {
            $this->disputeModel->update($dispute['id'], [
                'status' => DisputeModel::STATUS_UNDER_REVIEW,
            ]);

            $updatedDispute = $this->getDisputeById($dispute['id']);

            // Send notifications
            $this->notifyDisputeStatusChanged($updatedDispute, 'under_review');

            return $updatedDispute;
        });

        return $result;
    }

    /**
     * Admin resolve dispute (UNDER_REVIEW -> RESOLVED).
     */
    public function resolveDispute(int $disputeId, int $adminId, array $data): array
    {
        $this->validateRequired($data, [
            'resolution' => 'Resolution',
        ]);

        $dispute = $this->disputeModel->find($disputeId);

        if (!$dispute) {
            throw BusinessException::notFound('Dispute not found');
        }

        if ($dispute['status'] !== DisputeModel::STATUS_UNDER_REVIEW) {
            throw BusinessException::conflict('Only under review disputes can be resolved');
        }

        $result = $this->transaction(function () use ($dispute, $adminId, $data) {
            $this->disputeModel->update($dispute['id'], [
                'status'       => DisputeModel::STATUS_RESOLVED,
                'admin_note'   => $data['resolution'],
                'resolved_by'  => $adminId,
                'resolved_at'  => date('Y-m-d H:i:s'),
            ]);

            $updatedDispute = $this->getDisputeById($dispute['id']);

            // Send notifications
            $this->notifyDisputeStatusChanged($updatedDispute, 'resolved');

            return $updatedDispute;
        });

        return $result;
    }

    /**
     * Admin reject dispute (UNDER_REVIEW -> REJECTED).
     */
    public function rejectDispute(int $disputeId, int $adminId, array $data): array
    {
        $dispute = $this->disputeModel->find($disputeId);

        if (!$dispute) {
            throw BusinessException::notFound('Dispute not found');
        }

        if ($dispute['status'] !== DisputeModel::STATUS_UNDER_REVIEW) {
            throw BusinessException::conflict('Only under review disputes can be rejected');
        }

        $result = $this->transaction(function () use ($dispute, $adminId, $data) {
            $this->disputeModel->update($dispute['id'], [
                'status'       => DisputeModel::STATUS_REJECTED,
                'admin_note'   => $data['resolution'] ?? 'Dispute rejected by admin',
                'resolved_by'  => $adminId,
                'resolved_at'  => date('Y-m-d H:i:s'),
            ]);

            $updatedDispute = $this->getDisputeById($dispute['id']);

            // Send notifications
            $this->notifyDisputeStatusChanged($updatedDispute, 'rejected');

            return $updatedDispute;
        });

        return $result;
    }

    /**
     * Validate dispute ownership for authorization.
     */
    public function validateDisputeOwnership(int $disputeId, int $userId): bool
    {
        return $this->disputeModel->isInvolved($disputeId, $userId);
    }

    /**
     * Check if task has active dispute.
     */
    public function hasActiveDispute(int $taskId): bool
    {
        return $this->disputeModel->hasActiveDispute($taskId);
    }

    /**
     * Send notification when dispute is created.
     */
    private function notifyDisputeCreated(array $dispute, array $task, int $creatorId): void
    {
        $creator = $this->userModel->find($creatorId);
        $creatorName = $creator['name'] ?? 'User';

        // Notify admin (find all admins)
        $admins = $this->userModel->where('role', 'admin')->findAll();
        $adminIds = array_column($admins, 'id');

        if (!empty($adminIds)) {
            $this->notificationService->createBulk(
                $adminIds,
                'dispute_created',
                'New Dispute Created',
                "A new dispute has been created for task \"{$task['title']}\" by {$creatorName}.",
                ['dispute_id' => $dispute['id'], 'task_id' => $task['id']]
            );
        }

        // Notify counterparty
        $counterpartyId = null;
        if ($creatorId == $task['user_id'] && $task['helper_id']) {
            $counterpartyId = $task['helper_id'];
        } elseif ($creatorId == $task['helper_id']) {
            $counterpartyId = $task['user_id'];
        }

        if ($counterpartyId) {
            $this->notificationService->create(
                $counterpartyId,
                'dispute_created',
                'Dispute Created',
                "{$creatorName} has created a dispute for task \"{$task['title']}\".",
                ['dispute_id' => $dispute['id'], 'task_id' => $task['id']]
            );
        }
    }

    /**
     * Send notification when dispute status changes.
     */
    private function notifyDisputeStatusChanged(array $dispute, string $action): void
    {
        $task = $this->taskModel->find($dispute['task_id']);
        $taskTitle = $task['title'] ?? 'Unknown Task';

        $statusMessages = [
            'under_review' => [
                'title' => 'Dispute Under Review',
                'message' => "Your dispute for task \"{$taskTitle}\" is now under review.",
            ],
            'resolved' => [
                'title' => 'Dispute Resolved',
                'message' => "Your dispute for task \"{$taskTitle}\" has been resolved.",
            ],
            'rejected' => [
                'title' => 'Dispute Rejected',
                'message' => "Your dispute for task \"{$taskTitle}\" has been rejected.",
            ],
        ];

        $msg = $statusMessages[$action] ?? [
            'title' => 'Dispute Updated',
            'message' => "Your dispute for task \"{$taskTitle}\" has been updated.",
        ];

        // Notify both parties
        $userIds = array_filter([$dispute['user_id'], $dispute['helper_id']]);

        foreach ($userIds as $userId) {
            $this->notificationService->create(
                $userId,
                "dispute_" . $action,
                $msg['title'],
                $msg['message'],
                ['dispute_id' => $dispute['id'], 'task_id' => $dispute['task_id']]
            );
        }
    }
}

<?php

namespace App\Services;

use App\Models\TaskProgressModel;
use App\Models\TaskModel;
use App\Models\TaskStatusHistoryModel;
use App\Models\UserModel;
use App\Exceptions\BusinessException;
use App\Exceptions\ValidationException;

class ProgressService extends BaseService
{
    protected TaskProgressModel $progressModel;
    protected TaskModel $taskModel;
    protected TaskStatusHistoryModel $historyModel;
    protected UserModel $userModel;
    protected AttachmentService $attachmentService;

    public function __construct()
    {
        parent::__construct();
        $this->progressModel = new TaskProgressModel();
        $this->taskModel = new TaskModel();
        $this->historyModel = new TaskStatusHistoryModel();
        $this->userModel = new UserModel();
        $this->attachmentService = new AttachmentService();
    }

    /**
     * Buat progress baru untuk task.
     * 
     * @param int $taskId
     * @param int $helperId
     * @param array $data ['description', 'attachment_ids']
     * @return array Progress data
     * @throws BusinessException Jika task tidak ditemukan atau tidak ada akses
     * @throws ValidationException Jika validasi gagal
     */
    public function createProgress(int $taskId, int $helperId, array $data): array
    {
        $this->validateRequired($data, [
            'description' => 'Description',
        ]);

        $this->validateLength($data['description'], 'description', 10, 2000);

        // Validate task exists
        $task = $this->taskModel->find($taskId);
        if (!$task) {
            throw BusinessException::notFound('Task not found');
        }

        // Validate helper is assigned to this task
        if ($task['helper_id'] != $helperId) {
            throw BusinessException::forbidden('You are not assigned to this task');
        }

        // Validate task status allows progress
        $allowedStatuses = ['accepted', 'in_progress', 'waiting_approval'];
        if (!in_array($task['status'], $allowedStatuses)) {
            throw BusinessException::conflict('Cannot add progress in current task status');
        }

        // Process attachment_ids if provided
        $attachmentIds = $data['attachment_ids'] ?? [];
        if (!empty($attachmentIds)) {
            $this->validateAttachments($attachmentIds, $taskId, $helperId);
        }

        $result = $this->transaction(function () use ($taskId, $helperId, $data, $attachmentIds) {
            // Create progress
            $progressId = $this->progressModel->insert([
                'task_id'     => $taskId,
                'helper_id'   => $helperId,
                'description' => $data['description'],
                'attachment'  => !empty($attachmentIds) ? json_encode($attachmentIds) : null,
                'status'      => TaskProgressModel::STATUS_ACTIVE,
            ]);

            if (!$progressId) {
                throw BusinessException::failed('Failed to create progress');
            }

            // If task is in ACCEPTED status, transition to IN_PROGRESS
            $task = $this->taskModel->find($taskId);
            if ($task['status'] === 'accepted') {
                $this->taskModel->update($taskId, [
                    'status' => 'in_progress',
                ]);

                // Create status history
                $this->historyModel->insert([
                    'task_id'    => $taskId,
                    'status'     => 'in_progress',
                    'note'       => 'Work started - progress update',
                    'created_by' => $helperId,
                ]);
            }

            return $this->getProgressById($progressId);
        });

        return $result;
    }

    /**
     * Ambil progress berdasarkan ID.
     * 
     * @param int $progressId
     * @return array
     * @throws BusinessException Jika progress tidak ditemukan
     */
    public function getProgressById(int $progressId): array
    {
        $progress = $this->progressModel->find($progressId);
        if (!$progress) {
            throw BusinessException::notFound('Progress not found');
        }

        // Get helper info
        $helper = $this->userModel->find($progress['helper_id']);
        if ($helper) {
            $progress['helper_name'] = $helper['name'];
        }

        // Parse attachment IDs
        $progress['attachment_ids'] = $progress['attachment'] ? json_decode($progress['attachment'], true) : [];

        // Get attachment details
        if (!empty($progress['attachment_ids'])) {
            $progress['attachments'] = $this->attachmentService->getAttachmentsByIds($progress['attachment_ids']);
        } else {
            $progress['attachments'] = [];
        }

        return $progress;
    }

    /**
     * Ambil semua progress untuk task.
     * 
     * @param int $taskId
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getProgressByTask(int $taskId, int $page = 1, int $perPage = 20): array
    {
        $builder = $this->progressModel->builder();
        $builder->select('task_progress.*, users.name as helper_name');
        $builder->join('users', 'users.id = task_progress.helper_id', 'left');
        $builder->where('task_progress.task_id', $taskId);
        $builder->where('task_progress.status', TaskProgressModel::STATUS_ACTIVE);
        $builder->orderBy('task_progress.created_at', 'ASC');

        $total = $builder->countAllResults(false);
        $builder->limit($perPage, ($page - 1) * $perPage);

        $progress = $builder->get()->getResultArray();

        // Enrich with attachment details
        foreach ($progress as &$item) {
            $item['attachment_ids'] = $item['attachment'] ? json_decode($item['attachment'], true) : [];
            if (!empty($item['attachment_ids'])) {
                $item['attachments'] = $this->attachmentService->getAttachmentsByIds($item['attachment_ids']);
            } else {
                $item['attachments'] = [];
            }
        }

        return [
            'data'      => $progress,
            'total'     => $total,
            'page'      => $page,
            'per_page'  => $perPage,
        ];
    }

    /**
     * Hapus progress (soft delete).
     * 
     * @param int $progressId
     * @param int $helperId
     * @return bool
     * @throws BusinessException Jika tidak ada akses
     */
    public function deleteProgress(int $progressId, int $helperId): bool
    {
        $progress = $this->progressModel->find($progressId);
        if (!$progress) {
            throw BusinessException::notFound('Progress not found');
        }

        if ($progress['helper_id'] != $helperId) {
            throw BusinessException::forbidden('You can only delete your own progress');
        }

        return $this->progressModel->update($progressId, [
            'status' => TaskProgressModel::STATUS_DELETED,
        ]);
    }

    /**
     * Validate that attachments belong to the task and user.
     * 
     * @param array $attachmentIds
     * @param int $taskId
     * @param int $userId
     * @throws ValidationException Jika attachment tidak valid
     */
    private function validateAttachments(array $attachmentIds, int $taskId, int $userId): void
    {
        foreach ($attachmentIds as $attachmentId) {
            $attachment = $this->attachmentModel->find($attachmentId);
            if (!$attachment) {
                throw ValidationException::single('attachment_ids', "Attachment {$attachmentId} not found");
            }
            if ($attachment['task_id'] != $taskId) {
                throw ValidationException::single('attachment_ids', "Attachment {$attachmentId} does not belong to this task");
            }
            if ($attachment['user_id'] != $userId) {
                throw ValidationException::single('attachment_ids', "Attachment {$attachmentId} was not uploaded by you");
            }
        }
    }
}

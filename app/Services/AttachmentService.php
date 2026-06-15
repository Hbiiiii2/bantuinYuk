<?php

namespace App\Services;

use App\Models\TaskAttachmentModel;
use App\Models\TaskModel;
use App\Models\UserModel;
use App\Exceptions\BusinessException;
use App\Exceptions\ValidationException;

class AttachmentService extends BaseService
{
    protected TaskAttachmentModel $attachmentModel;
    protected TaskModel $taskModel;
    protected UserModel $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->attachmentModel = new TaskAttachmentModel();
        $this->taskModel = new TaskModel();
        $this->userModel = new UserModel();
    }

    /**
     * Upload attachment untuk task.
     * 
     * @param int $taskId
     * @param int $userId
     * @param array $file $_FILES['file']
     * @return array Attachment data
     * @throws BusinessException Jika task tidak ditemukan atau tidak ada akses
     * @throws ValidationException Jika validasi gagal
     */
    public function uploadAttachment(int $taskId, int $userId, array $file): array
    {
        // Validate task exists
        $task = $this->taskModel->find($taskId);
        if (!$task) {
            throw BusinessException::notFound('Task not found');
        }

        // Ownership validation
        $user = $this->userModel->find($userId);
        if (!$user) {
            throw BusinessException::notFound('User not found');
        }

        // User can upload to own tasks, helper can upload to assigned tasks
        $isOwner = $task['user_id'] == $userId;
        $isHelper = $task['helper_id'] == $userId && $user->role === 'helper';

        if (!$isOwner && !$isHelper) {
            throw BusinessException::forbidden('You can only upload attachments to your own tasks');
        }

        // Validate file
        $this->validateFile($file);

        // Get file extension and MIME type
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $mimeType = $file['type'];

        // Generate unique filename
        $fileName = 'task_' . $taskId . '_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        $filePath = 'uploads/tasks/' . $fileName;

        // Create upload directory if not exists
        $uploadPath = WRITEPATH . '../public/' . 'uploads/tasks/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Move file
        if (!move_uploaded_file($file['tmp_name'], $uploadPath . $fileName)) {
            throw BusinessException::failed('Failed to upload file');
        }

        // Save to database
        $attachmentId = $this->attachmentModel->insert([
            'task_id'    => $taskId,
            'user_id'    => $userId,
            'file_name'  => $file['name'],
            'file_path'  => $filePath,
            'file_type'  => $mimeType,
            'file_size'  => $file['size'],
        ]);

        if (!$attachmentId) {
            // Delete uploaded file if database insert fails
            unlink($uploadPath . $fileName);
            throw BusinessException::failed('Failed to save attachment');
        }

        return $this->attachmentModel->find($attachmentId);
    }

    /**
     * Upload multiple attachments.
     * 
     * @param int $taskId
     * @param int $userId
     * @param array $files Array of $_FILES
     * @return array Array of attachment data
     */
    public function uploadMultiple(int $taskId, int $userId, array $files): array
    {
        $results = [];
        $errors = [];

        $fileCount = count($files['name']);
        for ($i = 0; $i < $fileCount; $i++) {
            $file = [
                'name'     => $files['name'][$i],
                'type'     => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i],
            ];

            try {
                $result = $this->uploadAttachment($taskId, $userId, $file);
                $results[] = $result;
            } catch (\Exception $e) {
                $errors[] = [
                    'file' => $files['name'][$i],
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'uploaded' => $results,
            'errors'   => $errors,
        ];
    }

    /**
     * Ambil semua attachments untuk task.
     * 
     * @param int $taskId
     * @return array
     */
    public function getAttachmentsByTask(int $taskId): array
    {
        return $this->attachmentModel
            ->where('task_id', $taskId)
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }

    /**
     * Ambil attachments berdasarkan IDs.
     * 
     * @param array $ids
     * @return array
     */
    public function getAttachmentsByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        return $this->attachmentModel
            ->whereIn('id', $ids)
            ->findAll();
    }

    /**
     * Hapus attachment.
     * 
     * @param int $attachmentId
     * @param int $userId
     * @return bool
     * @throws BusinessException Jika tidak ada akses
     */
    public function deleteAttachment(int $attachmentId, int $userId): bool
    {
        $attachment = $this->attachmentModel->find($attachmentId);
        if (!$attachment) {
            throw BusinessException::notFound('Attachment not found');
        }

        // Only uploader or task owner can delete
        if ($attachment['user_id'] != $userId) {
            $task = $this->taskModel->find($attachment['task_id']);
            if (!$task || $task['user_id'] != $userId) {
                throw BusinessException::forbidden('You can only delete your own attachments');
            }
        }

        // Delete file from filesystem
        $filePath = WRITEPATH . '../public/' . $attachment['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return $this->attachmentModel->delete($attachmentId);
    }

    /**
     * Validate uploaded file.
     * 
     * @param array $file
     * @throws ValidationException Jika validasi gagal
     */
    private function validateFile(array $file): void
    {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw ValidationException::single('file', 'File upload failed with error code: ' . $file['error']);
        }

        // Check file size
        if ($file['size'] > TaskAttachmentModel::MAX_FILE_SIZE) {
            $maxMB = TaskAttachmentModel::MAX_FILE_SIZE / (1024 * 1024);
            throw ValidationException::single('file', "File size must not exceed {$maxMB}MB");
        }

        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, TaskAttachmentModel::ALLOWED_MIME_TYPES)) {
            throw ValidationException::single('file', 'File type not allowed. Allowed: images, videos, documents');
        }

        // Check extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, TaskAttachmentModel::ALLOWED_EXTENSIONS)) {
            throw ValidationException::single('file', 'File extension not allowed');
        }
    }
}

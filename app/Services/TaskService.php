<?php

namespace App\Services;

use App\Models\TaskModel;
use App\Models\TaskStatusHistoryModel;
use App\Models\CategoryModel;
use App\Models\UserModel;
use App\Exceptions\BusinessException;
use App\Exceptions\ValidationException;

class TaskService extends BaseService
{
    protected TaskModel $taskModel;
    protected TaskStatusHistoryModel $historyModel;
    protected CategoryModel $categoryModel;
    protected UserModel $userModel;
    protected NotificationService $notificationService;

    // Valid status transitions
    protected array $validTransitions = [
        TaskModel::STATUS_OPEN             => [TaskModel::STATUS_ACCEPTED, TaskModel::STATUS_CANCELLED],
        TaskModel::STATUS_ACCEPTED         => [TaskModel::STATUS_IN_PROGRESS, TaskModel::STATUS_CANCELLED],
        TaskModel::STATUS_IN_PROGRESS      => [TaskModel::STATUS_WAITING_APPROVAL],
        TaskModel::STATUS_WAITING_APPROVAL => [TaskModel::STATUS_COMPLETED],
        TaskModel::STATUS_COMPLETED        => [], // Terminal state
    ];

    public function __construct()
    {
        parent::__construct();
        $this->taskModel = new TaskModel();
        $this->historyModel = new TaskStatusHistoryModel();
        $this->categoryModel = new CategoryModel();
        $this->userModel = new UserModel();
        $this->notificationService = new NotificationService();
    }

    /**
     * Ambil semua tasks dengan filter, search, dan pagination.
     * 
     * @param array $filters ['status', 'category_id', 'user_id', 'helper_id', 'search', 'sort_by', 'sort_order']
     * @param int $page
     * @param int $perPage
     * @return array ['data' => [...], 'total' => int, 'page' => int, 'per_page' => int, 'total_pages' => int]
     */
    public function getAllTasks(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $builder = $this->taskModel->builder();
        $builder->select('tasks.*, categories.name as category_name, users.name as user_name');
        $builder->join('categories', 'categories.id = tasks.category_id', 'left');
        $builder->join('users', 'users.id = tasks.user_id', 'left');

        // Filter by status
        if (!empty($filters['status'])) {
            $builder->where('tasks.status', $filters['status']);
        }

        // Filter by category
        if (!empty($filters['category_id'])) {
            $builder->where('tasks.category_id', $filters['category_id']);
        }

        // Filter by user (task owner)
        if (!empty($filters['user_id'])) {
            $builder->where('tasks.user_id', $filters['user_id']);
        }

        // Filter by helper
        if (!empty($filters['helper_id'])) {
            $builder->where('tasks.helper_id', $filters['helper_id']);
        }

        // Search by title or description
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart();
            $builder->like('tasks.title', $search);
            $builder->orLike('tasks.description', $search);
            $builder->groupEnd();
        }

        // Get total count before pagination
        $total = $builder->countAllResults(false);

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'DESC';
        $allowedSortFields = ['created_at', 'price', 'deadline_end', 'status'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }
        $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
        $builder->orderBy("tasks.{$sortBy}", $sortOrder);

        // Pagination
        $builder->limit($perPage, ($page - 1) * $perPage);

        $tasks = $builder->get()->getResultArray();

        return [
            'data'        => $tasks,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Ambil task berdasarkan ID lengkap dengan status history.
     * 
     * @param int $taskId
     * @return array
     * @throws BusinessException Jika task tidak ditemukan
     */
    public function getTaskById(int $taskId): array
    {
        $builder = $this->taskModel->builder();
        $builder->select('tasks.*, categories.name as category_name, 
                         users.name as user_name, users.email as user_email');
        $builder->join('categories', 'categories.id = tasks.category_id', 'left');
        $builder->join('users', 'users.id = tasks.user_id', 'left');
        $builder->where('tasks.id', $taskId);

        $task = $builder->get()->getRowArray();

        if (!$task) {
            throw BusinessException::notFound('Task not found');
        }

        // Add helper info
        if ($task['helper_id']) {
            $helper = $this->userModel->find($task['helper_id']);
            if ($helper) {
                $task['helper_name'] = $helper['name'];
                $task['helper_email'] = $helper['email'];
            }
        }

        // Add status history
        $task['status_history'] = $this->getStatusHistory($taskId);

        return $task;
    }

    /**
     * Ambil status history untuk task.
     * 
     * @param int $taskId
     * @return array
     */
    public function getStatusHistory(int $taskId): array
    {
        $builder = $this->historyModel->builder();
        $builder->select('task_status_histories.*, users.name as created_by_name');
        $builder->join('users', 'users.id = task_status_histories.created_by', 'left');
        $builder->where('task_status_histories.task_id', $taskId);
        $builder->orderBy('task_status_histories.created_at', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Buat task baru.
     * 
     * @param int $userId ID user yang membuat task
     * @param array $data ['title', 'description', 'price', 'category_id', 'deadline_start', 'deadline_end', 'location']
     * @return array Data task yang dibuat
     * @throws ValidationException Jika validasi gagal
     * @throws BusinessException Jika create gagal
     */
    public function createTask(int $userId, array $data): array
    {
        $this->validateRequired($data, [
            'title'          => 'Title',
            'description'    => 'Description',
            'price'          => 'Price',
            'category_id'    => 'Category',
            'deadline_start' => 'Deadline start',
            'deadline_end'   => 'Deadline end',
        ]);

        $this->validateLength($data['title'], 'title', 5, 255);
        $this->validatePositive($data['price'], 'price');

        $category = $this->categoryModel->find($data['category_id']);
        if (!$category || $category['status'] !== 'active') {
            throw ValidationException::single('category_id', 'Category is not valid');
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            throw BusinessException::notFound('User not found');
        }

        // Role validation: Only users can create tasks (not helpers, not admins)
        if ($user->role !== 'user') {
            throw BusinessException::forbidden('Only users can create tasks');
        }

        $deadlineStart = strtotime($data['deadline_start']);
        $deadlineEnd = strtotime($data['deadline_end']);

        if ($deadlineEnd <= $deadlineStart) {
            throw ValidationException::single('deadline_end', 'Deadline end must be after deadline start');
        }

        if ($deadlineStart < time()) {
            throw ValidationException::single('deadline_start', 'Deadline start must be in the future');
        }

        $taskId = $this->taskModel->insert([
            'user_id'        => $userId,
            'category_id'    => $data['category_id'],
            'title'          => $data['title'],
            'description'    => $data['description'],
            'price'          => $data['price'],
            'location'       => $data['location'] ?? null,
            'deadline_start' => $data['deadline_start'],
            'deadline_end'   => $data['deadline_end'],
            'status'         => TaskModel::STATUS_OPEN,
        ]);

        if (!$taskId) {
            throw BusinessException::failed('Failed to create task');
        }

        $task = $this->getTaskById($taskId);

        // Send notification
        $this->notificationService->notifyTaskCreated($userId, $task);

        return $task;
    }

    /**
     * Update task.
     * 
     * @param int $taskId
     * @param int $userId ID user yang melakukan update
     * @param array $data ['title', 'description', 'price', 'location']
     * @return array Updated task data
     * @throws BusinessException Jika task tidak ditemukan atau tidak ada akses
     */
    public function updateTask(int $taskId, int $userId, array $data): array
    {
        $task = $this->taskModel->find($taskId);

        if (!$task) {
            throw BusinessException::notFound('Task not found');
        }

        if ($task['user_id'] != $userId) {
            throw BusinessException::forbidden('You can only update your own tasks');
        }

        if (!in_array($task['status'], [TaskModel::STATUS_DRAFT, TaskModel::STATUS_OPEN])) {
            throw BusinessException::conflict('Cannot update task in current status');
        }

        $allowedFields = ['title', 'description', 'price', 'location'];
        $updateData = $this->filterData($data, $allowedFields);

        if (empty($updateData)) {
            throw ValidationException::single('data', 'No valid data to update');
        }

        if (isset($updateData['title'])) {
            $this->validateLength($updateData['title'], 'title', 5, 255);
        }

        if (isset($updateData['price'])) {
            $this->validatePositive($updateData['price'], 'price');
        }

        $updated = $this->taskModel->update($taskId, $updateData);

        if (!$updated) {
            throw BusinessException::failed('Failed to update task');
        }

        return $this->getTaskById($taskId);
    }

    /**
     * Cancel task oleh owner.
     * 
     * @param int $taskId
     * @param int $userId ID user yang melakukan cancel
     * @param string|null $note Catatan pembatalan
     * @return array Updated task data
     * @throws BusinessException Jika task tidak ditemukan atau tidak ada akses
     */
    public function cancelTask(int $taskId, int $userId, ?string $note = null): array
    {
        $task = $this->taskModel->find($taskId);

        if (!$task) {
            throw BusinessException::notFound('Task not found');
        }

        if ($task['user_id'] != $userId) {
            throw BusinessException::forbidden('You can only cancel your own tasks');
        }

        if (!in_array($task['status'], [TaskModel::STATUS_OPEN, TaskModel::STATUS_ACCEPTED])) {
            throw BusinessException::conflict('Cannot cancel task in current status');
        }

        $result = $this->transaction(function () use ($taskId, $userId, $note, $task) {
            $this->changeStatus($taskId, TaskModel::STATUS_CANCELLED, $userId, $note);

            // Send notification to helper if task was accepted
            if ($task['helper_id']) {
                $owner = $this->userModel->find($userId);
                $this->notificationService->notifyTaskCancelled(
                    $taskId,
                    $task['title'],
                    $task['helper_id'],
                    $userId,
                    $owner['name'] ?? 'User'
                );
            }

            return $this->getTaskById($taskId);
        });

        return $result;
    }

    /**
     * Ubah status task dengan validasi transisi.
     * 
     * @param int $taskId
     * @param string $newStatus Status baru
     * @param int $userId ID user yang melakukan perubahan
     * @param string|null $note Catatan opsional
     * @return array Updated task data
     * @throws BusinessException Jika transisi tidak valid
     */
    public function changeStatus(int $taskId, string $newStatus, int $userId, ?string $note = null): array
    {
        $task = $this->taskModel->find($taskId);

        if (!$task) {
            throw BusinessException::notFound('Task not found');
        }

        $currentStatus = $task['status'];

        // Validate status transition
        if (!isset($this->validTransitions[$currentStatus]) || 
            !in_array($newStatus, $this->validTransitions[$currentStatus])) {
            throw BusinessException::conflict(
                "Cannot change status from '{$currentStatus}' to '{$newStatus}'"
            );
        }

        // Update status
        $updated = $this->taskModel->update($taskId, [
            'status' => $newStatus,
        ]);

        if (!$updated) {
            throw BusinessException::failed('Failed to update task status');
        }

        // Create status history
        $this->createStatusHistory($taskId, $newStatus, $userId, $note);

        return $this->getTaskById($taskId);
    }

    /**
     * Buat record status history.
     * 
     * @param int $taskId
     * @param string $status
     * @param int $createdBy User ID yang melakukan perubahan
     * @param string|null $note
     * @return int History ID
     */
    public function createStatusHistory(int $taskId, string $status, int $createdBy, ?string $note = null): int
    {
        $historyId = $this->historyModel->insert([
            'task_id'    => $taskId,
            'status'     => $status,
            'created_by' => $createdBy,
            'note'       => $note,
        ]);

        if (!$historyId) {
            throw BusinessException::failed('Failed to create status history');
        }

        return $historyId;
    }

    /**
     * Accept task oleh helper (dengan transaction + row locking).
     * 
     * @param int $taskId
     * @param int $helperId ID helper yang menerima task
     * @return array Updated task data
     * @throws BusinessException Jika task tidak ditemukan atau tidak bisa diterima
     */
    public function acceptTask(int $taskId, int $helperId): array
    {
        $result = $this->transaction(function () use ($taskId, $helperId) {
            // Lock row for update to prevent race condition
            $builder = $this->taskModel->builder();
            $builder->where('id', $taskId);
            $builder->where('status', TaskModel::STATUS_OPEN);
            $task = $builder->get()->getRowArray();

            if (!$task) {
                throw BusinessException::notFound('Task found or no longer available');
            }

            if ($task['user_id'] == $helperId) {
                throw BusinessException::conflict('You cannot accept your own task');
            }

            $helper = $this->userModel->find($helperId);
            if (!$helper || $helper['role'] !== 'helper') {
                throw BusinessException::forbidden('Only helpers can accept tasks');
            }

            // Atomic update - only update if still OPEN
            $builder = $this->taskModel->builder();
            $builder->where('id', $taskId);
            $builder->where('status', TaskModel::STATUS_OPEN);
            $updated = $builder->update([
                'helper_id' => $helperId,
                'status'    => TaskModel::STATUS_ACCEPTED,
            ]);

            if ($builder->affectedRows() === 0) {
                throw BusinessException::conflict('Task was just accepted by another helper');
            }

            // Create status history
            $this->createStatusHistory($taskId, TaskModel::STATUS_ACCEPTED, $helperId, 'Task accepted by helper');

            $taskData = $this->getTaskById($taskId);

            // Send notification to task owner
            $this->notificationService->notifyTaskAccepted(
                $taskId,
                $task['title'],
                $task['user_id'],
                $helperId,
                $helper['name']
            );

            return $taskData;
        });

        return $result;
    }

            if ($task['user_id'] == $helperId) {
                throw BusinessException::conflict('You cannot accept your own task');
            }

            $helper = $this->userModel->find($helperId);
            if (!$helper || $helper['role'] !== 'helper') {
                throw BusinessException::forbidden('Only helpers can accept tasks');
            }

            // Atomic update - only update if still OPEN
            $builder = $this->taskModel->builder();
            $builder->where('id', $taskId);
            $builder->where('status', TaskModel::STATUS_OPEN);
            $updated = $builder->update([
                'helper_id' => $helperId,
                'status'    => TaskModel::STATUS_ACCEPTED,
            ]);

            if ($builder->affectedRows() === 0) {
                throw BusinessException::conflict('Task was just accepted by another helper');
            }

            // Create status history
            $this->createStatusHistory($taskId, TaskModel::STATUS_ACCEPTED, $helperId, 'Task accepted by helper');

            return $this->getTaskById($taskId);
        });

        return $result;
    }

    /**
     * Mulai pengerjaan task (dengan transaction).
     * 
     * @param int $taskId
     * @param int $helperId ID helper yang mengerjakan
     * @return array Updated task data
     */
    public function startTask(int $taskId, int $helperId): array
    {
        $task = $this->taskModel->find($taskId);

        if (!$task) {
            throw BusinessException::notFound('Task not found');
        }

        if ($task['helper_id'] != $helperId) {
            throw BusinessException::forbidden('You are not assigned to this task');
        }

        if ($task['status'] !== TaskModel::STATUS_ACCEPTED) {
            throw BusinessException::conflict('Cannot start task in current status');
        }

        $result = $this->transaction(function () use ($taskId, $helperId, $task) {
            $this->changeStatus($taskId, TaskModel::STATUS_IN_PROGRESS, $helperId, 'Work started');

            // Send notification to task owner
            $helper = $this->userModel->find($helperId);
            $this->notificationService->notifyTaskStarted(
                $taskId,
                $task['title'],
                $task['user_id'],
                $helperId,
                $helper['name'] ?? 'Helper'
            );

            return $this->getTaskById($taskId);
        });

        return $result;
    }

    /**
     * Submit hasil pengerjaan task (dengan transaction).
     * 
     * @param int $taskId
     * @param int $helperId ID helper yang submit
     * @return array Updated task data
     */
    public function submitTask(int $taskId, int $helperId): array
    {
        $task = $this->taskModel->find($taskId);

        if (!$task) {
            throw BusinessException::notFound('Task not found');
        }

        if ($task['helper_id'] != $helperId) {
            throw BusinessException::forbidden('You are not assigned to this task');
        }

        if ($task['status'] !== TaskModel::STATUS_IN_PROGRESS) {
            throw BusinessException::conflict('Cannot submit task in current status');
        }

        $result = $this->transaction(function () use ($taskId, $helperId, $task) {
            $this->changeStatus($taskId, TaskModel::STATUS_WAITING_APPROVAL, $helperId, 'Work submitted for approval');

            // Send notification to task owner
            $helper = $this->userModel->find($helperId);
            $this->notificationService->notifyTaskSubmitted(
                $taskId,
                $task['title'],
                $task['user_id'],
                $helperId,
                $helper['name'] ?? 'Helper'
            );

            return $this->getTaskById($taskId);
        });

        return $result;
    }

    /**
     * Complete task oleh user (dengan transaction).
     * 
     * @param int $taskId
     * @param int $userId ID user yang menyelesaikan task
     * @return array Updated task data
     */
    public function completeTask(int $taskId, int $userId): array
    {
        $task = $this->taskModel->find($taskId);

        if (!$task) {
            throw BusinessException::notFound('Task not found');
        }

        if ($task['user_id'] != $userId) {
            throw BusinessException::forbidden('Only task owner can complete the task');
        }

        if ($task['status'] !== TaskModel::STATUS_WAITING_APPROVAL) {
            throw BusinessException::conflict('Cannot complete task in current status');
        }

        $result = $this->transaction(function () use ($taskId, $userId, $task) {
            $this->changeStatus($taskId, TaskModel::STATUS_COMPLETED, $userId, 'Task completed by owner');

            // Send notification to helper
            $owner = $this->userModel->find($userId);
            $this->notificationService->notifyTaskCompleted(
                $taskId,
                $task['title'],
                $task['helper_id'],
                $userId,
                $owner['name'] ?? 'User'
            );

            return $this->getTaskById($taskId);
        });

        return $result;
    }

    /**
     * Ambil tasks berdasarkan status.
     */
    public function getTasksByStatus(string $status, int $page = 1, int $perPage = 20): array
    {
        return $this->getAllTasks(['status' => $status], $page, $perPage);
    }

    /**
     * Ambil tasks milik user tertentu dengan filter tanggal.
     * 
     * @param int $userId
     * @param string|null $status
     * @param string|null $dateFrom Format: Y-m-d
     * @param string|null $dateTo Format: Y-m-d
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getUserTasks(int $userId, ?string $status = null, ?string $dateFrom = null, ?string $dateTo = null, int $page = 1, int $perPage = 20): array
    {
        $filters = ['user_id' => $userId];
        
        if ($status) {
            $filters['status'] = $status;
        }

        // Build custom query with date filters
        $builder = $this->taskModel->builder();
        $builder->select('tasks.*, categories.name as category_name, users.name as user_name');
        $builder->join('categories', 'categories.id = tasks.category_id', 'left');
        $builder->join('users', 'users.id = tasks.user_id', 'left');
        $builder->where('tasks.user_id', $userId);

        if ($status) {
            $builder->where('tasks.status', $status);
        }

        if ($dateFrom) {
            $builder->where('tasks.created_at >=', $dateFrom . ' 00:00:00');
        }

        if ($dateTo) {
            $builder->where('tasks.created_at <=', $dateTo . ' 23:59:59');
        }

        $total = $builder->countAllResults(false);

        $builder->orderBy('tasks.created_at', 'DESC');
        $builder->limit($perPage, ($page - 1) * $perPage);

        $tasks = $builder->get()->getResultArray();

        return [
            'data'        => $tasks,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Ambil tasks yang ditugaskan ke helper tertentu.
     * 
     * @param int $helperId
     * @param string|null $status
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getHelperTasks(int $helperId, ?string $status = null, int $page = 1, int $perPage = 20): array
    {
        $filters = ['helper_id' => $helperId];
        if ($status) {
            $filters['status'] = $status;
        }
        return $this->getAllTasks($filters, $page, $perPage);
    }

    /**
     * Ambil task stats untuk user.
     */
    public function getUserTaskStats(int $userId): array
    {
        $db = \Config\Database::connect();

        $stats = [
            'total'          => $this->getUserTasks($userId)['total'],
            'open'           => $this->getUserTasks($userId, TaskModel::STATUS_OPEN)['total'],
            'in_progress'    => $this->getUserTasks($userId, TaskModel::STATUS_IN_PROGRESS)['total'],
            'completed'      => $this->getUserTasks($userId, TaskModel::STATUS_COMPLETED)['total'],
        ];

        return $stats;
    }
}

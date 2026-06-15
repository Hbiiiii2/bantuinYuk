<?php

namespace App\Services;

use App\Models\UserModel;
use App\Models\TaskModel;
use App\Models\TransactionModel;
use App\Models\DisputeModel;
use App\Models\HelperProfileModel;
use App\Models\NotificationModel;
use App\Models\TaskStatusHistoryModel;
use App\Models\TaskAttachmentModel;
use App\Models\TaskProgressModel;
use App\Exceptions\BusinessException;

class AdminService extends BaseService
{
    protected UserModel $userModel;
    protected TaskModel $taskModel;
    protected TransactionModel $transactionModel;
    protected DisputeModel $disputeModel;
    protected HelperProfileModel $helperProfileModel;
    protected NotificationModel $notificationModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
        $this->taskModel = new TaskModel();
        $this->transactionModel = new TransactionModel();
        $this->disputeModel = new DisputeModel();
        $this->helperProfileModel = new HelperProfileModel();
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Get dashboard summary.
     */
    public function getDashboard(): array
    {
        $totalUsers    = $this->userModel->countAll();
        $totalHelpers  = $this->helperProfileModel->countAll();
        $totalTasks    = $this->taskModel->countAll();
        $openTasks     = $this->taskModel->where('status', 'open')->countAllResults();
        $completedTasks = $this->taskModel->where('status', 'completed')->countAllResults();
        $totalTransactions = $this->transactionModel->countAll();
        $totalDisputes = $this->disputeModel->countAll();
        $pendingDisputes = $this->disputeModel->where('status', 'open')->countAllResults();
        $totalNotifications = $this->notificationModel->countAll();

        return [
            'users'               => $totalUsers,
            'helpers'             => $totalHelpers,
            'tasks'               => $totalTasks,
            'open_tasks'          => $openTasks,
            'completed_tasks'     => $completedTasks,
            'wallet_transactions' => $totalTransactions,
            'disputes'            => $totalDisputes,
            'pending_disputes'    => $pendingDisputes,
            'notifications'       => $totalNotifications,
        ];
    }

    /**
     * Get users with pagination and filters.
     */
    public function getUsers(int $page = 1, int $perPage = 20, ?string $search = null, ?string $role = null, ?string $sortBy = null): array
    {
        $builder = $this->userModel->builder();

        if ($search) {
            $builder->groupStart();
            $builder->like('name', $search);
            $builder->orLike('email', $search);
            $builder->orLike('phone', $search);
            $builder->groupEnd();
        }

        if ($role) {
            $builder->where('role', $role);
        }

        $total = $builder->countAllResults(false);

        $sortField = $sortBy ?? 'created_at';
        $allowedSorts = ['name', 'email', 'role', 'created_at'];
        if (!in_array($sortField, $allowedSorts)) {
            $sortField = 'created_at';
        }

        $builder->orderBy($sortField, 'DESC');
        $builder->limit($perPage, ($page - 1) * $perPage);

        $users = $builder->get()->getResultArray();

        return [
            'data'       => $users,
            'total'      => $total,
            'page'       => $page,
            'per_page'   => $perPage,
        ];
    }

    /**
     * Get user detail.
     */
    public function getUserDetail(int $userId): array
    {
        $user = $this->userModel->find($userId);

        if (!$user) {
            throw BusinessException::notFound('User not found');
        }

        // Get user stats
        $totalTasks = $this->taskModel->where('user_id', $userId)->countAllResults();
        $completedTasks = $this->taskModel->where('user_id', $userId)->where('status', 'completed')->countAllResults();

        $user['stats'] = [
            'total_tasks'     => $totalTasks,
            'completed_tasks' => $completedTasks,
        ];

        return $user;
    }

    /**
     * Update user status.
     */
    public function updateUserStatus(int $userId, array $data): array
    {
        $user = $this->userModel->find($userId);

        if (!$user) {
            throw BusinessException::notFound('User not found');
        }

        $this->userModel->update($userId, [
            'active' => $data['active'] ?? $user['active']
        ]);

        return $this->userModel->find($userId)->toArray();
    }

    /**
     * Get helpers with pagination and filters.
     */
    public function getHelpers(int $page = 1, int $perPage = 20, ?string $search = null, ?string $verificationStatus = null): array
    {
        $builder = $this->helperProfileModel->builder();
        $builder->select('helper_profiles.*, users.name, users.email, users.phone, users.rating');
        $builder->join('users', 'users.id = helper_profiles.user_id', 'left');

        if ($search) {
            $builder->groupStart();
            $builder->like('users.name', $search);
            $builder->orLike('users.email', $search);
            $builder->groupEnd();
        }

        if ($verificationStatus) {
            $builder->where('helper_profiles.verification_status', $verificationStatus);
        }

        $total = $builder->countAllResults(false);

        $builder->orderBy('helper_profiles.created_at', 'DESC');
        $builder->limit($perPage, ($page - 1) * $perPage);

        $helpers = $builder->get()->getResultArray();

        return [
            'data'       => $helpers,
            'total'      => $total,
            'page'       => $page,
            'per_page'   => $perPage,
        ];
    }

    /**
     * Get helper detail.
     */
    public function getHelperDetail(int $helperId): array
    {
        $builder = $this->helperProfileModel->builder();
        $builder->select('helper_profiles.*, users.name, users.email, users.phone, users.rating');
        $builder->join('users', 'users.id = helper_profiles.user_id', 'left');
        $builder->where('helper_profiles.user_id', $helperId);

        $helper = $builder->get()->getRowArray();

        if (!$helper) {
            throw BusinessException::notFound('Helper not found');
        }

        // Get helper stats
        $totalTasks = $this->taskModel->where('helper_id', $helperId)->countAllResults();
        $completedTasks = $this->taskModel->where('helper_id', $helperId)->where('status', 'completed')->countAllResults();

        $helper['stats'] = [
            'total_tasks'     => $totalTasks,
            'completed_tasks' => $completedTasks,
        ];

        return $helper;
    }

    /**
     * Verify helper.
     */
    public function verifyHelper(int $helperId): array
    {
        $profile = $this->helperProfileModel->where('user_id', $helperId)->first();

        if (!$profile) {
            throw BusinessException::notFound('Helper profile not found');
        }

        $this->helperProfileModel->update($profile['id'], [
            'verification_status' => 'verified',
        ]);

        return $this->getHelperDetail($helperId);
    }

    /**
     * Reject helper verification.
     */
    public function rejectHelper(int $helperId, ?string $reason = null): array
    {
        $profile = $this->helperProfileModel->where('user_id', $helperId)->first();

        if (!$profile) {
            throw BusinessException::notFound('Helper profile not found');
        }

        $this->helperProfileModel->update($profile['id'], [
            'verification_status' => 'rejected',
            'rejection_reason'    => $reason,
        ]);

        return $this->getHelperDetail($helperId);
    }

    /**
     * Get tasks with pagination and filters.
     */
    public function getTasks(int $page = 1, int $perPage = 20, ?string $search = null, ?string $status = null, ?int $categoryId = null): array
    {
        $builder = $this->taskModel->builder();
        $builder->select('tasks.*, categories.name as category_name, users.name as user_name, helpers.name as helper_name');
        $builder->join('categories', 'categories.id = tasks.category_id', 'left');
        $builder->join('users', 'users.id = tasks.user_id', 'left');
        $builder->join('users as helpers', 'helpers.id = tasks.helper_id', 'left');

        if ($search) {
            $builder->groupStart();
            $builder->like('tasks.title', $search);
            $builder->orLike('users.name', $search);
            $builder->groupEnd();
        }

        if ($status) {
            $builder->where('tasks.status', $status);
        }

        if ($categoryId) {
            $builder->where('tasks.category_id', $categoryId);
        }

        $total = $builder->countAllResults(false);

        $builder->orderBy('tasks.created_at', 'DESC');
        $builder->limit($perPage, ($page - 1) * $perPage);

        $tasks = $builder->get()->getResultArray();

        return [
            'data'       => $tasks,
            'total'      => $total,
            'page'       => $page,
            'per_page'   => $perPage,
        ];
    }

    /**
     * Get task detail with all related data.
     */
    public function getTaskDetail(int $taskId): array
    {
        $builder = $this->taskModel->builder();
        $builder->select('tasks.*, categories.name as category_name, users.name as user_name, helpers.name as helper_name');
        $builder->join('categories', 'categories.id = tasks.category_id', 'left');
        $builder->join('users', 'users.id = tasks.user_id', 'left');
        $builder->join('users as helpers', 'helpers.id = tasks.helper_id', 'left');
        $builder->where('tasks.id', $taskId);

        $task = $builder->get()->getRowArray();

        if (!$task) {
            throw BusinessException::notFound('Task not found');
        }

        // Get status history
        $historyModel = new TaskStatusHistoryModel();
        $task['status_history'] = $historyModel->where('task_id', $taskId)->orderBy('created_at', 'ASC')->findAll();

        // Get attachments
        $attachmentModel = new TaskAttachmentModel();
        $task['attachments'] = $attachmentModel->where('task_id', $taskId)->findAll();

        // Get progress
        $progressModel = new TaskProgressModel();
        $task['progress'] = $progressModel->where('task_id', $taskId)->orderBy('created_at', 'DESC')->findAll();

        return $task;
    }

    /**
     * Get transactions with pagination and filters.
     */
    public function getTransactions(int $page = 1, int $perPage = 20, ?string $search = null, ?string $type = null, ?string $status = null): array
    {
        $builder = $this->transactionModel->builder();
        $builder->select('transactions.*, users.name as user_name');
        $builder->join('users', 'users.id = transactions.user_id', 'left');

        if ($search) {
            $builder->groupStart();
            $builder->like('transactions.reference_id', $search);
            $builder->orLike('users.name', $search);
            $builder->groupEnd();
        }

        if ($type) {
            $builder->where('transactions.type', $type);
        }

        if ($status) {
            $builder->where('transactions.status', $status);
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

    /**
     * Get transaction detail.
     */
    public function getTransactionDetail(int $transactionId): array
    {
        $builder = $this->transactionModel->builder();
        $builder->select('transactions.*, users.name as user_name');
        $builder->join('users', 'users.id = transactions.user_id', 'left');
        $builder->where('transactions.id', $transactionId);

        $transaction = $builder->get()->getRowArray();

        if (!$transaction) {
            throw BusinessException::notFound('Transaction not found');
        }

        return $transaction;
    }

    /**
     * Get wallets with pagination.
     */
    public function getWallets(int $page = 1, int $perPage = 20, ?string $search = null): array
    {
        $walletModel = new \App\Models\WalletModel();

        $builder = $walletModel->builder();
        $builder->select('wallets.*, users.name as user_name, users.email as user_email');
        $builder->join('users', 'users.id = wallets.user_id', 'left');

        if ($search) {
            $builder->groupStart();
            $builder->like('users.name', $search);
            $builder->orLike('users.email', $search);
            $builder->groupEnd();
        }

        $total = $builder->countAllResults(false);

        $builder->orderBy('wallets.balance', 'DESC');
        $builder->limit($perPage, ($page - 1) * $perPage);

        $wallets = $builder->get()->getResultArray();

        return [
            'data'       => $wallets,
            'total'      => $total,
            'page'       => $page,
            'per_page'   => $perPage,
        ];
    }

    /**
     * Get system analytics.
     */
    public function getAnalytics(): array
    {
        // User stats
        $totalUsers = $this->userModel->countAll();
        $totalHelpers = $this->helperProfileModel->countAll();
        $verifiedHelpers = $this->helperProfileModel->where('verification_status', 'verified')->countAllResults();

        // Task stats
        $totalTasks = $this->taskModel->countAll();
        $completedTasks = $this->taskModel->where('status', 'completed')->countAllResults();
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;

        // Dispute stats
        $totalDisputes = $this->disputeModel->countAll();
        $resolvedDisputes = $this->disputeModel->where('status', 'resolved')->countAllResults();
        $disputeRate = $totalTasks > 0 ? round(($totalDisputes / $totalTasks) * 100, 2) : 0;

        // Transaction stats
        $transactionModel = new TransactionModel();
        $totalTransactionAmount = $transactionModel->selectSum('amount')->where('status', 'completed')->get()->getRowArray();

        return [
            'total_users'              => $totalUsers,
            'total_helpers'            => $totalHelpers,
            'verified_helpers'         => $verifiedHelpers,
            'total_tasks'              => $totalTasks,
            'completed_tasks'          => $completedTasks,
            'completion_rate'          => $completionRate,
            'total_disputes'           => $totalDisputes,
            'resolved_disputes'        => $resolvedDisputes,
            'dispute_rate'             => $disputeRate,
            'total_transaction_amount' => (float) ($totalTransactionAmount['amount'] ?? 0),
        ];
    }
}

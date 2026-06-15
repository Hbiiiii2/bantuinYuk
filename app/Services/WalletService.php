<?php

namespace App\Services;

use App\Models\WalletModel;
use App\Models\TransactionModel;
use App\Models\TaskModel;
use App\Models\UserModel;
use App\Exceptions\BusinessException;
use App\Exceptions\ValidationException;

class WalletService extends BaseService
{
    protected WalletModel $walletModel;
    protected TransactionModel $transactionModel;
    protected TaskModel $taskModel;
    protected UserModel $userModel;
    protected NotificationService $notificationService;

    public function __construct()
    {
        parent::__construct();
        $this->walletModel      = new WalletModel();
        $this->transactionModel = new TransactionModel();
        $this->taskModel        = new TaskModel();
        $this->userModel        = new UserModel();
        $this->notificationService = new NotificationService();
    }

    /**
     * Get or create wallet for user.
     */
    public function getWallet(int $userId): array
    {
        $wallet = $this->walletModel->getByUserId($userId);

        if (!$wallet) {
            $walletId = $this->walletModel->createWallet($userId);
            $wallet   = $this->walletModel->find($walletId);
        }

        return $wallet;
    }

    /**
     * Get wallet summary with stats.
     */
    public function getWalletSummary(int $userId): array
    {
        $wallet = $this->getWallet($userId);

        $totalEarned    = $this->getTotalByType($userId, TransactionModel::TYPE_TASK_PAYMENT);
        $totalWithdrawn = $this->getTotalByType($userId, TransactionModel::TYPE_WITHDRAW);
        $totalRefunded  = $this->getTotalByType($userId, TransactionModel::TYPE_REFUND);

        $pendingWithdrawals = $this->transactionModel->builder()
            ->where('user_id', $userId)
            ->where('type', TransactionModel::TYPE_WITHDRAW)
            ->where('status', TransactionModel::STATUS_PENDING)
            ->sum('amount') ?? 0;

        return [
            'balance'             => (float) $wallet['balance'],
            'available_balance'   => (float) $wallet['balance'] - (float) ($wallet['pending_balance'] ?? 0),
            'pending_balance'     => (float) ($wallet['pending_balance'] ?? 0),
            'total_earned'        => $totalEarned,
            'total_withdrawn'     => $totalWithdrawn,
            'total_refunded'      => $totalRefunded,
            'pending_withdrawals' => (float) $pendingWithdrawals,
        ];
    }

    /**
     * Get transaction history for user.
     */
    public function getTransactionHistory(int $userId, ?string $type = null, int $page = 1, int $perPage = 20): array
    {
        if ($type) {
            return $this->transactionModel->getByType($userId, $type, $page, $perPage);
        }

        return $this->transactionModel->getByUserId($userId, $page, $perPage);
    }

    /**
     * Release payment to helper when task is completed.
     * Idempotent - only releases once per task.
     * Uses SELECT FOR UPDATE to prevent race conditions.
     */
    public function releasePayment(int $taskId, int $userId): array
    {
        $task = $this->taskModel->find($taskId);
        if (!$task) {
            throw BusinessException::notFound('Task not found');
        }

        if ($task['status'] !== TaskModel::STATUS_COMPLETED) {
            throw BusinessException::conflict('Payment can only be released for completed tasks');
        }

        if ($task['user_id'] != $userId) {
            throw BusinessException::forbidden('You can only release payment for your own tasks');
        }

        if (!$task['helper_id']) {
            throw BusinessException::conflict('Task has no assigned helper');
        }

        $result = $this->transaction(function () use ($taskId, $task) {
            // SELECT FOR UPDATE - Lock the row to prevent race condition
            $builder = $this->taskModel->builder();
            $builder->where('id', $taskId);
            $builder->lockForUpdate();
            $lockedTask = $builder->get()->getRowArray();

            if (!$lockedTask) {
                throw BusinessException::notFound('Task not found');
            }

            // Double-check payment status after lock
            $hasPayment = $this->transactionModel->builder()
                ->where('task_id', $taskId)
                ->where('type', TransactionModel::TYPE_TASK_PAYMENT)
                ->where('status', TransactionModel::STATUS_COMPLETED)
                ->countAllResults();

            if ($hasPayment > 0) {
                throw BusinessException::conflict('Payment has already been released for this task');
            }

            $amount      = (float) $task['price'];
            $helperId    = $task['helper_id'];
            $referenceId = $this->generateReferenceId('PAY');

            // Create transaction record
            $transactionId = $this->transactionModel->insert([
                'user_id'      => $helperId,
                'task_id'      => $taskId,
                'amount'       => $amount,
                'type'         => TransactionModel::TYPE_TASK_PAYMENT,
                'status'       => TransactionModel::STATUS_COMPLETED,
                'reference_id' => $referenceId,
                'description'  => "Payment for task: {$task['title']}",
            ]);

            if (!$transactionId) {
                throw BusinessException::failed('Failed to create transaction');
            }

            // Ensure helper has wallet
            $this->getWallet($helperId);

            // Increment helper balance
            $this->walletModel->incrementBalance($helperId, $amount);

            // Send notification to helper
            $this->notificationService->notifyPaymentReleased(
                $helperId,
                $task['title'],
                $amount,
                $taskId
            );

            return $this->transactionModel->find($transactionId);
        });

        return $result;
    }

    /**
     * Request withdrawal.
     * Uses hold balance to reserve funds.
     */
    public function requestWithdraw(int $userId, array $data): array
    {
        $this->validateRequired($data, [
            'amount' => 'Amount',
        ]);

        $amount = (float) $data['amount'];

        $this->validatePositive($amount, 'amount');

        $wallet = $this->getWallet($userId);
        $availableBalance = (float) $wallet['balance'] - (float) ($wallet['pending_balance'] ?? 0);

        if ($amount > $availableBalance) {
            throw BusinessException::conflict('Insufficient available balance');
        }

        $result = $this->transaction(function () use ($userId, $amount, $data) {
            $referenceId = $this->generateReferenceId('WD');

            // Create transaction record
            $transactionId = $this->transactionModel->insert([
                'user_id'      => $userId,
                'task_id'      => null,
                'amount'       => $amount,
                'type'         => TransactionModel::TYPE_WITHDRAW,
                'status'       => TransactionModel::STATUS_PENDING,
                'reference_id' => $referenceId,
                'description'  => $data['description'] ?? 'Withdrawal request',
            ]);

            if (!$transactionId) {
                throw BusinessException::failed('Failed to create withdrawal request');
            }

            // Hold balance atomically (move to pending)
            $held = $this->walletModel->holdBalance($userId, $amount);
            if (!$held) {
                throw BusinessException::conflict('Failed to hold balance');
            }

            // Send notification
            $this->notificationService->notifyWithdrawRequested($userId, $amount, $transactionId);

            return $this->transactionModel->find($transactionId);
        });

        return $result;
    }

    /**
     * Admin approve withdrawal.
     * Confirms held balance and deducts from total.
     */
    public function approveWithdraw(int $transactionId, int $adminId): array
    {
        $transaction = $this->transactionModel->find($transactionId);
        if (!$transaction) {
            throw BusinessException::notFound('Transaction not found');
        }

        if ($transaction['type'] !== TransactionModel::TYPE_WITHDRAW) {
            throw BusinessException::conflict('Transaction is not a withdrawal');
        }

        if ($transaction['status'] !== TransactionModel::STATUS_PENDING) {
            throw BusinessException::conflict('Withdrawal is not pending');
        }

        $result = $this->transaction(function () use ($transaction, $adminId) {
            // Confirm held balance (deduct from pending and total)
            $confirmed = $this->walletModel->confirmHeldBalance(
                $transaction['user_id'],
                (float) $transaction['amount']
            );

            if (!$confirmed) {
                throw BusinessException::failed('Failed to confirm balance');
            }

            $this->transactionModel->update($transaction['id'], [
                'status' => TransactionModel::STATUS_COMPLETED,
            ]);

            // Send notification
            $this->notificationService->notifyWithdrawApproved(
                $transaction['user_id'],
                (float) $transaction['amount'],
                $transaction['id']
            );

            return $this->transactionModel->find($transaction['id']);
        });

        return $result;
    }

    /**
     * Admin reject withdrawal.
     * Releases held balance back to available.
     */
    public function rejectWithdraw(int $transactionId, int $adminId, ?string $reason = null): array
    {
        $transaction = $this->transactionModel->find($transactionId);
        if (!$transaction) {
            throw BusinessException::notFound('Transaction not found');
        }

        if ($transaction['type'] !== TransactionModel::TYPE_WITHDRAW) {
            throw BusinessException::conflict('Transaction is not a withdrawal');
        }

        if ($transaction['status'] !== TransactionModel::STATUS_PENDING) {
            throw BusinessException::conflict('Withdrawal is not pending');
        }

        $result = $this->transaction(function () use ($transaction, $adminId, $reason) {
            // Release held balance (move back from pending to available)
            $released = $this->walletModel->releaseHeldBalance(
                $transaction['user_id'],
                (float) $transaction['amount']
            );

            if (!$released) {
                throw BusinessException::failed('Failed to release held balance');
            }

            // Update transaction status
            $this->transactionModel->update($transaction['id'], [
                'status'     => TransactionModel::STATUS_CANCELLED,
                'description' => $reason ?? 'Withdrawal rejected',
            ]);

            // Send notification
            $this->notificationService->notifyWithdrawRejected(
                $transaction['user_id'],
                (float) $transaction['amount'],
                $transaction['id'],
                $reason
            );

            return $this->transactionModel->find($transaction['id']);
        });

        return $result;
    }

    /**
     * Get pending withdrawals (admin).
     */
    public function getPendingWithdrawals(int $page = 1, int $perPage = 20): array
    {
        return $this->transactionModel->getPendingWithdrawals($page, $perPage);
    }

    /**
     * Get all transactions (admin).
     */
    public function getAllTransactions(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        return $this->transactionModel->getAllTransactions($filters, $page, $perPage);
    }

    /**
     * Get total amount by transaction type for user.
     */
    private function getTotalByType(int $userId, string $type): float
    {
        $result = $this->transactionModel->builder()
            ->selectSum('amount')
            ->where('user_id', $userId)
            ->where('type', $type)
            ->where('status', TransactionModel::STATUS_COMPLETED)
            ->get()
            ->getRowArray();

        return (float) ($result['amount'] ?? 0);
    }
}

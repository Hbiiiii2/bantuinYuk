<?php

namespace App\Models;

use CodeIgniter\Model;

class WalletModel extends Model
{
    protected $table = 'wallets';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'user_id',
        'balance',
        'pending_balance'
    ];

    /**
     * Get wallet by user_id.
     */
    public function getByUserId(int $userId): ?array
    {
        return $this->where('user_id', $userId)->first();
    }

    /**
     * Create wallet for user.
     */
    public function createWallet(int $userId, float $balance = 0, float $pendingBalance = 0): int
    {
        return $this->insert([
            'user_id'         => $userId,
            'balance'         => $balance,
            'pending_balance' => $pendingBalance,
        ]);
    }

    /**
     * Get balance for user.
     */
    public function getBalance(int $userId): float
    {
        $wallet = $this->getByUserId($userId);
        return $wallet ? (float) $wallet['balance'] : 0;
    }

    /**
     * Get available balance (balance - pending).
     */
    public function getAvailableBalance(int $userId): float
    {
        $wallet = $this->getByUserId($userId);
        if (!$wallet) {
            return 0;
        }
        return (float) $wallet['balance'] - (float) ($wallet['pending_balance'] ?? 0);
    }

    /**
     * Get pending balance for user.
     */
    public function getPendingBalance(int $userId): float
    {
        $wallet = $this->getByUserId($userId);
        return $wallet ? (float) ($wallet['pending_balance'] ?? 0) : 0;
    }

    /**
     * Atomic increment balance.
     * Prevents race conditions with atomic operation.
     */
    public function incrementBalance(int $userId, float $amount): bool
    {
        $builder = $this->builder();
        $builder->where('user_id', $userId);
        $builder->set('balance', "balance + {$amount}", false);
        
        return $builder->update() !== false;
    }

    /**
     * Atomic decrement balance with check.
     * Returns false if insufficient balance.
     */
    public function decrementBalance(int $userId, float $amount): bool
    {
        $builder = $this->builder();
        $builder->where('user_id', $userId);
        $builder->where('balance >=', $amount);
        $builder->set('balance', "balance - {$amount}", false);
        
        return $builder->update() !== false;
    }

    /**
     * Hold balance for pending withdrawal.
     * Moves amount from available to pending.
     */
    public function holdBalance(int $userId, float $amount): bool
    {
        $builder = $this->builder();
        $builder->where('user_id', $userId);
        $builder->where("(balance - pending_balance) >= {$amount}");
        $builder->set('pending_balance', "pending_balance + {$amount}", false);
        
        return $builder->update() !== false;
    }

    /**
     * Release held balance (on rejection).
     * Moves amount from pending back to available.
     */
    public function releaseHeldBalance(int $userId, float $amount): bool
    {
        $builder = $this->builder();
        $builder->where('user_id', $userId);
        $builder->where("pending_balance >= {$amount}");
        $builder->set('pending_balance', "pending_balance - {$amount}", false);
        
        return $builder->update() !== false;
    }

    /**
     * Confirm held balance (on approval).
     * Deducts from pending and reduces total balance.
     */
    public function confirmHeldBalance(int $userId, float $amount): bool
    {
        $builder = $this->builder();
        $builder->where('user_id', $userId);
        $builder->where("pending_balance >= {$amount}");
        $builder->set('pending_balance', "pending_balance - {$amount}", false);
        $builder->set('balance', "balance - {$amount}", false);
        
        return $builder->update() !== false;
    }

    /**
     * Check if user has sufficient available balance.
     */
    public function hasSufficientBalance(int $userId, float $amount): bool
    {
        return $this->getAvailableBalance($userId) >= $amount;
    }
}

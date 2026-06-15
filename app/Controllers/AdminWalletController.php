<?php

namespace App\Controllers;

use App\Services\WalletService;
use App\Traits\ApiResponseTrait;
use App\Exceptions\BusinessException;

class AdminWalletController extends BaseController
{
    use ApiResponseTrait;

    protected WalletService $walletService;

    public function __construct()
    {
        $this->walletService = new WalletService();
    }

    /**
     * GET /admin/withdrawals - Get pending withdrawals
     */
    public function pendingWithdrawals()
    {
        try {
            $page    = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 20;

            $withdrawals = $this->walletService->getPendingWithdrawals((int) $page, (int) $perPage);

            return $this->successResponse($withdrawals);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    /**
     * POST /admin/withdrawals/{id}/approve - Approve withdrawal
     */
    public function approveWithdraw($id)
    {
        try {
            $adminId     = auth()->id();
            $transaction = $this->walletService->approveWithdraw((int) $id, $adminId);

            return $this->successResponse($transaction, 'Withdrawal approved successfully');

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    /**
     * POST /admin/withdrawals/{id}/reject - Reject withdrawal
     */
    public function rejectWithdraw($id)
    {
        try {
            $adminId = auth()->id();
            $data    = $this->request->getJSON(true);

            $transaction = $this->walletService->rejectWithdraw(
                (int) $id,
                $adminId,
                $data['reason'] ?? null
            );

            return $this->successResponse($transaction, 'Withdrawal rejected successfully');

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    /**
     * GET /admin/transactions - Get all transactions
     */
    public function transactions()
    {
        try {
            $page    = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 20;
            $filters = [];

            if ($this->request->getGet('user_id')) {
                $filters['user_id'] = (int) $this->request->getGet('user_id');
            }
            if ($this->request->getGet('type')) {
                $filters['type'] = $this->request->getGet('type');
            }
            if ($this->request->getGet('status')) {
                $filters['status'] = $this->request->getGet('status');
            }

            $transactions = $this->walletService->getAllTransactions($filters, (int) $page, (int) $perPage);

            return $this->successResponse($transactions);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }
}

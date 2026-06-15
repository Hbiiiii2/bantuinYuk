<?php

namespace App\Controllers;

use App\Services\WalletService;
use App\Traits\ApiResponseTrait;
use App\Exceptions\BusinessException;
use App\Exceptions\ValidationException;

class WalletController extends BaseController
{
    use ApiResponseTrait;

    protected WalletService $walletService;

    public function __construct()
    {
        $this->walletService = new WalletService();
    }

    /**
     * GET /wallet - Get wallet summary
     */
    public function index()
    {
        try {
            $userId  = auth()->id();
            $summary = $this->walletService->getWalletSummary($userId);

            return $this->successResponse($summary);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    /**
     * GET /wallet/transactions - Get transaction history
     */
    public function transactions()
    {
        try {
            $userId    = auth()->id();
            $page      = $this->request->getGet('page') ?? 1;
            $perPage   = $this->request->getGet('per_page') ?? 20;
            $type      = $this->request->getGet('type');

            $transactions = $this->walletService->getTransactionHistory(
                $userId,
                $type,
                (int) $page,
                (int) $perPage
            );

            return $this->successResponse($transactions);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    /**
     * POST /wallet/release-payment/{taskId} - Release payment for completed task
     */
    public function releasePayment($taskId)
    {
        try {
            $userId = auth()->id();

            $transaction = $this->walletService->releasePayment((int) $taskId, $userId);

            return $this->successResponse($transaction, 'Payment released successfully');

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    /**
     * POST /wallet/withdraw - Request withdrawal
     */
    public function withdraw()
    {
        try {
            $userId = auth()->id();
            $data   = $this->request->getJSON(true);

            $transaction = $this->walletService->requestWithdraw($userId, $data);

            return $this->createdResponse($transaction, 'Withdrawal request submitted successfully');

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->getErrors(), $e->getMessage());

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    /**
     * GET /wallet/transactions/{id} - Get transaction detail
     */
    public function show($id)
    {
        try {
            $userId      = auth()->id();
            $transaction = model('TransactionModel')->find((int) $id);

            if (!$transaction) {
                return $this->errorResponse('Transaction not found', 404);
            }

            if ($transaction['user_id'] != $userId) {
                return $this->errorResponse('Forbidden', 403);
            }

            return $this->successResponse($transaction);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }
}

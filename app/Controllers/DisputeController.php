<?php

namespace App\Controllers;

use App\Services\DisputeService;
use App\Traits\ApiResponseTrait;
use App\Exceptions\BusinessException;
use App\Exceptions\ValidationException;

class DisputeController extends BaseController
{
    use ApiResponseTrait;

    protected DisputeService $disputeService;

    public function __construct()
    {
        $this->disputeService = new DisputeService();
    }

    /**
     * POST /disputes - Create dispute
     */
    public function create()
    {
        try {
            $userId = auth()->id();
            $data   = $this->request->getJSON(true);

            $dispute = $this->disputeService->createDispute($userId, $data);

            return $this->createdResponse($dispute, 'Dispute created successfully');

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->getErrors(), $e->getMessage());

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    /**
     * GET /disputes - List my disputes
     */
    public function index()
    {
        try {
            $userId  = auth()->id();
            $page    = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 20;
            $status  = $this->request->getGet('status');
            $search  = $this->request->getGet('search');

            $disputes = $this->disputeService->getUserDisputes(
                $userId,
                (int) $page,
                (int) $perPage,
                $status,
                $search
            );

            return $this->successResponse($disputes);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    /**
     * GET /disputes/{id} - Get dispute detail
     */
    public function show($id)
    {
        try {
            $userId  = auth()->id();
            $dispute = $this->disputeService->getDisputeById((int) $id);

            // Check authorization
            if (!$this->disputeService->validateDisputeOwnership((int) $id, $userId)) {
                // Check if admin
                $user = model('UserModel')->find($userId);
                if (!$user || $user['role'] !== 'admin') {
                    return $this->errorResponse('Forbidden', 403);
                }
            }

            return $this->successResponse($dispute);

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    /**
     * GET /admin/disputes - List all disputes (admin)
     */
    public function adminIndex()
    {
        try {
            $page    = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 20;
            $status  = $this->request->getGet('status');
            $search  = $this->request->getGet('search');

            $disputes = $this->disputeService->getAllDisputes(
                (int) $page,
                (int) $perPage,
                $status,
                $search
            );

            return $this->successResponse($disputes);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    /**
     * POST /admin/disputes/{id}/review - Admin review dispute
     */
    public function review($id)
    {
        try {
            $adminId = auth()->id();
            $dispute = $this->disputeService->reviewDispute((int) $id, $adminId);

            return $this->successResponse($dispute, 'Dispute is now under review');

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    /**
     * POST /admin/disputes/{id}/resolve - Admin resolve dispute
     */
    public function resolve($id)
    {
        try {
            $adminId = auth()->id();
            $data    = $this->request->getJSON(true);

            $dispute = $this->disputeService->resolveDispute((int) $id, $adminId, $data);

            return $this->successResponse($dispute, 'Dispute resolved successfully');

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->getErrors(), $e->getMessage());

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    /**
     * POST /admin/disputes/{id}/reject - Admin reject dispute
     */
    public function reject($id)
    {
        try {
            $adminId = auth()->id();
            $data    = $this->request->getJSON(true) ?? [];

            $dispute = $this->disputeService->rejectDispute((int) $id, $adminId, $data);

            return $this->successResponse($dispute, 'Dispute rejected successfully');

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }
}

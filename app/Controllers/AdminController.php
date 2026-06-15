<?php

namespace App\Controllers;

use App\Services\AdminService;
use App\Services\ReviewService;
use App\Traits\ApiResponseTrait;
use App\Exceptions\BusinessException;
use App\Exceptions\ValidationException;

class AdminController extends BaseController
{
    use ApiResponseTrait;

    protected AdminService $adminService;
    protected ReviewService $reviewService;

    public function __construct()
    {
        $this->adminService = new AdminService();
        $this->reviewService = new ReviewService();
    }

    // ============================================================
    // DASHBOARD
    // ============================================================

    public function dashboard()
    {
        try {
            $stats = $this->adminService->getDashboard();

            return $this->successResponse($stats);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    // ============================================================
    // USER MANAGEMENT
    // ============================================================

    public function users()
    {
        try {
            $page    = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 20;
            $search  = $this->request->getGet('search');
            $role    = $this->request->getGet('role');
            $sortBy  = $this->request->getGet('sort_by');

            $users = $this->adminService->getUsers((int) $page, (int) $perPage, $search, $role, $sortBy);

            return $this->successResponse($users);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function userDetail($id)
    {
        try {
            $user = $this->adminService->getUserDetail((int) $id);

            return $this->successResponse($user);

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function updateUserStatus($id)
    {
        try {
            $data = $this->request->getJSON(true);

            $user = $this->adminService->updateUserStatus((int) $id, $data);

            return $this->successResponse($user, 'User status updated successfully');

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    // ============================================================
    // HELPER MANAGEMENT
    // ============================================================

    public function helpers()
    {
        try {
            $page    = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 20;
            $search  = $this->request->getGet('search');
            $status  = $this->request->getGet('verification_status');

            $helpers = $this->adminService->getHelpers((int) $page, (int) $perPage, $search, $status);

            return $this->successResponse($helpers);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function helperDetail($id)
    {
        try {
            $helper = $this->adminService->getHelperDetail((int) $id);

            return $this->successResponse($helper);

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function verifyHelper($id)
    {
        try {
            $helper = $this->adminService->verifyHelper((int) $id);

            return $this->successResponse($helper, 'Helper verified successfully');

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function rejectHelper($id)
    {
        try {
            $data   = $this->request->getJSON(true);
            $reason = $data['reason'] ?? null;

            $helper = $this->adminService->rejectHelper((int) $id, $reason);

            return $this->successResponse($helper, 'Helper rejected successfully');

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    // ============================================================
    // TASK MANAGEMENT
    // ============================================================

    public function tasks()
    {
        try {
            $page       = $this->request->getGet('page') ?? 1;
            $perPage    = $this->request->getGet('per_page') ?? 20;
            $search     = $this->request->getGet('search');
            $status     = $this->request->getGet('status');
            $categoryId = $this->request->getGet('category_id');

            $tasks = $this->adminService->getTasks(
                (int) $page,
                (int) $perPage,
                $search,
                $status,
                $categoryId ? (int) $categoryId : null
            );

            return $this->successResponse($tasks);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function taskDetail($id)
    {
        try {
            $task = $this->adminService->getTaskDetail((int) $id);

            return $this->successResponse($task);

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    // ============================================================
    // TRANSACTION MANAGEMENT
    // ============================================================

    public function transactions()
    {
        try {
            $page    = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 20;
            $search  = $this->request->getGet('search');
            $type    = $this->request->getGet('type');
            $status  = $this->request->getGet('status');

            $transactions = $this->adminService->getTransactions((int) $page, (int) $perPage, $search, $type, $status);

            return $this->successResponse($transactions);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function transactionDetail($id)
    {
        try {
            $transaction = $this->adminService->getTransactionDetail((int) $id);

            return $this->successResponse($transaction);

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    // ============================================================
    // WALLET MONITORING
    // ============================================================

    public function wallets()
    {
        try {
            $page    = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 20;
            $search  = $this->request->getGet('search');

            $wallets = $this->adminService->getWallets((int) $page, (int) $perPage, $search);

            return $this->successResponse($wallets);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    // ============================================================
    // ANALYTICS
    // ============================================================

    public function analytics()
    {
        try {
            $analytics = $this->adminService->getAnalytics();

            return $this->successResponse($analytics);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    // ============================================================
    // CATEGORIES (Existing)
    // ============================================================

    public function categories()
    {
        try {
            $categories = model('CategoryModel')->findAll();

            return $this->successResponse($categories);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function createCategory()
    {
        try {
            $data = $this->request->getJSON(true);

            $category = model('CategoryModel')->insert([
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
            ]);

            return $this->createdResponse(model('CategoryModel')->find($category), 'Category created successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function updateCategory($id)
    {
        try {
            $data = $this->request->getJSON(true);

            model('CategoryModel')->update((int) $id, [
                'name'        => $data['name'] ?? null,
                'description' => $data['description'] ?? null,
            ]);

            $category = model('CategoryModel')->find((int) $id);

            return $this->successResponse($category, 'Category updated successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function deleteCategory($id)
    {
        try {
            model('CategoryModel')->delete((int) $id);

            return $this->noContentResponse('Category deleted successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    // ============================================================
    // REVIEWS (Existing)
    // ============================================================

    public function getReviews()
    {
        try {
            $page    = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 20;
            $filters = [];

            if ($this->request->getGet('helper_id')) {
                $filters['helper_id'] = (int) $this->request->getGet('helper_id');
            }
            if ($this->request->getGet('rating')) {
                $filters['rating'] = (int) $this->request->getGet('rating');
            }

            $reviews = $this->reviewService->getAllReviews($filters, (int) $page, (int) $perPage);

            return $this->successResponse($reviews);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }
}

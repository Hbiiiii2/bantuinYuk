<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Traits\ApiResponseTrait;
use App\Exceptions\BusinessException;
use App\Exceptions\ValidationException;

class AuthController extends BaseController
{
    use ApiResponseTrait;

    protected AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * Register new user.
     *
     * POST /api/v1/auth/register
     */
    public function register()
    {
        try {
            $data   = $this->request->getJSON(true);
            $result = $this->authService->register($data);

            return $this->createdResponse($result, 'User registered successfully');

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->getErrors(), $e->getMessage());

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    /**
     * Login user and return access token.
     *
     * POST /api/v1/auth/login
     */
    public function login()
    {
        try {
            $data  = $this->request->getJSON(true);
            $result = $this->authService->login($data);

            return $this->successResponse($result, 'Login successful');

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->getErrors(), $e->getMessage());

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    /**
     * Logout user and revoke token.
     *
     * POST /api/v1/auth/logout
     */
    public function logout()
    {
        try {
            $userId = auth()->id();

            if (!$userId) {
                return $this->unauthorizedResponse('Unauthorized');
            }

            $this->authService->logout($userId);

            return $this->successResponse(null, 'Logged out successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    /**
     * Get current user profile.
     *
     * GET /api/v1/auth/me
     */
    public function me()
    {
        try {
            $userId = auth()->id();

            if (!$userId) {
                return $this->unauthorizedResponse('Unauthorized');
            }

            $user = $this->authService->getUserById($userId);

            return $this->successResponse($user);

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    /**
     * Update current user profile.
     *
     * PUT /api/v1/auth/me
     */
    public function updateProfile()
    {
        try {
            $userId = auth()->id();

            if (!$userId) {
                return $this->unauthorizedResponse('Unauthorized');
            }

            $data = $this->request->getJSON(true);
            $user = $this->authService->updateProfile($userId, $data);

            return $this->successResponse($user, 'Profile updated successfully');

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->getErrors(), $e->getMessage());

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }
}

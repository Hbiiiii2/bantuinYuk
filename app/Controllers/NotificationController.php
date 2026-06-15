<?php

namespace App\Controllers;

use App\Services\NotificationService;
use App\Traits\ApiResponseTrait;
use App\Exceptions\BusinessException;

class NotificationController extends BaseController
{
    use ApiResponseTrait;

    protected NotificationService $notificationService;

    public function __construct()
    {
        $this->notificationService = new NotificationService();
    }

    /**
     * GET /notifications - Get user notifications
     */
    public function index()
    {
        try {
            $userId    = auth()->id();
            $page      = $this->request->getGet('page') ?? 1;
            $perPage   = $this->request->getGet('per_page') ?? 20;
            $unread    = $this->request->getGet('unread');
            $unreadOnly = $unread === '1' || $unread === 'true' ? true : null;

            $notifications = $this->notificationService->getUserNotifications(
                $userId,
                (int) $page,
                (int) $perPage,
                $unreadOnly
            );

            $notifications['unread_count'] = $this->notificationService->getUnreadCount($userId);

            return $this->successResponse($notifications);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    /**
     * GET /notifications/{id} - Get notification detail
     */
    public function show($id)
    {
        try {
            $userId       = auth()->id();
            $notification = $this->notificationService->getNotificationById((int) $id, $userId);

            return $this->successResponse($notification);

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    /**
     * POST /notifications/{id}/read - Mark notification as read
     */
    public function markAsRead($id)
    {
        try {
            $userId = auth()->id();

            $this->notificationService->markAsRead((int) $id, $userId);

            return $this->successResponse(null, 'Notification marked as read');

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    /**
     * POST /notifications/read-all - Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            $userId = auth()->id();

            $this->notificationService->markAllAsRead($userId);

            return $this->successResponse(null, 'All notifications marked as read');

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    /**
     * GET /notifications/unread-count - Get unread count
     */
    public function unreadCount()
    {
        try {
            $userId = auth()->id();

            $count = $this->notificationService->getUnreadCount($userId);

            return $this->successResponse(['unread_count' => $count]);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }
}

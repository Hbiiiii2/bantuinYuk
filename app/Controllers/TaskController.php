<?php

namespace App\Controllers;

use App\Services\TaskService;
use App\Services\AttachmentService;
use App\Services\ReviewService;
use App\Traits\ApiResponseTrait;
use App\Exceptions\BusinessException;
use App\Exceptions\ValidationException;

class TaskController extends BaseController
{
    use ApiResponseTrait;

    protected TaskService $taskService;
    protected AttachmentService $attachmentService;
    protected ReviewService $reviewService;

    public function __construct()
    {
        $this->taskService       = new TaskService();
        $this->attachmentService = new AttachmentService();
        $this->reviewService     = new ReviewService();
    }

    public function index()
    {
        try {
            $page    = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 20;
            $status  = $this->request->getGet('status');
            $categoryId = $this->request->getGet('category_id');
            $search = $this->request->getGet('search');
            $sortBy = $this->request->getGet('sort_by');
            $sortOrder = $this->request->getGet('sort_order');

            $filters = [];
            if ($status) {
                $filters['status'] = $status;
            }
            if ($categoryId) {
                $filters['category_id'] = $categoryId;
            }
            if ($search) {
                $filters['search'] = $search;
            }
            if ($sortBy) {
                $filters['sort_by'] = $sortBy;
            }
            if ($sortOrder) {
                $filters['sort_order'] = $sortOrder;
            }

            $tasks = $this->taskService->getAllTasks($filters, (int) $page, (int) $perPage);

            return $this->successResponse($tasks);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function show($id)
    {
        try {
            $task = $this->taskService->getTaskById((int) $id);

            return $this->successResponse($task);

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function store()
    {
        try {
            $data   = $this->request->getJSON(true);
            $userId = auth()->id();

            $task = $this->taskService->createTask($userId, $data);

            return $this->createdResponse($task, 'Task created successfully');

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->getErrors(), $e->getMessage());

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function update($id)
    {
        try {
            $data   = $this->request->getJSON(true);
            $userId = auth()->id();

            $task = $this->taskService->updateTask((int) $id, $userId, $data);

            return $this->successResponse($task, 'Task updated successfully');

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->getErrors(), $e->getMessage());

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function delete($id)
    {
        try {
            $userId = auth()->id();

            $task = $this->taskService->cancelTask((int) $id, $userId);

            return $this->successResponse($task, 'Task cancelled successfully');

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function myTasks()
    {
        try {
            $userId   = auth()->id();
            $page     = $this->request->getGet('page') ?? 1;
            $perPage  = $this->request->getGet('per_page') ?? 20;
            $status   = $this->request->getGet('status');
            $dateFrom = $this->request->getGet('date_from');
            $dateTo   = $this->request->getGet('date_to');

            $tasks = $this->taskService->getUserTasks(
                $userId, 
                $status, 
                $dateFrom, 
                $dateTo, 
                (int) $page, 
                (int) $perPage
            );

            return $this->successResponse($tasks);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function complete($id)
    {
        try {
            $userId = auth()->id();

            $task = $this->taskService->completeTask((int) $id, $userId);

            return $this->successResponse($task, 'Task completed successfully');

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function uploadAttachment($id)
    {
        try {
            $userId = auth()->id();
            $file   = $this->request->getFile('file');

            if (!$file) {
                return $this->errorResponse('No file uploaded', 400);
            }

            $fileArray = [
                'name'     => $file->getName(),
                'type'     => $file->getMimeType(),
                'tmp_name' => $file->getTempName(),
                'error'    => $file->getError(),
                'size'     => $file->getSize(),
            ];

            $attachment = $this->attachmentService->uploadAttachment((int) $id, $userId, $fileArray);

            return $this->createdResponse($attachment, 'Attachment uploaded successfully');

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->getErrors(), $e->getMessage());

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function getAttachments($id)
    {
        try {
            $attachments = $this->attachmentService->getAttachmentsByTask((int) $id);

            return $this->successResponse($attachments);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function deleteAttachment($id, $attachmentId)
    {
        try {
            $userId = auth()->id();

            $deleted = $this->attachmentService->deleteAttachment((int) $attachmentId, $userId);

            return $this->successResponse($deleted, 'Attachment deleted successfully');

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function createReview($id)
    {
        try {
            $userId = auth()->id();
            $data   = $this->request->getJSON(true);

            $review = $this->reviewService->createReview((int) $id, $userId, $data);

            return $this->createdResponse($review, 'Review created successfully');

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->getErrors(), $e->getMessage());

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function getReview($id)
    {
        try {
            $review = $this->reviewService->getReviewByTask((int) $id);

            if (!$review) {
                return $this->errorResponse('No review found for this task', 404);
            }

            return $this->successResponse($review);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }
}

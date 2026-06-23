<?php

namespace App\Controllers;

use App\Services\TaskService;
use App\Services\HelperService;
use App\Services\ProgressService;
use App\Services\AttachmentService;
use App\Services\ReviewService;
use App\Traits\ApiResponseTrait;
use App\Exceptions\BusinessException;
use App\Exceptions\ValidationException;

class HelperController extends BaseController
{
    use ApiResponseTrait;

    protected TaskService $taskService;
    protected HelperService $helperService;
    protected ProgressService $progressService;
    protected AttachmentService $attachmentService;
    protected ReviewService $reviewService;

    public function __construct()
    {
        $this->taskService      = new TaskService();
        $this->helperService    = new HelperService();
        $this->progressService  = new ProgressService();
        $this->attachmentService = new AttachmentService();
        $this->reviewService    = new ReviewService();
    }

    public function acceptTask($taskId)
    {
        try {
            $helperId = auth()->id();

            $task = $this->taskService->acceptTask((int) $taskId, $helperId);

            return $this->successResponse($task, 'Task accepted successfully');

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function startTask($taskId)
    {
        try {
            $helperId = auth()->id();

            $task = $this->taskService->startTask((int) $taskId, $helperId);

            return $this->successResponse($task, 'Task started successfully');

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function submitTask($taskId)
    {
        try {
            $helperId = auth()->id();

            $task = $this->taskService->submitTask((int) $taskId, $helperId);

            return $this->successResponse($task, 'Task submitted successfully');

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function availableTasks()
    {
        try {
            $page    = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 20;

            $tasks = $this->helperService->getAvailableTasks((int) $page, (int) $perPage);

            return $this->successResponse($tasks);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function myTasks()
    {
        try {
            $helperId = auth()->id();
            $page     = $this->request->getGet('page') ?? 1;
            $perPage  = $this->request->getGet('per_page') ?? 20;
            $statuses = $this->request->getGet('statuses');

            $statusArray = $statuses ? explode(',', $statuses) : [];

            $tasks = $this->helperService->getMyTasks($helperId, $statusArray, (int) $page, (int) $perPage);

            return $this->successResponse($tasks);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function profile()
    {
        try {
            $userId  = auth()->id();
            $profile = $this->helperService->getHelperProfile($userId);

            return $this->successResponse($profile);

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function updateProfile()
    {
        try {
            $userId = auth()->id();
            $data   = $this->request->getJSON(true);

            $profile = $this->helperService->updateProfile($userId, $data);

            return $this->successResponse($profile, 'Profile updated successfully');

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->getErrors(), $e->getMessage());

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function updateLocation()
    {
        try {
            $userId   = auth()->id();
            $data     = $this->request->getJSON(true);
            $latitude  = $data['latitude'] ?? 0;
            $longitude = $data['longitude'] ?? 0;

            $location = $this->helperService->updateLocation($userId, $latitude, $longitude);

            return $this->successResponse($location, 'Location updated successfully');

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->getErrors(), $e->getMessage());

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function submitVerification()
    {
        try {
            $userId = auth()->id();
            $data   = $this->request->getJSON(true);

            $profile = $this->helperService->submitVerification($userId, $data);

            return $this->successResponse($profile, 'Verification submitted successfully');

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->getErrors(), $e->getMessage());

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function stats()
    {
        try {
            $helperId = auth()->id();
            $stats    = $this->helperService->getHelperStats($helperId);

            return $this->successResponse($stats);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function show($id)
    {
        try {
            $profile = $this->helperService->getHelperProfile((int) $id);

            return $this->successResponse($profile);

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function createProgress($taskId)
    {
        try {
            $helperId = auth()->id();
            $data     = $this->request->getJSON(true);

            $progress = $this->progressService->createProgress((int) $taskId, $helperId, $data);

            return $this->createdResponse($progress, 'Progress created successfully');

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->getErrors(), $e->getMessage());

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function getProgress($taskId)
    {
        try {
            $page    = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 20;

            $progress = $this->progressService->getProgressByTask((int) $taskId, (int) $page, (int) $perPage);

            return $this->successResponse($progress);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function uploadAttachment($taskId)
    {
        try {
            $helperId = auth()->id();
            if (!$this->validate(['file' => 'uploaded[file]|ext_in[file,jpg,jpeg,png,pdf]|max_size[file,2048]'])) {
                return $this->validationErrorResponse($this->validator->getErrors(), 'Validation failed');
            }

            $file = $this->request->getFile('file');

            $fileArray = [
                'name'     => $file->getName(),
                'type'     => $file->getMimeType(),
                'tmp_name' => $file->getTempName(),
                'error'    => $file->getError(),
                'size'     => $file->getSize(),
            ];

            $attachment = $this->attachmentService->uploadAttachment((int) $taskId, $helperId, $fileArray);

            return $this->createdResponse($attachment, 'Attachment uploaded successfully');

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->getErrors(), $e->getMessage());

        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function getReviews()
    {
        try {
            $helperId = auth()->id();
            $page     = $this->request->getGet('page') ?? 1;
            $perPage  = $this->request->getGet('per_page') ?? 20;

            $reviews = $this->reviewService->getReviewsByHelper($helperId, (int) $page, (int) $perPage);

            return $this->successResponse($reviews);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    public function getRatingSummary()
    {
        try {
            $helperId = auth()->id();
            $summary  = $this->reviewService->getHelperRatingSummary($helperId);

            return $this->successResponse($summary);

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }
}

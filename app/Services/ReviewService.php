<?php

namespace App\Services;

use App\Models\TaskReviewModel;
use App\Models\TaskModel;
use App\Models\UserModel;
use App\Models\HelperProfileModel;
use App\Exceptions\BusinessException;
use App\Exceptions\ValidationException;

class ReviewService extends BaseService
{
    protected TaskReviewModel $reviewModel;
    protected TaskModel $taskModel;
    protected UserModel $userModel;
    protected HelperProfileModel $helperProfileModel;
    protected NotificationService $notificationService;

    public function __construct()
    {
        parent::__construct();
        $this->reviewModel = new TaskReviewModel();
        $this->taskModel = new TaskModel();
        $this->userModel = new UserModel();
        $this->helperProfileModel = new HelperProfileModel();
        $this->notificationService = new NotificationService();
    }

    /**
     * Buat review untuk task.
     * 
     * @param int $taskId
     * @param int $userId Owner task
     * @param array $data ['rating', 'review']
     * @return array Review data
     * @throws BusinessException Jika validasi gagal
     */
    public function createReview(int $taskId, int $userId, array $data): array
    {
        $this->validateRequired($data, [
            'rating' => 'Rating',
        ]);

        // Validate rating
        $rating = (int) $data['rating'];
        if ($rating < TaskReviewModel::MIN_RATING || $rating > TaskReviewModel::MAX_RATING) {
            throw ValidationException::single(
                'rating', 
                'Rating must be between ' . TaskReviewModel::MIN_RATING . ' and ' . TaskReviewModel::MAX_RATING
            );
        }

        // Validate task exists
        $task = $this->taskModel->find($taskId);
        if (!$task) {
            throw BusinessException::notFound('Task not found');
        }

        // Validate task is completed
        if ($task['status'] !== TaskModel::STATUS_COMPLETED) {
            throw BusinessException::conflict('Only completed tasks can be reviewed');
        }

        // Validate ownership
        if ($task['user_id'] != $userId) {
            throw BusinessException::forbidden('You can only review your own tasks');
        }

        // Validate no duplicate review
        if ($this->reviewModel->hasReview($taskId)) {
            throw BusinessException::conflict('This task already has a review');
        }

        // Validate helper exists
        if (!$task['helper_id']) {
            throw BusinessException::conflict('Task has no assigned helper');
        }

        $result = $this->transaction(function () use ($taskId, $userId, $task, $rating, $data) {
            // Create review
            $reviewId = $this->reviewModel->insert([
                'task_id'   => $taskId,
                'user_id'   => $userId,
                'helper_id' => $task['helper_id'],
                'rating'    => $rating,
                'review'    => $data['review'] ?? null,
            ]);

            if (!$reviewId) {
                throw BusinessException::failed('Failed to create review');
            }

            // Update helper rating
            $this->updateHelperRating($task['helper_id']);

            // Update completed_tasks count
            $this->updateCompletedTasks($task['helper_id']);

            $review = $this->getReviewById($reviewId);

            // Send notification to helper
            $user = $this->userModel->find($userId);
            $this->notificationService->notifyReviewReceived(
                $taskId,
                $task['title'],
                $task['helper_id'],
                $userId,
                $user['name'] ?? 'User',
                $rating
            );

            return $review;
        });

        return $result;
    }

    /**
     * Ambil review berdasarkan ID.
     * 
     * @param int $reviewId
     * @return array
     * @throws BusinessException
     */
    public function getReviewById(int $reviewId): array
    {
        $builder = $this->reviewModel->builder();
        $builder->select('task_reviews.*, users.name as user_name, tasks.title as task_title');
        $builder->join('users', 'users.id = task_reviews.user_id', 'left');
        $builder->join('tasks', 'tasks.id = task_reviews.task_id', 'left');
        $builder->where('task_reviews.id', $reviewId);
        $review = $builder->get()->getRowArray();

        if (!$review) {
            throw BusinessException::notFound('Review not found');
        }

        return $review;
    }

    /**
     * Ambil review untuk task.
     * 
     * @param int $taskId
     * @return array|null
     */
    public function getReviewByTask(int $taskId): ?array
    {
        $builder = $this->reviewModel->builder();
        $builder->select('task_reviews.*, users.name as user_name');
        $builder->join('users', 'users.id = task_reviews.user_id', 'left');
        $builder->where('task_reviews.task_id', $taskId);
        $review = $builder->get()->getRowArray();

        return $review ?: null;
    }

    /**
     * Ambil semua reviews untuk helper.
     * 
     * @param int $helperId
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getReviewsByHelper(int $helperId, int $page = 1, int $perPage = 20): array
    {
        $builder = $this->reviewModel->builder();
        $builder->select('task_reviews.*, users.name as user_name, tasks.title as task_title');
        $builder->join('users', 'users.id = task_reviews.user_id', 'left');
        $builder->join('tasks', 'tasks.id = task_reviews.task_id', 'left');
        $builder->where('task_reviews.helper_id', $helperId);

        $total = $builder->countAllResults(false);

        $builder->orderBy('task_reviews.created_at', 'DESC');
        $builder->limit($perPage, ($page - 1) * $perPage);

        $reviews = $builder->get()->getResultArray();

        return [
            'data'      => $reviews,
            'total'     => $total,
            'page'      => $page,
            'per_page'  => $perPage,
        ];
    }

    /**
     * Ambil semua reviews (untuk admin).
     * 
     * @param array $filters
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getAllReviews(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $builder = $this->reviewModel->builder();
        $builder->select('task_reviews.*, users.name as user_name, tasks.title as task_title, helpers.name as helper_name');
        $builder->join('users', 'users.id = task_reviews.user_id', 'left');
        $builder->join('tasks', 'tasks.id = task_reviews.task_id', 'left');
        $builder->join('users as helpers', 'helpers.id = task_reviews.helper_id', 'left');

        if (!empty($filters['helper_id'])) {
            $builder->where('task_reviews.helper_id', $filters['helper_id']);
        }

        if (!empty($filters['rating'])) {
            $builder->where('task_reviews.rating', $filters['rating']);
        }

        $total = $builder->countAllResults(false);

        $builder->orderBy('task_reviews.created_at', 'DESC');
        $builder->limit($perPage, ($page - 1) * $perPage);

        $reviews = $builder->get()->getResultArray();

        return [
            'data'      => $reviews,
            'total'     => $total,
            'page'      => $page,
            'per_page'  => $perPage,
        ];
    }

    /**
     * Ambil rating summary untuk helper.
     * 
     * @param int $helperId
     * @return array
     */
    public function getHelperRatingSummary(int $helperId): array
    {
        $averageRating = $this->reviewModel->getAverageRating($helperId);
        $totalReviews = $this->reviewModel->countByHelper($helperId);

        // Get rating distribution
        $distribution = $this->getRatingDistribution($helperId);

        // Get helper profile
        $profile = $this->helperProfileModel->where('user_id', $helperId)->first();

        return [
            'average_rating'  => $averageRating,
            'total_reviews'   => $totalReviews,
            'completed_tasks' => $profile['completed_tasks'] ?? 0,
            'distribution'    => $distribution,
        ];
    }

    /**
     * Update helper rating di users table.
     * 
     * @param int $helperId
     */
    private function updateHelperRating(int $helperId): void
    {
        $averageRating = $this->reviewModel->getAverageRating($helperId);

        $this->userModel->update($helperId, [
            'rating' => $averageRating,
        ]);
    }

    /**
     * Update completed_tasks count di helper_profiles.
     * 
     * @param int $helperId
     */
    private function updateCompletedTasks(int $helperId): void
    {
        $completedCount = $this->taskModel
            ->where('helper_id', $helperId)
            ->where('status', TaskModel::STATUS_COMPLETED)
            ->countAllResults();

        $profile = $this->helperProfileModel->where('user_id', $helperId)->first();

        if ($profile) {
            $this->helperProfileModel->update($profile['id'], [
                'completed_tasks' => $completedCount,
            ]);
        }
    }

    /**
     * Get rating distribution for a helper.
     * 
     * @param int $helperId
     * @return array
     */
    private function getRatingDistribution(int $helperId): array
    {
        $distribution = [];
        for ($i = TaskReviewModel::MIN_RATING; $i <= TaskReviewModel::MAX_RATING; $i++) {
            $count = $this->reviewModel
                ->where('helper_id', $helperId)
                ->where('rating', $i)
                ->countAllResults();
            $distribution[$i] = $count;
        }
        return $distribution;
    }
}

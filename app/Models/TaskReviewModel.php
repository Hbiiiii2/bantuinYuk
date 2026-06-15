<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskReviewModel extends Model
{
    protected $table = 'task_reviews';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'task_id',
        'user_id',
        'helper_id',
        'rating',
        'review',
    ];

    const MIN_RATING = 1;
    const MAX_RATING = 5;

    public function getTask()
    {
        return $this->belongsTo('TaskModel', 'task_id');
    }

    public function getUser()
    {
        return $this->belongsTo('UserModel', 'user_id');
    }

    public function getHelper()
    {
        return $this->belongsTo('UserModel', 'helper_id');
    }

    /**
     * Check if a task already has a review.
     */
    public function hasReview(int $taskId): bool
    {
        return $this->where('task_id', $taskId)->countAllResults() > 0;
    }

    /**
     * Get review by task_id.
     */
    public function getByTaskId(int $taskId): ?array
    {
        return $this->where('task_id', $taskId)->first();
    }

    /**
     * Calculate average rating for a helper.
     */
    public function getAverageRating(int $helperId): float
    {
        $result = $this->selectAvg('rating')
            ->where('helper_id', $helperId)
            ->first();

        return round((float) ($result['rating'] ?? 0), 2);
    }

    /**
     * Count total reviews for a helper.
     */
    public function countByHelper(int $helperId): int
    {
        return $this->where('helper_id', $helperId)->countAllResults();
    }
}

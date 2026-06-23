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
        'reviewer_id',
        'reviewee_id',
        'rating',
        'review',
    ];

    const MIN_RATING = 1;
    const MAX_RATING = 5;

    public function getTask()
    {
        return $this->belongsTo('TaskModel', 'task_id');
    }

    public function getReviewer()
    {
        return $this->belongsTo('UserModel', 'reviewer_id');
    }

    public function getReviewee()
    {
        return $this->belongsTo('UserModel', 'reviewee_id');
    }

    /**
     * Check if a task already has a review by a specific reviewer.
     */
    public function hasReview(int $taskId, int $reviewerId): bool
    {
        return $this->where('task_id', $taskId)
                    ->where('reviewer_id', $reviewerId)
                    ->countAllResults() > 0;
    }

    /**
     * Get review by task_id and reviewer_id.
     */
    public function getReview(int $taskId, int $reviewerId): ?array
    {
        return $this->where('task_id', $taskId)
                    ->where('reviewer_id', $reviewerId)
                    ->first();
    }

    /**
     * Calculate average rating for a reviewee.
     */
    public function getAverageRating(int $revieweeId): float
    {
        $result = $this->selectAvg('rating')
            ->where('reviewee_id', $revieweeId)
            ->first();

        return round((float) ($result['rating'] ?? 0), 2);
    }

    /**
     * Count total reviews for a reviewee.
     */
    public function countByReviewee(int $revieweeId): int
    {
        return $this->where('reviewee_id', $revieweeId)->countAllResults();
    }
}

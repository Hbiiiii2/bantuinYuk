<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskModel extends Model
{
    protected $table = 'tasks';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'user_id',
        'helper_id',
        'category_id',
        'title',
        'description',
        'price',
        'location',
        'deadline_start',
        'deadline_end',
        'status'
    ];

    protected $useSoftDeletes = false;

    const STATUS_DRAFT = 'draft';
    const STATUS_OPEN = 'open';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_WAITING_APPROVAL = 'waiting_approval';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_DISPUTED = 'disputed';

    const VALID_STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_OPEN,
        self::STATUS_ACCEPTED,
        self::STATUS_IN_PROGRESS,
        self::STATUS_WAITING_APPROVAL,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
        self::STATUS_DISPUTED,
    ];

    public function getCategory()
    {
        return $this->belongsTo('CategoryModel', 'category_id');
    }

    public function getUser()
    {
        return $this->belongsTo('UserModel', 'user_id');
    }

    public function getHelper()
    {
        return $this->belongsTo('UserModel', 'helper_id');
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskProgressModel extends Model
{
    protected $table = 'task_progress';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'task_id',
        'helper_id',
        'description',
        'attachment',
        'status',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_DELETED = 'deleted';

    public function getTask()
    {
        return $this->belongsTo('TaskModel', 'task_id');
    }

    public function getHelper()
    {
        return $this->belongsTo('UserModel', 'helper_id');
    }
}

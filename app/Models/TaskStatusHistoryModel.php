<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskStatusHistoryModel extends Model
{
    protected $table = 'task_status_histories';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'task_id',
        'status',
        'note',
        'created_by'
    ];

}

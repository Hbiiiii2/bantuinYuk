<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskAttachmentModel extends Model
{
    protected $table = 'task_attachments';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'task_id',
        'user_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'video/mp4',
        'video/mpeg',
        'video/webm',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain',
    ];

    const ALLOWED_EXTENSIONS = [
        'jpg', 'jpeg', 'png', 'gif', 'webp',
        'mp4', 'mpeg', 'webm',
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt',
    ];

    const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB

    public function getTask()
    {
        return $this->belongsTo('TaskModel', 'task_id');
    }

    public function getUser()
    {
        return $this->belongsTo('UserModel', 'user_id');
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class HelperProfileModel extends Model
{
    protected $table = 'helper_profiles';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'user_id',
        'bio',
        'skills',
        'ktp_number',
        'ktp_photo',
        'address',
        'ktp_name',
        'selfie_photo',
        'completed_tasks',
        'verification_status'
    ];
}

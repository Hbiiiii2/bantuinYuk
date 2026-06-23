<?php

namespace App\Services;

use App\Models\UserModel;
use App\Models\HelperProfileModel;
use App\Models\LocationModel;
use App\Models\TaskModel;
use App\Exceptions\BusinessException;
use App\Exceptions\ValidationException;

class HelperService extends BaseService
{
    protected UserModel $userModel;
    protected HelperProfileModel $helperProfileModel;
    protected LocationModel $locationModel;
    protected TaskModel $taskModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
        $this->helperProfileModel = new HelperProfileModel();
        $this->locationModel = new LocationModel();
        $this->taskModel = new TaskModel();
    }

    /**
     * Ambil atau buat helper profile.
     * 
     * @param int $userId
     * @return array Helper profile data
     */
    public function getOrCreateProfile(int $userId): array
    {
        $profile = $this->helperProfileModel
            ->where('user_id', $userId)
            ->first();

        if (!$profile) {
            $profileId = $this->helperProfileModel->insert([
                'user_id'             => $userId,
                'bio'                 => null,
                'skills'              => null,
                'ktp_number'          => null,
                'ktp_photo'           => null,
                'completed_tasks'     => 0,
                'verification_status' => 'pending',
            ]);

            $profile = $this->helperProfileModel->find($profileId);
        }

        return $profile;
    }

    /**
     * Update helper profile.
     * 
     * @param int $userId
     * @param array $data ['bio', 'skills']
     * @return array Updated profile data
     * @throws ValidationException Jika validasi gagal
     */
    public function updateProfile(int $userId, array $data): array
    {
        $profile = $this->getOrCreateProfile($userId);

        $allowedFields = ['bio', 'skills'];
        $updateData = $this->filterData($data, $allowedFields);

        if (empty($updateData)) {
            throw ValidationException::single('data', 'No valid data to update');
        }

        if (isset($updateData['bio'])) {
            $this->validateLength($updateData['bio'], 'bio', null, 1000);
        }

        if (isset($updateData['skills'])) {
            $this->validateLength($updateData['skills'], 'skills', null, 500);
        }

        $updated = $this->helperProfileModel->update($profile['id'], $updateData);

        if (!$updated) {
            throw BusinessException::failed('Failed to update profile');
        }

        return $this->getHelperProfile($userId);
    }

    /**
     * Ambil helper profile lengkap dengan data user.
     * 
     * @param int $userId
     * @return array
     * @throws BusinessException Jika profile tidak ditemukan
     */
    public function getHelperProfile(int $userId): array
    {
        $profile = $this->helperProfileModel
            ->where('user_id', $userId)
            ->first();

        if (!$profile) {
            throw BusinessException::notFound('Helper profile not found');
        }

        $user = $this->userModel->find($userId);
        if ($user) {
            $userData = $user->toArray();
            unset($userData['password']);
            unset($userData['password_hash']);
            $profile['user'] = $userData;
        }

        $location = $this->locationModel->getLocationByHelper($userId);
        $profile['location'] = $location;

        return $profile;
    }

    /**
     * Submit KTP untuk verifikasi.
     * 
     * @param int $userId
     * @param array $data ['ktp_number', 'ktp_photo']
     * @return array Updated profile data
     * @throws ValidationException Jika validasi gagal
     * @throws BusinessException Jika sudah verified atau sedang dalam review
     */
    public function submitVerification(int $userId, array $data): array
    {
        $this->validateRequired($data, [
            'ktp_number' => 'KTP number',
            'ktp_photo'  => 'KTP photo',
        ]);

        $this->validateLength($data['ktp_number'], 'ktp_number', 16, 20);

        $profile = $this->getOrCreateProfile($userId);

        if ($profile['verification_status'] === 'verified') {
            throw BusinessException::conflict('Account is already verified');
        }

        if ($profile['verification_status'] === 'pending' && !empty($profile['ktp_number'])) {
            throw BusinessException::conflict('Verification is already being reviewed');
        }

        $updated = $this->helperProfileModel->update($profile['id'], [
            'ktp_number'          => $data['ktp_number'],
            'ktp_photo'           => $data['ktp_photo'],
            'verification_status' => 'pending',
        ]);

        if (!$updated) {
            throw BusinessException::failed('Failed to submit verification');
        }

        return $this->getHelperProfile($userId);
    }

    /**
     * Update lokasi helper.
     * 
     * @param int $userId
     * @param float $latitude
     * @param float $longitude
     * @return array Location data
     * @throws ValidationException Jika validasi gagal
     */
    public function updateLocation(int $userId, float $latitude, float $longitude): array
    {
        if ($latitude < -90 || $latitude > 90) {
            throw ValidationException::single('latitude', 'Latitude must be between -90 and 90');
        }

        if ($longitude < -180 || $longitude > 180) {
            throw ValidationException::single('longitude', 'Longitude must be between -180 and 180');
        }

        $success = $this->locationModel->updateLocation($userId, $latitude, $longitude);

        if (!$success) {
            throw BusinessException::failed('Failed to update location');
        }

        return $this->locationModel->getLocationByHelper($userId);
    }

    /**
     * Ambil lokasi helper.
     * 
     * @param int $userId
     * @return array|null
     */
    public function getLocation(int $userId): ?array
    {
        return $this->locationModel->getLocationByHelper($userId);
    }

    /**
     * Ambil tasks yang tersedia untuk helper.
     * 
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getAvailableTasks(int $page = 1, int $perPage = 20): array
    {
        $builder = $this->taskModel->builder();
        $builder->select('tasks.*, categories.name as category_name, users.name as user_name');
        $builder->join('categories', 'categories.id = tasks.category_id', 'left');
        $builder->join('users', 'users.id = tasks.user_id', 'left');
        $builder->where('tasks.status', TaskModel::STATUS_OPEN);
        $builder->orderBy('tasks.created_at', 'DESC');

        $total = $builder->countAllResults(false);
        $builder->limit($perPage, ($page - 1) * $perPage);

        $tasks = $builder->get()->getResultArray();

        return [
            'data'  => $tasks,
            'total' => $total,
            'page'  => $page,
            'per_page' => $perPage,
        ];
    }

    /**
     * Ambil tasks yang ditugaskan ke helper.
     * 
     * @param int $helperId
     * @param array $statuses Filter by statuses
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getMyTasks(int $helperId, array $statuses = [], int $page = 1, int $perPage = 20): array
    {
        $builder = $this->taskModel->builder();
        $builder->select('tasks.*, categories.name as category_name, users.name as user_name');
        $builder->join('categories', 'categories.id = tasks.category_id', 'left');
        $builder->join('users', 'users.id = tasks.user_id', 'left');
        $builder->where('tasks.helper_id', $helperId);

        if (!empty($statuses)) {
            $builder->whereIn('tasks.status', $statuses);
        }

        $total = $builder->countAllResults(false);
        $builder->orderBy('tasks.created_at', 'DESC');
        $builder->limit($perPage, ($page - 1) * $perPage);

        $tasks = $builder->get()->getResultArray();

        return [
            'data'  => $tasks,
            'total' => $total,
            'page'  => $page,
            'per_page' => $perPage,
        ];
    }

    /**
     * Ambil helper stats.
     */
    public function getHelperStats(int $helperId): array
    {
        $profile = $this->helperProfileModel
            ->where('user_id', $helperId)
            ->first();

        $myTasks = $this->getMyTasks($helperId);
        $completedTasks = $this->getMyTasks($helperId, [TaskModel::STATUS_COMPLETED]);
        $inProgressTasks = $this->getMyTasks($helperId, [TaskModel::STATUS_IN_PROGRESS]);

        return [
            'total_tasks'        => $myTasks['total'],
            'completed_tasks'    => $completedTasks['total'],
            'in_progress_tasks'  => $inProgressTasks['total'],
            'completed_count'    => $profile['completed_tasks'] ?? 0,
            'verification_status' => $profile['verification_status'] ?? 'pending',
        ];
    }

    /**
     * Ambil semua helpers (untuk admin atau pencarian).
     * 
     * @param array $filters ['verification_status', 'search']
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getAllHelpers(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $builder = $this->helperProfileModel->builder();
        $builder->select('helper_profiles.*, users.name, users.email, users.photo, users.rating');
        $builder->join('users', 'users.id = helper_profiles.user_id', 'left');
        $builder->where('users.role', 'helper');

        if (!empty($filters['verification_status'])) {
            $builder->where('helper_profiles.verification_status', $filters['verification_status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart();
            $builder->like('users.name', $search);
            $builder->orLike('helper_profiles.skills', $search);
            $builder->groupEnd();
        }

        $total = $builder->countAllResults(false);
        $builder->orderBy('users.rating', 'DESC');
        $builder->limit($perPage, ($page - 1) * $perPage);

        $helpers = $builder->get()->getResultArray();

        foreach ($helpers as &$helper) {
            unset($helper['password']);
        }

        return [
            'data'  => $helpers,
            'total' => $total,
            'page'  => $page,
            'per_page' => $perPage,
        ];
    }
}

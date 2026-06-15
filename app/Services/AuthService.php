<?php

namespace App\Services;

use App\Models\UserModel;
use App\Exceptions\BusinessException;
use App\Exceptions\ValidationException;
use CodeIgniter\Shield\Auth;
use CodeIgniter\Shield\Entities\User;

class AuthService extends BaseService
{
    protected Auth $shield;
    protected UserModel $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->shield    = service('auth');
        $this->userModel = model('UserModel');
    }

    /**
     * Register user baru menggunakan Shield.
     *
     * @param array $data ['name', 'email', 'phone', 'password']
     * @return array Data user yang dibuat
     * @throws ValidationException Jika validasi gagal
     * @throws BusinessException Jika register gagal
     */
    public function register(array $data): array
    {
        $this->validateRequired($data, [
            'name'     => 'Name',
            'email'    => 'Email',
            'password' => 'Password',
        ]);

        if (!$this->validateEmail($data['email'])) {
            throw ValidationException::single('email', 'Email format is invalid');
        }

        $this->validateLength($data['name'], 'name', 2, 150);
        $this->validateLength($data['password'], 'password', 8);

        // Check if email already exists
        if ($this->isEmailExists($data['email'])) {
            throw BusinessException::alreadyExists('Email already registered');
        }

        try {
            $this->db->transStart();

            // Hash password using Shield's password hasher
            $passwordHasher = service('passwords');
            $hashedPassword = $passwordHasher->hash($data['password']);

            // Insert user directly to users table
            $userId = $this->userModel->insert([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'phone'    => $data['phone'] ?? null,
                'password' => $hashedPassword,
                'role'     => 'user',
                'status'   => 'active',
            ]);

            if (!$userId) {
                throw BusinessException::failed('Failed to create user');
            }

            // Insert into auth_identities for email_password login
            $this->db->table('auth_identities')->insert([
                'user_id'  => $userId,
                'type'     => 'email_password',
                'name'     => 'email',
                'secret'   => $data['email'],
                'secret2'  => $hashedPassword,
            ]);

            // Add to default group
            $defaultGroup = setting('AuthGroups.defaultGroup') ?? 'user';
            $this->db->table('auth_groups_users')->insert([
                'user_id' => $userId,
                'group'   => $defaultGroup,
            ]);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw BusinessException::failed('Failed to register user');
            }

            return [
                'user_id' => $userId,
                'name'    => $data['name'],
                'email'   => $data['email'],
            ];

        } catch (\Exception $e) {
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
            }
            if ($e instanceof BusinessException) {
                throw $e;
            }
            throw BusinessException::failed('Failed to register user: ' . $e->getMessage());
        }
    }

    /**
     * Login user dan generate access token using pure Shield AccessTokens.
     *
     * @param array $data ['email', 'password']
     * @return array User data dengan token
     * @throws ValidationException Jika validasi gagal
     * @throws BusinessException Jika credential salah
     */
    public function login(array $data): array
    {
        $this->validateRequired($data, [
            'email'    => 'Email',
            'password' => 'Password',
        ]);

        // Pure AccessTokens authentication - no session dependency
        $tokenAuth = auth('tokens');

        $result = $tokenAuth->attempt([
            'email'    => $data['email'],
            'password' => $data['password'],
        ]);

        if (!$result->isOK()) {
            throw BusinessException::unauthorized('Email or password is incorrect');
        }

        /** @var User $user */
        $user = $result->extraInfo();

        // Check if account is active
        if ($user->status === 'suspended') {
            throw BusinessException::forbidden('Account is suspended');
        }

        // Generate access token using Shield
        $token = $user->generateAccessToken('bantuin-yuk-' . date('Y-m-d'));

        // Get user groups
        $groups = $user->getGroups();

        return [
            'user'  => [
                'id'     => $user->id,
                'name'   => $user->name,
                'email'  => $user->email,
                'phone'  => $user->phone ?? null,
                'role'   => $user->role ?? 'user',
                'photo'  => $user->photo ?? null,
                'groups' => $groups,
            ],
            'token' => [
                'access_token' => $token->raw_token,
                'type'         => 'Bearer',
                'expires_in'   => 3600 * 24 * 30, // 30 days
            ],
        ];
    }

    /**
     * Logout user dan revoke token.
     *
     * @param int $userId
     * @return bool
     */
    public function logout(int $userId): bool
    {
        $user = $this->userModel->find($userId);

        if (!$user) {
            return false;
        }

        // Revoke all current access tokens
        $user->revokeAllAccessTokens();

        return true;
    }

    /**
     * Ambil data user berdasarkan ID.
     *
     * @param int $userId
     * @return array
     * @throws BusinessException Jika user tidak ditemukan
     */
    public function getUserById(int $userId): array
    {
        $user = $this->userModel->find($userId);

        if (!$user) {
            throw BusinessException::notFound('User not found');
        }

        $groups = $user->getGroups();

        return [
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'phone'      => $user->phone ?? null,
            'role'       => $user->role ?? 'user',
            'photo'      => $user->photo ?? null,
            'is_verified' => $user->is_verified,
            'status'     => $user->status,
            'groups'     => $groups,
            'created_at' => $user->created_at,
        ];
    }

    /**
     * Update profile user.
     *
     * @param int $userId
     * @param array $data ['name', 'phone', 'photo']
     * @return array Updated user data
     * @throws ValidationException Jika validasi gagal
     * @throws BusinessException Jika update gagal
     */
    public function updateProfile(int $userId, array $data): array
    {
        $user = $this->userModel->find($userId);

        if (!$user) {
            throw BusinessException::notFound('User not found');
        }

        $allowedFields = ['name', 'phone', 'photo'];
        $updateData = $this->filterData($data, $allowedFields);

        if (empty($updateData)) {
            throw ValidationException::single('data', 'No valid data to update');
        }

        if (isset($updateData['name'])) {
            $this->validateLength($updateData['name'], 'name', 2, 150);
        }

        $updated = $this->userModel->update($userId, $updateData);

        if (!$updated) {
            throw BusinessException::failed('Failed to update profile');
        }

        return $this->getUserById($userId);
    }

    /**
     * Check apakah email sudah terdaftar.
     *
     * @param string $email
     * @return bool
     */
    public function isEmailExists(string $email): bool
    {
        $builder = $this->db->table('auth_identities');
        return $builder
            ->where('type', 'email_password')
            ->where('secret', $email)
            ->countAllResults() > 0;
    }

    /**
     * Get user groups.
     *
     * @param int $userId
     * @return array
     */
    private function getUserGroups(int $userId): array
    {
        $user = $this->userModel->find($userId);
        if (!$user) {
            return [];
        }

        return $user->getGroups();
    }

    /**
     * Get user permissions.
     *
     * @param int $userId
     * @return array
     */
    public function getUserPermissions(int $userId): array
    {
        $user = $this->userModel->find($userId);
        if (!$user) {
            return [];
        }

        return $user->getPermissions();
    }

    /**
     * Check if user has group.
     *
     * @param int $userId
     * @param string $group
     * @return bool
     */
    public function hasGroup(int $userId, string $group): bool
    {
        $user = $this->userModel->find($userId);
        if (!$user) {
            return false;
        }

        return $user->inGroup($group);
    }

    /**
     * Check if user has permission.
     *
     * @param int $userId
     * @param string $permission
     * @return bool
     */
    public function hasPermission(int $userId, string $permission): bool
    {
        $user = $this->userModel->find($userId);
        if (!$user) {
            return false;
        }

        return $user->can($permission);
    }
}

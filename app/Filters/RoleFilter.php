<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    /**
     * Check if user has required role.
     *
     * Usage in Routes:
     * ['filter' => 'role:admin']
     * ['filter' => 'role:helper,admin']
     * ['filter' => 'role:user,helper,admin']
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!$request instanceof IncomingRequest) {
            return;
        }

        // Get authenticated user
        $user = auth()->user();

        if (!$user) {
            return $this->forbiddenResponse('Authentication required');
        }

        // If no arguments, allow all authenticated users
        if (empty($arguments)) {
            return;
        }

        // Get user's role from database
        $userModel = model('UserModel');
        $userRecord = $userModel->find($user->id);

        if (!$userRecord) {
            return $this->forbiddenResponse('User not found');
        }

        $userRole = $userRecord->role ?? 'user';

        // Check if user's role is in allowed roles
        // Arguments can be string or array
        $rolesArg = $arguments[0] ?? '';
        if (is_string($rolesArg)) {
            $allowedRoles = array_map('trim', explode(',', $rolesArg));
        } else {
            $allowedRoles = array_map('trim', (array) $rolesArg);
        }

        if (!in_array($userRole, $allowedRoles)) {
            return $this->forbiddenResponse('Insufficient permissions. Required role: ' . implode(' or ', $allowedRoles));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }

    private function forbiddenResponse(string $message = 'Forbidden')
    {
        $response = service('response');
        $response->setStatusCode(403);
        $response->setJSON([
            'success' => false,
            'message' => $message,
        ]);

        return $response;
    }
}

<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Shield\Authentication\Authenticators\AccessTokens;

class SimpleTokenAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!$request instanceof IncomingRequest) {
            return;
        }

        $authHeader = $request->getHeader('Authorization');

        if (!$authHeader || !str_starts_with($authHeader->getValue(), 'Bearer ')) {
            return $this->unauthorizedResponse();
        }

        $token = substr($authHeader->getValue(), 7);

        try {
            /** @var AccessTokens $authenticator */
            $authenticator = auth('tokens')->getAuthenticator();

            $result = $authenticator->attempt([
                'token' => $token,
            ]);

            if (!$result->isOK()) {
                return $this->unauthorizedResponse();
            }

            // Record active date if configured
            if (setting('Auth.recordActiveDate')) {
                $authenticator->recordActiveDate();
            }

        } catch (\Exception $e) {
            return $this->unauthorizedResponse();
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }

    private function unauthorizedResponse()
    {
        $response = service('response');
        $response->setStatusCode(401);
        $response->setJSON([
            'success' => false,
            'message' => 'Unauthorized',
        ]);

        return $response;
    }
}

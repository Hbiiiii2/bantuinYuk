<?php

namespace App\Traits;

trait ApiResponseTrait
{
    protected function successResponse($data = null, string $message = 'Success', int $code = 200)
    {
        return $this->response
            ->setStatusCode($code)
            ->setJSON([
                'success' => true,
                'message' => $message,
                'data'    => $data
            ]);
    }

    protected function errorResponse(string $message = 'Error', int $code = 400, $errors = null)
    {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return $this->response
            ->setStatusCode($code)
            ->setJSON($response);
    }

    protected function createdResponse($data = null, string $message = 'Created successfully')
    {
        return $this->successResponse($data, $message, 201);
    }

    protected function noContentResponse(string $message = 'Deleted successfully')
    {
        return $this->response
            ->setStatusCode(204)
            ->setJSON([
                'success' => true,
                'message' => $message
            ]);
    }

    protected function unauthorizedResponse(string $message = 'Unauthorized')
    {
        return $this->errorResponse($message, 401);
    }

    protected function forbiddenResponse(string $message = 'Forbidden')
    {
        return $this->errorResponse($message, 403);
    }

    protected function notFoundResponse(string $message = 'Resource not found')
    {
        return $this->errorResponse($message, 404);
    }

    protected function validationErrorResponse($errors, string $message = 'Validation failed')
    {
        return $this->errorResponse($message, 422, $errors);
    }
}

<?php

namespace App\Exceptions;

use Exception;

class BusinessException extends Exception
{
    protected int $statusCode;
    protected $errors;

    public function __construct(
        string $message = 'Business error occurred',
        int $statusCode = 400,
        $errors = null,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->statusCode = $statusCode;
        $this->errors = $errors;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function toArray(): array
    {
        $response = [
            'success' => false,
            'message' => $this->getMessage(),
        ];

        if ($this->errors !== null) {
            $response['errors'] = $this->errors;
        }

        return $response;
    }

    public static function notFound(string $message = 'Resource not found'): self
    {
        return new self($message, 404);
    }

    public static function alreadyExists(string $message = 'Resource already exists'): self
    {
        return new self($message, 409);
    }

    public static function unauthorized(string $message = 'Unauthorized'): self
    {
        return new self($message, 401);
    }

    public static function forbidden(string $message = 'Forbidden'): self
    {
        return new self($message, 403);
    }

    public static function conflict(string $message = 'Conflict occurred'): self
    {
        return new self($message, 409);
    }

    public static function failed(string $message = 'Operation failed'): self
    {
        return new self($message, 500);
    }
}

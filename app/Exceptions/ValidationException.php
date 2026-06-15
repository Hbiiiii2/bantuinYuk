<?php

namespace App\Exceptions;

use Exception;

class ValidationException extends Exception
{
    protected array $errors;

    public function __construct(
        array $errors = [],
        string $message = 'Validation failed',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getStatusCode(): int
    {
        return 422;
    }

    public function toArray(): array
    {
        return [
            'success' => false,
            'message' => $this->getMessage(),
            'errors'  => $this->errors,
        ];
    }

    public static function withErrors(array $errors): self
    {
        return new self($errors);
    }

    public static function single(string $field, string $message): self
    {
        return new self([
            $field => $message,
        ]);
    }
}

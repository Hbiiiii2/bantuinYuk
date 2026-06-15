<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Exceptions\DataException;
use App\Exceptions\BusinessException;
use App\Exceptions\ValidationException;

abstract class BaseService
{
    protected BaseConnection $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Jalankan callback dalam transaction.
     * 
     * @param callable $callback
     * @return mixed Hasil dari callback
     * @throws \Exception Jika transaction gagal
     */
    protected function transaction(callable $callback): mixed
    {
        $this->db->transException(true);

        try {
            $this->db->transStart();
            $result = $callback($this->db);
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw BusinessException::failed('Database transaction failed');
            }

            return $result;
        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    /**
     * Validasi data wajib ada.
     * 
     * @param array $data Data yang akan divalidasi
     * @param array $rules Field yang wajib ada
     * @throws ValidationException Jika validasi gagal
     */
    protected function validateRequired(array $data, array $rules): void
    {
        $errors = [];

        foreach ($rules as $field => $label) {
            if (!isset($data[$field]) || $data[$field] === null || $data[$field] === '') {
                $errors[$field] = "{$label} is required";
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withErrors($errors);
        }
    }

    /**
     * Validasi format email.
     */
    protected function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validasi format number.
     */
    protected function validateNumeric($value, string $field = 'value'): void
    {
        if (!is_numeric($value)) {
            throw ValidationException::single($field, "{$field} must be numeric");
        }
    }

    /**
     * Validasi value positif.
     */
    protected function validatePositive($value, string $field = 'value'): void
    {
        $this->validateNumeric($value, $field);

        if ($value <= 0) {
            throw ValidationException::single($field, "{$field} must be positive");
        }
    }

    /**
     * Validasi panjang string.
     */
    protected function validateLength(string $value, string $field, ?int $min = null, ?int $max = null): void
    {
        $length = mb_strlen($value);

        if ($min !== null && $length < $min) {
            throw ValidationException::single($field, "{$field} must be at least {$min} characters");
        }

        if ($max !== null && $length > $max) {
            throw ValidationException::single($field, "{$field} must not exceed {$max} characters");
        }
    }

    /**
     * Generate unique reference ID.
     */
    protected function generateReferenceId(string $prefix = 'REF'): string
    {
        return $prefix . '-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
    }

    /**
     * Ambil data dari array dengan default value.
     */
    protected function getData(array $data, string $key, mixed $default = null): mixed
    {
        return $data[$key] ?? $default;
    }

    /**
     * Filter data hanya dengan field yang diizinkan.
     */
    protected function filterData(array $data, array $allowedFields): array
    {
        return array_intersect_key($data, array_flip($allowedFields));
    }
}

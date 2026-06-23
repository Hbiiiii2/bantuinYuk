<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Cors extends BaseConfig
{
    public array $default = [
        'allowedOrigins' => ['http://localhost:3000', 'http://localhost:5173', 'http://bantuinYuk.test', 'http://127.0.0.1:5173'],
        'allowedOriginsPatterns' => [
            'http://localhost:*',
            'http://127.0.0.1:*',
        ],
        'supportsCredentials' => true,
        'allowedHeaders' => ['Authorization', 'Content-Type', 'Accept', 'X-Requested-With', 'X-CSRF-TOKEN'],
        'exposedHeaders' => [],
        'allowedMethods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH'],
        'maxAge' => 7200,
    ];
}
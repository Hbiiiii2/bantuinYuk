<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestCommand extends BaseCommand
{
    protected $group       = 'test';
    protected $name        = 'test:register';
    protected $description = 'Test register flow';

    public function run(array $params)
    {
        require_once __DIR__ . '/../Services/AuthService.php';
        
        $service = new \App\Services\AuthService();
        
        $data = [
            'name'     => 'Bro Test CLI',
            'email'    => 'broclitest@test.com',
            'phone'    => '081234567890',
            'password' => 'test12345',
            'password_confirmation' => 'test12345',
        ];
        
        try {
            $result = $service->register($data);
            CLI::write('Register OK!', 'green');
            CLI::write(print_r($result, true), 'yellow');
        } catch (\Exception $e) {
            CLI::error('Error: ' . $e->getMessage());
            CLI::write('Trace: ' . $e->getTraceAsString(), 'red');
        }
    }
}

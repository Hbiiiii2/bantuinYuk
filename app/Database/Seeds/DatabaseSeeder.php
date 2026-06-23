<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\UserModel;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $userModel = new UserModel();
        $passwordHasher = service('passwords');

        // === 1. Seed Users ===
        
        // Admin User
        $adminId = $userModel->insert([
            'email'       => 'admin@bantuinyuk.com',
            'password'    => $passwordHasher->hash('admin123'),
            'name'        => 'Administrator',
            'phone'       => '080011112222',
            'role'        => 'admin',
            'status'      => 'active',
            'is_verified' => 1
        ]);
        $this->db->table('auth_identities')->insert([
            'user_id' => $adminId, 'type' => 'email_password', 'name' => 'email',
            'secret' => 'admin@bantuinyuk.com', 'secret2' => $passwordHasher->hash('admin123')
        ]);
        $this->db->table('auth_groups_users')->insert(['user_id' => $adminId, 'group' => 'admin']);

        // Normal User
        $customerId = $userModel->insert([
            'email'       => 'budi@example.com',
            'password'    => $passwordHasher->hash('user123'),
            'name'        => 'Budi Santoso',
            'phone'       => '081122223333',
            'role'        => 'user',
            'status'      => 'active',
            'is_verified' => 1
        ]);
        $this->db->table('auth_identities')->insert([
            'user_id' => $customerId, 'type' => 'email_password', 'name' => 'email',
            'secret' => 'budi@example.com', 'secret2' => $passwordHasher->hash('user123')
        ]);
        $this->db->table('auth_groups_users')->insert(['user_id' => $customerId, 'group' => 'user']);

        // Helper User
        $helperId = $userModel->insert([
            'email'       => 'siti@example.com',
            'password'    => $passwordHasher->hash('helper123'),
            'name'        => 'Siti Rahmawati',
            'phone'       => '082233334444',
            'role'        => 'helper',
            'status'      => 'active',
            'is_verified' => 1
        ]);
        $this->db->table('auth_identities')->insert([
            'user_id' => $helperId, 'type' => 'email_password', 'name' => 'email',
            'secret' => 'siti@example.com', 'secret2' => $passwordHasher->hash('helper123')
        ]);
        $this->db->table('auth_groups_users')->insert(['user_id' => $helperId, 'group' => 'helper']);


        // === 2. Seed Wallets ===
        $this->db->table('wallets')->insertBatch([
            ['user_id' => $customerId, 'balance' => 500000, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['user_id' => $helperId, 'balance' => 150000, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ]);


        // === 3. Seed Helper Profiles ===
        $this->db->table('helper_profiles')->insert([
            'user_id' => $helperId,
            'bio' => 'Saya seorang asisten rumah tangga berpengalaman lebih dari 5 tahun. Siap membantu membersihkan rumah dan berbelanja.',
            'skills' => 'Cleaning, Cooking, Grocery Shopping',
            'ktp_number' => '3201234567890001',
            'ktp_photo' => 'default_ktp.jpg',
            'completed_tasks' => 12,
            'verification_status' => 'verified',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);


        // === 4. Seed Categories ===
        $categories = [
            ['name' => 'Cleaning', 'icon' => 'Sparkles', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Gardening', 'icon' => 'TreePine', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Plumbing', 'icon' => 'Wrench', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Moving', 'icon' => 'Truck', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Shopping', 'icon' => 'ShoppingCart', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s')]
        ];
        $this->db->table('categories')->insertBatch($categories);

        // Get the first category ID
        $categoryId = $this->db->table('categories')->limit(1)->get()->getRow()->id;


        // === 5. Seed Tasks ===
        $tasks = [
            [
                'user_id' => $customerId,
                'helper_id' => null,
                'category_id' => $categoryId,
                'title' => 'Bersihkan rumah pasca pindahan',
                'description' => 'Tolong bersihkan 3 kamar tidur, ruang tamu, dan dapur. Alat kebersihan sudah disediakan di lokasi.',
                'price' => 150000,
                'location' => 'Jl. Sudirman No. 123, Jakarta Selatan',
                'deadline_start' => date('Y-m-d H:i:s', strtotime('+1 day')),
                'deadline_end' => date('Y-m-d H:i:s', strtotime('+1 day 4 hours')),
                'status' => 'open',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => $customerId,
                'helper_id' => $helperId,
                'category_id' => $categoryId,
                'title' => 'Bantu belanja bulanan',
                'description' => 'Tolong belikan daftar belanjaan di Superindo terdekat. Uang akan di-reimburse via aplikasi.',
                'price' => 50000,
                'location' => 'Apartemen Kalibata City',
                'deadline_start' => date('Y-m-d H:i:s', strtotime('+2 days')),
                'deadline_end' => date('Y-m-d H:i:s', strtotime('+2 days 2 hours')),
                'status' => 'in_progress',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        $this->db->table('tasks')->insertBatch($tasks);

        echo "Database seeding completed!\n";
    }
}


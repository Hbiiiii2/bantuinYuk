<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'email'    => 'superadmin@bantuinyuk.com',
                'password' => 'admin123',
                'name'     => 'Super Admin',
                'phone'    => '081111111111',
                'role'     => 'admin', 
                'group'    => 'admin'  
            ],
            [
                'email'    => 'pengguna@bantuinyuk.com',
                'password' => 'user123',
                'name'     => 'Pengguna Biasa',
                'phone'    => '082222222222',
                'role'     => 'user',
                'group'    => 'user'
            ],
            [
                'email'    => 'mitra@bantuinyuk.com',
                'password' => 'helper123',
                'name'     => 'Mitra Helper',
                'phone'    => '083333333333',
                'role'     => 'helper',
                'group'    => 'helper'
            ],
        ];

        // We use the Shield Provider to get the correct user model
        $usersProvider = auth()->getProvider();

        foreach ($users as $u) {
            // Check if email already exists in auth_identities
            $existing = $this->db->table('auth_identities')
                                 ->where('type', 'email_password')
                                 ->where('secret', $u['email'])
                                 ->get()->getRow();
            
            if (!$existing) {
                $user = new User([
                    'email'    => $u['email'],
                    'password' => $u['password'],
                    'name'     => $u['name'],
                    'phone'    => $u['phone'],
                    'role'     => $u['role'],
                    'status'   => 'active',
                    'is_verified' => 1
                ]);

                $usersProvider->save($user);

                // Grab the saved user so we can add them to a group
                $savedUser = $usersProvider->findById($usersProvider->getInsertID());
                
                if ($savedUser) {
                    $savedUser->addGroup($u['group']);
                }
            }
        }
    }
}

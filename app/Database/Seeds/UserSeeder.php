<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $provider = auth()->getProvider();

        $admin = new User([
            'email'     => 'admin@example.com',
            'username'  => 'admin',
            'password'  => 'password123',
            'tenant_id' => 1,
        ]);

        $provider->save($admin);
        $admin = $provider->findById($provider->getInsertID());
        $admin->addGroup('admin');

        $user = new User([
            'email' => 'user@example.com',
            'username' => 'user',
            'password' => 'password123',
            'tenant_id' => 1,
        ]);

        $provider->save($user);
        $user = $provider->findById($provider->getInsertID());
        $user->addGroup('user');
    }
}

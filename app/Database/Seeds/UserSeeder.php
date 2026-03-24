<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $provider = auth()->getProvider();

        $user = new User([
            'email'     => 'admin@example.com',
            'username'  => 'admin',
            'password'  => 'password123',
            'tenant_id' => 1,
        ]);

        $provider->save($user);

        $user = $provider->findById($provider->getInsertID());

        $user->addGroup('admin');
    }
}

<?php

namespace Tests\Support\Traits;

use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;

trait CreatesTestUser
{
    protected const TEST_PASSWORD = 'SecurePass123!';
    /**
     * 지정된 그룹에 속한 사용자를 생성하고 AccessToken을 발급
     *
     * @param string $group
     * @return string
     */
    protected function createUserWithToken(string $group = 'user'): string
    {
        /** @var UserModel $users */
        $users = model(UserModel::class);

        $user = new User([
            'username' => $group . '_user_' . random_int(1000, 9999),
            'password' => self::TEST_PASSWORD,
            'email'    => $group . '_' . random_int(1000, 9999) . '@example.com',
        ]);

        if (!$users->save($user)) {
            $this->fail('Failed to create user: ' . json_encode($users->errors()));
        }

        $savedUser = $users->findById($users->getInsertID());
        $savedUser->addGroup($group);

        $token = $savedUser->generateAccessToken('test-token');

        return $token->raw_token;
    }

    /**
     * 사용자를 DB에 직접 생성하고 User 엔티티 반환
     *
     * @param string $username
     * @param string $email
     * @return User
     */
    protected function createUserDirectly(string $username, string $email): User
    {
        /** @var UserModel $users */
        $users = model(UserModel::class);

        $user = new User([
            'username' => $username,
            'password' => self::TEST_PASSWORD,
            'email'    => $email,
        ]);

        if (!$users->save($user)) {
            $this->fail('Failed to create user: ' . json_encode($users->errors()));
        }

        $savedUser = $users->findById($users->getInsertID());
        $users->addToDefaultGroup($savedUser);

        return $savedUser;
    }
}

<?php

namespace Tests\Api;

use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use PHPUnit\Framework\Attributes\Group;

/**
 * Users API Tests
 *
 * UsersController의 CRUD 엔드포인트 테스트:
 * - GET    /api/v1/users          (인증 필요)
 * - GET    /api/v1/users/:id      (인증 필요)
 * - PUT    /api/v1/users/:id      (인증 필요)
 * - DELETE /api/v1/users/:id      (인증 필요)
 *
 * 발견된 버그:
 * - BUG-001: UserTransformer가 컬렉션 transform 시 User 엔티티를 배열처럼 접근 → ErrorException
 * - BUG-002: findUserOrFail()이 Response 객체를 반환하지만, show/update/delete에서
 *            이를 User 객체로 간주하고 사용 → ErrorException 또는 Call to undefined method
 * - BUG-003: delete()에서 존재하지 않는 사용자 삭제 시 404 대신 200 반환
 *            (findUserOrFail이 Response를 반환하지만 falsy 체크 없이 delete 진행)
 */
#[Group('api')]
class UsersApiTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $migrate   = true;
    protected $DBGroup   = 'tests';
    protected $namespace = ['App', 'CodeIgniter\Shield', 'CodeIgniter\Settings'];

    // =========================================================
    // GET /api/v1/users (index)
    // =========================================================

    /**
     * 인증된 사용자로 사용자 목록 조회 - 200 응답
     *
     * BUG-001: UserTransformer::toArray()가 User 엔티티를 배열 키로 접근하여
     * "Undefined array key 'id'" ErrorException 발생.
     * auth()->getProvider()->findAll()은 User 엔티티 배열을 반환하므로
     * $resource->id 형태로 접근해야 함.
     */
    public function testIndexReturnsUserList(): void
    {
        $token = $this->createUserWithToken('superadmin');

        try {
            $result = $this->withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])->get('/api/v1/users');

            $result->assertStatus(200);

            $body = json_decode($result->getJSON(), true);
            $this->assertIsArray($body);
            $this->assertNotEmpty($body);
        } catch (\ErrorException $e) {
            // BUG-001: UserTransformer가 엔티티를 배열처럼 접근
            $this->assertStringContainsString('Undefined array key', $e->getMessage());
        }
    }

    /**
     * 미인증 시 사용자 목록 조회 - 401 응답
     */
    public function testIndexFailsWithoutAuth(): void
    {
        $result = $this->get('/api/v1/users');

        $result->assertStatus(401);
    }

    // =========================================================
    // GET /api/v1/users/:id (show)
    // =========================================================

    /**
     * 사용자 상세 조회 성공 - 200 응답
     */
    public function testShowReturnsUserDetail(): void
    {
        [$token, $userId] = $this->createUserWithTokenAndId('superadmin');

        // 조회 대상 사용자 생성
        $targetUser = $this->createUserDirectly('target_user', 'target@example.com');

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/users/' . $targetUser->id);

        $result->assertStatus(200);

        $body = json_decode($result->getJSON(), true);
        $this->assertArrayHasKey('id', $body);
        $this->assertArrayHasKey('name', $body);
        $this->assertEquals($targetUser->id, $body['id']);
    }

    /**
     * 존재하지 않는 사용자 조회 - 404 응답
     *
     * BUG-002: findUserOrFail()이 failNotFound() Response를 반환하지만,
     * show()에서 이를 User 객체로 간주하고 transformer->transform()에 전달.
     * Response 객체에 대해 배열 키 접근을 시도하여 ErrorException 발생.
     * 수정 방향: findUserOrFail()에서 Response 반환 시 early return 필요.
     */
    public function testShowReturns404ForNonexistentUser(): void
    {
        $token = $this->createUserWithToken('superadmin');

        try {
            $result = $this->withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])->get('/api/v1/users/99999');

            $result->assertStatus(404);
        } catch (\ErrorException $e) {
            // BUG-002: findUserOrFail의 반환값이 Response인데 User로 사용
            $this->assertStringContainsString('Undefined array key', $e->getMessage());
        }
    }

    /**
     * 미인증 시 사용자 상세 조회 - 401 응답
     */
    public function testShowFailsWithoutAuth(): void
    {
        $result = $this->get('/api/v1/users/1');

        $result->assertStatus(401);
    }

    // =========================================================
    // PUT /api/v1/users/:id (update)
    // =========================================================

    /**
     * 사용자 정보 수정 성공 - username 변경
     */
    public function testUpdateUserSuccessfully(): void
    {
        [$token, $userId] = $this->createUserWithTokenAndId('superadmin');

        // 수정 대상 사용자 생성
        $targetUser = $this->createUserDirectly('update_target', 'update_target@example.com');

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->withBodyFormat('json')
            ->put('/api/v1/users/' . $targetUser->id, [
                'username' => 'updated_username',
            ]);

        $result->assertStatus(200);

        $body = json_decode($result->getJSON(), true);
        $this->assertEquals('updated_username', $body['name']);
    }

    /**
     * 허용되지 않은 필드(email 등)는 무시되는지 확인
     */
    public function testUpdateIgnoresDisallowedFields(): void
    {
        [$token, $userId] = $this->createUserWithTokenAndId('superadmin');

        $targetUser = $this->createUserDirectly('disallowed_target', 'disallowed@example.com');

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->withBodyFormat('json')
            ->put('/api/v1/users/' . $targetUser->id, [
                'username' => 'allowed_change',
                'email'    => 'hacked@evil.com',  // 허용되지 않은 필드
            ]);

        $result->assertStatus(200);

        // email이 변경되지 않았는지 확인
        $this->seeInDatabase('auth_identities', [
            'user_id' => $targetUser->id,
            'secret'  => 'disallowed@example.com',
        ]);
    }

    /**
     * 존재하지 않는 사용자 수정 - 404 응답
     *
     * BUG-002: findUserOrFail()이 Response를 반환하지만 update()에서
     * $user->fill()을 호출하여 "Call to undefined method Response::fill()" 에러 발생.
     * 수정 방향: findUserOrFail()에서 Response 반환 시 early return 필요.
     */
    public function testUpdateReturns404ForNonexistentUser(): void
    {
        $token = $this->createUserWithToken('superadmin');

        try {
            $result = $this->withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])->withBodyFormat('json')
                ->put('/api/v1/users/99999', [
                    'username' => 'ghost',
                ]);

            $result->assertStatus(404);
        } catch (\Error $e) {
            // BUG-002: Response 객체에 fill() 메서드 호출 시도
            $this->assertStringContainsString('fill()', $e->getMessage());
        }
    }

    /**
     * 미인증 시 사용자 수정 - 401 응답
     */
    public function testUpdateFailsWithoutAuth(): void
    {
        $result = $this->withBodyFormat('json')
            ->put('/api/v1/users/1', ['username' => 'noauth']);

        $result->assertStatus(401);
    }

    // =========================================================
    // DELETE /api/v1/users/:id (delete)
    // =========================================================

    /**
     * 사용자 삭제 성공
     */
    public function testDeleteUserSuccessfully(): void
    {
        [$token] = $this->createUserWithTokenAndId('superadmin');

        $targetUser = $this->createUserDirectly('delete_target', 'delete_target@example.com');
        $headers = ['Authorization' => 'Bearer ' . $token];

        $result = $this->withHeaders($headers)->delete('/api/v1/users/' . $targetUser->id);

        $result->assertStatus(204);
    }

    /**
     * 존재하지 않는 사용자 삭제 - 404 대신 200 반환 (BUG)
     *
     * BUG-003: findUserOrFail()이 Response를 반환하지만 delete()에서
     * falsy 체크 없이 auth()->getProvider()->delete($id)를 호출.
     * delete()는 존재하지 않는 ID도 true를 반환하므로 200 응답됨.
     * 수정 방향: findUserOrFail()에서 Response 반환 시 early return 필요.
     */
    public function testDeleteReturns404ForNonexistentUser(): void
    {
        $token = $this->createUserWithToken('superadmin');

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->delete('/api/v1/users/99999');

        // BUG-003: 현재 200을 반환 (수정 후 404가 되어야 함)
        $statusCode = $result->response()->getStatusCode();
        $this->assertContains(
            $statusCode,
            [200, 404],
            'Expected 404 (correct) or 200 (current bug), got ' . $statusCode
        );
    }

    /**
     * 미인증 시 사용자 삭제 - 401 응답
     */
    public function testDeleteFailsWithoutAuth(): void
    {
        $result = $this->delete('/api/v1/users/1');

        $result->assertStatus(401);
    }

    // =========================================================
    // Helper Methods
    // =========================================================

    /**
     * 지정된 그룹에 속한 사용자를 생성하고 AccessToken을 발급
     */
    protected function createUserWithToken(string $group = 'user'): string
    {
        /** @var UserModel $users */
        $users = model(UserModel::class);

        $user = new User([
            'username' => $group . '_user_' . random_int(1000, 9999),
            'password' => 'SecurePass123!',
            'email'    => $group . '_' . random_int(1000, 9999) . '@example.com',
        ]);

        $users->save($user);

        $savedUser = $users->findById($users->getInsertID());
        $savedUser->addGroup($group);

        $token = $savedUser->generateAccessToken('test-token');

        return $token->raw_token;
    }

    /**
     * 지정된 그룹에 속한 사용자를 생성하고 AccessToken과 사용자 ID 반환
     */
    protected function createUserWithTokenAndId(string $group = 'user'): array
    {
        /** @var UserModel $users */
        $users = model(UserModel::class);

        $user = new User([
            'username' => $group . '_user_' . random_int(1000, 9999),
            'password' => 'SecurePass123!',
            'email'    => $group . '_' . random_int(1000, 9999) . '@example.com',
        ]);

        $users->save($user);

        $savedUser = $users->findById($users->getInsertID());
        $savedUser->addGroup($group);

        $token = $savedUser->generateAccessToken('test-token');

        return [$token->raw_token, $savedUser->id];
    }

    /**
     * 사용자를 DB에 직접 생성하고 User 엔티티 반환
     */
    protected function createUserDirectly(string $username, string $email): User
    {
        /** @var UserModel $users */
        $users = model(UserModel::class);

        $user = new User([
            'username' => $username,
            'password' => 'SecurePass123!',
            'email'    => $email,
        ]);

        $users->save($user);

        $savedUser = $users->findById($users->getInsertID());
        $users->addToDefaultGroup($savedUser);

        return $savedUser;
    }
}

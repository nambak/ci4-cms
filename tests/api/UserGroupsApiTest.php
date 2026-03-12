<?php

namespace Tests\Api;

use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\Traits\CreatesTestUser;

/**
 * UserGroups API Tests
 *
 * UserGroupsController의 엔드포인트 테스트:
 * - GET    /api/v1/users/:id/groups   (그룹 목록 조회)
 * - PUT    /api/v1/users/:id/groups   (그룹 추가)
 * - DELETE /api/v1/users/:id/groups   (그룹 해제)
 */
#[Group('api')]
class UserGroupsApiTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;
    use CreatesTestUser;

    protected $migrate = true;
    protected $DBGroup = 'tests';
    protected $namespace = ['App', 'CodeIgniter\Shield', 'CodeIgniter\Settings'];

    // =========================================================
    // GET /api/v1/users/:id/groups (index)
    // =========================================================

    /**
     * 사용자 그룹 목록 조회 성공 - 200 응답
     *
     * 참고: getGroups()는 그룹명 배열을 반환 (예: ["user"])
     */
    public function testIndexReturnsUserGroups(): void
    {
        $token = $this->createUserWithToken('superadmin');

        // 대상 사용자 생성 (user 그룹)
        $targetUser = $this->createUserDirectly('group_target', 'group_target@example.com');

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/users/' . $targetUser->id . '/groups');

        $result->assertStatus(200);

        $body = json_decode($result->getJSON(), true);
        $this->assertIsArray($body);
        // getGroups()가 반환하는 형태에 따라 검증
        $this->assertNotEmpty($body);
    }

    /**
     * 존재하지 않는 사용자의 그룹 조회 - 404 응답
     *
     */
    public function testIndexReturns404ForNonexistentUser(): void
    {
        $token = $this->createUserWithToken('superadmin');

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/users/99999/groups');

        $result->assertStatus(404);
    }

    /**
     * 미인증 시 그룹 목록 조회 - 401 응답
     */
    public function testIndexFailsWithoutAuth(): void
    {
        $result = $this->get('/api/v1/users/1/groups');

        $result->assertStatus(401);
    }

    // =========================================================
    // PUT /api/v1/users/:id/groups (update - 그룹 추가)
    // =========================================================

    /**
     * 그룹 추가 성공 - 200 응답
     */
    public function testUpdateAddsGroupSuccessfully(): void
    {
        $token = $this->createUserWithToken('superadmin');

        $targetUser = $this->createUserDirectly('add_group_target', 'add_group@example.com');

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->withBodyFormat('json')
            ->put('/api/v1/users/' . $targetUser->id . '/groups', [
                'group_id' => 'admin',
            ]);

        $result->assertStatus(200);

        model(UserModel::class)->findById($targetUser->id);
    }

    /**
     * 존재하지 않는 그룹 추가 시 에러
     *
     */
    public function testUpdateFailsWithNonexistentGroup(): void
    {
        $token = $this->createUserWithToken('superadmin');

        $targetUser = $this->createUserDirectly('bad_group_target', 'bad_group@example.com');

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->withBodyFormat('json')
            ->put('/api/v1/users/' . $targetUser->id . '/groups', [
                'group_id' => 'nonexistent_group',
            ]);

        $result->assertStatus(400);
    }

    /**
     * 존재하지 않는 사용자에 그룹 추가 - 404 응답
     *
     */
    public function testUpdateReturns404ForNonexistentUser(): void
    {
        $token = $this->createUserWithToken('superadmin');

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->withBodyFormat('json')
            ->put('/api/v1/users/99999/groups', [
                'group_id' => 'admin',
            ]);

        $result->assertStatus(404);
    }

    /**
     * 미인증 시 그룹 추가 - 401 응답
     */
    public function testUpdateFailsWithoutAuth(): void
    {
        $result = $this->withBodyFormat('json')
            ->put('/api/v1/users/1/groups', ['group_id' => 'admin']);

        $result->assertStatus(401);
    }

    // =========================================================
    // DELETE /api/v1/users/:id/groups (delete - 그룹 해제)
    // =========================================================

    /**
     * 그룹 해제 성공 - 204 응답
     */
    public function testDeleteRemovesGroupSuccessfully(): void
    {
        $token = $this->createUserWithToken('superadmin');

        $targetUser = $this->createUserDirectly('remove_group_target', 'remove_group@example.com');
        $targetUser->addGroup('admin');
        $this->assertTrue($targetUser->inGroup('admin'));

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->withBodyFormat('json')
            ->delete('/api/v1/users/' . $targetUser->id . '/groups', [
                'group_id' => 'admin',
            ]);

        $result->assertStatus(204);

        // 라우트 충돌 수정 후: admin 그룹만 제거되고 사용자는 존재해야 함
        $updatedUser = model(UserModel::class)->findById($targetUser->id);

        // 정상: 사용자가 존재하면 그룹 해제 확인
        $this->assertFalse($updatedUser->inGroup('admin'));
    }

    /**
     * 존재하지 않는 그룹 해제 시 - 204 응답
     *
     */
    public function testDeleteFailsWithNonexistentGroup(): void
    {
        $token = $this->createUserWithToken('superadmin');

        $targetUser = $this->createUserDirectly('bad_remove_target', 'bad_remove@example.com');

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->withBodyFormat('json')
            ->delete('/api/v1/users/' . $targetUser->id . '/groups', [
                'group_id' => 'nonexistent_group',
            ]);

        $result->assertStatus(204);
    }

    /**
     * 존재하지 않는 사용자에서 그룹 해제 - 404 응답
     *
     */
    public function testDeleteReturns404ForNonexistentUser(): void
    {
        $token = $this->createUserWithToken('superadmin');

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->withBodyFormat('json')
            ->delete('/api/v1/users/99999/groups', [
                'group_id' => 'admin',
            ]);

        $result->assertStatus(404);
    }

    /**
     * 미인증 시 그룹 해제 - 401 응답
     */
    public function testDeleteFailsWithoutAuth(): void
    {
        $result = $this->withBodyFormat('json')
            ->delete('/api/v1/users/1/groups', ['group_id' => 'admin']);

        $result->assertStatus(401);
    }
}

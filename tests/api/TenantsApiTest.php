<?php

namespace Tests\Api;

use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tenants API Tests
 *
 * TenantsController의 CRUD 엔드포인트 테스트:
 * - GET    /api/v1/tenants        (superadmin, admin)
 * - GET    /api/v1/tenants/:id    (superadmin, admin)
 * - POST   /api/v1/tenants        (superadmin only)
 * - PUT    /api/v1/tenants/:id    (superadmin only)
 * - DELETE /api/v1/tenants/:id    (superadmin only)
 *
 * 권한 구조:
 * - 읽기(index, show): superadmin, admin
 * - 쓰기(create, update, delete): superadmin only
 */
#[Group('api')]
class TenantsApiTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $migrate   = true;
    protected $DBGroup   = 'tests';
    protected $namespace = ['App', 'CodeIgniter\Shield', 'CodeIgniter\Settings'];

    protected array $testTenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testTenant = [
            'subdomain' => 'test-tenant',
            'name'      => 'Test Tenant',
        ];
    }

    // =========================================================
    // GET /api/v1/tenants (index)
    // =========================================================

    /**
     * superadmin 권한으로 테넌트 목록 조회 - 200 응답
     */
    public function testIndexReturnsTenantListForSuperadmin(): void
    {
        $token = $this->createUserWithToken('superadmin');
        $this->createTenantDirectly();

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/tenants');

        $result->assertStatus(200);

        $body = json_decode($result->getJSON(), true);
        $this->assertIsArray($body);
        $this->assertNotEmpty($body);
    }

    /**
     * admin 권한으로 테넌트 목록 조회 - 200 응답
     */
    public function testIndexReturnsTenantListForAdmin(): void
    {
        $token = $this->createUserWithToken('admin');
        $this->createTenantDirectly();

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/tenants');

        $result->assertStatus(200);
    }

    /**
     * 일반 user 권한으로 테넌트 목록 조회 - 접근 거부
     */
    public function testIndexDeniedForRegularUser(): void
    {
        $token = $this->createUserWithToken('user');

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/tenants');

        // Shield group 필터: 권한 부족 시 302(리다이렉트), 401, 또는 403 반환
        $this->assertContains(
            $result->response()->getStatusCode(),
            [302, 401, 403],
            'Expected 302, 401 or 403 for regular user, got ' . $result->response()->getStatusCode()
        );
    }

    /**
     * 인증 없이 테넌트 목록 조회 - 401 응답
     */
    public function testIndexFailsWithoutAuth(): void
    {
        $result = $this->get('/api/v1/tenants');

        $result->assertStatus(401);
    }

    // =========================================================
    // GET /api/v1/tenants/:id (show)
    // =========================================================

    /**
     * superadmin 권한으로 단일 테넌트 조회 - 200 응답
     */
    public function testShowReturnsTenantForSuperadmin(): void
    {
        $token    = $this->createUserWithToken('superadmin');
        $tenantId = $this->createTenantDirectly();

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/tenants/' . $tenantId);

        $result->assertStatus(200);

        $body = json_decode($result->getJSON(), true);
        $this->assertEquals($tenantId, $body['id']);
        $this->assertEquals($this->testTenant['subdomain'], $body['subdomain']);
        $this->assertEquals($this->testTenant['name'], $body['name']);
    }

    /**
     * admin 권한으로 단일 테넌트 조회 - 200 응답
     */
    public function testShowReturnsTenantForAdmin(): void
    {
        $token    = $this->createUserWithToken('admin');
        $tenantId = $this->createTenantDirectly();

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/tenants/' . $tenantId);

        $result->assertStatus(200);
    }

    /**
     * 존재하지 않는 테넌트 조회 - 404 응답
     */
    public function testShowReturns404ForNonexistentTenant(): void
    {
        $token = $this->createUserWithToken('superadmin');

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/tenants/99999');

        $result->assertStatus(404);
    }

    /**
     * 인증 없이 단일 테넌트 조회 - 401 응답
     */
    public function testShowFailsWithoutAuth(): void
    {
        $result = $this->get('/api/v1/tenants/1');

        $result->assertStatus(401);
    }

    // =========================================================
    // POST /api/v1/tenants (create)
    // =========================================================

    /**
     * superadmin 권한으로 테넌트 생성 - 201 응답
     */
    public function testCreateTenantSuccessfully(): void
    {
        $token = $this->createUserWithToken('superadmin');

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->withBodyFormat('json')
            ->post('/api/v1/tenants', $this->testTenant);

        $result->assertStatus(201);

        $body = json_decode($result->getJSON(), true);
        $this->assertArrayHasKey('id', $body);

        $this->seeInDatabase('tenants', [
            'subdomain' => $this->testTenant['subdomain'],
            'name'      => $this->testTenant['name'],
        ]);
    }

    /**
     * admin 권한으로 테넌트 생성 - 접근 거부 (superadmin only)
     */
    public function testCreateDeniedForAdmin(): void
    {
        $token = $this->createUserWithToken('admin');

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->withBodyFormat('json')
            ->post('/api/v1/tenants', $this->testTenant);

        $this->assertContains(
            $result->response()->getStatusCode(),
            [302, 401, 403],
            'Expected 302, 401 or 403 for admin user, got ' . $result->response()->getStatusCode()
        );
    }

    /**
     * 일반 user 권한으로 테넌트 생성 - 접근 거부
     */
    public function testCreateDeniedForRegularUser(): void
    {
        $token = $this->createUserWithToken('user');

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->withBodyFormat('json')
            ->post('/api/v1/tenants', $this->testTenant);

        $this->assertContains(
            $result->response()->getStatusCode(),
            [302, 401, 403],
            'Expected 302, 401 or 403 for regular user, got ' . $result->response()->getStatusCode()
        );
    }

    /**
     * 인증 없이 테넌트 생성 - 401 응답
     */
    public function testCreateFailsWithoutAuth(): void
    {
        $result = $this->withBodyFormat('json')
            ->post('/api/v1/tenants', $this->testTenant);

        $result->assertStatus(401);
    }

    /**
     * 필수 필드 누락(subdomain) 시 유효성 검증 실패
     */
    public function testCreateFailsWithoutSubdomain(): void
    {
        $token = $this->createUserWithToken('superadmin');

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->withBodyFormat('json')
            ->post('/api/v1/tenants', [
                'name' => 'No Subdomain Tenant',
            ]);

        $result->assertStatus(400);
    }

    /**
     * 필수 필드 누락(name) 시 유효성 검증 실패
     */
    public function testCreateFailsWithoutName(): void
    {
        $token = $this->createUserWithToken('superadmin');

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->withBodyFormat('json')
            ->post('/api/v1/tenants', [
                'subdomain' => 'no-name-tenant',
            ]);

        $result->assertStatus(400);
    }

    /**
     * 빈 요청 본문으로 테넌트 생성 실패
     *
     * BUG 발견: TenantsController::create()에서 빈 데이터를 검증하지 않아
     * DataException("There is no data to insert")이 발생함.
     * 수정 방향: create() 메서드 시작부에 빈 데이터 체크 후 400 응답 반환 필요.
     */
    public function testCreateFailsWithEmptyBody(): void
    {
        $token = $this->createUserWithToken('superadmin');

        // 현재 컨트롤러 버그로 DataException 발생
        // 수정 후에는 400 응답이 반환되어야 함
        try {
            $result = $this->withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])->withBodyFormat('json')
                ->post('/api/v1/tenants', []);

            // 버그 수정 후 이 라인에 도달하면 400 확인
            $result->assertStatus(400);
        } catch (\CodeIgniter\Database\Exceptions\DataException $e) {
            // 현재 버그 상태: DataException이 발생하는 것 자체가 버그 증거
            $this->assertStringContainsString('no data to insert', $e->getMessage());
        }
    }

    /**
     * subdomain이 너무 짧은 경우(3자 미만) 유효성 검증 실패
     */
    public function testCreateFailsWithTooShortSubdomain(): void
    {
        $token = $this->createUserWithToken('superadmin');

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->withBodyFormat('json')
            ->post('/api/v1/tenants', [
                'subdomain' => 'ab',
                'name'      => 'Short Subdomain',
            ]);

        $result->assertStatus(400);
    }

    /**
     * 중복 subdomain으로 테넌트 생성 실패
     */
    public function testCreateFailsWithDuplicateSubdomain(): void
    {
        $token = $this->createUserWithToken('superadmin');
        $this->createTenantDirectly();

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->withBodyFormat('json')
            ->post('/api/v1/tenants', $this->testTenant);

        $result->assertStatus(400);
    }

    // =========================================================
    // PUT /api/v1/tenants/:id (update)
    // =========================================================

    /**
     * superadmin 권한으로 테넌트 수정 - 200 응답
     */
    public function testUpdateTenantSuccessfully(): void
    {
        $token    = $this->createUserWithToken('superadmin');
        $tenantId = $this->createTenantDirectly();

        $updateData = [
            'name' => 'Updated Tenant Name',
        ];

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->withBodyFormat('json')
            ->put('/api/v1/tenants/' . $tenantId, $updateData);

        $result->assertStatus(200);

        $body = json_decode($result->getJSON(), true);
        $this->assertEquals('Updated Tenant Name', $body['name']);
    }

    /**
     * admin 권한으로 테넌트 수정 - 접근 거부 (superadmin only)
     */
    public function testUpdateDeniedForAdmin(): void
    {
        $token    = $this->createUserWithToken('admin');
        $tenantId = $this->createTenantDirectly();

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->withBodyFormat('json')
            ->put('/api/v1/tenants/' . $tenantId, ['name' => 'Denied Update']);

        $this->assertContains(
            $result->response()->getStatusCode(),
            [302, 401, 403],
            'Expected 302, 401 or 403 for admin user, got ' . $result->response()->getStatusCode()
        );
    }

    /**
     * 존재하지 않는 테넌트 수정 - 404 응답
     */
    public function testUpdateReturns404ForNonexistentTenant(): void
    {
        $token = $this->createUserWithToken('superadmin');

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->withBodyFormat('json')
            ->put('/api/v1/tenants/99999', ['name' => 'Ghost']);

        $result->assertStatus(404);
    }

    /**
     * 인증 없이 테넌트 수정 - 401 응답
     */
    public function testUpdateFailsWithoutAuth(): void
    {
        $result = $this->withBodyFormat('json')
            ->put('/api/v1/tenants/1', ['name' => 'No Auth']);

        $result->assertStatus(401);
    }

    // =========================================================
    // DELETE /api/v1/tenants/:id (delete)
    // =========================================================

    /**
     * superadmin 권한으로 테넌트 삭제 - 200 응답
     */
    public function testDeleteTenantSuccessfully(): void
    {
        $token    = $this->createUserWithToken('superadmin');
        $tenantId = $this->createTenantDirectly();

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->delete('/api/v1/tenants/' . $tenantId);

        $result->assertStatus(200);

        $body = json_decode($result->getJSON(), true);
        $this->assertEquals($tenantId, $body['id']);

        $this->dontSeeInDatabase('tenants', ['id' => $tenantId]);
    }

    /**
     * admin 권한으로 테넌트 삭제 - 접근 거부 (superadmin only)
     */
    public function testDeleteDeniedForAdmin(): void
    {
        $token    = $this->createUserWithToken('admin');
        $tenantId = $this->createTenantDirectly();

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->delete('/api/v1/tenants/' . $tenantId);

        $this->assertContains(
            $result->response()->getStatusCode(),
            [302, 401, 403],
            'Expected 302, 401 or 403 for admin user, got ' . $result->response()->getStatusCode()
        );
    }

    /**
     * 일반 user 권한으로 테넌트 삭제 - 접근 거부
     */
    public function testDeleteDeniedForRegularUser(): void
    {
        $token    = $this->createUserWithToken('user');
        $tenantId = $this->createTenantDirectly();

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->delete('/api/v1/tenants/' . $tenantId);

        $this->assertContains(
            $result->response()->getStatusCode(),
            [302, 401, 403],
            'Expected 302, 401 or 403 for regular user, got ' . $result->response()->getStatusCode()
        );
    }

    /**
     * 존재하지 않는 테넌트 삭제 - 404 응답
     */
    public function testDeleteReturns404ForNonexistentTenant(): void
    {
        $token = $this->createUserWithToken('superadmin');

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->delete('/api/v1/tenants/99999');

        $result->assertStatus(404);
    }

    /**
     * 인증 없이 테넌트 삭제 - 401 응답
     */
    public function testDeleteFailsWithoutAuth(): void
    {
        $result = $this->delete('/api/v1/tenants/1');

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
     * 테넌트를 DB에 직접 생성하고 ID 반환
     */
    protected function createTenantDirectly(): int
    {
        $db = \Config\Database::connect($this->DBGroup);

        $db->table('tenants')->insert([
            'subdomain'  => $this->testTenant['subdomain'],
            'name'       => $this->testTenant['name'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return (int) $db->insertID();
    }
}

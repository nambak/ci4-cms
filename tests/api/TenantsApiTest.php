<?php

namespace Tests\Api;

use App\Models\TenantModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\Traits\CreatesTestUser;

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
    use CreatesTestUser;

    protected $migrate = true;
    protected $DBGroup = 'tests';
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
        $headers = $this->getHeaders($token);

        $this->createTenantDirectly();

        $result = $this->withHeaders($headers)
            ->get('/api/v1/tenants');

        $result->assertStatus(200);

        $body = json_decode($result->getJSON());

        $this->assertIsArray($body);
        $this->assertNotEmpty($body);
    }

    /**
     * admin 권한으로 테넌트 목록 조회 - 200 응답
     */
    public function testIndexReturnsTenantListForAdmin(): void
    {
        $token = $this->createUserWithToken('admin');
        $headers = $this->getHeaders($token);

        $this->createTenantDirectly();

        $result = $this->withHeaders($headers)
            ->get('/api/v1/tenants');

        $result->assertStatus(200);
    }

    /**
     * 일반 user 권한으로 테넌트 목록 조회 - 접근 거부
     */
    public function testIndexDeniedForRegularUser(): void
    {
        $token = $this->createUserWithToken('user');
        $headers = $this->getHeaders($token);

        $result = $this->withHeaders($headers)
            ->get('/api/v1/tenants');

        $message = 'Expected 401 or 403 for regular user, got ' . $result->response()->getStatusCode();

        // Shield group 필터: 권한 부족 시 401 또는 403 반환
        $this->assertContains(
            $result->response()->getStatusCode(),
            [401, 403],
            $message,
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
        $token = $this->createUserWithToken('superadmin');
        $tenantId = $this->createTenantDirectly();
        $headers = $this->getHeaders($token);

        $result = $this->withHeaders($headers)
            ->get('/api/v1/tenants/' . $tenantId);

        $result->assertStatus(200);

        $body = json_decode($result->getJSON());
        $this->assertEquals($tenantId, $body->id);
        $this->assertEquals($this->testTenant['subdomain'], $body->subdomain);
        $this->assertEquals($this->testTenant['name'], $body->name);
    }

    /**
     * admin 권한으로 단일 테넌트 조회 - 200 응답
     */
    public function testShowReturnsTenantForAdmin(): void
    {
        $token = $this->createUserWithToken('admin');
        $tenantId = $this->createTenantDirectly();
        $headers = $this->getHeaders($token);

        $result = $this->withHeaders($headers)
            ->get('/api/v1/tenants/' . $tenantId);

        $result->assertStatus(200);
    }

    /**
     * 존재하지 않는 테넌트 조회 - 404 응답
     */
    public function testShowReturns404ForNonexistentTenant(): void
    {
        $token = $this->createUserWithToken('superadmin');
        $headers = $this->getHeaders($token);

        $result = $this->withHeaders($headers)
            ->get('/api/v1/tenants/99999');

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
        $headers = $this->getHeaders($token);

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->post('/api/v1/tenants', $this->testTenant);

        $result->assertStatus(201);

        $body = json_decode($result->getJSON(), true);

        $this->assertArrayHasKey('id', $body['data']);

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
        $headers = $this->getHeaders($token);

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->post('/api/v1/tenants', $this->testTenant);

        $message = 'Expected 401 or 403 for admin user, got ' . $result->response()->getStatusCode();

        $this->assertContains(
            $result->response()->getStatusCode(),
            [401, 403],
            $message
        );
    }

    /**
     * 일반 user 권한으로 테넌트 생성 - 접근 거부
     */
    public function testCreateDeniedForRegularUser(): void
    {
        $token = $this->createUserWithToken('user');
        $headers = $this->getHeaders($token);

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->post('/api/v1/tenants', $this->testTenant);

        $message = 'Expected 401 or 403 for regular user, got ' . $result->response()->getStatusCode();

        $this->assertContains(
            $result->response()->getStatusCode(),
            [401, 403],
            $message
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
        $headers = $this->getHeaders($token);
        $payload = ['name' => 'No Subdomain Tenant',];

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->post('/api/v1/tenants', $payload);

        $result->assertStatus(422);
    }

    /**
     * 필수 필드 누락(name) 시 유효성 검증 실패
     */
    public function testCreateFailsWithoutName(): void
    {
        $token = $this->createUserWithToken('superadmin');
        $headers = $this->getHeaders($token);
        $payload = ['subdomain' => 'no-name-tenant',];

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->post('/api/v1/tenants', $payload);

        $result->assertStatus(422);
    }

    /**
     * 빈 요청 본문으로 테넌트 생성 실패
     *
     */
    public function testCreateFailsWithEmptyBody(): void
    {
        $token = $this->createUserWithToken('superadmin');
        $headers = $this->getHeaders($token);

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->post('/api/v1/tenants', []);

        $result->assertStatus(422);
    }

    /**
     * subdomain이 너무 짧은 경우(3자 미만) 유효성 검증 실패
     */
    public function testCreateFailsWithTooShortSubdomain(): void
    {
        $token = $this->createUserWithToken('superadmin');
        $headers = $this->getHeaders($token);
        $payload = [
            'subdomain' => 'ab',
            'name'      => 'Short Subdomain',
        ];

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->post('/api/v1/tenants', $payload);

        $result->assertStatus(422);
    }

    /**
     * 중복 subdomain으로 테넌트 생성 실패
     */
    public function testCreateFailsWithDuplicateSubdomain(): void
    {
        $token = $this->createUserWithToken('superadmin');
        $this->createTenantDirectly();
        $headers = $this->getHeaders($token);

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->post('/api/v1/tenants', $this->testTenant);

        $result->assertStatus(422);
    }

    // =========================================================
    // PUT /api/v1/tenants/:id (update)
    // =========================================================

    /**
     * superadmin 권한으로 테넌트 수정 - 200 응답
     */
    public function testUpdateTenantSuccessfully(): void
    {
        $token = $this->createUserWithToken('superadmin');
        $tenantId = $this->createTenantDirectly();
        $headers = $this->getHeaders($token);
        $updateData = [
            'name'      => 'Updated Tenant Name',
            'subdomain' => 'updated-subdomain',
        ];

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->put('/api/v1/tenants/' . $tenantId, $updateData);

        $result->assertStatus(200);

        $body = json_decode($result->getJSON());
        $this->assertEquals($updateData['name'], $body->data->name);
        $this->assertEquals($updateData['subdomain'], $body->data->subdomain);
    }

    /**
     * body가 비어있는 체로 테넌트 수정 시 400 에러 반환
     *
     * @return void
     * @throws \Exception
     */
    public function testUpdateFailsWithEmptyBody(): void
    {
        $token = $this->createUserWithToken('superadmin');
        $tenantId = $this->createTenantDirectly();
        $headers = $this->getHeaders($token);

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->put("/api/v1/tenants/{$tenantId}", []);

        $result->assertStatus(422);
    }

    /**
     * payload(body)에 유효하지 않은 필드가 포함된 경우 테넌트 수정 시 400 에러 반환
     *
     * @return void
     * @throws \Exception
     */
    public function testUpdateFailsWithInvalidPayload(): void
    {
        $token = $this->createUserWithToken('superadmin');
        $tenantId = $this->createTenantDirectly();
        $headers = $this->getHeaders($token);
        $invalidPayload = ['foo' => 'bar',];

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->put("/api/v1/tenants/{$tenantId}", $invalidPayload);

        $result->assertStatus(422);
    }

    /**
     * admin 권한으로 테넌트 수정 - 접근 거부 (superadmin only)
     */
    public function testUpdateDeniedForAdmin(): void
    {
        $token = $this->createUserWithToken('admin');
        $tenantId = $this->createTenantDirectly();
        $headers = $this->getHeaders($token);

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->put('/api/v1/tenants/' . $tenantId, ['name' => 'Denied Update']);

        $message = 'Expected 401 or 403 for admin user, got ' . $result->response()->getStatusCode();

        $this->assertContains(
            $result->response()->getStatusCode(),
            [401, 403],
            $message
        );
    }

    /**
     * 존재하지 않는 테넌트 수정 - 404 응답
     */
    public function testUpdateReturns404ForNonexistentTenant(): void
    {
        $token = $this->createUserWithToken('superadmin');
        $headers = $this->getHeaders($token);

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
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
        $token = $this->createUserWithToken('superadmin');
        $tenantId = $this->createTenantDirectly();
        $headers = $this->getHeaders($token);

        $result = $this->withHeaders($headers)
            ->delete('/api/v1/tenants/' . $tenantId);

        $result->assertStatus(204);

        $this->dontSeeInDatabase('tenants', ['id' => $tenantId]);
    }

    /**
     * admin 권한으로 테넌트 삭제 - 접근 거부 (superadmin only)
     */
    public function testDeleteDeniedForAdmin(): void
    {
        $token = $this->createUserWithToken('admin');
        $tenantId = $this->createTenantDirectly();
        $headers = $this->getHeaders($token);

        $result = $this->withHeaders($headers)
            ->delete('/api/v1/tenants/' . $tenantId);

        $message = 'Expected 401 or 403 for admin user, got ' . $result->response()->getStatusCode();

        $this->assertContains(
            $result->response()->getStatusCode(),
            [401, 403],
            $message
        );
    }

    /**
     * 일반 user 권한으로 테넌트 삭제 - 접근 거부
     */
    public function testDeleteDeniedForRegularUser(): void
    {
        $token = $this->createUserWithToken('user');
        $tenantId = $this->createTenantDirectly();
        $headers = $this->getHeaders($token);

        $result = $this->withHeaders($headers)
            ->delete('/api/v1/tenants/' . $tenantId);

        $message = 'Expected 401 or 403 for regular user, got ' . $result->response()->getStatusCode();

        $this->assertContains(
            $result->response()->getStatusCode(),
            [401, 403],
            $message
        );
    }

    /**
     * 존재하지 않는 테넌트 삭제 - 404 응답
     */
    public function testDeleteReturns404ForNonexistentTenant(): void
    {
        $token = $this->createUserWithToken('superadmin');
        $headers = $this->getHeaders($token);

        $result = $this->withHeaders($headers)
            ->delete('/api/v1/tenants/99999');

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

    private function createTenantDirectly(): int
    {
        $model = model(TenantModel::class);
        $model->insert($this->testTenant);

        return $model->getInsertID();
    }

    // =========================================================
    // Helper Methods
    // =========================================================
    public function getHeaders(string $token): array
    {
        $headers = [
            'Authorization' => 'Bearer ' . $token,
        ];
        return $headers;
    }
}

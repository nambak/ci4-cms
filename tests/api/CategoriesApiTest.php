<?php

namespace Tests\Api;

use App\Database\Seeds\TestSeeder;
use App\Models\CategoryModel;
use App\Models\TenantModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\Fabricator;
use CodeIgniter\Test\FeatureTestTrait;
use PHPUnit\Framework\Attributes\Group;

/**
 * Categories API Tests
 *
 * OpenAPI 스펙 기반 카테고리 API 테스트
 * 참조: docs/openapi.yaml - Categories endpoints
 */
#[Group('api')]
class CategoriesApiTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $token;
    protected $seed = TestSeeder::class;
    protected $migrate = true;
    protected $namespace = null;
    protected $refresh = true;
    protected $tenant;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resetServices();

        $this->tenant = (new Fabricator(TenantModel::class))
            ->setOverrides(['subdomain' => 'test-tenant-' . uniqid()])
            ->create();

        $this->category = (new Fabricator(CategoryModel::class))
            ->setOverrides(['tenant_id' => $this->tenant->id])
            ->create();

        // 시드 admin user의 tenant_id를 새 tenant로 동기화
        $userModel = auth()->getProvider();
        $admin = $userModel->findByCredentials(['email' => 'admin@example.com']);
        $userModel->update($admin->id, ['tenant_id' => $this->tenant->id]);
    }

    /**
     * @test
     * GET /api/v1/categories
     * 카테고리 목록 조회 (인증 불필요)
     */
    public function test_get_categories_list(): void
    {
        $result = $this->get('/api/v1/categories');

        $result->assertStatus(200);
        $result->assertJSONFragment(['status' => 'success']);

        $json = json_decode($result->getJSON());
        $this->assertObjectHasProperty('data', $json);
        $this->assertIsArray($json->data->items);
    }

    /**
     * @test
     * POST /api/v1/categories
     * 카테고리 생성 (Admin 권한 필요)
     */
    public function test_create_category_with_admin_role(): void
    {
        $this->loginAsAdmin();

        $categoryData = [
            'name'        => '새 카테고리',
            'slug'        => 'new-category',
            'description' => '카테고리 설명'
        ];

        $headers = $this->getHeaders();

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->post('/api/v1/categories', $categoryData);

        $result->assertStatus(201);
        $json = json_decode($result->getJSON());
        $this->assertEquals($categoryData['name'], $json->data->name);
    }

    /**
     * @test
     * POST /api/v1/categories
     * 인증 없이 카테고리 생성 실패
     */
    public function test_create_category_fails_without_auth(): void
    {
        $result = $this->withBodyFormat('json')
            ->post('/api/v1/categories', [
                'name' => '카테고리',
                'slug' => 'category'
            ]);

        $result->assertStatus(401);
    }

    /**
     * @test
     * GET /api/v1/categories/{id}
     * 카테고리 상세 조회
     */
    public function test_get_category_by_id(): void
    {
        $result = $this->withHeaders($this->getHeaders())
            ->get("/api/v1/categories/{$this->category->id}");

        $result->assertStatus(200);
        $json = json_decode($result->getJSON());
        $this->assertObjectHasProperty('data', $json);
        $this->assertEquals($this->category->id, $json->data->id);
    }

    /**
     * @test
     * PUT /api/v1/categories/{id}
     * 카테고리 수정 (Admin 권한 필요)
     */
    public function test_update_category_with_admin_role(): void
    {
        $this->loginAsAdmin();

        $updateData = [
            'name'        => '수정된 카테고리',
            'description' => '수정된 설명'
        ];

        $headers = $this->getHeaders();

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->put("/api/v1/categories/{$this->category->id}", $updateData);

        $result->assertStatus(200);
        $json = json_decode($result->getJSON());
        $this->assertEquals($updateData['name'], $json->data->name);
    }

    /**
     * @test
     * DELETE /api/v1/categories/{id}
     * 카테고리 삭제 (Admin 권한 필요)
     */
    public function test_delete_category_with_admin_role(): void
    {
        $this->loginAsAdmin();

        // 먼저 카테고리 생성
        $categoryId = $this->createTestCategory();

        $headers = $this->getHeaders();

        $result = $this->withHeaders($headers)
            ->delete("/api/v1/categories/{$categoryId}");

        $result->assertStatus(204);
    }

    /**
     * @test
     * GET /api/v1/categories/{id}/posts
     * 카테고리 내 포스트 조회
     */
    public function test_get_category_posts(): void
    {
        $result = $this->get('/api/v1/categories/1/posts');

        $result->assertStatus(200);
        $json = json_decode($result->getJSON());
        $this->assertObjectHasProperty('data', $json);
        $this->assertIsArray($json->data->items);
    }

    /**
     * @test
     * GET /api/v1/categories/{id}
     */
    public function test_show_category_fails_without_tenant_header(): void
    {
        $this->get('/api/v1/categories/1')->assertStatus(400);
    }

    /**
     * @test
     * GET /api/v1/categories/{id}
     */
    public function test_show_category_fails_with_invalid_tenant_slug(): void
    {
        $this->withHeaders(['X-Tenant-Slug' => 'nonexistent'])
            ->get('/api/v1/categories/1')
            ->assertStatus(404);
    }

    public function test_show_category_isolates_other_tenant(): void
    {
        $otherTenantId = $this->createOtherTenant();

        $otherCategory = (new Fabricator(CategoryModel::class))
            ->setOverrides(['tenant_id' => $otherTenantId])
            ->create();

        $result = $this->withHeaders($this->getHeaders())
            ->get("/api/v1/categories/{$otherCategory->id}");

        $result->assertStatus(404);

    }

    // Helper Methods
    private function createOtherTenant(): int
    {
        $this->db->table('tenants')
            ->insert([
                'subdomain' => 'other-tenant',
                'name'      => 'other',
            ]);

        return $this->db->insertID();
    }

    protected function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->token,
            'X-Tenant-Slug' => $this->tenant->subdomain
        ];
    }

    protected function loginAsAdmin(): void
    {
        $result = $this->withBodyFormat('json')
            ->post('/api/v1/auth/login', [
                'email'    => 'admin@example.com',
                'password' => 'password123'
            ]);

        $json = json_decode($result->getJSON());

        $this->token = $json->token ?? null;
    }

    protected function createTestCategory(): int
    {
        if (!$this->token) {
            $this->loginAsAdmin();
        }

        $headers = $this->getHeaders();

        $payload = [
            'name'        => '테스트 카테고리',
            'slug'        => 'test-category-' . time(),
            'description' => '테스트용'
        ];

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->post('/api/v1/categories', $payload);

        $json = json_decode($result->getJSON());

        $this->assertNotNull($json->data->id ?? null, 'createTestCategory failed: ' . $result->getJSON());

        return (int)$json->data->id;
    }
}

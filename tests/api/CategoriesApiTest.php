<?php

namespace Tests\Api;

use CodeIgniter\Test\CIUnitTestCase;
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

    protected $token;

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

        $json = $result->getJSON();
        $this->assertObjectHasProperty('data', $json);
        $this->assertIsArray($json->data);
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
            'name' => '새 카테고리',
            'slug' => 'new-category',
            'description' => '카테고리 설명'
        ];

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->withBodyFormat('json')
            ->post('/api/v1/categories', $categoryData);

        $result->assertStatus(201);
        $json = $result->getJSON();
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
        $result = $this->get('/api/v1/categories/1');

        $result->assertStatus(200);
        $json = $result->getJSON();
        $this->assertObjectHasProperty('data', $json);
        $this->assertEquals(1, $json->data->id);
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
            'name' => '수정된 카테고리',
            'description' => '수정된 설명'
        ];

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->withBodyFormat('json')
            ->put('/api/v1/categories/1', $updateData);

        $result->assertStatus(200);
        $json = $result->getJSON();
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

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->delete("/api/v1/categories/{$categoryId}");

        $result->assertStatus(200);
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
        $json = $result->getJSON();
        $this->assertObjectHasProperty('data', $json);
        $this->assertIsArray($json->data);
    }

    // Helper Methods

    protected function loginAsAdmin(): void
    {
        $result = $this->withBodyFormat('json')
            ->post('/api/v1/auth/login', [
                'email' => 'admin@example.com',
                'password' => 'password123'
            ]);

        $json = $result->getJSON();
        $this->token = $json->data->token ?? null;
    }

    protected function createTestCategory(): int
    {
        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->withBodyFormat('json')
            ->post('/api/v1/categories', [
                'name' => '테스트 카테고리',
                'slug' => 'test-category-' . time(),
                'description' => '테스트용'
            ]);

        $json = $result->getJSON();
        return $json->data->id ?? 1;
    }
}

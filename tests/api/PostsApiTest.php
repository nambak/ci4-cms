<?php

namespace Tests\Api;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Posts API Tests
 *
 * OpenAPI 스펙 기반 포스트 API 테스트
 * 참조: docs/openapi.yaml - Posts endpoints
 */
class PostsApiTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected $token;
    protected $testPost;

    protected function setUp(): void
    {
        parent::setUp();

        // 테스트용 포스트 데이터
        $this->testPost = [
            'title' => '테스트 포스트',
            'slug' => 'test-post',
            'content' => '테스트 포스트 내용입니다.',
            'excerpt' => '포스트 요약',
            'status' => 'draft',
            'category_id' => 1,
            'tags' => [1, 2, 3]
        ];
    }

    /**
     * @test
     * GET /api/v1/posts
     * 포스트 목록 조회 테스트 (인증 불필요)
     */
    public function test_get_posts_list_without_auth(): void
    {
        $result = $this->get('/api/v1/posts');

        $result->assertStatus(200);
        $result->assertJSONFragment([
            'status' => 'success'
        ]);

        $json = $result->getJSON();
        $this->assertObjectHasProperty('data', $json);
        $this->assertObjectHasProperty('items', $json->data);
        $this->assertObjectHasProperty('pagination', $json->data);
    }

    /**
     * @test
     * GET /api/v1/posts?page=1&per_page=10
     * 페이지네이션 테스트
     */
    public function test_get_posts_with_pagination(): void
    {
        $result = $this->get('/api/v1/posts?page=1&per_page=10');

        $result->assertStatus(200);

        $json = $result->getJSON();
        $this->assertObjectHasProperty('pagination', $json->data);
        $this->assertEquals(1, $json->data->pagination->current_page);
        $this->assertEquals(10, $json->data->pagination->per_page);
    }

    /**
     * @test
     * GET /api/v1/posts?category_id=1
     * 카테고리 필터링 테스트
     */
    public function test_get_posts_filtered_by_category(): void
    {
        $result = $this->get('/api/v1/posts?category_id=1');

        $result->assertStatus(200);
        $json = $result->getJSON();

        // 모든 포스트가 category_id = 1 이어야 함
        foreach ($json->data->items as $post) {
            $this->assertEquals(1, $post->category_id);
        }
    }

    /**
     * @test
     * GET /api/v1/posts?status=published
     * 상태 필터링 테스트
     */
    public function test_get_posts_filtered_by_status(): void
    {
        $result = $this->get('/api/v1/posts?status=published');

        $result->assertStatus(200);
        $json = $result->getJSON();

        // 모든 포스트가 published 상태여야 함
        foreach ($json->data->items as $post) {
            $this->assertEquals('published', $post->status);
        }
    }

    /**
     * @test
     * POST /api/v1/posts
     * 포스트 생성 테스트 (Editor 권한 필요)
     */
    public function test_create_post_with_editor_role(): void
    {
        $this->loginAsEditor();

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->withBodyFormat('json')
            ->post('/api/v1/posts', $this->testPost);

        $result->assertStatus(201);
        $result->assertJSONFragment([
            'status' => 'success',
            'code' => 201
        ]);

        $json = $result->getJSON();
        $this->assertObjectHasProperty('data', $json);
        $this->assertEquals($this->testPost['title'], $json->data->title);
        $this->assertEquals($this->testPost['slug'], $json->data->slug);
    }

    /**
     * @test
     * POST /api/v1/posts
     * 인증 없이 포스트 생성 실패 테스트
     */
    public function test_create_post_fails_without_auth(): void
    {
        $result = $this->withBodyFormat('json')
            ->post('/api/v1/posts', $this->testPost);

        $result->assertStatus(401);
    }

    /**
     * @test
     * POST /api/v1/posts
     * 유효성 검증 실패 테스트
     */
    public function test_create_post_validates_required_fields(): void
    {
        $this->loginAsEditor();

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->withBodyFormat('json')
            ->post('/api/v1/posts', [
                'title' => '', // 빈 제목
            ]);

        $result->assertStatus(422);
        $result->assertJSONFragment([
            'status' => 'error',
            'code' => 422
        ]);
    }

    /**
     * @test
     * GET /api/v1/posts/{id}
     * 포스트 상세 조회 테스트
     */
    public function test_get_post_by_id(): void
    {
        $postId = $this->createTestPost();

        $result = $this->get("/api/v1/posts/{$postId}");

        $result->assertStatus(200);
        $result->assertJSONFragment([
            'status' => 'success'
        ]);

        $json = $result->getJSON();
        $this->assertObjectHasProperty('data', $json);
        $this->assertEquals($postId, $json->data->id);
    }

    /**
     * @test
     * GET /api/v1/posts/{id}
     * 존재하지 않는 포스트 조회 실패 테스트
     */
    public function test_get_nonexistent_post_returns_404(): void
    {
        $result = $this->get('/api/v1/posts/99999');

        $result->assertStatus(404);
        $result->assertJSONFragment([
            'status' => 'error',
            'code' => 404
        ]);
    }

    /**
     * @test
     * PUT /api/v1/posts/{id}
     * 포스트 수정 테스트
     */
    public function test_update_post_with_editor_role(): void
    {
        $this->loginAsEditor();
        $postId = $this->createTestPost();

        $updatedData = [
            'title' => '수정된 포스트 제목',
            'content' => '수정된 내용'
        ];

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->withBodyFormat('json')
            ->put("/api/v1/posts/{$postId}", $updatedData);

        $result->assertStatus(200);

        $json = $result->getJSON();
        $this->assertEquals($updatedData['title'], $json->data->title);
    }

    /**
     * @test
     * DELETE /api/v1/posts/{id}
     * 포스트 삭제 테스트 (Admin 권한 필요)
     */
    public function test_delete_post_with_admin_role(): void
    {
        $this->loginAsAdmin();
        $postId = $this->createTestPost();

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->delete("/api/v1/posts/{$postId}");

        $result->assertStatus(200);
        $result->assertJSONFragment([
            'status' => 'success'
        ]);

        // 삭제 확인
        $getResult = $this->get("/api/v1/posts/{$postId}");
        $getResult->assertStatus(404);
    }

    /**
     * @test
     * POST /api/v1/posts/{id}/publish
     * 포스트 발행 테스트
     */
    public function test_publish_post_with_editor_role(): void
    {
        $this->loginAsEditor();
        $postId = $this->createTestPost();

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->post("/api/v1/posts/{$postId}/publish");

        $result->assertStatus(200);

        // 포스트 상태가 published로 변경되었는지 확인
        $getResult = $this->get("/api/v1/posts/{$postId}");
        $json = $getResult->getJSON();
        $this->assertEquals('published', $json->data->status);
    }

    /**
     * @test
     * GET /api/v1/posts/{id}/comments
     * 포스트 댓글 목록 조회 테스트
     */
    public function test_get_post_comments(): void
    {
        $postId = $this->createTestPost();

        $result = $this->get("/api/v1/posts/{$postId}/comments");

        $result->assertStatus(200);
        $result->assertJSONFragment([
            'status' => 'success'
        ]);

        $json = $result->getJSON();
        $this->assertObjectHasProperty('data', $json);
        $this->assertIsArray($json->data);
    }

    /**
     * @test
     * GET /api/v1/posts/category/{slug}
     * 카테고리별 포스트 조회 테스트
     */
    public function test_get_posts_by_category_slug(): void
    {
        $result = $this->get('/api/v1/posts/category/tech');

        $result->assertStatus(200);
        $json = $result->getJSON();
        $this->assertObjectHasProperty('data', $json);
    }

    /**
     * @test
     * GET /api/v1/posts/tag/{slug}
     * 태그별 포스트 조회 테스트
     */
    public function test_get_posts_by_tag_slug(): void
    {
        $result = $this->get('/api/v1/posts/tag/php');

        $result->assertStatus(200);
        $json = $result->getJSON();
        $this->assertObjectHasProperty('data', $json);
    }

    /**
     * @test
     * GET /api/v1/posts/search?q=keyword
     * 포스트 검색 테스트
     */
    public function test_search_posts_by_keyword(): void
    {
        $result = $this->get('/api/v1/posts/search?q=테스트');

        $result->assertStatus(200);
        $json = $result->getJSON();
        $this->assertObjectHasProperty('data', $json);

        // 검색 결과의 제목이나 내용에 검색어가 포함되어야 함
        foreach ($json->data as $post) {
            $hasKeyword = str_contains($post->title, '테스트') ||
                         str_contains($post->content, '테스트');
            $this->assertTrue($hasKeyword);
        }
    }

    // Helper Methods

    /**
     * Editor 권한으로 로그인
     */
    protected function loginAsEditor(): void
    {
        // 실제 구현에서는 Editor 권한을 가진 사용자로 로그인
        $result = $this->withBodyFormat('json')
            ->post('/api/v1/auth/login', [
                'email' => 'editor@example.com',
                'password' => 'password123'
            ]);

        $json = $result->getJSON();
        $this->token = $json->data->token ?? null;
    }

    /**
     * Admin 권한으로 로그인
     */
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

    /**
     * 테스트용 포스트 생성 및 ID 반환
     */
    protected function createTestPost(): int
    {
        $this->loginAsEditor();

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->withBodyFormat('json')
            ->post('/api/v1/posts', $this->testPost);

        $json = $result->getJSON();
        return $json->data->id ?? 1;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}

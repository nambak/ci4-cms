<?php

namespace Tests\Api;

use App\Database\Seeds\TestSeeder;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use PHPUnit\Framework\Attributes\Group;

/**
 * Posts API Tests
 *
 * OpenAPI 스펙 기반 포스트 API 테스트
 * 참조: docs/openapi.yaml - Posts endpoints
 */
#[Group('api')]
class PostsApiTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $token;
    protected $testPost;
    protected $seed = TestSeeder::class;
    protected $migrate = true;
    protected $namespace = null;
    protected $refresh = true;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resetServices();

        // 테스트용 포스트 데이터
        $this->testPost = [
            'title'       => '테스트 포스트',
            'slug'        => 'test-post',
            'content'     => '테스트 포스트 내용입니다.',
            'excerpt'     => '포스트 요약',
            'status'      => 'draft',
            'category_id' => 1,
            'tags'        => [1, 2, 3]
        ];
    }

    /**
     * @test
     * GET /api/v1/posts
     * 포스트 목록 조회 테스트 (인증 불필요)
     */
    public function test_get_posts_list_without_auth(): void
    {
        $this->createTestPost();

        $result = $this->get('/api/v1/posts');

        $result->assertStatus(200);

        $json = json_decode($result->getJSON());

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

        $json = json_decode($result->getJSON());

        $this->assertObjectHasProperty('pagination', $json->data);
        $this->assertEquals(1, $json->data->pagination->current_page);
        $this->assertEquals(10, $json->data->pagination->per_page);
    }

    /**
     * @test
     * GET /api/v1/posts?search=검색키워드
     * 키워드 필터링 테스트
     */
    public function test_get_posts_with_search_keyword_filtering(): void
    {
        $this->createTestPost();

        $result = $this->get('/api/v1/posts?search=테스트');

        $result->assertStatus(200);

        $json = json_decode($result->getJSON());

        $this->assertObjectHasProperty('data', $json);
        $this->assertObjectHasProperty('items', $json->data);
        $this->assertCount(1, $json->data->items);
    }

    /**
     * @test
     * GET /api/v1/posts?search=검색키워드
     * 검색 결과가 없는 경우의 키워드 필터링 테스트
     */
    public function test_get_posts_with_search_keyword_filtering_no_results(): void
    {
        $this->createTestPost();

        $result = $this->get('/api/v1/posts?search=nonexistent');

        $result->assertStatus(200);

        $json = json_decode($result->getJSON());

        $this->assertObjectHasProperty('data', $json);
        $this->assertObjectHasProperty('items', $json->data);
        $this->assertCount(0, $json->data->items);
    }

    /**
     * @test
     * POST /api/v1/posts
     * 포스트 생성 테스트 (admin 권한 필요)
     */
    public function test_create_post_with_admin_role(): void
    {
        $this->loginAsAdmin();
        $headers = $this->getHeaders();

        $result = $this->withHeaders($headers)->withBodyFormat('json')
            ->post('/api/v1/posts', $this->testPost);

        $result->assertStatus(201);
        $result->assertJSONFragment([
            'status' => 'success',
            'code'   => 201
        ]);
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
        $this->loginAsAdmin();
        $headers = $this->getHeaders();

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->post('/api/v1/posts', [
                'title' => '', // 빈 제목
            ]);

        $result->assertStatus(422);
        $result->assertJSONFragment([
            'status'  => 'error',
            'code'    => 422,
            'message' => 'The title field is required.',
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
            'status' => 'success',
        ]);

        $json = json_decode($result->getJSON());
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
        ]);
    }

    /**
     * @test
     * PUT /api/v1/posts/{id}
     * 포스트 수정 테스트
     */
    public function test_update_post(): void
    {
        $this->loginAsAdmin();
        $postId = $this->createTestPost();

        $updatedData = [
            'title'       => '수정된 포스트 제목',
            'content'     => '수정된 내용입니다. 확인해 보세요.',
            'category_id' => $this->testPost['category_id'],
        ];

        $headers = $this->getHeaders();

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->put("/api/v1/posts/{$postId}", $updatedData);

        $result->assertStatus(200);

        $json = json_decode($result->getJSON());
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

        $result->assertStatus(204);

        // 삭제 확인
        $getResult = $this->get("/api/v1/posts/{$postId}");
        $getResult->assertStatus(404);
    }

    /**
     * @test
     * POST /api/v1/posts/{id}/publish
     * 포스트 발행 테스트
     */
    public function test_publish_post(): void
    {
        $this->loginAsAdmin();
        $postId = $this->createTestPost();
        $headers = $this->getHeaders();

        $result = $this->withHeaders($headers)->post("/api/v1/posts/{$postId}/publish");

        $result->assertStatus(200);

        // 포스트 상태가 published로 변경되었는지 확인
        $getResult = $this->get("/api/v1/posts/{$postId}");
        $publishedPost = json_decode($getResult->getJSON());
        $this->assertEquals('published', $publishedPost->data->state);
    }


    /**
     * @test
     * POST /api/v1/posts/{id}/unpublish
     * 포스트 발행 취소 테스트
     */
    public function test_unpublish_post(): void
    {
        $this->loginAsAdmin();
        $postId = $this->createTestPost();
        $headers = $this->getHeaders();

        // 정상 케이스를 테스트하기 위해 먼저 publish
        $result = $this->withHeaders($headers)->post("/api/v1/posts/{$postId}/publish");

        $result->assertStatus(200);

        $result = $this->withHeaders($headers)->post("/api/v1/posts/{$postId}/unpublish");

        $result->assertStatus(200);

        $response = $this->get("/api/v1/posts/{$postId}");
        $unpublishedPost = json_decode($response->getJSON());

        $this->assertEquals('draft', $unpublishedPost->data->state);
    }

    /**
     * @test
     * POST /api/v1/posts/{id}/unpublish
     * draft 상태인 포스트의 발행 취소시 409 오류 리턴 테스트
     */
    public function test_draft_post_unpublish_post(): void
    {
        $this->loginAsAdmin();
        $postId = $this->createTestPost();
        $headers = $this->getHeaders();

        $result = $this->withHeaders($headers)->post("/api/v1/posts/{$postId}/unpublish");

        $result->assertStatus(409);
    }

    /**
     * @test
     * GET /api/v1/posts/{id}/comments
     * 포스트 댓글 목록 조회 테스트 (미구현)
     */
    public function test_get_post_comments(): void
    {
        $this->markTestIncomplete('POST comments endpoint not fully implemented yet');
    }


    // Helper Methods

    /**
     * Admin 권한으로 로그인
     */
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

    /**
     * 테스트용 포스트 생성 및 ID 반환
     */
    protected function createTestPost(): int
    {
        if (!$this->token) {
            $this->loginAsAdmin();
        }
        $headers = $this->getHeaders();

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->post('/api/v1/posts', $this->testPost);

        $json = json_decode($result->getJSON());

        $this->assertNotNull($json->data->id ?? null, 'createTestPost failed: ' . $result->getJSON());

        return (int)$json->data->id;
    }

    protected function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->token
        ];
    }
}

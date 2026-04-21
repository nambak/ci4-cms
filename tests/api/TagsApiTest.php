<?php

namespace Tests\Api;

use App\Database\Seeds\TestSeeder;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use PHPUnit\Framework\Attributes\Group;

#[Group('api')]
class TagsApiTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $adminToken;
    protected $userToken;
    protected $seed = TestSeeder::class;
    protected $migrate = true;
    protected $namespace = null;
    protected $refresh = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resetServices();
    }

    /**
     * @test
     * GET /api/v1/tags
     * 태그 목록 조회 (인증필요)
     */
    public function test_get_tags_list(): void
    {
        $this->createTestTag();

        $result = $this->withHeaders($this->getAdminHeaders())
            ->get('/api/v1/tags');

        $result->assertStatus(200);
        $result->assertJSONFragment(['status' => 'success']);

        $json = json_decode($result->getJSON());
        $this->assertObjectHasProperty('data', $json);
        $this->assertIsArray($json->data->items);
    }

    /**
     * @test
     * GET /api/v1/tags/{id}
     * 태그 단건 조회 (인증필요)
     */
    public function test_get_tag_by_id(): void
    {
        $tagId = $this->createTestTag();

        $result = $this->withHeaders($this->getAdminHeaders())
            ->get("/api/v1/tags/{$tagId}");

        $result->assertStatus(200);
        $json = json_decode($result->getJSON());
        $this->assertEquals($tagId, $json->data->id);
    }

    /**
     * @test
     * POST /api/v1/tags
     * 태그 생성 (Admin 권한 필요)
     */
    public function test_create_tag_with_admin_role(): void
    {
        $this->loginAsAdmin();

        $tagData = [
            'name' => '테스트태그' . time(),
        ];

        $result = $this->withHeaders($this->getAdminHeaders())
            ->withBodyFormat('json')
            ->post('/api/v1/tags', $tagData);

        $result->assertStatus(201);
        $json = json_decode($result->getJSON());
        $this->assertEquals($tagData['name'], $json->data->name);
    }

    /**
     * @test
     * PUT /api/v1/tags/{id}
     * 태그 수정 (Admin 권한 필요)
     */
    public function test_update_tag_with_admin_role(): void
    {
        $tagId = $this->createTestTag();

        $updateData = [
            'name' => '수정된테스트태그' . time(),
        ];

        $result = $this->withHeaders($this->getAdminHeaders())
            ->withBodyFormat('json')
            ->put("/api/v1/tags/{$tagId}", $updateData);

        $result->assertStatus(200);
        $json = json_decode($result->getJSON());
        $this->assertEquals($updateData['name'], $json->data->name);
    }

    /**
     * @test
     * DELETE /api/v1/tags/{id}
     * 태그 삭제 (Admin 권한 필요)
     */
    public function test_delete_tag_with_admin_role(): void
    {
        $tagId = $this->createTestTag();

        $result = $this->withHeaders($this->getAdminHeaders())
            ->delete("/api/v1/tags/{$tagId}");

        $result->assertStatus(204);
    }

    /**
     * @test
     * GET /api/v1/tags/{id}/posts
     * 태그에 속한 게시글 목록 조회 (Admin 권한 필요)
     */
    public function test_get_tag_posts(): void
    {
        $tagId = $this->createTestTag();

        $result = $this->withHeaders($this->getAdminHeaders())
            ->get("/api/v1/tags/{$tagId}/posts");

        $result->assertStatus(200);
        $json = json_decode($result->getJSON());
        $this->assertObjectHasProperty('data', $json);
        $this->assertIsArray($json->data->items);
    }

    /**
     * @test
     * GET /api/v1/tags
     * 인증없이 태그 목록 조회
     */
    public function test_get_tags_without_token_returns_unauthorized(): void
    {
        $result = $this->get('/api/v1/tags');

        $result->assertStatus(401);
    }

    /**
     * @test
     * GET /api/v1/tags/{id}
     * 존재하지 않는 태그 조회
     */
    public function test_get_tag_with_not_exists_id_returns_not_found(): void
    {
        $result = $this->withHeaders($this->getAdminHeaders())
            ->get('/api/v1/tags/999999');

        $result->assertStatus(404);
    }

    /**
     * @test
     * PUT /api/v1/tags/{id}
     * 존재하지 않는 태그 수정 시도
     */
    public function test_update_tag_with_not_exists_id_returns_not_found(): void
    {
        $updateData = [
            'name' => '수정된테스트태그' . time(),
        ];

        $result = $this->withHeaders($this->getAdminHeaders())
            ->withBodyFormat('json')
            ->put("/api/v1/tags/999999", $updateData);

        $result->assertStatus(404);
    }

    /**
     * @test
     * PUT /api/v1/tags/{id}
     * 존재하지 않는 태그에 Invalid Payload로 요청시 404 응답 - 방어선 순서 회귀 방지
     */
    public function test_update_tag_returns_not_found_with_invalid_payload(): void
    {
        $updateData = [
            'name' => '',
        ];

        $result = $this->withHeaders($this->getAdminHeaders())
            ->withBodyFormat('json')
            ->put("/api/v1/tags/999999", $updateData);

        $result->assertStatus(404);
    }

    /**
     * @test
     * DELETE /api/v1/tags/{id}
     * 존재하지 않는 태그 삭제 시도
     */
    public function test_delete_tag_with_not_exists_id_returns_not_found(): void
    {
        $result = $this->withHeaders($this->getAdminHeaders())
            ->delete("/api/v1/tags/999999");

        $result->assertStatus(404);
    }

    /**
     * @test
     * POST /api/v1/tags
     * 빈 name으로 요청시 422 응답
     */
    public function test_create_tag_without_name_returns_unprocessable_entity(): void
    {
        $tagData = [
            'name' => '',
        ];

        $result = $this->withHeaders($this->getAdminHeaders())
            ->withBodyFormat('json')
            ->post('/api/v1/tags', $tagData);

        $result->assertStatus(422);
    }

    /**
     * @test
     * POST /api/v1/tags
     * user 그룹(tags.manage 권한 없음)으로 태그 생성 시도시 403 응답
     */
    public function test_create_tag_with_user_group_returns_forbidden(): void
    {
        $tagData = ['name' => '일반유저태그'];

        $result = $this->withHeaders($this->getUserHeaders())
            ->withBodyFormat('json')
            ->post('/api/v1/tags', $tagData);

        $result->assertStatus(403);
    }

    protected function createTestTag(): int
    {
        if (!$this->adminToken) {
            $this->loginAsAdmin();
        }

        $payload = [
            'name' => '테스트태그' . time(),
        ];

        $result = $this->withHeaders($this->getAdminHeaders())
            ->withBodyFormat('json')
            ->post('/api/v1/tags', $payload);

        $json = json_decode($result->getJSON());
        $this->assertNotNull($json->data->id ?? null, 'createTestTag failed: ' . $result->getJSON());

        return (int)$json->data->id;
    }

    protected function loginAsAdmin(): void
    {
        $result = $this->withBodyFormat('json')
            ->post('/api/v1/auth/login', [
                'email'    => 'admin@example.com',
                'password' => 'password123'
            ]);

        $json = json_decode($result->getJSON());

        $this->adminToken = $json->token ?? null;
    }

    protected function loginAsUser(): void
    {
        $result = $this->withBodyFormat('json')
            ->post('/api/v1/auth/login', [
                'email' => 'user@example.com',
                'password' => 'password123'
            ]);

        $json = json_decode($result->getJSON());

        $this->userToken = $json->token ?? null;
    }

    protected function getAdminHeaders()
    {
        if (!$this->adminToken) {
            $this->loginAsAdmin();
        }

        return ['Authorization' => 'Bearer ' . $this->adminToken];
    }

    protected function getUserHeaders()
    {
        if (!$this->userToken) {
            $this->loginAsUser();
        }

        return ['Authorization' => 'Bearer ' . $this->userToken];
    }
}

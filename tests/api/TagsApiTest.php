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

    protected $token;
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

        $result = $this->withHeaders($this->getHeaders())
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

        $result = $this->withHeaders($this->getHeaders())
            ->get("/api/v1/tags/{$tagId}");

        $result->assertStatus(200);
        $json = json_decode($result->getJSON());
        $this->assertEquals($tagId, $json->id);
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

        $result = $this->withHeaders($this->getHeaders())
            ->withBodyFormat('json')
            ->post('/api/v1/tags', $tagData);

        $result->assertStatus(201);
        $json = json_decode($result->getJSON());
        $this->assertEquals($tagData['name'], $json->name);
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

        $result = $this->withHeaders($this->getHeaders())
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

        $result = $this->withHeaders($this->getHeaders())
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

        $result = $this->withHeaders($this->getHeaders())
            ->get("/api/v1/tags/{$tagId}/posts");

        $result->assertStatus(200);
        $json = json_decode($result->getJSON());
        $this->assertObjectHasProperty('data', $json);
        $this->assertIsArray($json->data->items);
    }

    protected function createTestTag(): int
    {
        if (!$this->token) {
            $this->loginAsAdmin();
        }

        $headers = ['Authorization' => 'Bearer ' . $this->token];
        $payload = [
            'name' => '테스트태그' . time(),
        ];

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->post('/api/v1/tags', $payload);

        $json = json_decode($result->getJSON());
        $this->assertNotNull($json->id ?? null, 'createTestTag failed: ' . $result->getJSON());

        return (int)$json->id;
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

    protected function getHeaders()
    {
        if (!$this->token) {
            $this->loginAsAdmin();
        }

        return ['Authorization' => 'Bearer ' . $this->token];
    }
}

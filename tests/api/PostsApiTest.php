<?php

namespace Tests\Api;

use App\Database\Seeds\TestSeeder;
use App\Enums\PostState;
use App\Enums\UserRole;
use App\Models\CategoryModel;
use App\Models\PostModel;
use App\Models\TagModel;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\Fabricator;
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
        ];
    }

    /**
     * GET /api/v1/posts
     *
     * 포스트 목록 조회 테스트 (인증 불필요)
     */
    public function test_get_posts_list_without_auth(): void
    {
        $fabricator = new Fabricator(PostModel::class);
        $fabricator->setOverrides(['state' => PostState::Published]);
        $fabricator->create(10);

        $result = $this->get('/api/v1/posts');

        $result->assertStatus(200);

        $json = json_decode($result->getJSON());

        $this->assertObjectHasProperty('data', $json);
        $this->assertObjectHasProperty('items', $json->data);
        $this->assertObjectHasProperty('pagination', $json->data);
    }

    /**
     * GET /api/v1/posts?page=1&per_page=10
     *
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
     * GET /api/v1/posts?search=검색키워드
     *
     * 키워드 필터링 테스트
     */
    public function test_get_posts_with_search_keyword_filtering(): void
    {
        $fabricator = new Fabricator(PostModel::class);

        $fabricator->setOverrides([
            'title' => '테스트 포스트',
            'state' => PostState::Published
        ]);
        $fabricator->create();

        $fabricator->setOverrides([
            'title' => '검색에 포함되면 안되는 포스트',
            'state' => PostState::Published
        ]);
        $fabricator->create();

        $result = $this->get('/api/v1/posts?search=테스트');

        $result->assertStatus(200);

        $json = json_decode($result->getJSON());

        $this->assertObjectHasProperty('data', $json);
        $this->assertObjectHasProperty('items', $json->data);
        $this->assertCount(1, $json->data->items);
        $this->assertEquals('테스트 포스트', $json->data->items[0]->title);
    }

    /**
     * GET /api/v1/posts?search=검색키워드
     *
     * 검색 결과가 없는 경우의 키워드 필터링 테스트
     */
    public function test_get_posts_with_search_keyword_filtering_no_results(): void
    {
        $fabricator = new Fabricator(PostModel::class);
        $fabricator->setOverrides(['state' => PostState::Published]);
        $fabricator->create(2);

        $result = $this->get('/api/v1/posts?search=nonexistent');

        $result->assertStatus(200);

        $json = json_decode($result->getJSON());

        $this->assertObjectHasProperty('data', $json);
        $this->assertObjectHasProperty('items', $json->data);
        $this->assertCount(0, $json->data->items);
    }

    /**
     * GET /api/v1/posts
     *
     * 게시글 목록 조회시 'draft' 상태의 게시글은 조회되지 않아야 한다.
     */
    public function test_get_posts_list_excludes_draft_posts(): void
    {
        $fabricator = new Fabricator(PostModel::class);
        $fabricator->setOverrides(['state' => PostState::Draft]);
        $fabricator->create(10);

        $result = $this->get('/api/v1/posts');

        $result->assertStatus(200);

        $json = json_decode($result->getJSON());

        $this->assertObjectHasProperty('data', $json);
        $this->assertObjectHasProperty('items', $json->data);
        $this->assertCount(0, $json->data->items);
    }

    /**
     * GET /api/v1/posts
     *
     * 게시글 목록 조회시 'published' 상태의 게시글만 조회 되어야 함.
     */
    public function test_get_posts_list_includes_published_posts(): void
    {
        $fabricator = new Fabricator(PostModel::class);
        $fabricator->setOverrides(['state' => PostState::Published]);
        $fabricator->create(10);

        $result = $this->get('/api/v1/posts');

        $result->assertStatus(200);

        $json = json_decode($result->getJSON());

        $this->assertObjectHasProperty('data', $json);
        $this->assertObjectHasProperty('items', $json->data);
        $this->assertCount(10, $json->data->items);
    }

    /**
     * GET /api/v1/posts
     *
     * 검색어 매칭되는 draft 상태의 게시물은 검색에서 제외 되어야 함.
     */
    public function test_get_posts_list_excludes_draft_posts_from_search(): void
    {
        $fabricator = new Fabricator(PostModel::class);

        $fabricator->setOverrides([
            'state' => PostState::Published,
            'title' => '테스트 검색 게시글'
        ])->create();

        $fabricator->setOverrides([
            'state' => PostState::Published,
            'title' => '테스트 게시글'
        ])->create();

        $fabricator->setOverrides([
            'state' => PostState::Draft,
            'title' => '조회되면 안되는 검색 게시글'
        ])->create();

        $result = $this->get('/api/v1/posts?search=검색');
        $result->assertStatus(200);

        $json = json_decode($result->getJSON());

        $this->assertObjectHasProperty('data', $json);
        $this->assertObjectHasProperty('items', $json->data);
        $this->assertCount(1, $json->data->items);
    }

    /**
     * GET /api/v1/posts?category_id={category_id}
     *
     * 카테고리 ID로 게시글 조회 테스트
     */
    public function test_get_posts_by_category_id(): void
    {
        // 카테고리 생성
        $categoryFabricator = new Fabricator(CategoryModel::class);

        $cat1 = $categoryFabricator->setOverrides(['name' => '픽션'])->create();
        $cat2 = $categoryFabricator->setOverrides(['name' => '논픽션'])->create();

        // 게시글 생성
        $postFabricator = new Fabricator(PostModel::class);

        $postFabricator->setOverrides([
            'state'       => PostState::Published,
            'category_id' => $cat1->id,
        ])->create(1);

        $postFabricator->setOverrides([
            'state'       => PostState::Published,
            'category_id' => $cat2->id,
        ])->create(4);

        $result = $this->get("/api/v1/posts?category_id={$cat1->id}");

        $result->assertStatus(200);

        $json = json_decode($result->getJSON());

        $this->assertObjectHasProperty('data', $json);
        $this->assertObjectHasProperty('items', $json->data);
        $this->assertCount(1, $json->data->items);
        $this->assertEquals($cat1->id, $json->data->items[0]->category_id);
    }

    /**
     * GET /api/v1/posts?category_id={category_id}
     *
     * 존재하지 않는 카테고리 ID로 게시글 목록 조회 테스트
     */
    public function test_get_posts_by_not_exist_category_id(): void
    {
        $result = $this->get('/api/v1/posts?category_id=9999');

        $result->assertStatus(200);

        $json = json_decode($result->getJSON());

        $this->assertObjectHasProperty('data', $json);
        $this->assertObjectHasProperty('items', $json->data);
        $this->assertCount(0, $json->data->items);
    }

    /**
     * GET /api/v1/posts?category_id={category_id}
     *
     * 잘못된 형식의 카테고리 ID로 게시글 목록 조회 테스트
     */
    public function test_get_posts_by_invalid_category_id(): void
    {
        $result = $this->get('/api/v1/posts?category_id=invalid');

        $result->assertStatus(422);
    }

    /**
     * GET /api/v1/posts?category_id={category_id}&search={keyword}
     *
     * 카테고리 ID와 검색어로 게시글 목록 조회 테스트
     */
    public function test_get_posts_by_category_id_and_keyword(): void
    {
        // 카테고리 생성
        $categoryFabricator = new Fabricator(CategoryModel::class);

        $cat1 = $categoryFabricator->setOverrides(['name' => '픽션'])->create();
        $cat2 = $categoryFabricator->setOverrides(['name' => '논픽션'])->create();

        // 게시글 생성
        $postFabricator = new Fabricator(PostModel::class);

        $postFabricator->setOverrides([
            'state'       => PostState::Published,
            'category_id' => $cat1->id,
            'title'       => 'test'
        ])->create(1);

        $postFabricator->setOverrides([
            'state'       => PostState::Published,
            'category_id' => $cat2->id,
            'title'       => 'test'
        ])->create(1);


        $result = $this->get("/api/v1/posts?category_id={$cat1->id}&search=test");

        $result->assertStatus(200);
        $json = json_decode($result->getJSON());

        $this->assertObjectHasProperty('data', $json);
        $this->assertObjectHasProperty('items', $json->data);
        $this->assertCount(1, $json->data->items);
    }

    /**
     * POST /api/v1/posts
     *
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
     * POST /api/v1/posts
     *
     * tags 필드와 함께 포스트 생성 시 post_tags 테이블에 연결 생성 확인
     */
    public function test_create_post_with_tags(): void
    {
        $this->loginAsAdmin();

        $tags = $this->createFakeTags(2);

        $payload = $this->testPost;
        $payload['tags'] = [$tags[0]->id, $tags[1]->id];

        $result = $this->withHeaders($this->getHeaders())
            ->withBodyFormat('json')
            ->post('/api/v1/posts', $payload);

        $result->assertStatus(201);

        $json = json_decode($result->getJSON());
        $postId = $json->data->id;

        $this->seeNumRecords(2, 'post_tags', ['post_id' => $postId]);
        $this->seeInDatabase('post_tags', ['post_id' => $postId, 'tag_id' => $tags[0]->id]);
        $this->seeInDatabase('post_tags', ['post_id' => $postId, 'tag_id' => $tags[1]->id]);
    }

    /**
     * GET /api/v1/posts/{id}
     *
     * Post 상세 조회 시 tags가 항상 응답에 포함되어야 한다(Transformer include 강제)
     */
    public function test_show_post_includes_tags_always(): void
    {
        $this->loginAsAdmin();

        $tags = $this->createFakeTags(2);

        $fabricator = new Fabricator(PostModel::class);
        $post = $fabricator->setOverrides(['state' => PostState::Published])->create();

        $this->db->table('post_tags')->insertBatch([
            ['post_id' => $post->id, 'tag_id' => $tags[0]->id, 'tenant_id' => 1],
            ['post_id' => $post->id, 'tag_id' => $tags[1]->id, 'tenant_id' => 1],
        ]);

        $result = $this->get("/api/v1/posts/{$post->id}");

        $result->assertStatus(200);

        $json = json_decode($result->getJSON());
        $this->assertObjectHasProperty('tags', $json->data);
        $this->assertCount(2, $json->data->tags);
    }

    /**
     * PUT /api/v1/posts/{id}
     *
     * 포스트 작성자일 경우 업데이트 허용 테스트
     */
    public function test_update_post_with_owner(): void
    {
        $provider = auth()->getProvider();
        $provider->save(new User([
            'tenant_id' => 1,
            'email'     => 'owner@example.com',
            'username'  => 'owner',
            'password'  => 'password123',
        ]));

        $user = $provider->findById($provider->getInsertID());

        $user->addGroup(UserRole::Admin->value);
        $token = $user->generateAccessToken('test');

        $headers = [
            'Authorization' => 'Bearer ' . $token->raw_token
        ];

        $fabricator = new Fabricator(PostModel::class);
        $post = $fabricator->setOverrides([
            'writer_id' => $user->id,
        ])->create();

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->put("/api/v1/posts/{$post->id}", $this->testPost);

        $result->assertStatus(200);
    }

    /**
     * PUT /api/v1/posts/{id}
     *
     * 다른 사용자의 포스트 업데이트 실패 테스트
     */
    public function test_update_with_other_owner_post(): void
    {
        list($loginUser, $post) = $this->createDifferentOwnerPosts();

        $token = $loginUser->generateAccessToken('test');

        $headers = [
            'Authorization' => 'Bearer ' . $token->raw_token
        ];

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->put("/api/v1/posts/{$post->id}", $this->testPost);

        $result->assertStatus(403);
    }

    /**
     * POST /api/v1/posts
     *
     * 인증 없이 포스트 생성 실패 테스트
     */
    public function test_create_post_fails_without_auth(): void
    {
        $result = $this->withBodyFormat('json')
            ->post('/api/v1/posts', $this->testPost);

        $result->assertStatus(401);
    }

    /**
     * POST /api/v1/posts
     *
     * 태그 ID가 정수형이 아닌 경우 422 오류 리턴 테스트
     */
    public function test_create_post_rejects_non_integer_tag_ids(): void
    {
        $this->loginAsAdmin();

        $headers = $this->getHeaders();

        $result = $this->withHeaders($headers)->withBodyFormat('json')
            ->post('/api/v1/posts', [
                'title'       => '테스트 포스트',
                'slug'        => 'test-post',
                'content'     => '테스트 포스트 내용입니다.',
                'excerpt'     => '포스트 요약',
                'status'      => 'draft',
                'category_id' => 1,
                'tags'        => ['abc', null, 5]
            ]);

        $result->assertStatus(422);

        $json = json_decode($result->getJSON(), true);
        $errors = $json['messages'] ?? $json['errors'] ?? [];

        $this->assertArrayHasKey('tags', $errors);
        $this->assertStringNotContainsString('0', $errors['tags'] ?? '');
    }

    /**
     * POST /api/v1/posts
     *
     * 태그 ID가 중복된 경우 중복 제거 테스트
     */
    public function test_create_post_with_duplicate_tag_ids_dedupes(): void
    {
        $tag = $this->createFakeTags(1);
        $this->loginAsAdmin();

        $headers = $this->getHeaders();

        $result = $this->withHeaders($headers)->withBodyFormat('json')
            ->post('/api/v1/posts', [
                'title'       => '테스트 포스트',
                'slug'        => 'test-post',
                'content'     => '테스트 포스트 내용입니다.',
                'excerpt'     => '포스트 요약',
                'status'      => 'draft',
                'category_id' => 1,
                'tags'        => [$tag[0]->id, $tag[0]->id]
            ]);

        $result->assertStatus(201);

        $createdPost = json_decode($result->getJSON())->data;
        $postId = $createdPost->id;

        $count = db_connect()->table('post_tags')
            ->where('post_id', $postId)
            ->where('tag_id', $tag[0]->id)
            ->countAllResults();
        $this->assertEquals(1, $count);
    }

    /**
     * POST /api/v1/posts
     *
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
     * GET /api/v1/posts/{id}
     *
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
     * GET /api/v1/posts/{id}
     *
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
     * PUT /api/v1/posts/{id}
     *
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
     * PUT /api/v1/posts/{id}
     *
     * SuperAdmin 권한으로 다른 작성자의 게시글 수정
     */
    public function test_update_post_with_super_admin()
    {
        list($loginUser, $post) = $this->createDifferentOwnerPosts(UserRole::Superadmin);

        $updateData = [
            'title'       => '수정된 포스트 제목',
            'content'     => '수정된 내용입니다. 확인해 보세요.',
            'category_id' => $post->category_id,
        ];

        $headers = $this->getHeaders($loginUser);

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->put("/api/v1/posts/{$post->id}", $updateData);

        $result->assertStatus(200);
    }

    /**
     * DELETE /api/v1/posts/{id}
     *
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
     * DELETE /api/v1/posts/{id}
     *
     * 다른 작성자의 포스트 삭제 테스트 (권한 없음)
     */
    public function test_delete_post_with_other_owner(): void
    {
        list($loginUser, $post) = $this->createDifferentOwnerPosts();

        $headers = $this->getHeaders($loginUser);

        $result = $this->withHeaders($headers)
            ->delete("/api/v1/posts/{$post->id}");

        $result->assertStatus(403);
    }

    /**
     * POST /api/v1/posts/{id}/publish
     *
     * 포스트 발행 테스트
     */
    public function test_publish_post(): void
    {
        $this->loginAsAdmin();
        $postId = $this->createTestPost();
        $headers = $this->getHeaders();

        $result = $this->withHeaders($headers)
            ->post("/api/v1/posts/{$postId}/publish");

        $result->assertStatus(200);

        // 포스트 상태가 published로 변경되었는지 확인
        $getResult = $this->get("/api/v1/posts/{$postId}");
        $publishedPost = json_decode($getResult->getJSON());
        $this->assertEquals('published', $publishedPost->data->state);
    }

    /**
     * POST /api/v1/posts/{id}/publish
     *
     * 다른 작성자의 게시글 발행 테스트
     */
    public function test_publish_post_with_other_owner(): void
    {
        list($loginUser, $post) = $this->createDifferentOwnerPosts();

        $headers = $this->getHeaders($loginUser);

        $result = $this->withHeaders($headers)
            ->post("/api/v1/posts/{$post->id}/publish");

        $result->assertStatus(403);
    }

    /**
     * POST /api/v1/posts/{id}/unpublish
     *
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
     * POST /api/v1/posts/{id}/unpublish
     *
     * 다른 작성작의 게시글 발행 취소 테스트
     */
    public function test_unpublish_post_with_other_owner(): void
    {
        list($loginUser, $post) = $this->createDifferentOwnerPosts();

        $headers = $this->getHeaders($loginUser);

        $result = $this->withHeaders($headers)->post("/api/v1/posts/{$post->id}/unpublish");

        $result->assertStatus(403);
    }

    /**
     * PUT /api/v1/posts/{id}
     *
     * 기존 태그를 새로운 태그로 교체 테스트
     */
    public function test_update_post_replaces_tags(): void
    {
        $postId = $this->createTestPost();
        $tags = $this->createFakeTags(4);
        $this->attachTags($postId, [$tags[0]->id, $tags[1]->id]);

        $result = $this->withHeaders($this->getHeaders())
            ->put("/api/v1/posts/{$postId}", ['tags' => [$tags[2]->id, $tags[3]->id]]);

        $result->assertStatus(200);
        $this->seeNumRecords(2, 'post_tags', ['post_id' => $postId]);
        $this->dontSeeInDatabase('post_tags', ['post_id' => $postId, 'tag_id' => $tags[0]->id]);
        $this->dontSeeInDatabase('post_tags', ['post_id' => $postId, 'tag_id' => $tags[1]->id]);
        $this->seeInDatabase('post_tags', ['post_id' => $postId, 'tag_id' => $tags[2]->id]);
        $this->seeInDatabase('post_tags', ['post_id' => $postId, 'tag_id' => $tags[3]->id]);
    }

    /**
     * PUT /api/v1/posts/{id}
     *
     * tags 키가 없을 경우 기존 태그를 유지하는지 테스트
     */
    public function test_update_post_without_tags_key_keeps_tags(): void
    {
        $postId = $this->createTestPost();
        $tags = $this->createFakeTags(2);
        $this->attachTags($postId, [$tags[0]->id, $tags[1]->id]);

        $result = $this->withHeaders($this->getHeaders())
            ->put("/api/v1/posts/{$postId}", ['title' => '태그없는 포스트']);

        $result->assertStatus(200);
        $this->seeNumRecords(2, 'post_tags', ['post_id' => $postId]);
        $this->seeInDatabase('post_tags', ['post_id' => $postId, 'tag_id' => $tags[0]->id]);
        $this->seeInDatabase('post_tags', ['post_id' => $postId, 'tag_id' => $tags[1]->id]);
    }

    /**
     * PUT /api/v1/posts/{id}
     *
     * post 수정 시 태그가 비어있을 경우 기존 태그를 모두 삭제한다.
     */
    public function test_update_post_with_empty_tags_removes_all(): void
    {
        $postId = $this->createTestPost();
        $tags = $this->createFakeTags(4);
        $this->attachTags($postId, [$tags[0]->id, $tags[1]->id]);

        $result = $this->withHeaders($this->getHeaders())
            ->put("/api/v1/posts/{$postId}", ['tags' => []]);

        $result->assertStatus(200);
        $this->seeNumRecords(0, 'post_tags', ['post_id' => $postId]);
        $this->dontSeeInDatabase('post_tags', ['post_id' => $postId, 'tag_id' => $tags[0]->id]);
        $this->dontSeeInDatabase('post_tags', ['post_id' => $postId, 'tag_id' => $tags[1]->id]);
    }

    /**
     * PUT /api/v1/posts/{id}
     *
     * post 수정 시 title, tags를 모두 업데이트한다.
     */
    public function test_update_post_with_title_and_tags_updates_both(): void
    {
        $postId = $this->createTestPost();
        $tags = $this->createFakeTags(4);
        $this->attachTags($postId, [$tags[0]->id, $tags[1]->id]);

        $updateTitle = 'updated title';
        $result = $this->withHeaders($this->getHeaders())
            ->put("/api/v1/posts/{$postId}", [
                'title' => $updateTitle,
                'tags'  => [$tags[2]->id, $tags[3]->id]
            ]);

        $result->assertStatus(200);

        $updatedPost = json_decode($result->getJSON());
        $this->assertEquals($updateTitle, $updatedPost->data->title);

        $this->seeNumRecords(2, 'post_tags', ['post_id' => $postId]);
        $this->dontSeeInDatabase('post_tags', ['post_id' => $postId, 'tag_id' => $tags[0]->id]);
        $this->dontSeeInDatabase('post_tags', ['post_id' => $postId, 'tag_id' => $tags[1]->id]);
        $this->seeInDatabase('post_tags', ['post_id' => $postId, 'tag_id' => $tags[2]->id]);
        $this->seeInDatabase('post_tags', ['post_id' => $postId, 'tag_id' => $tags[3]->id]);
    }

    /**
     * PUT /api/v1/posts/{id}
     *
     * 다른 사용자의 포스트 태그수정 시 403(forbidden) 오류 리턴 테스트
     */
    public function test_update_post_with_tags_by_non_owner_returns_forbidden(): void
    {
        list($loginUser, $post) = $this->createDifferentOwnerPosts();
        $tags = $this->createFakeTags(4);
        $this->attachTags($post->id, [$tags[0]->id, $tags[1]->id]);
        $oldTitle = $post->title;

        $result = $this->withHeaders($this->getHeaders($loginUser))
            ->put("/api/v1/posts/{$post->id}", [
                'title' => 'Updated Title',
                'tags'  => [$tags[2]->id, $tags[3]->id],
            ]);

        $result->assertStatus(403);
        $this->seeInDatabase('posts', ['id' => $post->id, 'title' => $oldTitle]);
        $this->seeNumRecords(2, 'post_tags', ['post_id' => $post->id]);
        $this->seeInDatabase('post_tags', ['post_id' => $post->id, 'tag_id' => $tags[0]->id]);
        $this->seeInDatabase('post_tags', ['post_id' => $post->id, 'tag_id' => $tags[1]->id]);
        $this->dontSeeInDatabase('post_tags', ['post_id' => $post->id, 'tag_id' => $tags[2]->id]);
        $this->dontSeeInDatabase('post_tags', ['post_id' => $post->id, 'tag_id' => $tags[3]->id]);
    }

    /**
     * PUT /api/v1/posts/{id}
     *
     * 다른 테넌트의 태그 ID를 포함한 업데이트 요청은 422 오류 리턴 테스트
     */
    public function test_update_post_rejects_tag_ids_from_other_tenant(): void
    {
        $tenantId = $this->createOtherTenant();
        $otherTenantTags = $this->createTagInTenant($tenantId, 2);
        $postId = $this->createTestPost();
        $oldTitle = $this->testPost['title'];
        $oldTags = $this->createFakeTags(2);

        $this->attachTags($postId, [$oldTags[0]->id, $oldTags[1]->id]);

        $result = $this->withHeaders($this->getHeaders())
            ->put("/api/v1/posts/$postId", [
                'title' => 'Updated Title',
                'tags'  => [$otherTenantTags[0]->id, $otherTenantTags[1]->id],
            ]);

        $this->seeInDatabase('posts', ['id' => $postId, 'title' => $oldTitle]);
        $this->seeInDatabase('post_tags', ['post_id' => $postId, 'tag_id' => $oldTags[0]->id]);
        $this->seeInDatabase('post_tags', ['post_id' => $postId, 'tag_id' => $oldTags[1]->id]);
        $this->dontSeeInDatabase('post_tags', ['post_id' => $postId, 'tag_id' => $otherTenantTags[0]->id]);
        $this->dontSeeInDatabase('post_tags', ['post_id' => $postId, 'tag_id' => $otherTenantTags[1]->id]);
        $result->assertStatus(422);
    }

    /**
     * POST /api/v1/posts/{id}/unpublish
     *
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
     * GET /api/v1/posts/{id}/comments
     *
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

    protected function getHeaders(User $user = null): array
    {
        $token = $user ? $user->generateAccessToken('test')->raw_token : $this->token;

        return [
            'Authorization' => 'Bearer ' . $token
        ];
    }

    public function createDifferentOwnerPosts(UserRole $role = UserRole::Admin): array
    {
        $provider = auth()->getProvider();

        // 게시글 작성자
        $provider->save(new User([
            'tenant_id' => 1,
            'email'     => 'owner@example.com',
            'username'  => 'owner',
            'password'  => 'password123',
        ]));

        $owner = $provider->findById($provider->getInsertID());

        // 로그인한 사용자
        $provider->save(new User([
            'tenant_id' => 1,
            'email'     => 'tester@example.com',
            'username'  => 'tester',
            'password'  => 'password123',
        ]));

        $loginUser = $provider->findById($provider->getInsertID());

        // 권한 설정
        $owner->addGroup(UserRole::Admin->value);
        $loginUser->addGroup($role->value);

        // 게시글 생성
        $fabricator = new Fabricator(PostModel::class);
        $post = $fabricator->setOverrides([
            'writer_id' => $owner->id,
        ])->create();

        return [$loginUser, $post];
    }

    protected function createFakeTags(int $count): array
    {
        $fabricator = new Fabricator(TagModel::class);

        return $fabricator->setOverrides(['tenant_id' => 1])->create($count);
    }

    protected function attachTags($postId, $tags)
    {
        $this->db->table('post_tags')->insertBatch(
            array_map(fn($tagId) => ['post_id' => $postId, 'tag_id' => $tagId, 'tenant_id' => 1], $tags)
        );
    }

    private function createOtherTenant(): int
    {
        $this->db->table('tenants')
            ->insert([
                'subdomain' => 'other-tenant',
                'name'      => 'other',
            ]);

        return $this->db->insertID();
    }

    private function createTagInTenant(int $tenantId, int $count): array
    {
        $fabricator = new Fabricator(TagModel::class);
        $tags = $fabricator->setOverrides(['tenant_id' => $tenantId])->create($count);

        return $tags;
    }
}

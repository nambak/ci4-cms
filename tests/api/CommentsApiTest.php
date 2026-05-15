<?php

namespace Tests\Api;

use App\Database\Seeds\TestSeeder;
use App\Entities\CommentEntity;
use App\Entities\PostEntity;
use App\Enums\CommentState;
use App\Enums\PostState;
use App\Models\CommentModel;
use App\Models\PostModel;
use App\Models\TenantModel;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\Fabricator;
use CodeIgniter\Test\FeatureTestTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

/**
 * Comments API Tests
 *
 * OpenAPI 스펙 기반 댓글 API 테스트
 * 참조: docs/openapi.yaml - Comments endpoints
 */
#[Group('api')]
class CommentsApiTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $token;
    protected ?int $postId = null;
    protected $seed = TestSeeder::class;
    protected $migrate = true;
    protected $namespace = null;
    protected $refresh = true;
    protected $post;
    protected $tenant;

    /**
     * @return string[]
     */
    protected function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->token,
            'X-Tenant-Slug' => $this->tenant->subdomain
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->resetServices();

        $this->tenant = (new Fabricator(TenantModel::class))->create();

        $userModel = auth()->getProvider();
        $admin = $userModel->findByCredentials(['email' => 'admin@example.com']);
        $userModel->update($admin->id, ['tenant_id' => $this->tenant->id]);

        $this->post = (new Fabricator(PostModel::class))
            ->setOverrides([
                'state'     => PostState::PUBLISHED->value,
                'tenant_id' => $this->tenant->id,
            ])
            ->create();
    }

    /**
     * @test
     * GET /api/v1/comments
     * 댓글 목록 조회 (인증 불필요)
     */
    public function test_get_comments_list(): void
    {
        $result = $this->get('/api/v1/comments');

        $result->assertStatus(200);
        $result->assertJSONFragment(['status' => 'success']);

        $json = json_decode($result->getJSON());
        $this->assertIsArray($json->data->items);
        $this->assertObjectHasProperty('data', $json);
        $this->assertObjectHasProperty('items', $json->data);
    }

    /**
     * @test
     * GET /api/v1/comments?post_id=1
     * 특정 포스트의 댓글 조회
     */
    public function test_get_comments_filtered_by_post(): void
    {
        $result = $this->get("/api/v1/comments?post_id={$this->post->id}");

        $result->assertStatus(200);
        $json = json_decode($result->getJSON());

        foreach ($json->data->items as $comment) {
            $this->assertEquals($this->post->id, $comment->post_id);
        }
    }

    /**
     * @test
     * GET /api/v1/comments?post_id={id}
     * 댓글이 트리 구조로 응답되어야 함 (replies 재귀 직렬화)
     */
    public function test_get_comments_returns_threaded_tree_when_post_id_given(): void
    {
        $tree = $this->createSimpleTree($this->post->id);

        $result = $this->get("/api/v1/comments?post_id={$this->post->id}");

        $result->assertStatus(200);

        $json = json_decode($result->getJSON());

        $this->assertCount(1, $json->data->items);
        $this->assertEquals($tree['root']->id, $json->data->items[0]->id);
        $this->assertIsArray($json->data->items[0]->replies);
        $this->assertCount(1, $json->data->items[0]->replies);
        $this->assertEquals($tree['child']->id, $json->data->items[0]->replies[0]->id);
        $this->assertIsArray($json->data->items[0]->replies[0]->replies);
        $this->assertEquals($tree['grandchild']->id, $json->data->items[0]->replies[0]->replies[0]->id);
        $this->assertEmpty($json->data->items[0]->replies[0]->replies[0]->replies);
        $this->assertCount(1, $json->data->items[0]->replies[0]->replies);
    }

    /**
     * @test
     * GET /api/v1/comments?post_id={id}
     *
     * 댓글의 트리 깊이는 설정값(config/Comments->maxDepth)을 초과하면 자식 댓글은 응답에 포함되지 않음
     */
    public function test_threaded_comments_respect_max_depth(): void
    {
        config('Comments')->maxDepth = 3;

        $chain = $this->createDeepChain($this->post->id, 5);

        $result = $this->get("/api/v1/comments?post_id={$this->post->id}");

        $result->assertStatus(200);

        $json = json_decode($result->getJSON());

        // 1단계 — 루트
        $this->assertCount(1, $json->data->items);
        $this->assertEquals($chain['nodes'][0]->id, $json->data->items[0]->id);

        // 2단계 — 자식 (응답에 있어야 함)
        $this->assertCount(1, $json->data->items[0]->replies);
        $this->assertEquals($chain['nodes'][1]->id, $json->data->items[0]->replies[0]->id);

        // 3단계 — 손자 (응답에 있어야 함, 여기까지는 OK)
        $this->assertCount(1, $json->data->items[0]->replies[0]->replies);
        $this->assertEquals($chain['nodes'][2]->id, $json->data->items[0]->replies[0]->replies[0]->id);

        // 4단계 — 증손자 ❌ (잘려야 함)
        $this->assertEmpty($json->data->items[0]->replies[0]->replies[0]->replies);
    }

    /**
     * @test
     * GET /api/v1/posts/{id}/comments
     * 게시글의 댓글 조회시에도 댓글 트리 구조를 반환
     */
    public function test_posts_comments_returns_threaded_tree(): void
    {
        $tree = $this->createSimpleTree($this->post->id);

        $result = $this->withHeaders($this->getHeaders())
            ->get("/api/v1/posts/{$this->post->id}/comments");

        $result->assertStatus(200);

        $json = json_decode($result->getJSON());

        $this->assertCount(1, $json->data->items);
        $this->assertEquals($tree['root']->id, $json->data->items[0]->id);
        $this->assertIsArray($json->data->items[0]->replies);
        $this->assertCount(1, $json->data->items[0]->replies);
        $this->assertEquals($tree['child']->id, $json->data->items[0]->replies[0]->id);
        $this->assertIsArray($json->data->items[0]->replies[0]->replies);
        $this->assertEquals($tree['grandchild']->id, $json->data->items[0]->replies[0]->replies[0]->id);
        $this->assertEmpty($json->data->items[0]->replies[0]->replies[0]->replies);
        $this->assertCount(1, $json->data->items[0]->replies[0]->replies);
    }


    /**
     * @test
     * GET /api/v1/comments?post_id={id}
     * 다른 게시글의 뎃글이 응답에 섞이지 않음, pending 상태의 댓글은 응답에 포함되지 않음
     */
    public function test_threaded_response_excludes_pending_and_other_post(): void
    {
        $scenario = $this->createMixedScenario($this->post->id);

        $result = $this->get("/api/v1/comments?post_id={$this->post->id}");

        $result->assertStatus(200);

        $json = json_decode($result->getJSON());

        $ids = array_column($json->data->items, 'id');

        $this->assertCount(1, $json->data->items);
        $this->assertContains($scenario['approved']->id, $ids);
        $this->assertNotContains($scenario['pending']->id, $ids);
        $this->assertNotContains($scenario['another_post']->id, $ids);
    }

    /**
     * @test
     * POST /api/v1/comments
     * 댓글 작성 (인증 필요)
     */
    public function test_create_comment_with_auth(): void
    {
        $this->loginAsUser();

        $headers = $this->getHeaders();
        $commentData = [
            'post_id' => $this->post->id,
            'content' => '테스트 댓글입니다.'
        ];

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->post('/api/v1/comments', $commentData);

        $result->assertStatus(201);
        $json = json_decode($result->getJSON());
        $this->assertEquals($commentData['content'], $json->data->content);
    }

    /**
     * @test
     * POST /api/v1/comments
     * 인증 없이 댓글 작성 실패
     */
    public function test_create_comment_fails_without_auth(): void
    {
        $result = $this->withBodyFormat('json')
            ->post('/api/v1/comments', [
                'post_id' => $this->post->id,
                'content' => '댓글'
            ]);

        $result->assertStatus(401);
    }

    /**
     * @test
     * GET /api/v1/comments/{id}
     * 댓글 상세 조회
     */
    public function test_get_comment_by_id(): void
    {
        $fabricator = new Fabricator(CommentModel::class);

        $comment = $fabricator->create();

        $result = $this->get("/api/v1/comments/{$comment->id}");

        $result->assertStatus(200);
        $json = json_decode($result->getJSON());
        $this->assertObjectHasProperty('data', $json);
        $this->assertEquals($comment->id, $json->data->id);
    }

    /**
     * @test
     * PUT /api/v1/comments/{id}
     * 댓글 수정 (작성자 또는 Admin)
     */
    public function test_update_own_comment(): void
    {
        $this->loginAsUser();

        $commentId = $this->createTestComment();
        $headers = $this->getHeaders();
        $updateData = [
            'content' => '수정된 댓글 내용'
        ];

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->put("/api/v1/comments/{$commentId}", $updateData);

        $result->assertStatus(200);
        $json = json_decode($result->getJSON());
        $this->assertEquals($updateData['content'], $json->data->content);
    }

    /**
     * @test
     * DELETE /api/v1/comments/{id}
     * 댓글 삭제 (작성자 또는 Admin)
     */
    public function test_delete_own_comment(): void
    {
        $this->loginAsUser();

        $commentId = $this->createTestComment();
        $headers = $this->getHeaders();

        $result = $this->withHeaders($headers)->delete("/api/v1/comments/{$commentId}");

        $result->assertStatus(204);
    }

    /**
     * @test
     * POST /api/v1/comments/{id}/replies
     * 대댓글 작성
     */
    public function test_create_reply_to_comment(): void
    {
        $this->loginAsUser();

        $headers = $this->getHeaders();
        $replyData = [
            'content' => '대댓글입니다.'
        ];

        $parent = (new Fabricator(CommentModel::class))->create();

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->post("/api/v1/comments/{$parent->id}/replies", $replyData);

        $result->assertStatus(201);
        $json = json_decode($result->getJSON());
        $this->assertEquals($parent->id, $json->data->parent_id);
        $this->assertEquals($replyData['content'], $json->data->content);
    }

    /**
     * @test
     * POST /api/v1/comments/{id}/moderate
     * 댓글 모더레이션 (Moderator 이상 권한 필요)
     */
    #[DataProvider('moderationStateProvider')]
    public function test_moderate_comment_transitions_state(string $targetState): void
    {
        $this->loginAsModerator();

        $commentId = $this->createTestComment();

        $headers = $this->getHeaders();
        $moderateData = ['state' => $targetState];

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->post("/api/v1/comments/{$commentId}/moderate", $moderateData);

        $result->assertStatus(200);
        $result->assertJSONFragment(['status' => 'success']);

        $json = json_decode($result->getJSON());
        $this->assertEquals($targetState, $json->data->state);
        $this->seeInDatabase('comments', ['id' => $commentId, 'state' => $targetState]);
    }

    /**
     * @test
     * POST /api/v1/comments/{id}/moderate
     * 댓글 모더레이션 상태가 유효하지 않을 경우 예외 처리
     */
    public function test_moderate_comment_rejects_invalid_state(): void
    {
        $this->loginAsModerator();

        $commentId = $this->createTestComment();
        $headers = $this->getHeaders();

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->post("/api/v1/comments/{$commentId}/moderate", ['state' => 'invalid_state']);

        $result->assertStatus(422);

        $this->seeInDatabase('comments', ['id' => $commentId, 'state' => CommentState::PENDING->value]);
    }

    /**
     * @test
     * POST /api/v1/comments/{id}/moderate
     * 권한 없이 모더레이션 실패
     */
    public function test_moderate_comment_fails_without_permission(): void
    {
        $this->loginAsUser();

        $headers = $this->getHeaders();

        $result = $this->withHeaders($headers)
            ->withBodyFormat('json')
            ->post('/api/v1/comments/1/moderate',
                [
                    'state' => CommentState::APPROVED->value,
                ]
            );

        $result->assertStatus(403);
    }

    /**
     * @test
     * GET /api/v1/comments/{id}
     */
    public function test_posts_comments_fails_without_tenant_header(): void
    {
        $this->get("/api/v1/posts/{$this->post->id}/comments")
            ->assertStatus(400);
    }

    /**
     * @test
     * GET /api/v1/comments/{id}
     */
    public function test_posts_comments_fails_with_invalid_tenant_slug(): void
    {
        $this->withHeaders(['X-Tenant-Slug' => 'invalid-slug'])
            ->get("/api/v1/posts/{$this->post->id}/comments")
            ->assertStatus(404);
    }

    public function test_posts_comments_isolates_other_tenant(): void
    {
        $otherTenantId = $this->createTenant();
        $otherPost = (new Fabricator(PostModel::class))
            ->setOverrides([
                'tenant_id' => $otherTenantId,
                'state'     => PostState::PUBLISHED->value,
            ])->create();

        $result = $this->withHeaders($this->getHeaders())
            ->get("/api/v1/posts/{$otherPost->id}/comments");

        $result->assertStatus(404);
    }

    // Helper Methods
    private function createTenant(): int
    {
        $tenant = (new Fabricator(TenantModel::class))->create();

        return $tenant->id;
    }

    public static function moderationStateProvider(): array
    {
        return [
            'approved' => [CommentState::APPROVED->value],
            'rejected' => [CommentState::REJECTED->value],
        ];
    }

    protected function loginAsUser(): void
    {
        $payload = [
            'email'    => 'user@example.com',
            'password' => 'password123'
        ];

        $result = $this->withBodyFormat('json')
            ->post('/api/v1/auth/login', $payload);

        $json = json_decode($result->getJSON());

        $this->token = $json->token ?? null;
    }

    protected function loginAsModerator(): void
    {
        $provider = auth()->getProvider();

        $plainPassword = 'password123';

        $user = new User([
            'email'    => 'moderator@example.com',
            'password' => $plainPassword,
            'username' => 'moderator',
        ]);

        $provider->save($user);

        $moderator = $provider->findById($provider->getInsertID());

        $moderator->addPermission('comments.manage');

        $result = $this->withBodyFormat('json')
            ->post('/api/v1/auth/login', [
                'email'    => $moderator->email,
                'password' => $plainPassword,
            ]);

        $json = json_decode($result->getJSON());

        $this->token = $json->token ?? null;
    }

    protected function createTestComment(): int
    {
        $result = $this->withHeaders($this->getHeaders())
            ->withBodyFormat('json')
            ->post('/api/v1/comments', [
                'post_id' => $this->post->id,
                'content' => '테스트 댓글'
            ]);

        $json = json_decode($result->getJSON());

        $this->assertNotNull($json->data->id ?? null, 'createTestComment failed: ' . $result->getJSON());

        return (int)$json->data->id;
    }


    /**
     * 단순 3단 트리
     *
     * @return array{root: CommentEntity, child: CommentEntity, grandchild: CommentEntity}
     */
    protected function createSimpleTree(int $postId): array
    {
        $fab = new Fabricator(CommentModel::class);

        $fab->setOverrides([
            'post_id'   => $postId,
            'parent_id' => null,
            'state'     => CommentState::APPROVED->value
        ]);

        /** @var CommentEntity $root */
        $root = $fab->create();

        $fab->setOverrides([
            'post_id'   => $postId,
            'parent_id' => $root->id,
            'state'     => CommentState::APPROVED->value
        ]);

        /** @var CommentEntity $child */
        $child = $fab->create();

        $fab->setOverrides([
            'post_id'   => $postId,
            'parent_id' => $child->id,
            'state'     => CommentState::APPROVED->value
        ]);

        /** @var CommentEntity $grandchild */
        $grandchild = $fab->create();

        return ['root' => $root, 'child' => $child, 'grandchild' => $grandchild];
    }

    /**
     * 깊은 사슬
     *
     * @return array ['nodes' => CommentEntity[]]
     */
    protected function createDeepChain(int $postId, int $depth): array
    {
        $parentId = null;
        /** @var CommentEntity[] $nodes */
        $nodes = [];

        $fab = new Fabricator(CommentModel::class);

        for ($i = 0; $i < $depth; $i++) {
            $fab->setOverrides(['post_id' => $postId, 'parent_id' => $parentId, 'state' => CommentState::APPROVED->value]);

            /** @var CommentEntity $node */
            $node = $fab->create();
            $nodes[] = $node;

            $parentId = $node->id;
        }

        return ['nodes' => $nodes];
    }

    /**
     * 혼합 시나리오
     *
     * @return array{approved: CommentEntity, pending: CommentEntity, another_post: CommentEntity}
     */
    protected function createMixedScenario(int $postId): array
    {
        $fab = new Fabricator(CommentModel::class);

        /** @var CommentEntity $approved */
        $approved = $fab->setOverrides(['post_id' => $postId, 'state' => CommentState::APPROVED->value])->create();
        /** @var CommentEntity $pending */
        $pending = $fab->setOverrides(['post_id' => $postId, 'state' => CommentState::PENDING->value])->create();

        /** @var PostEntity $otherPost */
        $otherPost = (new Fabricator(PostModel::class))->create();

        /** @var CommentEntity $anotherPost */
        $anotherPost = $fab->setOverrides(['post_id' => $otherPost->id, 'state' => CommentState::APPROVED->value])->create();

        return ['approved' => $approved, 'pending' => $pending, 'another_post' => $anotherPost];
    }
}

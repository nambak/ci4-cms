<?php

namespace Tests\Feature;

use App\Database\Seeds\TestSeeder;
use App\Enums\CommentState;
use App\Enums\PostState;
use App\Models\CategoryModel;
use App\Models\CommentModel;
use App\Models\PostModel;
use App\Models\TenantModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

class TenantPostsShowTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $refresh = true;
    protected $namespace = null;
    protected $seed = TestSeeder::class;
    protected $tenant;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = fake(TenantModel::class, ['subdomain' => 'acme', 'name' => 'Acme']);
        $this->category = fake(CategoryModel::class, ['tenant_id' => $this->tenant->id]);
        service('tenant')->setTenant($this->tenant);
    }

    /**
     * @test
     * 발행 포스트 → 200 + 제목/본문/날짜 노출 - Happy Path.
     */
    public function test_renders_published_post(): void
    {
        $post = $this->createPost();

        $path = $this->getPath(tenant: $this->tenant, post: $post);
        $response = $this->get(path: $path);

        $response->assertStatus(200);
        $response->assertSee($post->title);
        $response->assertSee('본문 내용');
        $response->assertSee(date('Y-m-d', strtotime($post->created_at)));
    }

    /**
     * @test
     * 존재하지 않는 slug → 404
     */
    public function test_returns_404_for_unknown_slug(): void
    {
        $this->expectException(PageNotFoundException::class);
        $this->get("/{$this->tenant->subdomain}/posts/unknown-slug");
    }

    /**
     * @test
     * DRAFT 상태 → 404 (상태 격리)
     */
    public function test_returns_404_for_draft_post(): void
    {
        $post = $this->createPost(PostState::DRAFT->value);

        $path = $this->getPath(tenant: $this->tenant, post: $post);

        $this->expectException(PageNotFoundException::class);
        $this->get(path: $path);
    }

    /**
     * @test
     * B사 slug를 A사 URL로 접근 → 404 (테넌트 격리)
     */
    public function test_isolates_other_tenant_post(): void
    {
        $otherTenant = fake(TenantModel::class, ['subdomain' => 'other-co']);
        $otherCategory = fake(CategoryModel::class, ['tenant_id' => $otherTenant->id]);
        $otherPost = fake(PostModel::class, [
            'tenant_id'   => $otherTenant->id,
            'category_id' => $otherCategory->id,
            'title'       => 'Other Post Title',
            'state'       => PostState::PUBLISHED->value,
            'content'     => '이것은 다른 테넌트의 게시물입니다.'
        ]);

        $otherPath = $this->getPath(tenant: $otherTenant, post: $otherPost);

        $response = $this->get(path: $otherPath);
        $response->assertStatus(200);
        $response->assertSee('Other Post Title');

        $path = $this->getPath(tenant: $this->tenant, post: $otherPost);

        $this->expectException(PageNotFoundException::class);
        $this->get(path: $path);
    }

    /**
     * @test
     * XSS 공격 문자열이 본문에 포함되어도 제대로 렌더링
     */
    public function test_escapes_xss_in_content(): void
    {
        $post = fake(PostModel::class, [
            'tenant_id'   => $this->tenant->id,
            'category_id' => $this->category->id,
            'title'       => 'Test Post Title',
            'state'       => PostState::PUBLISHED->value,
            'content'     => '<script>alert(1)</script>안전한본문'
        ]);

        $path = $this->getPath(tenant: $this->tenant, post: $post);

        $response = $this->get(path: $path);

        $response->assertStatus(200);
        $response->assertDontSee('<script>alert(1)</script>');
        $response->assertSee('&lt;script&gt;');
        $response->assertSee('안전한본문');
    }

    /**
     * @test
     * 승인된 댓글이 보여야 함
     */
    public function test_displays_approved_comment(): void
    {
        $post = $this->createPost();
        $path = $this->getPath(tenant: $this->tenant, post: $post);

        fake(CommentModel::class, [
            'post_id' => $post->id,
            'content' => '승인된 댓글입니다',
            'state'   => CommentState::APPROVED->value,
        ]);

        $response = $this->get(path: $path);

        $response->assertStatus(200);
        $response->assertSee('승인된 댓글입니다');
    }

    /**
     * @test
     * 미승인 댓글 숨김
     */
    public function test_hides_non_approved_comments(): void
    {
        $post = $this->createPost();
        $path = $this->getPath(tenant: $this->tenant, post: $post);

        fake(CommentModel::class, [
            'post_id' => $post->id,
            'content' => '승인된 댓글입니다',
            'state'   => CommentState::APPROVED->value,
        ]);

        fake(CommentModel::class, [
            'post_id' => $post->id,
            'content' => '미승인된 댓글입니다',
            'state'   => CommentState::PENDING->value,
        ]);

        $response = $this->get(path: $path);

        $response->assertStatus(200);
        $response->assertSee('승인된 댓글입니다');
        $response->assertDontSee('미승인된 댓글입니다');
    }

    /**
     * @test
     * 댓글이 없을 때 안내문구 표시
     */
    public function test_shows_placeholder_when_no_comments(): void
    {
        $post = $this->createPost();
        $path = $this->getPath(tenant: $this->tenant, post: $post);

        $response = $this->get(path: $path);

        $response->assertStatus(200);
        $response->assertSee('아직 댓글이 없습니다.');
    }

    /**
     * @test
     * 댓글 재귀 렌더링 테스트
     */
    public function test_displays_nested_reply(): void
    {
        $post = $this->createPost();
        $path = $this->getPath(tenant: $this->tenant, post: $post);

        $rootComment = fake(CommentModel::class, [
            'post_id' => $post->id,
            'parent_id' => null,
            'content' => '첫 번째 댓글입니다',
            'state'   => CommentState::APPROVED->value,
        ]);

        $childComment = fake(CommentModel::class, [
            'post_id' => $post->id,
            'parent_id' => $rootComment->id,
            'content' => '첫 번째 댓글의 대댓글입니다',
            'state'   => CommentState::APPROVED->value,
        ]);

        fake(CommentModel::class, [
            'post_id' => $post->id,
            'parent_id' => $childComment->id,
            'content' => '첫 번째 대댓글의 대대댓글입니다',
            'state'   => CommentState::APPROVED->value,
        ]);

        $response = $this->get(path: $path);

        $response->assertStatus(200);
        $response->assertSee('첫 번째 댓글입니다');
        $response->assertSee('첫 번째 댓글의 대댓글입니다');
        $response->assertSee('첫 번째 대댓글의 대대댓글입니다');
    }
// helper methods
    private function createPost(string $state = PostState::PUBLISHED->value): object
    {
        return fake(PostModel::class, [
            'tenant_id'   => $this->tenant->id,
            'category_id' => $this->category->id,
            'title'       => 'Test Post Title',
            'state'       => $state,
            'content'     => '이것은 본문 내용입니다.'
        ]);
    }


    private function getPath(object $tenant, object $post): string
    {
        return "/{$tenant->subdomain}/posts/{$post->slug}";
    }
}

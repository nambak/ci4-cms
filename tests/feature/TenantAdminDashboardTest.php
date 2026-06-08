<?php

namespace Tests\Feature;

use App\Enums\CommentState;
use App\Enums\PostState;
use App\Models\CategoryModel;
use App\Models\CommentModel;
use App\Models\PostModel;
use App\Models\TenantModel;
use App\Models\UserModel;
use CodeIgniter\I18n\Time;
use CodeIgniter\Shield\Test\AuthenticationTesting;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\Fabricator;
use CodeIgniter\Test\FeatureTestTrait;

class TenantAdminDashboardTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;
    use AuthenticationTesting;

    protected $refresh = true;
    protected $namespace = null;

    /**
     * @test 대시보드 테스트
     */
    public function test_dashboard_loads_post_count_is_correct(): void
    {
        // Given:
        $postCount = 5;
        $tenant = $this->createTenant('acme');
        $category = $this->createCategory($tenant);
        $adminUser = $this->createUser($tenant);

        $this->actingAs($adminUser);

        $this->createPost($tenant, $category, $adminUser, '대시보드 테스트용 포스트', $postCount);

        // When:
        $response = $this->get("{$tenant->subdomain}/admin");

        // Then:
        $response->assertStatus(200);
        $response->assertSee((string)$postCount, 'div[data-testid=stat-posts]');
    }

    /**
     * @test 대시보드 격리 테스트
     */
    public function test_isolate_dashboard(): void
    {
        // Given:
        $tenantA = $this->createTenant('acme');
        $tenantB = $this->createTenant('example');

        $categoryA = $this->createCategory($tenantA);
        $categoryB = $this->createCategory($tenantB);

        $postCountA = 5;
        $postCountB = 3;

        $userA = $this->createUser($tenantA);
        $userB = $this->createUser($tenantB);

        $this->createPost($tenantA, $categoryA, $userA, 'A사 포스트', $postCountA);
        $this->createPost($tenantB, $categoryB, $userB, 'B사 포스트', $postCountB);

        // When:
        $this->actingAs($userA);

        $response = $this->get("{$tenantA->subdomain}/admin");

        // Then:
        $response->assertStatus(200);
        $response->assertSee((string)$postCountA, 'div[data-testid=stat-posts]');
        $response->assertDontSee((string)($postCountA + $postCountB), 'div[data-testid=stat-posts]');
    }

    /**
     * @test 대시보드 최근 게시물 로드 테스트
     */
    public function test_dashboard_loads_recent_posts(): void
    {
        // Given:
        $tenant = $this->createTenant('acme');
        $category = $this->createCategory($tenant);
        $adminUser = $this->createUser($tenant);

        $this->actingAs($adminUser);

        $title = '나의 테스트 포스트';

        $this->createPost($tenant, $category, $adminUser, $title);

        // When:
        $response = $this->get("{$tenant->subdomain}/admin");

        // Then:
        $response->assertStatus(200);
        $response->assertSee($title, 'div[data-testid=widget-recent-posts]');
    }

    /**
     * @test 대시보드 최근 댓글 로드 테스트
     */
    public function test_dashboard_loads_recent_comments(): void
    {
        // Given:
        $tenant = $this->createTenant('acme');
        $category = $this->createCategory($tenant);
        $adminUser = $this->createUser($tenant);

        $this->actingAs($adminUser);

        $title = '나의 테스트 포스트';

        $post = $this->createPost($tenant, $category, $adminUser, $title);

        $this->createComments($post[0], CommentState::APPROVED->value);

        // When:
        $response = $this->get("{$tenant->subdomain}/admin");

        // Then:
        $response->assertStatus(200);
        $response->assertSee($title, 'div[data-testid=widget-recent-comments]');
    }

    /**
     * @test 대시보드 인기 포스트 로드 테스트
     */
    public function test_dashboard_loads_popular_post(): void
    {
        // Given:
        $tenant = $this->createTenant('acme');
        $category = $this->createCategory($tenant);
        $adminUser = $this->createUser($tenant);

        $this->actingAs($adminUser);

        $popularPost = $this->createPost($tenant, $category, $adminUser, '인기글');
        $this->createComments($popularPost[0], CommentState::APPROVED->value);

        $this->createPost($tenant, $category, $adminUser, '조용한 글');


        // When:
        $response = $this->get("{$tenant->subdomain}/admin");

        // Then:
        $response->assertStatus(200);
        $response->assertSee('인기글', 'div[data-testid=widget-popular-posts]');
        $response->assertDontSee('조용한 글', 'div[data-testid=widget-popular-posts]');
    }

    /**
     * @test 최근 댓글 미승인 제외
     */
    public function test_dashboard_loads_recent_comment_without_pending(): void
    {
        // Given:
        $tenant = $this->createTenant('test');
        $category = $this->createCategory($tenant);
        $adminUser = $this->createUser($tenant);
        $this->actingAs($adminUser);

        $popularPost = $this->createPost($tenant, $category, $adminUser, '인기글');
        $this->createComments($popularPost[0], CommentState::APPROVED->value);

        $pendingPost = $this->createPost($tenant, $category, $adminUser, '미승인 댓글');
        $this->createComments($pendingPost[0], CommentState::PENDING->value);

        // When:
        $response = $this->get("{$tenant->subdomain}/admin");

        // Then:
        $response->assertStatus(200);
        $response->assertSee('인기글', 'div[data-testid=widget-recent-comments]');
        $response->assertDontSee('미승인 댓글', 'div[data-testid=widget-recent-comments]');
    }

    /**
     * @test draft 포스트 댓글 제외
     */
    public function test_dashboard_loads_recent_comment_without_draft_post_comments(): void
    {
        // Given:
        $tenant = $this->createTenant('test');
        $adminUser = $this->createUser($tenant);
        $category = $this->createCategory($tenant);
        $this->actingAs($adminUser);

        $publishedPost = $this->createPost($tenant, $category, $adminUser, '최근글');
        $draftPost = $this->createPost($tenant, $category, $adminUser, '임시저장글', 1, PostState::DRAFT->value);

        $this->createComments($publishedPost[0], CommentState::APPROVED->value);
        $this->createComments($draftPost[0], CommentState::APPROVED->value);

        // When:
        $response = $this->get("{$tenant->subdomain}/admin");

        // Then:
        $response->assertStatus(200);
        $response->assertSee('최근글', 'div[data-testid=widget-recent-comments]');
        $response->assertDontSee('임시저장글', 'div[data-testid=widget-recent-comments]');
    }

    /**
     * @test 소프트 삭제 댓글은 인기 포스트 댓글 수에서 제외
     */
    public function test_dashboard_loads_soft_deleted_comments_excluded(): void
    {
        // Given:
        $tenant = $this->createTenant('acme');
        $category = $this->createCategory($tenant);
        $adminUser = $this->createUser($tenant);
        $this->actingAs($adminUser);

        $post = $this->createPost($tenant, $category, $adminUser, '인기글', 1, PostState::PUBLISHED->value);
        $comment = $this->createComments($post[0], CommentState::APPROVED->value, 2);

        model(CommentModel::class)->delete($comment[0]->id);

        // When:
        $response = $this->get("{$tenant->subdomain}/admin");

        // Then:
        $response->assertStatus(200);
        $response->assertSee('1', 'div[data-testid=widget-popular-posts]');
    }

    /**
     * @test 대시보드 활동 추이 차트 렌더
     */
    public function test_dashboard_renders_activity_trend_chart(): void
    {
        // Given:
        cache()->clean();

        $tenant = $this->createTenant('acme');
        $category = $this->createCategory($tenant);
        $user = $this->createUser($tenant);

        $this->actingAs($user);

        $this->createPost($tenant, $category, $user, '추이 테스트', 7);

        // When:
        $response = $this->get("{$tenant->subdomain}/admin");

        // Then:
        $response->assertStatus(200);
        $response->assertSee('chart-activity-trend');
        $response->assertSee(date('Y-m-d'));
    }

    /**
     * @test 활동 추이 30일 0 체움
     */
    public function test_dashboard_trend_fills_zero_for_empty_days(): void
    {
        // Given:
        cache()->clean();

        $tenant = $this->createTenant('acme');
        $category = $this->createCategory($tenant);
        $user = $this->createUser($tenant);

        $this->actingAs($user);

        $this->createPost($tenant, $category, $user, '오늘 포스트', 1);

        $emptyDay = Time::now()->subDays(29)->format('Y-m-d');

        // When:
        $response = $this->get("{$tenant->subdomain}/admin");

        // Then:
        $response->assertStatus(200);
        $response->assertSee($emptyDay);
    }

    /**
     * helper methods
     */
    private function createTenant($subdomain): object
    {
        return fake(TenantModel::class, ['subdomain' => $subdomain]);
    }

    private function createCategory($tenant): object
    {
        return fake(CategoryModel::class, ['tenant_id' => $tenant->id]);
    }

    private function createPost($tenant, $category, $user, $title, $count = 1, $state = PostState::PUBLISHED->value): array
    {
        $fabricator = new Fabricator(PostModel::class);
        $fabricator->setOverrides([
            'tenant_id'   => $tenant->id,
            'category_id' => $category->id,
            'writer_id'   => $user->id,
            'state'       => $state,
            'title'       => $title
        ]);

        return $fabricator->create($count);
    }

    private function createUser($tenant): object
    {
        return fake(UserModel::class, ['tenant_id' => $tenant->id])->addGroup('admin');
    }

    private function createComments($post, $state, $count = 1): array
    {
        $comments = [];

        for ($i = 0; $i < $count; $i++) {
            $comments[] = fake(CommentModel::class, [
                'post_id' => $post->id,
                'state'   => $state,
            ]);
        }

        return $comments;
    }
}

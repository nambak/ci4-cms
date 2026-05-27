<?php

namespace Tests\Feature;

use App\Database\Seeds\TestSeeder;
use App\Enums\PostState;
use App\Models\CategoryModel;
use App\Models\PostModel;
use App\Models\TenantModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

class TenantCategoryPostsTest extends CIUnitTestCase
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
        $this->category = $this->createCategory('news');

        service('tenant')->setTenant($this->tenant);
    }

    /**
     * @test
     * 카테고리 + published 포스트 표시
     */
    public function test_shows_category_posts(): void
    {
        // Given:
        $this->createPost();

        // When:
        $response = $this->get($this->getApiUrl());

        // Then:
        $response->assertStatus(200);
        $response->assertSee('News');
        $response->assertSee('Test Post Title');
    }

    /**
     * @test
     * 미존재 카테고리 slug → 404
     */
    public function test_returns_404_for_missing_category(): void
    {
        // Given: setUp이 이미 'news' 카테고리 만들어둠

        // When:
        $this->expectException(PageNotFoundException::class);

        $this->get($this->getApiUrl('unknown'));
    }

    /**
     * @test
     * 다른 테넌트의 카테고리 slug → 404
     */
    public function test_isolates_categories_by_tenant(): void
    {
        // Given:
        $otherTenant = fake(TenantModel::class, ['subdomain' => 'bravo', 'name' => 'Bravo']);
        $otherCategory = $this->createCategory('news', $otherTenant->id);
        fake(PostModel::class, [
            'tenant_id'   => $otherTenant->id,
            'category_id' => $otherCategory->id,
            'title'       => 'Bravo 포스트',
            'state'       => PostState::PUBLISHED->value,
            'content'     => '다른 포스트 내용입니다.'
        ]);

        $this->createPost('Acme 포스트');

        // When:
        $response = $this->get($this->getApiUrl());

        // Then:
        $response->assertStatus(200);
        $response->assertSee('Acme 포스트');
        $response->assertDontSee('Bravo 포스트');
    }

    /**
     * @test
     * draft 포스트 미표시
     */
    public function test_lists_only_published_post(): void
    {
        // Given:
        $this->createPost('Draft 포스트', $this->category->id, PostState::DRAFT->value);
        $this->createPost('Published 포스트', $this->category->id, PostState::PUBLISHED->value);

        // When:
        $response = $this->get($this->getApiUrl());

        // Then:
        $response->assertStatus(200);
        $response->assertSee('Published 포스트');
        $response->assertDontSee('Draft 포스트');
    }

    /**
     * @test
     * 빈 카테고리 placeholder 표시
     */
    public function test_lists_placeholder(): void
    {
        // Given: 포스트 생성안함.

        // When:
        $response = $this->get($this->getApiUrl());

        // Then:
        $response->assertStatus(200);
        $response->assertSee('아직 발행된 포스트가 없습니다.');
    }

    /**
     * @test
     * 같은 tenant 내 category 격리 테스트
     */
    public function test_isolates_same_tenant(): void
    {
        // Given:
        $this->createPost('News 포스트');

        $otherCategory = $this->createCategory('sport');
        $this->createPost('Sports 포스트', $otherCategory->id);

        // When:
        $response = $this->get($this->getApiUrl());

        // Then:
        $response->assertStatus(200);
        $response->assertSee('News 포스트');
        $response->assertDontSee('Sports 포스트');
    }

    // helper methods
    private function createPost(string $title = 'Test Post Title', ?int $categoryId = null, ?string $state = null): object
    {
        return fake(PostModel::class, [
            'tenant_id'   => $this->tenant->id,
            'category_id' => $categoryId ?? $this->category->id,
            'title'       => $title,
            'state'       => $state ?? PostState::PUBLISHED->value,
            'content'     => '이것은 본문 내용입니다.'
        ]);
    }

    private function createCategory(string $slug, ?int $tenantId = null): object
    {
        return fake(CategoryModel::class, [
            'tenant_id' => $tenantId ?? $this->tenant->id,
            'name'      => ucfirst($slug),
            'slug'      => $slug,
        ]);
    }

    private function getApiUrl($categorySlug = null): string
    {
        return "/{$this->tenant->subdomain}/categories/" . ($categorySlug ?? $this->category->slug);
    }
}

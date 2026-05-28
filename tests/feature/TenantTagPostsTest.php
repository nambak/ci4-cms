<?php

namespace Tests\Feature;

use App\Database\Seeds\TestSeeder;
use App\Enums\PostState;
use App\Models\CategoryModel;
use App\Models\PostModel;
use App\Models\TagModel;
use App\Models\TenantModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

class TenantTagPostsTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $refresh = true;
    protected $namespace = null;
    protected $seed = TestSeeder::class;
    protected $tenant;
    protected $category;
    protected $tag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = fake(TenantModel::class, ['subdomain' => 'acme', 'name' => 'Acme']);
        $this->category = $this->createCategory();
        $this->tag = fake(TagModel::class, [
            'tenant_id' => $this->tenant->id,
            'name'      => 'Php',
            'slug'      => 'php'
        ]);
    }

    /**
     * @test
     * 태그 + 공개 포스트 표시
     */
    public function test_shows_tag_posts(): void
    {
        // Given:
        $this->createPost(tagId: $this->tag->id);

        // When:
        $response = $this->get($this->getApiUrl());

        // Then:
        $response->assertStatus(200);
        $response->assertSee('Php');
        $response->assertSee('Test post title');
    }

    /**
     * @test
     * 미존재 태그 slug → 404
     */
    public function test_returns_404_for_missing_tag(): void
    {
        // Given: setUp에서 이미 Tag 생성함.

        // When:
        $this->expectException(PageNotFoundException::class);

        $this->get($this->getApiUrl('nonexistent-tag-slug'));
    }

    /**
     * @test
     * 테넌트 격리
     */
    public function test_isolates_tag_by_tenant(): void
    {
        // Given:
        $otherTenant = fake(TenantModel::class, ['subdomain' => 'charlie', 'name' => 'Charlie']);

        $otherCategory = $this->createCategory($otherTenant->id);

        $otherTag = fake(TagModel::class, [
            'tenant_id' => $otherTenant->id,
            'slug'      => 'php',
            'name'      => 'PHP',
        ]);

        $this->createPost(
            title: 'charlie post title',
            categoryId: $otherCategory->id,
            tagId: $otherTag->id,
            tenantId: $otherTenant->id
        );

        $this->createPost('acme post title', tagId: $this->tag->id);

        // When:
        $response = $this->get($this->getApiUrl());

        // Then:
        $response->assertStatus(200);
        $response->assertDontSee('charlie post title');
        $response->assertSee('acme post title');

    }

    /**
     * @test
     * 같은 테넌트 내 다른 태그 격리
     */
    public function test_same_tenant_different_tag_isolation(): void
    {
        // Given:
        $jsTag = $this->createTag('javascript');

        $this->createPost('php post', tagId: $this->tag->id);
        $this->createPost('javascript post', tagId: $jsTag->id);

        // When:
        $response = $this->get($this->getApiUrl('php'));

        // Then:
        $response->assertStatus(200);
        $response->assertSee('php post');
        $response->assertDontSee('javascript post');
    }

    /**
     * @test
     * 미공개 포스트는 리스트에서 제외
     */
    public function test_lists_only_published_post(): void
    {
        // Given:
        $this->createPost('php post', tagId: $this->tag->id);
        $this->createPost('python post', state: PostState::DRAFT->value, tagId: $this->tag->id);

        // When:
        $response = $this->get($this->getApiUrl());

        // Then:
        $response->assertStatus(200);
        $response->assertSee('php post');
        $response->assertDontSee('python post');
    }

    /**
     * @test
     * 태그 이름으로 생성된 포스트가 없는 경우 placeholder를 표시
     */
    public function test_lists_placeholder(): void
    {
        // Given
        $this->createTag('javascript');

        // When:
        $response = $this->get($this->getApiUrl('javascript'));

        // Then:
        $response->assertStatus(200);
        $response->assertSee('아직 발행된 포스트가 없습니다.');
    }

    /**
     * @test
     * 다중 태그를 가진 포스트를 생성하고, 해당 태그로 검색했을 때 포스트가 표시되는지 확인
     */
    public function test_post_with_multiple_tags(): void
    {
        // Given:
        $jsTag = $this->createTag('javascript');
        $post = $this->createPost(title: 'Multi-tag post', tagId: $this->tag->id);

        model(PostModel::class)->syncTags($post->id, [$this->tag->id, $jsTag->id], $this->tenant->id);

        // When:
        $response = $this->get($this->getApiUrl('php'));

        // Then:
        $response->assertStatus(200);
        $response->assertSee('Multi-tag post');

        // When:
        $response = $this->get($this->getApiUrl('javascript'));

        // Then:
        $response->assertStatus(200);
        $response->assertSee('Multi-tag post');
    }

    // helper method
    private function createPost(string  $title = 'Test post title',
                                ?int    $categoryId = null,
                                ?string $state = null,
                                ?int    $tagId = null,
                                ?int    $tenantId = null): object
    {
        $post = fake(PostModel::class, [
            'tenant_id'   => $tenantId ?? $this->tenant->id,
            'category_id' => $categoryId ?? $this->category->id,
            'title'       => $title,
            'state'       => $state ?? PostState::PUBLISHED->value,
        ]);

        if ($tagId !== null) {
            model(PostModel::class)->syncTags($post->id, [$tagId], $tenantId ?? $this->tenant->id);
        }

        return $post;
    }

    private function getApiUrl($tagSlug = null): string
    {
        return "/{$this->tenant->subdomain}/tags/" . ($tagSlug ?? $this->tag->slug);
    }

    private function createCategory($tenantId = null): object
    {
        return fake(CategoryModel::class, [
            'tenant_id' => $tenantId ?? $this->tenant->id,
            'name'      => 'Test',
            'slug'      => 'test'
        ]);
    }

    private function createTag($tagSlug): object
    {
        return fake(TagModel::class, [
            'tenant_id' => $this->tenant->id,
            'slug'      => $tagSlug,
            'name'      => ucfirst($tagSlug)
        ]);
    }
}

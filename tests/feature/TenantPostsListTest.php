<?php

namespace Tests\Feature;

use App\Database\Seeds\TestSeeder;
use App\Enums\PostState;
use App\Models\CategoryModel;
use App\Models\PostModel;
use App\Models\TenantModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

class TenantPostsListTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $refresh = true;
    protected $namespace = null;
    protected $seed = TestSeeder::class;

    public function test_lists_only_published_posts_of_current_tenant(): void
    {
        $tenant = fake(TenantModel::class, ['subdomain' => 'acme', 'name' => 'Acme']);
        service('tenant')->setTenant($tenant);
        $category = fake(CategoryModel::class, ['tenant_id' => $tenant->id]);

        fake(PostModel::class, [
            'tenant_id'   => $tenant->id,
            'category_id' => $category->id,
            'title'       => '공개 포스트',
            'state'       => PostState::PUBLISHED->value,
        ]);

        fake(PostModel::class, [
            'tenant_id'   => $tenant->id,
            'category_id' => $category->id,
            'title'       => '비공개 포스트',
            'state'       => PostState::DRAFT->value,
        ]);

        $result = $this->get("/{$tenant->subdomain}/posts");

        $result->assertStatus(200);
        $result->assertSee('공개 포스트');
        $result->assertDontSee('비공개 포스트');
    }

    public function test_isolates_other_tenant_posts(): void
    {
        $tenantA = fake(TenantModel::class, ['subdomain' => 'a-co']);
        $tenantB = fake(TenantModel::class, ['subdomain' => 'b-co']);

        service('tenant')->setTenant($tenantA);
        $categoryA = fake(CategoryModel::class, ['tenant_id' => $tenantA->id]);
        fake(PostModel::class, [
            'tenant_id'   => $tenantA->id,
            'category_id' => $categoryA->id,
            'title'       => 'A사 포스트',
            'state'       => PostState::PUBLISHED->value,
        ]);

        service('tenant')->setTenant($tenantB);
        $categoryB = fake(CategoryModel::class, ['tenant_id' => $tenantB->id]);
        fake(PostModel::class, [
            'tenant_id'   => $tenantB->id,
            'category_id' => $categoryB->id,
            'title'       => 'B사 포스트',
            'state'       => PostState::PUBLISHED->value,
        ]);

        $result = $this->get("/{$tenantA->subdomain}/posts");

        $result->assertStatus(200);
        $result->assertSee('A사 포스트');
        $result->assertDontSee('B사 포스트');
    }

    public function test_empty_list_renders_placeholder(): void
    {
        $tenant = fake(TenantModel::class, ['subdomain' => 'empty-co']);

        $result = $this->get("/{$tenant->subdomain}/posts");

        $result->assertStatus(200);
        $result->assertSee('아직 발행된 포스트가 없습니다');
    }
}

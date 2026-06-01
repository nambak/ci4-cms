<?php

namespace Tests\Feature;

use App\Models\CategoryModel;
use App\Models\PostModel;
use App\Models\TenantModel;
use App\Models\UserModel;
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

        $fabricator = new Fabricator(PostModel::class);
        $fabricator->setOverrides([
            'tenant_id'   => $tenant->id,
            'category_id' => $category->id,
        ]);

        $fabricator->create($postCount);

        // When:
        $response = $this->get("{$tenant->subdomain}/admin");

        // Then:
        $response->assertStatus(200);
        $response->assertSee((string)$postCount);
    }

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

        $this->createPost($tenantA, $categoryA, $userA, $postCountA);
        $this->createPost($tenantB, $categoryB, $userB, $postCountB);

        // When:
        $this->actingAs($userA);

        $response = $this->get("{$tenantA->subdomain}/admin");

        // Then:
        $response->assertStatus(200);
        $response->assertSee('5', 'div[data-testid=stat-posts]');
        $response->assertDontSee('8', 'div[data-testid=stat-posts]');
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

    private function createPost($tenant, $category, $user, $count): void
    {
        $fabricator = new Fabricator(PostModel::class);
        $fabricator->setOverrides([
            'tenant_id'   => $tenant->id,
            'category_id' => $category->id,
            'writer_id'   => $user->id,
        ]);

        $fabricator->create($count);
    }

    private function createUser($tenant): object
    {
        return fake(UserModel::class, ['tenant_id' => $tenant->id])->addGroup('admin');
    }
}
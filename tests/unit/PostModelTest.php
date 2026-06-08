<?php

namespace Tests\Unit;

use App\Models\CategoryModel;
use App\Models\PostModel;
use App\Models\TenantModel;
use App\Models\UserModel;
use CodeIgniter\I18n\Time;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class PostModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $DBGroup = 'tests';
    protected $namespace = null;
    protected $model;
    protected $tenant;
    protected $category;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new PostModel($this->db);
        $this->tenant = fake(TenantModel::class, ['subdomain' => 'acme', 'name' => 'Acme']);
        $this->category = fake(CategoryModel::class, ['tenant_id' => $this->tenant->id]);
        $this->user = fake(UserModel::class, ['tenant_id' => $this->tenant->id]);
    }

    /**
     * @test 날짜별 포스트 집계
     */
    public function test_post_daily_count_by_tenant(): void
    {
        // Given:
        $today = Time::now();
        $yesterday = $today->subDays(1);
        $twoDaysAgo = $today->subDays(2);

        $this->createPost($today);
        $this->createPost($yesterday);
        $this->createPost($twoDaysAgo);

        // When:
        $result = $this->model->dailyCountsByTenant($this->tenant->id, 30);

        // Then:
        $this->assertArrayHasKey($today->format('Y-m-d'), $result);
        $this->assertEquals(1, $result[$today->format('Y-m-d')]);
        $this->assertCount(3,  $result);
    }

    private function createPost($createdAt)
    {
        $post = fake(PostModel::class, [
            'tenant_id'   => $this->tenant->id,
            'category_id' => $this->category->id,
            'writer_id'   => $this->user->id,
        ]);

        $this->db->table('posts')
            ->where('id', $post->id)
            ->update(['created_at' => $createdAt->toDateTimeString()]);

        return $post;
    }
}

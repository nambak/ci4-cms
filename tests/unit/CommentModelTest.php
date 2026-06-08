<?php

namespace Tests\Unit;

use App\Models\CategoryModel;
use App\Models\CommentModel;
use App\Models\PostModel;
use App\Models\TenantModel;
use App\Models\UserModel;
use CodeIgniter\I18n\Time;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class CommentModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $DBGroup = 'tests';
    protected $namespace = null;
    protected $tenant;
    protected $category;
    protected $user;
    protected $post;
    protected $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new CommentModel($this->db);
        $this->tenant = fake(TenantModel::class, ['subdomain' => 'acme', 'name' => 'Acme']);
        $this->category = fake(CategoryModel::class, ['tenant_id' => $this->tenant->id]);
        $this->user = fake(UserModel::class, ['tenant_id' => $this->tenant->id]);
        $this->post = fake(PostModel::class, [
            'tenant_id'   => $this->tenant->id,
            'category_id' => $this->category->id,
            'writer_d'    => $this->user->id
        ]);
    }

    /**
     * @test 날짜별 집계
     */
    public function test_comment_daily_count(): void
    {
        // Given:
        $today = Time::now();
        $yesterday = $today->subDays(1);
        $twoDaysAgo = $today->subDays(2);

        $this->createComment($today);
        $this->createComment($yesterday);
        $this->createComment($twoDaysAgo);

        // When:
        $result = $this->model->dailyCountsByTenant($this->tenant->id, 30);

        // Then:
        $this->assertArrayHasKey($today->format('Y-m-d'), $result);
        $this->assertEquals(1, $result[$today->format('Y-m-d')]);
        $this->assertCount(3, $result);
    }

    /**
     * @test 날짜별 집계 sofeDelete 제외
     */
    public function test_comment_daily_count_without_soft_delete(): void
    {
        // Given:
        $today = Time::now();
        $yesterday = $today->subDays(1);
        $twoDaysAgo = $today->subDays(2);

        $keep = $this->createComment($today);
        $delete = $this->createComment($today);
        $this->createComment($yesterday);
        $this->createComment($twoDaysAgo);

        $this->model->delete($delete->id);

        // When:
        $result = $this->model->dailyCountsByTenant($this->tenant->id, 30);

        // Then:
        $this->assertArrayHasKey($today->format('Y-m-d'), $result);
        $this->assertEquals(1, $result[$today->format('Y-m-d')]);
        $this->assertCount(3, $result);
    }

    /**
     * helper method
     */
    private function createComment($createdAt): object
    {
        $comment = fake(CommentModel::class, [
            'post_id'   => $this->post->id,
            'writer_id' => $this->user->id
        ]);

        $this->db->table('comments')
            ->where('id', $comment->id)
            ->update(['created_at' => $createdAt->toDateTimeString()]);

        return $comment;
    }
}

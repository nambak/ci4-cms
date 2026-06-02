<?php

namespace App\Models;

use App\Entities\CommentEntity;
use App\Enums\CommentState;
use CodeIgniter\Model;
use CodeIgniter\Test\Fabricator;
use Faker\Generator;

class CommentModel extends Model
{
    protected $table = 'comments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = CommentEntity::class;
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = ['post_id', 'user_id', 'parent_id', 'content', 'state'];


    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'post_id'   => 'required|integer|is_natural_no_zero',
        'user_id'   => 'required',
        'parent_id' => 'permit_empty|integer|is_natural_no_zero',
        'content'   => 'required|min_length[1]',
        'state'     => 'required|enumValue[' . CommentState::class . ']',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function fake(Generator &$faker): array
    {
        $tenant = (new Fabricator(TenantModel::class))->create();

        $category = (new Fabricator(CategoryModel::class))
            ->setOverrides([
                'tenant_id' => $tenant->id,
            ])
            ->create();

        $post = (new Fabricator(PostModel::class))
            ->setOverrides([
                'tenant_id' => $tenant->id,
                'category_id' => $category->id,
            ])
            ->create();

        $user = (new Fabricator(UserModel::class))->create();

        return [
            'post_id'   => $post->id,
            'user_id'   => $user->id,
            'parent_id' => null,
            'content'   => $faker->paragraph,
            'state'     => CommentState::APPROVED->value,
        ];
    }

    public function findThreaded(int $postId, int $perPage = 10, int $page = 1): array
    {
        $roots = [];
        $childrenByParent = [];

        $allComments = $this->where('post_id', $postId)
            ->where('state', CommentState::APPROVED->value)
            ->orderBy('created_at', 'asc')
            ->findAll();

        foreach ($allComments as $comment) {
            if ($comment->parent_id === null) {
                $roots[] = $comment;
            } else {
                $childrenByParent[$comment->parent_id][] = $comment;
            }
        }

        $totalRoots = count($roots);

        $pagedRoots = array_slice($roots, ($page - 1) * $perPage, $perPage);

        $maxDepth = config('Comments')->maxDepth;

        foreach ($pagedRoots as $root) {
            $root->replies = $this->attachReplies($root->id, $childrenByParent, 2, $maxDepth);
        }

        $this->pager = service('pager')->store('default', $page, $perPage, $totalRoots);

        return $pagedRoots;
    }

    private function attachReplies(int $parentId, array $childrenByParent, int $depth, int $maxDepth): array
    {
        if ($depth > $maxDepth) {
            return [];
        }

        $children = $childrenByParent[$parentId] ?? [];

        foreach ($children as $child) {
            $child->replies = $this->attachReplies($child->id, $childrenByParent, $depth + 1, $maxDepth);
        }

        return $children;
    }

    /**
     * 최근 댓글
     * @param int $tenantId
     * @param int $limit
     * @return array
     */
    public function recent(int $tenantId, int $limit): array
    {
        return $this->select(['comments.*', 'posts.title as post_title', 'posts.slug as post_slug'])
            ->join('posts', 'comments.post_id = posts.id')
            ->where('posts.tenant_id', $tenantId)
            ->orderBy('comments.created_at', 'DESC')
            ->orderBy('comments.id', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}

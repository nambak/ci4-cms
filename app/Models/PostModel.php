<?php

namespace App\Models;

use Throwable;
use Faker\Generator;
use CodeIgniter\Model;
use InvalidArgumentException;
use App\Entities\PostEntity;
use App\Traits\SlugGeneratorTrait;

/**
 * @method PostEntity|null find($id = null)
 * @method PostEntity[]   findAll(int $limit = 0, int $offset = 0)
 * @method PostEntity|null first()
 */
class PostModel extends Model
{
    use SlugGeneratorTrait;

    protected $table = 'posts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = PostEntity::class;
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'tenant_id', 'category_id', 'writer_id', 'title', 'content', 'state', 'slug',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'title'   => 'required|min_length[3]|max_length[255]',
        'content' => 'required|min_length[10]',
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // callbacks
    protected $beforeInsert = ['generateSlug'];
    protected $slugSource = 'title';

    /**
     * Filtering by title and content
     *
     * @param string $keyword
     * @return $this
     */
    public function search(string $keyword): static
    {
        return $this->groupStart()
            ->like('title', $keyword)
            ->orLike('content', $keyword)
            ->groupEnd();
    }

    /**
     * fake Post 모델 인스턴스 반환
     *
     * @param Generator $faker
     * @return array
     */
    public function fake(Generator &$faker): array
    {
        return [
            'title'       => $faker->sentence,
            'content'     => $faker->paragraph,
            'state'       => $faker->randomElement(['draft', 'published']),
            'category_id' => 1,
            'writer_id'   => 1,
            'tenant_id'   => 1,
        ];
    }

    public function syncTags(int $postId, array $tagIds, int $tenantId): void
    {
        $tagIds = array_unique($tagIds, SORT_NUMERIC);

        if (empty($tagIds)) {
            $this->deleteOldTags($postId, $tenantId);
            return;
        }

        // tenant validation
        $results = $this->db->table('tags')
            ->select('id')
            ->whereIn('id', $tagIds)
            ->where('tenant_id', $tenantId)
            ->get()
            ->getResultArray();

        $validTagIds = array_column($results, 'id');

        if (count($validTagIds) !== count($tagIds)) {
            throw new InvalidArgumentException('Invalid tag ids: ' . implode(', ', array_diff($tagIds, $validTagIds)));
        }

        $this->db->transStart();

        // delete old tags
        $this->deleteOldTags($postId, $tenantId);

        // insert new tags
        if (!empty($validTagIds)) {
            $rows = array_map(
                fn($tagId) => ['post_id' => $postId, 'tag_id' => $tagId, 'tenant_id' => $tenantId],
                $validTagIds
            );
            $this->db->table('post_tags')
                ->insertBatch($rows);
        }

        $this->db->transComplete();
    }

    public function createWithTags(array $payload, ?array $tags): ?PostEntity
    {
        $this->db->transBegin();

        try {
            $result = $this->insert($payload);

            if (!$result) {
                $this->db->transRollback();
                log_message('error', '[post.create] Insert failed', ['errors' => $this->errors()]);
                return null;
            }

            $postId = $this->getInsertID();

            if ($tags !== null) {
                $this->syncTags($postId, $tags, $payload['tenant_id']);
            }

            if (!$this->db->transStatus()) {
                $this->db->transRollback();
                log_message('error', '[post.create] Transaction failed', ['db_error' => $this->db->error()]);
                return null;
            }

            $this->db->transCommit();

            return $this->find($postId);
        } catch (Throwable $throwable) {
            $this->db->transRollback();
            throw $throwable;
        }
    }

    public function updateWithTags(int $id, array $payload, ?array $tags, int $tenantId): ?PostEntity
    {
        $this->db->transBegin();

        try {
            if ($payload) {
                $result = $this->update($id, $payload);

                if (!$result) {
                    $this->db->transRollback();
                    log_message('error', '[post.update] Update failed', ['errors' => $this->errors()]);
                    return null;
                }
            }

            if ($tags !== null) {
                $this->syncTags($id, $tags, $tenantId);
            }

            if (!$this->db->transStatus()) {
                $this->db->transRollback();
                log_message('error', '[post.update] Transaction failed', ['db_error' => $this->db->error()]);
                return null;
            }

            $this->db->transCommit();

            return $this->find($id);
        } catch (Throwable $throwable) {
            $this->db->transRollback();
            throw $throwable;
        }
    }

    private function deleteOldTags(int $postId, int $tenantId): void
    {
        $this->db->table('post_tags')
            ->where('post_id', $postId)
            ->where('tenant_id', $tenantId)
            ->delete();
    }

    public function findByTag(int $tagId, int $tenantId): array
    {
        return $this->select('posts.*')
            ->join('post_tags', 'post_tags.post_id = posts.id')
            ->where('post_tags.tag_id', $tagId)
            ->where('posts.tenant_id', $tenantId)
            ->where('posts.state', 'published')
            ->findAll();
    }
}

<?php

namespace App\Models;

use App\Entities\PostEntity;
use CodeIgniter\Model;
use Faker\Generator;

class PostModel extends Model
{
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


    /**
     * Generate slug
     *
     * @param array $data
     * @return array
     */
    protected function generateSlug(array $data)
    {
        $slug = url_title($data['data']['title'], '-', true);

        $existingSlugCount = $this
            ->where('slug LIKE', "{$slug}%")
            ->where('tenant_id', $data['data']['tenant_id'])
            ->countAllResults();

        if ($existingSlugCount > 0) {
            $slug .= '-' . ($existingSlugCount);
        }

        $data['data']['slug'] = $slug;

        return $data;
    }

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
}

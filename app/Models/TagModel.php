<?php

namespace App\Models;

use Faker\Generator;
use CodeIgniter\Model;
use App\Entities\TagEntity;
use App\Traits\SlugGeneratorTrait;

class TagModel extends Model
{
    use SlugGeneratorTrait;

    protected $table = 'tags';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = TagEntity::class;
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['tenant_id', 'name', 'slug'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'tenant_id' => 'required|integer',
        'name'      => 'required|min_length[3]|max_length[255]',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $slugSource = 'name';
    protected $beforeInsert = ['generateSlug'];
    protected $beforeUpdate = ['generateSlug'];

    public function findByPost(int $postId, int $tenantId): array
    {
        return $this->select('tags.*')
            ->join('post_tags', 'post_tags.tag_id = tags.id')
            ->where('post_tags.post_id', $postId)
            ->where('tags.tenant_id', $tenantId)
            ->findAll();
    }

    public function fake(Generator &$faker): array
    {
        $slug = "{$faker->unique()->word}-{$faker->randomNumber(4)}";

        return [
            'tenant_id' => service('tenant')->getId() ?? 1,
            'name'      => ucfirst($slug),
            'slug'      => $slug,
        ];
    }
}

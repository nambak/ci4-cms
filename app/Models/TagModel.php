<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\TagEntity;

class TagModel extends Model
{
    protected $table            = 'tags';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = TagEntity::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['tenant_id', 'name'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'tenant_id' => 'required|integer',
        'name'      => 'required|min_length[3]|max_length[255]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function findByPost(int $postId, int $tenantId): array
    {
        return $this->join('post_tags', 'post_tags.tag_id = tags.id')
            ->where('post_tags.post_id', $postId)
            ->where('tags.tenant_id', $tenantId)
            ->findAll();
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class PostModel extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
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
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'title'   => 'required|min_length[3]|max_length[255]',
        'content' => 'required|min_length[10]',
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // callbacks
    protected $beforeInsert = ['generateSlug'];

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
}

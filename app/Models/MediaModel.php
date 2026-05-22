<?php

namespace App\Models;

use App\Entities\MediaEntity;
use App\Enums\MediaType;
use CodeIgniter\Model;

class MediaModel extends Model
{
    protected $table = 'media';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = MediaEntity::class;
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'tenant_id', 'post_id', 'uploader_id', 'type', 'mime_type', 'filename', 'original_name', 'file_size', 'path'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'tenant_id'     => 'required|integer',
        'post_id'       => 'permit_empty|integer',
        'uploader_id'   => 'required|integer',
        'type'          => 'required|enumValue[' . MediaType::class. ']',
        'mime_type'     => 'required|string',
        'filename'      => 'required|string',
        'original_name' => 'required|string',
        'file_size'     => 'required|integer',
        'path'          => 'required|string'
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
}

<?php

namespace App\Entities;

use App\Enums\MediaType;
use CodeIgniter\Entity\Entity;

class Media extends Entity
{
    protected $casts = [
        'type'      => 'enum[App\Enums\MediaType]',
        'file_size' => 'integer',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public function isImage(): bool
    {
        return $this->type === MediaType::Image;
    }
}

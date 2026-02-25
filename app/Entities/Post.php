<?php

namespace App\Entities;

use App\Enums\PostStatus;
use CodeIgniter\Entity\Entity;

class Post extends Entity
{
    protected $casts = [
        'status'     => '?enum[App\Enums\PostStatus]',
        'is_pinned'  => 'boolean',
        'view_count' => 'integer',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function isPublished(): bool
    {
        return $this->status === PostStatus::Published;
    }

    public function isDraft(): bool
    {
        return $this->status === PostStatus::Draft;
    }
}

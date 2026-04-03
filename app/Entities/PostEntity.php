<?php

namespace App\Entities;

use App\Enums\PostState;
use CodeIgniter\Entity\Entity;

class PostEntity extends Entity
{
    protected $casts = [
        'state'     => '?enum[App\Enums\PostState]',
        'view_count' => 'integer',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public function isPublished(): bool
    {
        return $this->state === PostState::Published;
    }

    public function isDraft(): bool
    {
        return $this->state === PostState::Draft;
    }

    public function isOwnedBy(int $userId): bool
    {
        return $this->writer_id === $userId;
    }
}

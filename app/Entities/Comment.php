<?php

namespace App\Entities;

use App\Enums\CommentStatus;
use CodeIgniter\Entity\Entity;

class Comment extends Entity
{
    protected $casts = [
        'status' => 'enum[App\Enums\CommentStatus]',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function isVisible(): bool
    {
        return $this->status === CommentStatus::Approved;
    }
}

<?php

namespace App\Entities;

use App\Enums\CommentStatus;
use CodeIgniter\Entity\Entity;

class Comment extends Entity
{
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}

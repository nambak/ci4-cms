<?php

namespace App\Entities;

use App\Enums\MediaType;
use CodeIgniter\Entity\Entity;

class Media extends Entity
{
    protected $casts = [
        'file_size' => 'integer',
    ];

    protected $dates = ['created_at', 'updated_at'];
}

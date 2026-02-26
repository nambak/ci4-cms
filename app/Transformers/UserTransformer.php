<?php

declare(strict_types=1);

namespace App\Transformers;

use CodeIgniter\API\BaseTransformer;

class UserTransformer extends BaseTransformer
{
    public function toArray(mixed $resource): array
    {
        return [
            'id'         => $resource['id'],
            'name'       => $resource['username'] ?? $resource['name'] ?? null,
            'created_at' => $resource['created_at'],
            'updated_at' => $resource['updated_at'],
        ];
    }

    protected function getAllowedFields(): ?array
    {
        return ['id', 'name', 'created_at', 'updated_at'];
    }
}

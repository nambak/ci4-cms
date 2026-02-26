<?php

declare(strict_types=1);

namespace App\Transformers;

use CodeIgniter\API\BaseTransformer;

class CategoryTransformer extends BaseTransformer
{
    public function toArray(mixed $resource): array
    {
        return [
            'id'          => $resource['id'],
            'name'        => $resource['name'],
            'slug'        => $resource['slug'],
            'description' => $resource['description'] ?? null,
            'created_at'  => $resource['created_at'],
            'updated_at'  => $resource['updated_at'],
        ];
    }

    protected function getAllowedFields(): ?array
    {
        return ['id', 'name', 'slug', 'description', 'created_at', 'updated_at'];
    }

    protected function getAllowedIncludes(): ?array
    {
        return ['posts'];
    }

    protected function includePosts(): array
    {
        // DB 구현 후 채워질 자리
        return [];
    }
}

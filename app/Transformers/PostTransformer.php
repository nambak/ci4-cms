<?php

declare(strict_types=1);

namespace App\Transformers;

use CodeIgniter\API\BaseTransformer;

class PostTransformer extends BaseTransformer
{
    public function toArray(mixed $resource): array
    {
        return [
            'id'           => $resource['id'],
            'title'        => $resource['title'],
            'slug'         => $resource['slug'],
            'excerpt'      => $resource['excerpt'] ?? null,
            'content'      => $resource['content'],
            'status'       => $resource['status'] instanceof \BackedEnum
                ? $resource['status']->value
                : $resource['status'],
            'category_id'  => $resource['category_id'] ?? null,
            'author_id'    => $resource['author_id'],
            'published_at' => $resource['published_at'] ?? null,
            'created_at'   => $resource['created_at'],
            'updated_at'   => $resource['updated_at'],
        ];
    }

    protected function getAllowedFields(): ?array
    {
        return ['id', 'title', 'slug', 'excerpt', 'content', 'status', 'category_id', 'author_id', 'published_at', 'created_at', 'updated_at'];
    }

    protected function getAllowedIncludes(): ?array
    {
        return ['category', 'tags', 'author'];
    }

    protected function includeCategory(): array
    {
        // DB 구현 후 채워질 자리
        return [];
    }

    protected function includeTags(): array
    {
        // DB 구현 후 채워질 자리
        return [];
    }

    protected function includeAuthor(): array
    {
        // DB 구현 후 채워질 자리
        return [];
    }
}

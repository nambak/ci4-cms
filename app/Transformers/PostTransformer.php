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
            'state'        => $resource['state'] instanceof \BackedEnum
                ? $resource['state']->value
                : $resource['state'],
            'category_id'  => $resource['category_id'] ?? null,
            'writer_id'    => $resource['writer_id'] ?? null,
            'published_at' => $resource['published_at'] ?? null,
            'created_at'   => $resource['created_at'],
            'updated_at'   => $resource['updated_at'],
        ];
    }

    protected function getAllowedFields(): ?array
    {
        return [
            'id',
            'title',
            'slug',
            'excerpt',
            'content',
            'state',
            'category_id',
            'writer_id',
            'published_at',
            'created_at',
            'updated_at'
        ];
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

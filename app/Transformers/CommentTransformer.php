<?php

declare(strict_types=1);

namespace App\Transformers;

use CodeIgniter\API\BaseTransformer;

class CommentTransformer extends BaseTransformer
{
    public function toArray(mixed $resource): array
    {
        return [
            'id'         => $resource['id'],
            'post_id'    => $resource['post_id'],
            'user_id'    => $resource['user_id'],
            'parent_id'  => $resource['parent_id'] ?? null,
            'content'    => $resource['content'],
            'status'     => $resource['status'] instanceof \BackedEnum
                ? $resource['status']->value
                : $resource['status'],
            'created_at' => $resource['created_at'],
            'updated_at' => $resource['updated_at'],
        ];
    }

    protected function getAllowedFields(): ?array
    {
        return ['id', 'post_id', 'user_id', 'parent_id', 'content', 'status', 'created_at', 'updated_at'];
    }

    protected function getAllowedIncludes(): ?array
    {
        return ['author', 'replies'];
    }

    protected function includeAuthor(): array
    {
        // DB 구현 후 채워질 자리
        return [];
    }

    protected function includeReplies(): array
    {
        // DB 구현 후 채워질 자리
        return [];
    }
}

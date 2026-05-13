<?php

namespace App\Enums;

enum PostState: string
{
    case DRAFT     = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED  = 'archived';

    public function label(): string
    {
        return match($this) {
            PostState::DRAFT     => '임시저장',
            PostState::PUBLISHED => '게시됨',
            PostState::ARCHIVED  => '보관됨',
        };
    }

    public function isPublic(): bool
    {
        return $this === PostState::PUBLISHED;
    }
}

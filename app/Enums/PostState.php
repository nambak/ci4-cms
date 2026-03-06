<?php

namespace App\Enums;

enum PostState: string
{
    case Draft     = 'draft';
    case Published = 'published';
    case Archived  = 'archived';

    public function label(): string
    {
        return match($this) {
            PostState::Draft     => '임시저장',
            PostState::Published => '게시됨',
            PostState::Archived  => '보관됨',
        };
    }

    public function isPublic(): bool
    {
        return $this === PostState::Published;
    }
}

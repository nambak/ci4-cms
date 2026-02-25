<?php

namespace App\Enums;

enum PostStatus: string
{
    case Draft     = 'draft';
    case Published = 'published';
    case Archived  = 'archived';

    public function label(): string
    {
        return match($this) {
            PostStatus::Draft     => '임시저장',
            PostStatus::Published => '게시됨',
            PostStatus::Archived  => '보관됨',
        };
    }

    public function isPublic(): bool
    {
        return $this === PostStatus::Published;
    }
}

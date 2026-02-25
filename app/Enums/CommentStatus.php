<?php

namespace App\Enums;

enum CommentStatus: string
{
    case Pending  = 'pending';
    case Approved = 'approved';
    case Spam     = 'spam';

    public function label(): string
    {
        return match($this) {
            CommentStatus::Pending  => '검토 중',
            CommentStatus::Approved => '승인됨',
            CommentStatus::Spam     => '스팸',
        };
    }

    public function isVisible(): bool
    {
        return $this === CommentStatus::Approved;
    }
}

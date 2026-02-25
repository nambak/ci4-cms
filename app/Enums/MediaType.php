<?php

namespace App\Enums;

enum MediaType: string
{
    case Image    = 'image';
    case Video    = 'video';
    case Document = 'document';

    public function label(): string
    {
        return match($this) {
            MediaType::Image    => '이미지',
            MediaType::Video    => '동영상',
            MediaType::Document => '문서',
        };
    }
}

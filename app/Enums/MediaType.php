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

    public static function fromMimeType(string $mimeType): self
    {
        foreach (self::cases() as $type) {
            if (str_starts_with($mimeType, $type->value)) {
                return $type;
            }
        }

        return self::Document;
    }
}

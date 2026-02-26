<?php

declare(strict_types=1);

namespace App\Transformers;

/**
 * 인증된 사용자 전용 Transformer
 *
 * UserTransformer를 확장하여 email 등 민감 필드를 추가로 노출합니다.
 * 로그인 응답, 프로필(me) 엔드포인트 등 본인 정보 조회에만 사용하세요.
 * 공개 include(작성자 표시 등)에는 UserTransformer를 사용합니다.
 */
class AuthUserTransformer extends UserTransformer
{
    public function toArray(mixed $resource): array
    {
        return array_merge(parent::toArray($resource), [
            'email' => $resource['email'],
        ]);
    }

    protected function getAllowedFields(): ?array
    {
        return ['id', 'email', 'name', 'created_at', 'updated_at'];
    }
}

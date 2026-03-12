<?php

declare(strict_types=1);

namespace App\Controllers\Api\V1;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\RESTful\ResourceController;
use RuntimeException;

/**
 * API V1 공통 기반 컨트롤러
 *
 * 모든 API 컨트롤러가 상속받는 추상 클래스.
 * JSON 응답 포맷을 기본으로 설정합니다.
 */
abstract class BaseApiController extends ResourceController
{
    protected $format = 'json';

    protected function findUserOrFail($id)
    {
        $user = auth()->getProvider()->findById($id);

        if (!$user) {
            throw new PageNotFoundException('User not found');
        }

        return $user;
    }
}

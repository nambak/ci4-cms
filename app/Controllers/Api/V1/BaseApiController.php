<?php

declare(strict_types=1);

namespace App\Controllers\Api\V1;

use CodeIgniter\RESTful\ResourceController;

/**
 * API V1 공통 기반 컨트롤러
 *
 * 모든 API 컨트롤러가 상속받는 추상 클래스.
 * JSON 응답 포맷을 기본으로 설정합니다.
 */
abstract class BaseApiController extends ResourceController
{
    protected $format = 'json';
}

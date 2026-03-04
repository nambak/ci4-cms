<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * 테넌트 감지 필터
 *
 * URL 첫 번째 세그먼트에서 테넌트 슬러그를 감지하고
 * 테넌트 컨텍스트를 설정합니다.
 *
 * TODO: #7 테넌트 관리 기능에서 실제 구현 필요
 * - DB에서 테넌트 조회
 * - 유효하지 않은 테넌트 슬러그 처리 (404)
 * - 예약된 슬러그 차단 (admin, api, docs 등)
 * - TenantService에 현재 테넌트 저장
 */
class TenantFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // TODO: #7 에서 구현
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // TODO: #7 에서 구현
    }
}

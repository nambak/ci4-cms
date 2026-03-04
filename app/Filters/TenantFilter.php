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
 * - TenantService에 현재 테넌트 저장
 */
class TenantFilter implements FilterInterface
{
    /**
     * 시스템 예약 슬러그 — 테넌트 슬러그로 사용 불가
     */
    private const RESERVED_SLUGS = [
        'admin', 'api', 'docs', 'login', 'logout',
        'register', 'auth', 'assets', 'static',
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        $slug = $request->getUri()->getSegment(1);

        if (in_array($slug, self::RESERVED_SLUGS, true)) {
            return response()->setStatusCode(404);
        }

        // TODO: #7 에서 DB 테넌트 조회 및 TenantService 설정 구현
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}

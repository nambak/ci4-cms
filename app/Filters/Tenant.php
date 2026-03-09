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
 */
class Tenant implements FilterInterface
{
    /**
     * 시스템 예약 슬러그 — 테넌트 슬러그로 사용 불가
     */
    private const RESERVED_SUBDOMAINS = [
        'admin',
        'api',
        'docs',
        'login',
        'logout',
        'register',
        'auth',
        'assets',
        'static',
    ];

    public function before(RequestInterface $request, $arguments = null): null|ResponseInterface
    {
        $subdomain = $request->getUri()->getSegment(1);

        // 예약된 subdomain 확인
        if (in_array($subdomain, self::RESERVED_SUBDOMAINS, true)) {
            return response()->setStatusCode(404);
        }

        // DB에서 이미 등록된 subdomain 확인
        $model = model('TenantModel');
        $tenant = $model->where('subdomain', $subdomain)->first();

        if ($tenant) {
            service('tenant')->setTenant($tenant);
            return null;
        } else {
            return response()->setStatusCode(404);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}

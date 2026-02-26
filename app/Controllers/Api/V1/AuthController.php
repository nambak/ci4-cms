<?php

declare(strict_types=1);

namespace App\Controllers\Api\V1;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Router\Attributes\Filter;

/**
 * 인증 컨트롤러
 *
 * 로그인/로그아웃/회원가입은 인증 불필요.
 * me, logout, refresh는 Bearer Token 인증 필요.
 */
class AuthController extends BaseApiController
{
    public function register(): ResponseInterface
    {
        return $this->response->setJSON(['message' => 'Not implemented'])->setStatusCode(501);
    }

    public function login(): ResponseInterface
    {
        return $this->response->setJSON(['message' => 'Not implemented'])->setStatusCode(501);
    }

    #[Filter(by: 'tokens')]
    public function me(): ResponseInterface
    {
        return $this->response->setJSON(['message' => 'Not implemented'])->setStatusCode(501);
    }

    #[Filter(by: 'tokens')]
    public function logout(): ResponseInterface
    {
        return $this->response->setJSON(['message' => 'Not implemented'])->setStatusCode(501);
    }

    #[Filter(by: 'tokens')]
    public function refresh(): ResponseInterface
    {
        return $this->response->setJSON(['message' => 'Not implemented'])->setStatusCode(501);
    }
}

<?php

declare(strict_types=1);

namespace App\Controllers\Api\V1;

use App\Transformers\UserTransformer;
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
    protected UserTransformer $transformer;

    public function __construct()
    {
        $this->transformer = new UserTransformer();
    }

    public function register(): ResponseInterface
    {
        // TODO: validate, Shield user create
        // return $this->respondCreated($this->transformer->transform($user));
        return $this->failServerError('Not implemented');
    }

    public function login(): ResponseInterface
    {
        // TODO: Shield attempt(), return token + user info
        // return $this->respond(['token' => $token, 'user' => $this->transformer->transform($user)]);
        return $this->failServerError('Not implemented');
    }

    #[Filter(by: 'tokens')]
    public function me(): ResponseInterface
    {
        // TODO: return $this->respond($this->transformer->transform(auth()->user()));
        return $this->failServerError('Not implemented');
    }

    #[Filter(by: 'tokens')]
    public function logout(): ResponseInterface
    {
        // TODO: auth()->logout()
        // return $this->respondNoContent();
        return $this->failServerError('Not implemented');
    }

    #[Filter(by: 'tokens')]
    public function refresh(): ResponseInterface
    {
        // TODO: rotate token, return new token
        return $this->failServerError('Not implemented');
    }
}

<?php

declare(strict_types=1);

namespace App\Controllers\Api\V1;

use App\Transformers\AuthUserTransformer;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Router\Attributes\Filter;
use CodeIgniter\Shield\Entities\User;

/**
 * 인증 컨트롤러
 *
 * 로그인/로그아웃/회원가입은 인증 불필요.
 * me, logout, refresh는 Bearer Token 인증 필요.
 */
class AuthController extends BaseApiController
{
    protected AuthUserTransformer $transformer;

    public function __construct()
    {
        $this->transformer = new AuthUserTransformer();
    }

    /**
     * 사용자 등록
     *
     * @return ResponseInterface
     */
    public function register(): ResponseInterface
    {
        $payload = $this->request->getJSON(true);
        $userProvider = auth()->getProvider();
        $user = $this->createUserFromPayload($payload);

        if (!$userProvider->save($user)) {
            return $this->fail($userProvider->errors());
        }

        $registeredUser = $this->registerUser($userProvider);

        return $this->respondCreated($this->transformer->transform($registeredUser));
    }

    public function login(): ResponseInterface
    {
        // TODO: Shield attempt(), return token + user info
        // return $this->respond(['token' => $token, 'user' => $this->transformer->transform($user)]);
        return $this->fail('Not Implemented', 501);
    }

    #[Filter(by: 'tokens')]
    public function me(): ResponseInterface
    {
        // TODO: return $this->respond($this->transformer->transform(auth()->user()));
        return $this->fail('Not Implemented', 501);
    }

    #[Filter(by: 'tokens')]
    public function logout(): ResponseInterface
    {
        // TODO: auth()->logout()
        // return $this->respondNoContent();
        return $this->fail('Not Implemented', 501);
    }

    #[Filter(by: 'tokens')]
    public function refresh(): ResponseInterface
    {
        // TODO: rotate token, return new token
        return $this->fail('Not Implemented', 501);
    }

    private function createUserFromPayload(array $payload): User
    {
        return new User([
            'username' => $payload['username'] ?? null,
            'password' => $payload['password'] ?? null,
            'email'    => $payload['email'] ?? null,
        ]);
    }

    private function registerUser(object $userProvider): User
    {
        $registeredUser = $userProvider->findById($userProvider->getInsertID());
        $userProvider->addToDefaultGroup($registeredUser);

        return $registeredUser;
    }
}

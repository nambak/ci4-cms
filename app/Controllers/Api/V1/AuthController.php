<?php

declare(strict_types=1);

namespace App\Controllers\Api\V1;

use App\Transformers\AuthUserTransformer;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Router\Attributes\Filter;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Database\Exceptions\DatabaseException;
use InvalidArgumentException;
use RuntimeException;

/**
 * 인증 컨트롤러
 *
 * 로그인/로그아웃/회원가입은 인증 불필요.
 * me, logout, refresh는 Bearer Token 인증 필요.
 */
class AuthController extends BaseApiController
{
    private const AUTH_RULES = [
        'email'    => 'required|valid_email',
        'password' => 'required',
    ];
    protected AuthUserTransformer $transformer;
    private ?User $currentUser = null;

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
        try {
            $payload = $this->getValidatedAuthPayload();
        } catch (InvalidArgumentException) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $userProvider = auth()->getProvider();
        $user = $this->createUserFromPayload($payload);

        try {
            if (!$userProvider->save($user)) {
                return $this->fail($userProvider->errors());
            }
        } catch (DatabaseException $e) {
            return $this->fail(['error' => 'The email address is already registered.']);
        }

        $registeredUser = $this->registerUser($userProvider);

        return $this->respondCreated($this->transformer->transform($registeredUser));
    }

    /**
     * 사용자 로그인
     *
     * @return ResponseInterface
     */
    public function login(): ResponseInterface
    {
        try {
            $payload = $this->getValidatedAuthPayload();
        } catch (InvalidArgumentException) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $credentials = $this->extractCredentialsFromPayload($payload);
        $result = auth()->attempt($credentials);

        if (!$result->isOK()) {
            return $this->fail($result->extraInfo(), 401);
        }

        return $this->respondWithToken($this->currentUser(), 'login');
    }

    /**
     * 로그인한 사용자 정보 확인
     *
     * @return ResponseInterface
     */
    #[Filter(by: 'tokens')]
    public function me(): ResponseInterface
    {
        return $this->respond($this->transformer->transform($this->currentUser()));
    }

    /**
     * 로그아웃
     *
     * @return ResponseInterface
     */
    #[Filter(by: 'tokens')]
    public function logout(): ResponseInterface
    {
        $this->revokeCurrentAccessToken();

        return $this->respondNoContent();
    }

    /**
     * 토큰 갱신
     *
     * @return ResponseInterface
     */
    #[Filter(by: 'tokens')]
    public function refresh(): ResponseInterface
    {
        $user = $this->currentUser();
        $this->revokeCurrentAccessToken();

        return $this->respondWithToken($user, 'refresh');
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

    private function extractCredentialsFromPayload(array $payload): array
    {
        return [
            'email'    => $payload['email'] ?? null,
            'password' => $payload['password'] ?? null,
        ];
    }

    private function respondWithToken(User $user, string $tokenName): ResponseInterface
    {
        $token = $user->generateAccessToken($tokenName)->raw_token;

        return $this->respond([
            'token' => $token,
            'user' => $this->transformer->transform($user)]);
    }

    private function currentUser(): User
    {
        if ($this->currentUser) {
            return $this->currentUser;
        }

        $user = auth()->user();

        if (!$user) {
            throw new RuntimeException('User not found');
        }

        $this->currentUser = $user;

        return $this->currentUser();
    }

    private function revokeCurrentAccessToken(): void
    {
        $user = $this->currentUser();
        $token = $user->currentAccessToken();

        $user->revokeAccessTokenBySecret($token->secret);
    }

    private function getValidatedAuthPayload(): ?array
    {
        $payload = $this->request->getJSON(true) ?? [];

        if (!$this->validate(self::AUTH_RULES)) {
            throw new InvalidArgumentException('Invalid authentication payload.');
        }

        return $payload;
    }
}

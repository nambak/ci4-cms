<?php

declare(strict_types=1);

namespace App\Controllers\Api\V1;

use App\Transformers\AuthUserTransformer;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Router\Attributes\Filter;
use CodeIgniter\Shield\Entities\User;
use InvalidArgumentException;
use RuntimeException;
use CodeIgniter\Shield\Models\DatabaseException as ShieldException;
use CodeIgniter\Database\Exceptions\DatabaseException as DatabaseException;

/**
 * 인증 컨트롤러
 *
 * 로그인/로그아웃/회원가입은 인증 불필요.
 * me, logout, refresh는 Bearer Token 인증 필요.
 */
class AuthController extends BaseApiController
{
    protected AuthUserTransformer $transformer;
    private ?User $currentUser = null;

    public function __construct()
    {
        $this->transformer = new AuthUserTransformer();
    }

    /**
     * 사용자 등록
     *
     *
     */
    public function register(): ResponseInterface
    {
        $validateRules = [
            'username' => 'required|alpha_numeric_space',
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[8]|max_length[20]'
        ];

        try {
            $payload = $this->getValidatedAuthPayload($validateRules);
        } catch (InvalidArgumentException) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $userProvider = auth()->getProvider();

        // TODO: 트랜잭션 처리 필요함.
        try {
            $user = $this->createUserFromPayload($payload);

            if (!$userProvider->save($user)) {
                return $this->fail($userProvider->errors());
            }

            $registeredUser = $this->assignDefaultGroup($userProvider);
        } catch (ShieldException | DatabaseException $e) {
            log_message('error', $e->getMessage());
            return $this->fail('server error');
        }

        return $this->respondCreated($this->transformer->transform($registeredUser));
    }

    /**
     * 사용자 로그인
     *
     */
    public function login(): ResponseInterface
    {
        $validateRules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[8]|max_length[20]'
        ];

        try {
            $payload = $this->getValidatedAuthPayload($validateRules);
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
     */
    #[Filter(by: 'tokens')]
    public function me(): ResponseInterface
    {
        return $this->respond($this->transformer->transform($this->currentUser()));
    }

    /**
     * 로그아웃
     *
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
     */
    #[Filter(by: 'tokens')]
    public function refresh(): ResponseInterface
    {
        $user = $this->currentUser();
        $response = $this->respondWithToken($user, 'refresh');
        $this->revokeCurrentAccessToken();

        return $response;
    }

    private function createUserFromPayload(array $payload): User
    {
        return new User([
            'username' => $payload['username'] ?? null,
            'password' => $payload['password'] ?? null,
            'email'    => $payload['email'] ?? null,
        ]);
    }

    private function assignDefaultGroup(object $userProvider): User
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
            'user'  => $this->transformer->transform($user)]);
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

        return $this->currentUser;
    }

    private function revokeCurrentAccessToken(): void
    {
        $user = $this->currentUser();
        $token = $user->currentAccessToken();

        $user->revokeAccessTokenBySecret($token->secret);
    }

    private function getValidatedAuthPayload(array $rules): ?array
    {
        $payload = $this->request->getJSON(true) ?? [];

        if (!$this->validateData($payload, $rules)) {
            throw new InvalidArgumentException('Invalid authentication payload.');
        }

        return $payload;
    }
}

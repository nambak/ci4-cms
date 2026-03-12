<?php

namespace Tests\Api;

use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use PHPUnit\Framework\Attributes\Group;

/**
 * Authentication API Tests
 *
 * AuthController의 5개 엔드포인트 테스트:
 * - POST /api/v1/auth/register (공개)
 * - POST /api/v1/auth/login (공개)
 * - GET  /api/v1/auth/me (토큰 필요)
 * - POST /api/v1/auth/logout (토큰 필요)
 * - POST /api/v1/auth/refresh (토큰 필요)
 *
 * 수정 완료된 버그:
 * - BUG-001: AuthUserTransformer email 접근 방식 수정 ($this->resource->email)
 * - BUG-002: register 입력 검증 추가 (InvalidArgumentException)
 * - BUG-003: revokeAccessTokenBySecret 사용으로 변경
 * - BUG-004: 중복 이메일 DatabaseException 처리
 * - BUG-005: login 입력 검증 추가
 */
#[Group('api')]
class AuthenticationApiTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $migrate   = true;
    protected $DBGroup   = 'tests';
    protected $namespace = ['App', 'CodeIgniter\Shield', 'CodeIgniter\Settings'];

    protected array $testUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testUser = [
            'username' => 'testuser',
            'email'    => 'test@example.com',
            'password' => 'SecurePass123!',
        ];
    }

    // =========================================================
    // POST /api/v1/auth/register
    // =========================================================

    /**
     * 사용자 등록 - 유효한 데이터로 요청하면 201 응답 및 사용자 정보 반환
     *
     * BUG-001 수정 확인: AuthUserTransformer가 email을 정상 반환
     */
    public function testRegisterCreatesUserSuccessfully(): void
    {
        $result = $this->withBodyFormat('json')
            ->post('/api/v1/auth/register', $this->testUser);

        $result->assertStatus(201);

        $body = json_decode($result->getJSON(), true);
        $this->assertArrayHasKey('id', $body);
        $this->assertArrayHasKey('email', $body);
        $this->assertArrayHasKey('name', $body);
        $this->assertEquals($this->testUser['email'], $body['email']);

        $this->seeInDatabase('users', ['username' => $this->testUser['username']]);
    }

    /**
     * 사용자 등록 - 유효하지 않은 이메일로 400 응답
     *
     * BUG-002 수정 확인: 입력 검증이 Entity 생성 전에 수행됨
     */
    public function testRegisterFailsWithInvalidEmail(): void
    {
        $result = $this->withBodyFormat('json')
            ->post('/api/v1/auth/register', [
                'username' => 'baduser',
                'email'    => 'not-an-email',
                'password' => 'SecurePass123!',
            ]);

        $result->assertStatus(400);
    }

    /**
     * 사용자 등록 - password 누락 시 400 응답
     *
     * BUG-002 수정 확인: InvalidArgumentException 대신 검증 에러 반환
     */
    public function testRegisterFailsWithoutPassword(): void
    {
        $result = $this->withBodyFormat('json')
            ->post('/api/v1/auth/register', [
                'username' => 'nopassuser',
                'email'    => 'nopass@example.com',
            ]);

        $result->assertStatus(400);
    }

    /**
     * 사용자 등록 - email 누락 시 400 응답
     *
     * BUG-002 수정 확인: 필수 필드 누락 검증
     */
    public function testRegisterFailsWithoutEmail(): void
    {
        $result = $this->withBodyFormat('json')
            ->post('/api/v1/auth/register', [
                'username' => 'noemailuser',
                'password' => 'SecurePass123!',
            ]);

        $result->assertStatus(400);
    }

    /**
     * 사용자 등록 - 중복 이메일로 400 응답
     *
     * BUG-004 수정 확인: DatabaseException 대신 적절한 에러 응답 반환
     */
    public function testRegisterFailsWithDuplicateEmail(): void
    {
        $this->createUserDirectly();

        $result = $this->withBodyFormat('json')
            ->post('/api/v1/auth/register', [
                'username' => 'anotheruser',
                'email'    => $this->testUser['email'],
                'password' => 'AnotherPass456!',
            ]);

        $result->assertStatus(400);
    }

    /**
     * 사용자 등록 - 빈 요청 본문으로 400 응답
     *
     * BUG-002 수정 확인: 빈 본문도 검증 에러로 처리
     */
    public function testRegisterFailsWithEmptyBody(): void
    {
        $result = $this->withBodyFormat('json')
            ->post('/api/v1/auth/register', []);

        $result->assertStatus(400);
    }

    // =========================================================
    // POST /api/v1/auth/login
    // =========================================================

    /**
     * 로그인 성공 시 토큰과 사용자 정보 반환
     *
     * BUG-001 수정 확인: 응답에 email 포함
     */
    public function testLoginReturnsTokenOnSuccess(): void
    {
        $this->createUserDirectly();

        $result = $this->withBodyFormat('json')
            ->post('/api/v1/auth/login', [
                'email'    => $this->testUser['email'],
                'password' => $this->testUser['password'],
            ]);

        $result->assertStatus(200);

        $body = json_decode($result->getJSON(), true);
        $this->assertArrayHasKey('token', $body);
        $this->assertArrayHasKey('user', $body);
        $this->assertNotEmpty($body['token']);
        $this->assertEquals($this->testUser['email'], $body['user']['email']);
    }

    /**
     * 로그인 실패 - 잘못된 비밀번호 (401 응답)
     */
    public function testLoginFailsWithWrongPassword(): void
    {
        $this->createUserDirectly();

        $result = $this->withBodyFormat('json')
            ->post('/api/v1/auth/login', [
                'email'    => $this->testUser['email'],
                'password' => 'WrongPassword!',
            ]);

        $result->assertStatus(401);
    }

    /**
     * 로그인 실패 - 존재하지 않는 이메일 (401 응답)
     */
    public function testLoginFailsWithNonexistentEmail(): void
    {
        $result = $this->withBodyFormat('json')
            ->post('/api/v1/auth/login', [
                'email'    => 'nobody@example.com',
                'password' => 'SomePassword!',
            ]);

        $result->assertStatus(401);
    }

    /**
     * 로그인 실패 - 빈 자격증명 (400 응답)
     *
     * BUG-005 수정 확인: 입력 검증이 Shield 호출 전에 수행됨
     */
    public function testLoginFailsWithEmptyCredentials(): void
    {
        $result = $this->withBodyFormat('json')
            ->post('/api/v1/auth/login', []);

        $result->assertStatus(400);
    }

    /**
     * 로그인 실패 - email만 제공 (400 응답)
     *
     * BUG-005 수정 확인: password 필수 검증
     */
    public function testLoginFailsWithEmailOnly(): void
    {
        $result = $this->withBodyFormat('json')
            ->post('/api/v1/auth/login', [
                'email' => 'test@example.com',
            ]);

        $result->assertStatus(400);
    }

    // =========================================================
    // GET /api/v1/auth/me
    // =========================================================

    /**
     * 유효한 토큰으로 /me 접근 시 사용자 정보 반환
     *
     * BUG-001 수정 확인: email 필드 포함
     */
    public function testMeReturnsUserInfoWithValidToken(): void
    {
        $token = $this->createUserWithToken();

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/auth/me');

        $result->assertStatus(200);

        $body = json_decode($result->getJSON(), true);
        $this->assertArrayHasKey('id', $body);
        $this->assertArrayHasKey('name', $body);
        $this->assertArrayHasKey('email', $body);
        $this->assertEquals($this->testUser['email'], $body['email']);
    }

    /**
     * 토큰 없이 /me 접근 시 401 응답
     */
    public function testMeFailsWithoutToken(): void
    {
        $result = $this->get('/api/v1/auth/me');

        $result->assertStatus(401);
    }

    /**
     * 유효하지 않은 토큰으로 /me 접근 시 401 응답
     */
    public function testMeFailsWithInvalidToken(): void
    {
        $result = $this->withHeaders([
            'Authorization' => 'Bearer invalid-token-value-12345',
        ])->get('/api/v1/auth/me');

        $result->assertStatus(401);
    }

    // =========================================================
    // POST /api/v1/auth/logout
    // =========================================================

    /**
     * 로그아웃 성공 시 204 응답
     *
     * BUG-003 수정 확인: revokeAccessTokenBySecret 사용
     */
    public function testLogoutReturns204(): void
    {
        $token = $this->createUserWithToken();

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/auth/logout');

        $result->assertStatus(204);
    }

    /**
     * 로그아웃 후 토큰이 무효화되는지 확인
     *
     * BUG-003 수정 확인: 토큰 폐기 후 재사용 불가
     */
    public function testLogoutRevokesToken(): void
    {
        $token = $this->createUserWithToken();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/auth/logout');

        // 로그아웃 후 같은 토큰으로 /me 접근 불가
        $meResult = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/auth/me');

        $meResult->assertStatus(401);
    }

    /**
     * 토큰 없이 로그아웃 시 401 응답
     */
    public function testLogoutFailsWithoutToken(): void
    {
        $result = $this->post('/api/v1/auth/logout');

        $result->assertStatus(401);
    }

    // =========================================================
    // POST /api/v1/auth/refresh
    // =========================================================

    /**
     * 토큰 갱신 성공 - 새 토큰 반환
     *
     * BUG-003 수정 확인: revokeAccessTokenBySecret 사용
     * BUG-001 수정 확인: 응답에 email 포함
     */
    public function testRefreshReturnsNewToken(): void
    {
        $oldToken = $this->createUserWithToken();

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $oldToken,
        ])->post('/api/v1/auth/refresh');

        $result->assertStatus(200);

        $body = json_decode($result->getJSON(), true);
        $this->assertArrayHasKey('token', $body);
        $this->assertArrayHasKey('user', $body);
        $this->assertNotEquals($oldToken, $body['token']);
        $this->assertEquals($this->testUser['email'], $body['user']['email']);
    }

    /**
     * 토큰 갱신 후 이전 토큰 무효화 확인
     */
    public function testRefreshRevokesOldToken(): void
    {
        $oldToken = $this->createUserWithToken();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $oldToken,
        ])->post('/api/v1/auth/refresh');

        // 이전 토큰으로 /me 접근 불가
        $meResult = $this->withHeaders([
            'Authorization' => 'Bearer ' . $oldToken,
        ])->get('/api/v1/auth/me');

        $meResult->assertStatus(401);
    }

    /**
     * 토큰 없이 갱신 시 401 응답
     */
    public function testRefreshFailsWithoutToken(): void
    {
        $result = $this->post('/api/v1/auth/refresh');

        $result->assertStatus(401);
    }

    // =========================================================
    // Helper Methods
    // =========================================================

    /**
     * Shield UserModel을 사용하여 사용자를 DB에 직접 생성
     */
    protected function createUserDirectly(): User
    {
        /** @var UserModel $users */
        $users = model(UserModel::class);

        $user = new User([
            'username' => $this->testUser['username'],
            'password' => $this->testUser['password'],
            'email'    => $this->testUser['email'],
        ]);

        $users->save($user);

        $savedUser = $users->findById($users->getInsertID());
        $users->addToDefaultGroup($savedUser);

        return $savedUser;
    }

    /**
     * 사용자 생성 후 AccessToken을 직접 발급
     */
    protected function createUserWithToken(): string
    {
        $user = $this->createUserDirectly();
        $token = $user->generateAccessToken('test-token');

        return $token->raw_token;
    }
}

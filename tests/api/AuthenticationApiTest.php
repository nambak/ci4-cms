<?php

namespace Tests\Api;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use PHPUnit\Framework\Attributes\Group;

/**
 * Authentication API Tests
 *
 * OpenAPI 스펙 기반 인증 API 테스트
 * 참조: docs/openapi.yaml - Authentication endpoints
 */
#[Group('api')]
class AuthenticationApiTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected $namespace;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        // 테스트용 사용자 데이터
        $this->testUser = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'name' => '테스트 사용자'
        ];
    }

    /**
     * @test
     * POST /api/v1/auth/register
     * 사용자 등록 테스트
     */
    public function test_register_creates_new_user(): void
    {
        $result = $this->withBodyFormat('json')
            ->post('/api/v1/auth/register', [
                'email' => $this->testUser['email'],
                'password' => $this->testUser['password'],
                'name' => $this->testUser['name']
            ]);

        $result->assertStatus(201);
        $result->assertJSONFragment([
            'status' => 'success',
            'code' => 201
        ]);

        // 응답에 토큰과 사용자 정보가 포함되어야 함
        $json = $result->getJSON();
        $this->assertObjectHasProperty('data', $json);
        $this->assertObjectHasProperty('token', $json->data);
        $this->assertObjectHasProperty('user', $json->data);
        $this->assertEquals($this->testUser['email'], $json->data->user->email);
    }

    /**
     * @test
     * POST /api/v1/auth/register
     * 유효성 검증 실패 테스트
     */
    public function test_register_validates_required_fields(): void
    {
        $result = $this->withBodyFormat('json')
            ->post('/api/v1/auth/register', [
                'email' => 'invalid-email'
            ]);

        $result->assertStatus(422);
        $result->assertJSONFragment([
            'status' => 'error',
            'code' => 422
        ]);
    }

    /**
     * @test
     * POST /api/v1/auth/login
     * 로그인 성공 테스트
     */
    public function test_login_with_valid_credentials(): void
    {
        // 먼저 사용자 등록
        $this->registerTestUser();

        // 로그인 시도
        $result = $this->withBodyFormat('json')
            ->post('/api/v1/auth/login', [
                'email' => $this->testUser['email'],
                'password' => $this->testUser['password']
            ]);

        $result->assertStatus(200);
        $result->assertJSONFragment([
            'status' => 'success',
            'code' => 200
        ]);

        // 토큰 저장 (다른 테스트에서 사용)
        $json = $result->getJSON();
        $this->token = $json->data->token;

        $this->assertNotEmpty($this->token);
        $this->assertObjectHasProperty('user', $json->data);
    }

    /**
     * @test
     * POST /api/v1/auth/login
     * 잘못된 자격증명으로 로그인 실패 테스트
     */
    public function test_login_fails_with_invalid_credentials(): void
    {
        $result = $this->withBodyFormat('json')
            ->post('/api/v1/auth/login', [
                'email' => 'wrong@example.com',
                'password' => 'wrongpassword'
            ]);

        $result->assertStatus(401);
        $result->assertJSONFragment([
            'status' => 'error',
            'code' => 401
        ]);
    }

    /**
     * @test
     * GET /api/v1/auth/me
     * 인증된 사용자 정보 조회 테스트
     */
    public function test_get_current_user_info_with_valid_token(): void
    {
        // 로그인하여 토큰 획득
        $this->loginAndGetToken();

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->get('/api/v1/auth/me');

        $result->assertStatus(200);
        $result->assertJSONFragment([
            'status' => 'success'
        ]);

        $json = $result->getJSON();
        $this->assertObjectHasProperty('data', $json);
        $this->assertEquals($this->testUser['email'], $json->data->email);
    }

    /**
     * @test
     * GET /api/v1/auth/me
     * 토큰 없이 접근 실패 테스트
     */
    public function test_get_current_user_fails_without_token(): void
    {
        $result = $this->get('/api/v1/auth/me');

        $result->assertStatus(401);
        $result->assertJSONFragment([
            'status' => 'error',
            'code' => 401
        ]);
    }

    /**
     * @test
     * POST /api/v1/auth/refresh
     * 토큰 갱신 테스트
     */
    public function test_refresh_token_with_valid_token(): void
    {
        // 로그인하여 토큰 획득
        $this->loginAndGetToken();

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->post('/api/v1/auth/refresh');

        $result->assertStatus(200);
        $result->assertJSONFragment([
            'status' => 'success'
        ]);

        $json = $result->getJSON();
        $this->assertObjectHasProperty('data', $json);
        $this->assertObjectHasProperty('token', $json->data);

        // 새 토큰이 이전 토큰과 다른지 확인
        $newToken = $json->data->token;
        $this->assertNotEquals($this->token, $newToken);
    }

    /**
     * @test
     * POST /api/v1/auth/logout
     * 로그아웃 테스트
     */
    public function test_logout_with_valid_token(): void
    {
        // 로그인하여 토큰 획득
        $this->loginAndGetToken();

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->post('/api/v1/auth/logout');

        $result->assertStatus(200);
        $result->assertJSONFragment([
            'status' => 'success'
        ]);

        // 로그아웃 후 토큰이 무효화되었는지 확인
        $meResult = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->get('/api/v1/auth/me');

        $meResult->assertStatus(401);
    }

    // Helper Methods

    /**
     * 테스트용 사용자 등록
     */
    protected function registerTestUser(): void
    {
        $this->withBodyFormat('json')
            ->post('/api/v1/auth/register', [
                'email' => $this->testUser['email'],
                'password' => $this->testUser['password'],
                'name' => $this->testUser['name']
            ]);
    }

    /**
     * 로그인하여 토큰 획득
     */
    protected function loginAndGetToken(): void
    {
        // 사용자가 없으면 먼저 등록
        $this->registerTestUser();

        $result = $this->withBodyFormat('json')
            ->post('/api/v1/auth/login', [
                'email' => $this->testUser['email'],
                'password' => $this->testUser['password']
            ]);

        $json = $result->getJSON();
        $this->token = $json->data->token ?? null;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // 테스트 후 정리 작업 (필요시)
    }
}

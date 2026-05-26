<?php

namespace Tests\Api;

use App\Database\Seeds\TestSeeder;
use App\Models\TenantModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\Fabricator;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\Libraries\MediaStorage\FakeMediaStorage;

/**
 * Media API Tests
 *
 * OpenAPI 스펙 기반 업로드 미디어 API 테스트
 * 참조: docs/openapi.yaml - Media endpoints
 */
#[Group('api')]
class MediaApiTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $seed = TestSeeder::class;
    protected $migrate = true;
    protected $namespace = null;
    protected $refresh = true;
    protected $tenant;
    protected FakeMediaStorage $fakeStorage;
    protected $userModel;
    protected $admin;
    protected array $tempFiles = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->resetServices();

        $this->tenant = (new Fabricator(TenantModel::class))->create();

        service('tenant')->setTenant($this->tenant);

        $this->userModel = auth()->getProvider();
        $this->admin = $this->userModel->findByCredentials(['email' => 'admin@example.com']);
        $this->userModel->update($this->admin->id, ['tenant_id' => $this->tenant->id]);

        $this->fakeStorage = new FakeMediaStorage();
        Services::injectMock('mediaStorage', $this->fakeStorage);

        service('superglobals')->setFilesArray([]);
    }

    /**
     * @test 업로드된 이미지가 성공적으로 업로드되는지 테스트
     */
    public function test_upload_image_success(): void
    {
        $this->makeFakeUploadedFile();

        $response = $this->withHeaders($this->getAuthHeader())
            ->post('/api/v1/media/upload');

        $response->assertStatus(201);

        $json = json_decode($response->getJSON(), true);

        $this->assertSame('image/jpeg', $json['data']['mime_type']);
        $this->assertSame('test.jpg', $json['data']['original_name']);

        $this->assertArrayHasKey('id', $json['data']);
        $this->assertArrayHasKey('path', $json['data']);

        $this->seeInDatabase('media', ['tenant_id' => $this->tenant->id]);
        $this->assertCount(1, $this->fakeStorage->getStoredFiles());
    }

    /**
     * @test 업로드 요청에 인증 토큰이 없을 경우 실패하는지 테스트
     */
    public function test_upload_requires_authentication(): void
    {
        $response = $this->post('/api/v1/media/upload');

        $response->assertStatus(401);
        $this->assertCount(0, $this->fakeStorage->getStoredFiles());
    }

    /**
     * @test 업로드 요청에 파일이 없을 경우 실패하는지 테스트
     */
    public function test_upload_fails_without_file(): void
    {
        $response = $this->withHeaders($this->getAuthHeader())
            ->post('/api/v1/media/upload');

        $response->assertStatus(422);

        $this->assertCount(0, $this->fakeStorage->getStoredFiles());
    }

    /**
     * @test 업로드 요청에 유효하지 않은 MIME 타입의 파일이 있을 경우 실패하는지 테스트
     */
    public function test_upload_rejects_invalid_mime(): void
    {
        $this->makeFakeUploadedFile('test.txt', 'text/plain');

        $response = $this->withHeaders($this->getAuthHeader())
            ->post('/api/v1/media/upload');

        $response->assertStatus(422);

        $this->assertCount(0, $this->fakeStorage->getStoredFiles());
    }

    /**
     * @test 업로드 요청에 파일 크기가 제한을 초과하는 경우 실패하는지 테스트
     */
    public function test_upload_rejects_oversize_file(): void
    {
        $this->makeFakeUploadedFile('test.jpg', 'image/jpeg', 3 * 1024 * 1024);

        $response = $this->withHeaders($this->getAuthHeader())
            ->post('/api/v1/media/upload');

        $response->assertStatus(422);

        $this->assertCount(0, $this->fakeStorage->getStoredFiles());
    }

    protected function tearDown(): void
    {
        foreach ($this->tempFiles as $tempFile) {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
        $this->tempFiles = [];

        $path = WRITEPATH . 'uploads/' . $this->tenant->id;

        if (is_dir($path)) {
            foreach (glob($path . '/*') as $file) {
                unlink($file);
            }
            rmdir($path);
        }

        $_FILES = [];

        parent::tearDown();
    }

    // Helper methods
    private function makeFakeUploadedFile(string $filename = 'test.jpg', string $mimeType = 'image/jpeg', ?int $size = null): void
    {
        $tempName = tempnam(sys_get_temp_dir(), 'phpunit_upload_');
        $this->tempFiles[] = $tempName;

        // jpeg 바이너리 생성
        $image = imagecreatetruecolor(10, 10);

        match ($mimeType) {
            'image/jpeg' => imagejpeg($image, $tempName),
            'image/png' => imagepng($image, $tempName),
            'image/gif' => imagegif($image, $tempName),
            default => file_put_contents($tempName, 'plain text'),  // 거부 케이스용
        };

        imagedestroy($image);

        service('superglobals')->setFilesArray([
            'file' => [
                'name'     => $filename,
                'type'     => $mimeType,
                'tmp_name' => $tempName,
                'error'    => UPLOAD_ERR_OK,
                'size'     => $size ?? filesize($tempName),
            ],
        ]);
    }

    private function getAuthHeader(): array
    {
        $admin = $this->userModel->findById($this->admin->id);
        $admin->addGroup('admin');
        $token = $admin->generateAccessToken('test')->raw_token;

        return [
            'Authorization' => 'Bearer ' . $token,
            'X-Tenant-Slug' => $this->tenant->subdomain,
        ];
    }
}

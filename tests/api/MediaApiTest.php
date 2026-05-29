<?php

namespace Tests\Api;

use App\Database\Seeds\TestSeeder;
use App\Enums\MediaType;
use App\Models\MediaModel;
use App\Models\TenantModel;
use CodeIgniter\Shield\Entities\User;
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

        $response->assertRedirect();

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

    /**
     * @test 인증 토큰 없이 미디어 목록을 요청하면 리다이렉트(302) 반환
     */
    public function test_index_requires_authentication(): void
    {
        $response = $this->get('/api/v1/media');

        $response->assertStatus(302);
        $response->assertRedirect();
    }

    /**
     * @test 본인 테넌트 미디어만 조회되고 타 테넌트 미디어는 노출되지 않음
     */
    public function test_index_returns_only_own_tenant_media(): void
    {
        $otherTenant = (new Fabricator(TenantModel::class))->create();

        $myMediaId = $this->createMediaFixture($this->tenant->id, ['original_name' => 'my-tenant.jpg']);
        $otherMediaId = $this->createMediaFixture($otherTenant->id, ['original_name' => 'other-tenant.jpg']);

        $response = $this->withHeaders($this->getAuthHeader())->get('/api/v1/media');

        $response->assertStatus(200);

        $json = json_decode($response->getJSON(), true);
        $ids = array_column($json['data']['items'], 'id');

        $this->assertContains($myMediaId, $ids, '본인 테넌트 미디어가 목록에 포함되어야 함');
        $this->assertNotContains($otherMediaId, $ids, '타 테넌트 미디어는 목록에 노출되지 않아야 함');
    }

    /**
     * @test 페이지네이션 기본 perPage=20 동작 및 메타 응답
     */
    public function test_index_paginates_with_default_per_page_20(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $this->createMediaFixture($this->tenant->id);
        }

        $response = $this->withHeaders($this->getAuthHeader())->get('/api/v1/media');

        $response->assertStatus(200);

        $json = json_decode($response->getJSON(), true);

        $this->assertCount(20, $json['data']['items']);
        $this->assertSame(25, $json['data']['pagination']['total']);
        $this->assertSame(20, $json['data']['pagination']['per_page']);
        $this->assertSame(1, $json['data']['pagination']['current_page']);
        $this->assertSame(2, $json['data']['pagination']['last_page']);

        $response2 = $this->withHeaders($this->getAuthHeader())->get('/api/v1/media?page=2');
        $json2 = json_decode($response2->getJSON(), true);

        $this->assertCount(5, $json2['data']['items']);
        $this->assertSame(2, $json2['data']['pagination']['current_page']);
    }

    /**
     * @test 미디어가 없을 때 빈 items 배열과 pagination 메타 반환
     */
    public function test_index_returns_empty_items_when_no_media(): void
    {
        $response = $this->withHeaders($this->getAuthHeader())->get('/api/v1/media');

        $response->assertStatus(200);

        $json = json_decode($response->getJSON(), true);

        $this->assertArrayHasKey('items', $json['data']);
        $this->assertArrayHasKey('pagination', $json['data']);
        $this->assertCount(0, $json['data']['items']);
        $this->assertSame(0, $json['data']['pagination']['total']);
    }

    /**
     * @test 본인 테넌트 미디어 상세 조회 시 200과 transformer 응답 반환
     */
    public function test_show_returns_own_tenant_media(): void
    {
        $mediaId = $this->createMediaFixture($this->tenant->id, [
            'original_name' => 'detail.jpg',
            'mime_type'     => 'image/jpeg',
        ]);

        $response = $this->withHeaders($this->getAuthHeader())->get('/api/v1/media/' . $mediaId);

        $response->assertStatus(200);

        $json = json_decode($response->getJSON(), true);

        $this->assertSame($mediaId, $json['data']['id']);
        $this->assertSame('detail.jpg', $json['data']['original_name']);
        $this->assertSame('image/jpeg', $json['data']['mime_type']);
    }

    /**
     * @test 타 테넌트 미디어 ID로 조회 시 404 반환 (테넌트 격리)
     */
    public function test_show_returns_404_for_other_tenant(): void
    {
        $otherTenant = (new Fabricator(TenantModel::class))->create();
        $otherMediaId = $this->createMediaFixture($otherTenant->id);

        $response = $this->withHeaders($this->getAuthHeader())->get('/api/v1/media/' . $otherMediaId);

        $response->assertStatus(404);
    }

    /**
     * @test 존재하지 않는 미디어 ID 조회 시 404 반환
     */
    public function test_show_returns_404_for_nonexistent_id(): void
    {
        $response = $this->withHeaders($this->getAuthHeader())->get('/api/v1/media/999999');

        $response->assertStatus(404);
    }

    /**
     * @test 인증 토큰 없이 상세 조회 시 리다이렉트(302) 반환
     */
    public function test_show_requires_authentication(): void
    {
        $mediaId = $this->createMediaFixture($this->tenant->id);

        $response = $this->get('/api/v1/media/' . $mediaId);

        $response->assertStatus(302);
        $response->assertRedirect();
    }

    /**
     * @test 인증 토큰 없이 삭제 시 리다이렉트(302) 반환
     */
    public function test_delete_requires_authentication(): void
    {
        $mediaId = $this->createMediaFixture($this->tenant->id);

        $response = $this->delete('/api/v1/media/' . $mediaId);

        $response->assertStatus(302);
        $response->assertRedirect();

        $this->seeInDatabase('media', ['id' => $mediaId]);
        $this->assertTrue($this->fakeStorage->hasFile(
            model(MediaModel::class)->find($mediaId)->path
        ));
    }

    /**
     * @test 타 테넌트 미디어 삭제 시 404 반환 (격리 — 존재 여부 노출 차단)
     */
    public function test_delete_returns_404_for_other_tenant(): void
    {
        $otherTenant = (new Fabricator(TenantModel::class))->create();
        $otherMediaId = $this->createMediaFixture($otherTenant->id);
        $otherPath = model(MediaModel::class)->find($otherMediaId)->path;

        $response = $this->withHeaders($this->getAuthHeader())
            ->delete('/api/v1/media/' . $otherMediaId);

        $response->assertStatus(404);

        $this->seeInDatabase('media', ['id' => $otherMediaId]);
        $this->assertTrue($this->fakeStorage->hasFile($otherPath));
    }

    /**
     * @test 존재하지 않는 미디어 ID 삭제 시 404 반환
     */
    public function test_delete_returns_404_for_nonexistent_id(): void
    {
        $response = $this->withHeaders($this->getAuthHeader())
            ->delete('/api/v1/media/999999');

        $response->assertStatus(404);
    }

    /**
     * @test 업로더 본인이 자신의 미디어 삭제 시 204 + DB row + Storage 파일 정리
     */
    public function test_delete_by_uploader_succeeds(): void
    {
        $uploader = $this->createTenantUser($this->tenant->id, 'uploader@example.com', ['user']);
        $mediaId = $this->createMediaFixture($this->tenant->id, ['uploader_id' => $uploader->id]);
        $path = model(MediaModel::class)->find($mediaId)->path;

        $response = $this->withHeaders($this->getAuthHeaderForUser($uploader))
            ->delete('/api/v1/media/' . $mediaId);

        $response->assertStatus(204);

        $this->dontSeeInDatabase('media', ['id' => $mediaId]);
        $this->assertFalse($this->fakeStorage->hasFile($path), '업로더 본인 삭제 시 Storage 파일도 정리되어야 함');
    }

    /**
     * @test 같은 테넌트의 admin이 타인 업로드 미디어 삭제 시 204 + 파일 정리
     */
    public function test_delete_by_admin_for_other_uploader_succeeds(): void
    {
        $uploader = $this->createTenantUser($this->tenant->id, 'uploader2@example.com', ['user']);
        $mediaId = $this->createMediaFixture($this->tenant->id, ['uploader_id' => $uploader->id]);
        $path = model(MediaModel::class)->find($mediaId)->path;

        $response = $this->withHeaders($this->getAuthHeader())
            ->delete('/api/v1/media/' . $mediaId);

        $response->assertStatus(204);

        $this->dontSeeInDatabase('media', ['id' => $mediaId]);
        $this->assertFalse($this->fakeStorage->hasFile($path), 'admin 삭제 시 Storage 파일도 정리되어야 함');
    }

    /**
     * @test 같은 테넌트의 다른 일반 사용자가 타인 업로드 미디어 삭제 시 403 + DB/Storage 유지
     */
    public function test_delete_by_other_regular_user_returns_403(): void
    {
        $uploader = $this->createTenantUser($this->tenant->id, 'uploader3@example.com', ['user']);
        $intruder = $this->createTenantUser($this->tenant->id, 'intruder@example.com', ['user']);
        $mediaId = $this->createMediaFixture($this->tenant->id, ['uploader_id' => $uploader->id]);
        $path = model(MediaModel::class)->find($mediaId)->path;

        $response = $this->withHeaders($this->getAuthHeaderForUser($intruder))
            ->delete('/api/v1/media/' . $mediaId);

        $response->assertStatus(403);

        $this->seeInDatabase('media', ['id' => $mediaId]);
        $this->assertTrue($this->fakeStorage->hasFile($path), '권한 없을 시 Storage 파일은 유지되어야 함');
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
    private function makeFakeUploadedFile(string $filename = 'test.jpg', string $mimeType = 'image/jpeg',
                                          ?int   $size = null): void
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

    private function createTenantUser(int $tenantId, string $email, array $groups = ['user']): User
    {
        $user = new User([
            'email'    => $email,
            'username' => explode('@', $email)[0],
            'password' => 'password123!',
        ]);

        $this->userModel->save($user);
        $newId = $this->userModel->getInsertID();
        $this->userModel->update($newId, ['tenant_id' => $tenantId]);

        $created = $this->userModel->findById($newId);
        foreach ($groups as $group) {
            $created->addGroup($group);
        }

        return $created;
    }

    private function getAuthHeaderForUser(User $user): array
    {
        $token = $user->generateAccessToken('test')->raw_token;

        return [
            'Authorization' => 'Bearer ' . $token,
            'X-Tenant-Slug' => $this->tenant->subdomain,
        ];
    }

    private function createMediaFixture(int $tenantId, array $overrides = []): int
    {
        $uniq = uniqid('fixture_', true);

        $data = array_merge([
            'tenant_id'     => $tenantId,
            'uploader_id'   => $this->admin->id,
            'type'          => MediaType::Image->value,
            'mime_type'     => 'image/jpeg',
            'filename'      => $uniq . '.jpg',
            'original_name' => 'fixture.jpg',
            'file_size'     => 1024,
            'path'          => $tenantId . '/' . $uniq . '.jpg',
        ], $overrides);

        $id = model(MediaModel::class)->insert($data, true);

        $this->fakeStorage->addExisting($data['path']);

        return $id;
    }
}

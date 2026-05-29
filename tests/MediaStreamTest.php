<?php

namespace Tests;

use App\Database\Seeds\TestSeeder;
use App\Models\MediaModel;
use App\Models\TenantModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\Fabricator;
use CodeIgniter\Test\FeatureTestTrait;

class MediaStreamTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $refresh = true;
    protected $migrate = true;
    protected $namespace = null;
    protected array $createdPaths = [];
    protected $seed = TestSeeder::class;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resetServices();

        $this->admin = auth()->getPRovider()->findByCredentials(['email' => 'admin@example.com']);
    }

    protected function tearDown(): void
    {
        foreach ($this->createdPaths as $path) {
            if (file_exists($path)) {
                unlink($path);
            }
        }

        parent::tearDown();
    }

    /**
     * @test 자기 테넌트에서 파일을 스트림으로 제공하는지 테스트
     */
    public function test_stream_serves_file_for_own_tenant(): void
    {
        // Given:
        $tenant = (new Fabricator(TenantModel::class))->create();
        $media = $this->createMediaFile($tenant->id, 'test.jpg', 'image/jpeg', 'FAKE_IMAGE_BYTES');

        // When:
        $response = $this->get("/{$tenant->subdomain}/media/{$media->filename}");

        // Then
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/jpeg');
        $this->assertStringContainsString('FAKE_IMAGE_BYTES', $response->getBody());
    }

    /**
     * @test 다른 테넌트의 파일을 스트림으로 제공하면 404를 반환하는지 테스트
     */
    public function test_stream_other_tenant_returns_404(): void
    {
        // Given:
        $tenantA = (new Fabricator(TenantModel::class))->create();
        $tenantB = (new Fabricator(TenantModel::class))->create();

        $mediaB = $this->createMediaFile($tenantB->id, 'b.jpg', 'image/jpeg', 'B_BYTES');

        // When:
        $this->expectException(PageNotFoundException::class);

        $this->get("/{$tenantA->subdomain}/media/{$mediaB->filename}");
    }

    /**
     * helper methods
     */
    private function createMediaFile(int $tenantId, string $filename, string $mimeType, string $bytes): object
    {
        $dir = WRITEPATH . "uploads/{$tenantId}";

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $absolute = "{$dir}/{$filename}";

        file_put_contents($absolute, $bytes);

        $this->createdPaths[] = $absolute;


        $id = model(MediaModel::class)
            ->insert([
                'tenant_id'     => $tenantId,
                'filename'      => $filename,
                'original_name' => $filename,
                'mime_type'     => $mimeType,
                'file_size'     => strlen($bytes),
                'path'          => "uploads/{$tenantId}/{$filename}",
                'uploader_id'   => $this->admin->id,
                'type'          => 'image',
            ], true);

        return model(MediaModel::class)->find($id);
    }
}

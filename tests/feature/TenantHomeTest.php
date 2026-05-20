<?php

namespace Tests\Feature;

use App\Models\TenantModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

class TenantHomeTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $refresh = true;
    protected $namespace = 'App';

    public function test_existing_tenant_home_renders(): void
    {
        $tenant = fake(TenantModel::class, [
            'subdomain' => 'acme',
            'name' => 'Acme Inc.'
        ]);

        $result = $this->get("/{$tenant->subdomain}");

        $result->assertStatus(200);
        $result->assertSee('Acme Inc.');
    }

    public function test_unknown_tenant_returns_404(): void
    {
        $result = $this->get('/nonexistent-tenant-xyz');
        $result->assertStatus(404);
    }
}

<?php

namespace Tests\Unit;

use App\Models\TenantModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class TenantModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate   = true;
    protected $DBGroup   = 'tests';
    protected $namespace = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new TenantModel($this->db);
    }

    public function testIndex(): void
    {
        $result = $this->model->findAll();
        $this->assertEmpty($result);
    }

    public function testCreate(): void
    {
        $result = $this->model->insert([
            'name' => 'Test Tenant',
            'subdomain' => 'test',
        ]);

        $this->assertNotFalse($result);
        $this->seeInDatabase('tenants', ['id' => $result]);
    }

    public function testValidationFail(): void
    {
        $result = $this->model->insert([
            'name' => 'Test Tenant',
        ]);

        $this->assertFalse($result);
        $this->assertNotEmpty($this->model->errors());
    }

    public function testUpdate(): void
    {
        $this->model->insert(['subdomain' => 'test', 'name' => '테스트']);
        $id = $this->model->getInsertID();

        $result = $this->model->update($id, ['name' => 'Updated Test']);

        $this->assertNotFalse($result);
        $this->seeInDatabase('tenants', ['id' => $id, 'name' => 'Updated Test']);
    }

    public function testDelete(): void
    {
        $this->model->insert(['subdomain' => 'test', 'name' => '테스트']);
        $id = $this->model->getInsertID();

        $result = $this->model->delete($id);

        $this->assertNotFalse($result);
        $this->dontSeeInDatabase('tenants', ['id' => $id]);
    }
}

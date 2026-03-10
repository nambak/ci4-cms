<?php

namespace App\Traits;

trait TenantScopeTrait
{
    protected function initialize()
    {
        $this->beforeFind[] = 'beforeTenantFind';
        $this->beforeInsert[] = 'beforeTenantInsert';
        $this->beforeUpdate[] = 'beforeTenantUpdate';
        $this->beforeDelete[] = 'beforeTenantDelete';
    }

    protected function beforeTenantFind($data)
    {
        $this->applyTenantScope();

        return $data;
    }

    protected function beforeTenantInsert($data)
    {
        return $this->applyTenantId($data);
    }

    protected function beforeTenantUpdate($data)
    {
        $this->applyTenantScope();

        return $this->applyTenantId($data);
    }

    protected function beforeTenantDelete($data)
    {
        $this->applyTenantScope();

        return $data;
    }

    private function applyTenantId(array $data): array
    {
        $data['data']['tenant_id'] = service('tenant')->getId();

        return $data;
    }

    private function applyTenantScope(): void
    {
        $this->where('tenant_id', service('tenant')->getId());
    }
}

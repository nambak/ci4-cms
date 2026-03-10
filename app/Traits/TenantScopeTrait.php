<?php

namespace App\Traits;

trait TenantScopeTrait
{
    protected function initialize()
    {
        $this->beforeFind[] = 'beforeTenantFind';
        $this->beforeInsert[] = 'beforeTenantInsert';
        $this->beforeUpdate[] = 'beforeTenantUpdate';
    }

    protected function beforeTenantFind($data)
    {
        $this->where('tenant_id', service('tenant')->getId());

        return $data;
    }

    protected function beforeTenantInsert($data)
    {
        return $this->applyTenantId($data);
    }

    protected function beforeTenantUpdate($data)
    {
        return $this->applyTenantId($data);
    }

    protected function applyTenantId(array $data): array
    {
        $data['data']['tenant_id'] = service('tenant')->getId();

        return $data;
    }
}

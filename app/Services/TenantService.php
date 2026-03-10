<?php

namespace App\Services;

use App\Entities\TenantEntity as TenantEntity;

class TenantService
{
    private ?TenantEntity $currentTenant = null;

    public function setTenant(TenantEntity $tenant): void
    {
        $this->currentTenant = $tenant;
    }

    public function getTenant(): ?TenantEntity
    {
        return $this->currentTenant;
    }

    public function getId(): ?int
    {
        return $this->currentTenant?->id ?? null;
    }

    public function hasTenant(): bool
    {
        return $this->currentTenant !== null;
    }
}
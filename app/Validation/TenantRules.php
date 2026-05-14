<?php

namespace App\Validation;

class TenantRules
{
    public function is_not_unique_in_tenant($value, ?string $params, array $data, ?string &$error = null): bool
    {
        $param = explode('.', $params);
        $table = $param[0];
        $column = $param[1];
        $tenantId = auth()->user()?->tenant_id;

        if (!$tenantId) {
            return false;
        }

        $count = db_connect()
            ->table($table)
            ->where($column, $value)
            ->where('tenant_id', $tenantId)
            ->countAllResults();

        return $count > 0;
    }
}
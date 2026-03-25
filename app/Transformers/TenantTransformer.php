<?php

namespace App\Transformers;

use CodeIgniter\API\BaseTransformer;

class TenantTransformer extends BaseTransformer
{
    /**
     * Transform the resource into an array.
     *
     * @param mixed $resource
     *
     * @return array<string, mixed>
     */
    public function toArray(mixed $resource): array
    {
        return [
            'id' => $resource['id'],
            'name' => $resource['name'],
            'subdomain' => $resource['subdomain'],
            'created_at' => $resource['created_at'],
            'updated_at' => $resource['updated_at'],
        ];
    }

    protected function getAllowedFields(): ?array
    {
        return ['id', 'name', 'subdomain', 'created_at', 'updated_at'];
    }
}

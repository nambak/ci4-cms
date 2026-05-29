<?php

declare(strict_types=1);

namespace App\Transformers;

use CodeIgniter\API\BaseTransformer;

class MediaTransformer extends BaseTransformer
{
    public function toArray(mixed $resource): array
    {
        $tenant = service('tenant')->getTenant();

        return [
            'id'            => $resource['id'],
            'filename'      => $resource['filename'],
            'original_name' => $resource['original_name'],
            'mime_type'     => $resource['mime_type'],
            'size'          => $resource['file_size'] ?? $resource['size'] ?? null,
            'path'          => $resource['path'],
            'url'           => !is_null($tenant)
                ? site_url("{$tenant->subdomain}/media/{$resource['filename']}")
                : null,
            'type'          => ($resource['type'] ?? null) instanceof \BackedEnum
                ? $resource['type']->value
                : ($resource['type'] ?? null),
            'created_at'    => $resource['created_at'],
            'updated_at'    => $resource['updated_at'],
        ];
    }

    protected function getAllowedFields(): ?array
    {
        return [
            'id',
            'filename',
            'original_name',
            'mime_type',
            'size',
            'path',
            'url',
            'type',
            'created_at',
            'updated_at'
        ];
    }
}

<?php

namespace App\Traits;

use CodeIgniter\Model;
use RuntimeException;

/** @mixin Model */
trait SlugGeneratorTrait
{
    protected function generateSlug(array $data): array
    {
        if (!property_exists($this, 'slugSource')) {
            throw new RuntimeException('slugSource property is not defined in the model');
        }

        if (isset($data['data'][$this->slugSource])) {
            $slug = mb_url_title($data['data'][$this->slugSource], '-', true);

            $tenantId = isset($data['data']['tenant_id'])
                ? $data['data']['tenant_id']
                : $this->find($data['id'])->tenant_id;

            $existingSlugCount = $this
                ->where('slug LIKE', "{$slug}%")
                ->where('tenant_id', $tenantId)
                ->countAllResults();

            if ($existingSlugCount > 0) {
                $slug .= '-' . ($existingSlugCount + 1);
            }

            $data['data']['slug'] = $slug;
        }

        return $data;
    }
}
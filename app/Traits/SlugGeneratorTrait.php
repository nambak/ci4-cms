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

        $id = isset($data['id']) ? (is_array($data['id']) ? $data['id'][0] : $data['id']) : null;

        if (isset($data['data'][$this->slugSource])) {
            $slug = mb_url_title($data['data'][$this->slugSource], '-', true);

            if (isset($data['data']['tenant_id'])) {
                $tenantId = $data['data']['tenant_id'];
            } else {
                $result = $this->find($id);

                if (!$result) {
                    throw new RuntimeException('Failed to find the model with the given ID');
                }

                $tenantId = $result->tenant_id;
            }

            $existingSlugCount = $this
                ->groupStart()
                    ->where('slug', $slug)
                    ->orLike('slug', $slug . '-', 'after')  // "slug-%" 패턴
                ->groupEnd()
                ->where('tenant_id', $tenantId)
                ->when($id, function ($query) use ($id) {
                    return $query->where('id !=', $id);
                })
                ->countAllResults();

            if ($existingSlugCount > 0) {
                $slug .= '-' . ($existingSlugCount + 1);
            }

            $data['data']['slug'] = $slug;
        }

        return $data;
    }
}
<?php

namespace App\Controllers\Tenant;

use App\Controllers\BaseController;
use App\Models\MediaModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

class MediaController extends BaseController
{
    public function stream(string $tenantSlug, string $filename): ResponseInterface
    {
        $tenant = service('tenant')->getTenant();

        $media = model(MediaModel::class)
            ->where('tenant_id', $tenant->id)
            ->where('filename', $filename)
            ->first();

        if (is_null($media)) {
            throw new PageNotFoundException('Media not found');
        }

        $absolutePath = WRITEPATH . $media->path;
        $realPath = realpath($absolutePath);
        $realRoot = realpath(WRITEPATH . 'uploads');

        if (!$realPath || !$realRoot || !str_starts_with($realPath, $realRoot . DIRECTORY_SEPARATOR)) {
            throw new PageNotFoundException('Invalid media file path');
        }

        if (!file_exists($realPath)) {
            throw new PageNotFoundException('Media file not found');
        }

        return $this->response
            ->setHeader('Content-Type', $media->mime_type)
            ->setHeader('Content-Length', (string) filesize($realPath))
            ->setHeader('Cache-Control', 'public, max-age=31536000')
            ->setBody(file_get_contents($realPath));
    }
}

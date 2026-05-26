<?php

namespace App\Libraries\MediaStorage;

use CodeIgniter\HTTP\Files\UploadedFile;
use RuntimeException;

class LocalMediaStorage implements MediaStorageInterface
{
    public function store(UploadedFile $file, int $tenantId): string
    {
        $path = WRITEPATH . 'uploads/' . $tenantId;

        if (!is_dir($path) && !mkdir($path, 0755, true) && !is_dir($path)) {
            throw new RuntimeException("Failed to create directory: {$path}");
        }

        $fileName = $file->getRandomName();
        $file->move($path, $fileName);

        return "uploads/{$tenantId}/{$fileName}";
    }

    public function delete(string $path): void
    {
        $absolutePath = WRITEPATH . $path;

        if (file_exists($absolutePath)) {
            unlink($absolutePath);
        }
    }
}
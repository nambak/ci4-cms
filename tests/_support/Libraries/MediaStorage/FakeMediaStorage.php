<?php

namespace Tests\Support\Libraries\MediaStorage;

use App\Libraries\MediaStorage\MediaStorageInterface;
use CodeIgniter\HTTP\Files\UploadedFile;

class FakeMediaStorage implements MediaStorageInterface
{
    private array $stored = [];

    public function store(UploadedFile $file, int $tenantId): string
    {
        $fileName = $file->getRandomName();
        $relativePath = "uploads/{$tenantId}/{$fileName}";

        $this->stored[] = $relativePath;

        return $relativePath;
    }

    public function delete(string $path): void
    {
        $key = array_search($path, $this->stored, true);

        if ($key !== false) {
            unset($this->stored[$key]);
        }
    }

    public function addExisting(string $path): void
    {
        $this->stored[] = $path;
    }

    public function hasFile(string $path): bool
    {
        return in_array($path, $this->stored, true);
    }

    public function getStoredFiles(): array
    {
        return $this->stored;
    }
}

<?php

namespace App\Libraries\MediaStorage;

use CodeIgniter\HTTP\Files\UploadedFile;
use Exception;

interface MediaStorageInterface
{
    public function store(UploadedFile $file, int $tenantId):string;

    public function delete(string $path): void;

}
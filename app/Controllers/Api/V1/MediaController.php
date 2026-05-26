<?php

namespace App\Controllers\Api\V1;

use App\Enums\MediaType;
use App\Libraries\MediaStorage\MediaStorageInterface;
use App\Models\MediaModel;
use App\Transformers\MediaTransformer;
use CodeIgniter\Router\Attributes\Filter;
use Config\Services;
use Exception;

class MediaController extends BaseApiController
{
    protected MediaTransformer $transformer;
    protected MediaStorageInterface $storage;
    protected $modelName = MediaModel::class;

    public function __construct()
    {
        $this->transformer = new MediaTransformer();
        $this->storage = Services::mediaStorage();
    }

    #[Filter(by: 'tokens')]
    public function upload()
    {
        $rules = [
            'file' => 'uploaded[file]|mime_in[file,image/jpeg,image/png,image/gif]|max_size[file,2048]',
        ];

        if (!$this->validateData([], $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $uploadedFile = $this->request->getFile('file');

        $originalName = $uploadedFile->getClientName();
        $mimeType = $uploadedFile->getMimeType();
        $fileSize = $uploadedFile->getSize();

        $tenantId = auth()->user()->tenant_id;

        try {
            $relativePath = $this->storage->store($uploadedFile, $tenantId);
        } catch (Exception $exception) {
            return $this->failServerError('Failed to store media file');
        }

        $insertResult = $this->model->insert([
            'tenant_id'     => $tenantId,
            'original_name' => $originalName,
            'mime_type'     => $mimeType,
            'file_size'     => $fileSize,
            'path'          => $relativePath,
            'uploader_id'   => auth()->user()->id,
            'type'          => MediaType::fromMimeType($mimeType)->value,
            'filename'      => basename($relativePath)
        ]);

        if ($insertResult === false) {
            $this->storage->delete($relativePath);

            return $this->failServerError('Failed to create media');
        }

        $insertId = $this->model->getInsertID();
        $createdMedia = $this->model->find($insertId);

        if (!$createdMedia) {
            $this->storage->delete($relativePath);
            $this->model->delete($insertId);

            return $this->failServerError('Failed to create media');
        }

        return $this->responseWithItem($this->transformer->transform($createdMedia), 201);
    }
}

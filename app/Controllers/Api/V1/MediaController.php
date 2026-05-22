<?php

namespace App\Controllers\Api\V1;

use App\Enums\MediaType;
use App\Models\MediaModel;
use App\Transformers\MediaTransformer;
use CodeIgniter\Router\Attributes\Filter;
use Exception;

class MediaController extends BaseApiController
{
    protected MediaTransformer $transformer;
    protected $modelName = MediaModel::class;

    public function __construct()
    {
        $this->transformer = new MediaTransformer();
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

        if (!$uploadedFile->isValid()) {
            return $this->failValidationErrors(['file' => 'Invalid file upload']);
        }

        $originalName = $uploadedFile->getClientName();
        $mimeType = $uploadedFile->getMimeType();
        $fileSize = $uploadedFile->getSize();

        $tenantId = auth()->user()->tenant_id;
        $path = WRITEPATH . 'uploads/' . $tenantId;

        $fileName = $uploadedFile->getRandomName();

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        try {
            $uploadedFile->move($path, $fileName);
        } catch (Exception $exception) {
            return $this->failServerError('Failed to move uploaded file');
        }

        $this->model->insert([
            'tenant_id'     => $tenantId,
            'original_name' => $originalName,
            'mime_type'     => $mimeType,
            'file_size'     => $fileSize,
            'path'          => "uploads/{$tenantId}/{$fileName}",
            'uploader_id'   => auth()->user()->id,
            'type'          => MediaType::fromMimeType($mimeType)->value,
            'filename'      => $fileName
        ]);

        $createdMedia = $this->model->find($this->model->getInsertID());

        if (!$createdMedia) {
            unlink("{$path}/{$fileName}");
            return $this->failServerError('Failed to create media');
        }

        return $this->responseWithItem($this->transformer->transform($createdMedia), 201);
    }
}

<?php

namespace App\Controllers\Api\V1;

use App\Models\PostModel;
use App\Models\TagModel;
use App\Transformers\PostTransformer;
use App\Transformers\TagTransformer;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Router\Attributes\Filter;

class TagsController extends BaseApiController
{
    protected TagTransformer $transformer;
    protected $modelName = TagModel::class;
    protected $format = 'json';
    protected $rules = ['name' => 'required|min_length[3]|max_length[255]'];

    public function __construct()
    {
        $this->transformer = new TagTransformer();
    }

    #[Filter(by: 'tokens')]
    public function index(): ResponseInterface
    {
        $tenantId = auth()->user()->tenant_id;
        $tags = $this->model->where('tenant_id', $tenantId)->findAll();

        return $this->responseWith($this->transformer->transformMany($tags));
    }

    #[Filter(by: 'tokens')]
    public function show($id = null): ResponseInterface
    {
        $tenantId = auth()->user()->tenant_id;
        $tag = $this->model->where('tenant_id', $tenantId)->find($id);

        if (!$tag) {
            return $this->failNotFound("Tag not found: $id");
        }

        return $this->respond($this->transformer->transform($tag));
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'apipermission', having: ['tags.manage'])]
    public function create(): ResponseInterface
    {
        $payload = $this->request->getJSON(true);

        if (!$payload) {
            return $this->failValidationErrors('No payload provided');
        }

        $allowedPayload = array_intersect_key($payload, $this->rules);

        if (!$allowedPayload) {
            return $this->failValidationErrors('Invalid payload');
        }

        if (!$this->validateData($allowedPayload, $this->rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $allowedPayload['tenant_id'] = auth()->user()->tenant_id;

        try {
            $this->model->insert($allowedPayload);
        } catch (DatabaseException $exception) {
            log_message('error', $exception->getMessage());
            return $this->failServerError('Database error');
        }

        $createdTag = $this->model->find($this->model->getInsertID());

        return $this->respondCreated($this->transformer->transform($createdTag));
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'apipermission', having: ['tags.manage'])]
    public function update($id = null): ResponseInterface
    {
        $tenantId = auth()->user()->tenant_id;
        $tag = $this->model
            ->where('tenant_id', $tenantId)
            ->find($id);

        if (!$tag) {
            return $this->failNotFound("Tag not found: $id");
        }

        $payload = $this->request->getJSON(true);

        if (!$payload) {
            return $this->failValidationErrors('No payload provided');
        }

        $allowedPayload = array_intersect_key($payload, $this->rules);

        if (!$allowedPayload) {
            return $this->failValidationErrors('Invalid payload');
        }

        $updateRules = array_intersect_key($this->rules, $allowedPayload);

        if (!$this->validateData($allowedPayload, $updateRules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        try {
            $this->model->update($id, $allowedPayload);
        } catch (DatabaseException $exception) {
            log_message('error', $exception->getMessage());
            return $this->failServerError('Database error');
        }

        $updatedTag = $this->model->find($id);

        return $this->responseWithItem($this->transformer->transform($updatedTag));
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'apipermission', having: ['tags.manage'])]
    public function delete($id = null): ResponseInterface
    {
        $tenantId = auth()->user()->tenant_id;
        $tag = $this->model
            ->where('tenant_id', $tenantId)
            ->find($id);

        if (!$tag) {
            return $this->failNotFound("Tag not found: $id");
        }

        try {
            $this->model->delete($id);
        } catch (DatabaseException $exception) {
            log_message('error', $exception->getMessage());
            return $this->failServerError('Database error');
        }

        return $this->respondNoContent();
    }

    #[Filter(by: 'tokens')]
    public function posts($id = null): ResponseInterface
    {
        $tenantId = auth()->user()->tenant_id;
        $tag = $this->model->where('tenant_id', $tenantId)->find($id);

        if (!$tag) {
            return $this->failNotFound("Tag not found: $id");
        }

        $posts = model(PostModel::class)->findByTag($id, $tenantId);

        return $this->responseWith((new PostTransformer())->transformMany($posts));
    }
}

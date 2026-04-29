<?php

declare(strict_types=1);

namespace App\Controllers\Api\V1;

use App\Models\CategoryModel;
use App\Transformers\CategoryTransformer;
use App\Transformers\PostTransformer;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Router\Attributes\Cache;
use CodeIgniter\Router\Attributes\Filter;

/**
 * Categories API 컨트롤러
 *
 * - index, show, posts: 인증 불필요 + 캐싱 적용 (카테고리는 변경이 적으므로 10분)
 * - create, update, delete: tokens 인증 + categories.manage 권한 필요
 */
class CategoriesController extends BaseApiController
{
    protected CategoryTransformer $transformer;
    protected $modelName = CategoryModel::class;
    protected $format = 'json';
    protected $rules = [
        'name'        => 'required|min_length[3]|max_length[255]',
        'description' => 'permit_empty|min_length[3]|max_length[255]',
    ];

    public function __construct()
    {
        $this->transformer = new CategoryTransformer();
    }

    public function index(): ResponseInterface
    {
        $categories = $this->model->findAll();

        return $this->responseWith($this->transformer->transformMany($categories));
    }

    public function show($id = null): ResponseInterface
    {
        $category = $this->model->find($id);

        if ($category === null) {
            return $this->failNotFound();
        }

        return $this->responseWithItem($this->transformer->transform($category));
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'permission', having: ['categories.manage'])]
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

        $createdCategory = $this->model->find($this->model->getInsertID());

        return $this->responseWithItem($this->transformer->transform($createdCategory), 201);
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'permission', having: ['categories.manage'])]
    public function update($id = null): ResponseInterface
    {
        $category = $this->model
            ->where('tenant_id', auth()->user()->tenant_id)
            ->find($id);

        if (!$category) {
            return $this->failNotFound("Category not found: $id");
        }

        $payload = $this->request->getJSON(true);

        if (!$payload) {
            return $this->failValidationErrors('No payload provided');
        }

        $allowedPayload = array_intersect_key($payload, $this->rules);

        if (!$allowedPayload) {
            return $this->failValidationErrors('No valid data provided');
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

        $updatedCategory = $this->model->find($id);

        return $this->responseWithItem($this->transformer->transform($updatedCategory));
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'permission', having: ['categories.manage'])]
    public function delete($id = null): ResponseInterface
    {
        $category = $this->model
            ->where('tenant_id', auth()->user()->tenant_id)
            ->find($id);

        if (!$category) {
            return $this->failNotFound("Category not found: $id");
        }

        try {
            $this->model->delete($id);
        } catch (DatabaseException $exception) {
            log_message('error', $exception->getMessage());
            return $this->failServerError('Database error');
        }

        return $this->respondNoContent();
    }

    public function posts($id = null): ResponseInterface
    {
        $category = $this->model->find($id);

        if (!$category) {
            return $this->failNotFound("Category not found: $id");
        }

        $posts = model('PostModel')
            ->where('category_id', $id)
            ->where('state', 'published')
            ->findAll();

        return $this->responseWith((new PostTransformer())->transformMany($posts));
    }

}

<?php

declare(strict_types=1);

namespace App\Controllers\Api\V1;

use App\Transformers\CategoryTransformer;
use App\Transformers\PostTransformer;
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

    public function __construct()
    {
        $this->transformer = new CategoryTransformer();
    }

    #[Cache(for: 10 * MINUTE)]
    public function index(): ResponseInterface
    {
        // TODO: $categories = model('CategoryModel')->findAll();
        // return $this->respond($this->transformer->transformMany($categories));
        return $this->fail('Not Implemented', 501);
    }

    #[Cache(for: 10 * MINUTE)]
    public function show($id = null): ResponseInterface
    {
        // TODO: $category = model('CategoryModel')->find($id);
        // if ($category === null) { return $this->failNotFound(); }
        // return $this->respond($this->transformer->transform($category));
        return $this->fail('Not Implemented', 501);
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'permission', having: ['categories.manage'])]
    public function create(): ResponseInterface
    {
        // TODO: validate, model save, return transformer result
        // return $this->respondCreated($this->transformer->transform($category));
        return $this->fail('Not Implemented', 501);
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'permission', having: ['categories.manage'])]
    public function update($id = null): ResponseInterface
    {
        // TODO: validate, model update, return transformer result
        // return $this->respond($this->transformer->transform($category));
        return $this->fail('Not Implemented', 501);
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'permission', having: ['categories.manage'])]
    public function delete($id = null): ResponseInterface
    {
        // TODO: model delete
        // return $this->respondDeleted(['id' => $id]);
        return $this->fail('Not Implemented', 501);
    }

    #[Cache(for: 5 * MINUTE)]
    public function posts($id = null): ResponseInterface
    {
        // TODO: $posts = model('PostModel')->where('category_id', $id)->findAll();
        // return $this->respond((new PostTransformer())->transformMany($posts));
        return $this->fail('Not Implemented', 501);
    }
}

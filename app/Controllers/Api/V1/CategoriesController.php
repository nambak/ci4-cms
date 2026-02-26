<?php

declare(strict_types=1);

namespace App\Controllers\Api\V1;

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
    #[Cache(for: 10 * MINUTE)]
    public function index(): ResponseInterface
    {
        return $this->response
            ->setJSON(['message' => 'Not implemented'])
            ->setStatusCode(501);
    }

    #[Cache(for: 10 * MINUTE)]
    public function show($id = null): ResponseInterface
    {
        return $this->response
            ->setJSON(['message' => 'Not implemented'])
            ->setStatusCode(501);
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'permission', having: ['categories.manage'])]
    public function create(): ResponseInterface
    {
        return $this->response
            ->setJSON(['message' => 'Not implemented'])
            ->setStatusCode(501);
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'permission', having: ['categories.manage'])]
    public function update($id = null): ResponseInterface
    {
        return $this->response
            ->setJSON(['message' => 'Not implemented'])
            ->setStatusCode(501);
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'permission', having: ['categories.manage'])]
    public function delete($id = null): ResponseInterface
    {
        return $this->response
            ->setJSON(['message' => 'Not implemented'])
            ->setStatusCode(501);
    }

    #[Cache(for: 5 * MINUTE)]
    public function posts($id = null): ResponseInterface
    {
        return $this->response
            ->setJSON(['message' => 'Not implemented'])
            ->setStatusCode(501);
    }
}

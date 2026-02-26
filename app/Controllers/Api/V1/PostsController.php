<?php

declare(strict_types=1);

namespace App\Controllers\Api\V1;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Router\Attributes\Cache;
use CodeIgniter\Router\Attributes\Filter;

/**
 * Posts API 컨트롤러
 *
 * - index, show: 인증 불필요 + 응답 캐싱 적용
 * - create: tokens 인증 + posts.create 또는 posts.manage 권한 필요
 * - update: tokens 인증 + posts.edit 또는 posts.manage 권한 필요
 * - delete: tokens 인증 + posts.delete 또는 posts.manage 권한 필요
 *
 * 주의: 클래스 레벨 Filter는 메서드별로 비활성화 불가.
 * index/show는 공개 접근이 필요하므로 메서드별 적용 방식 사용.
 */
class PostsController extends BaseApiController
{
    #[Cache(for: 5 * MINUTE)]
    public function index(): ResponseInterface
    {
        return $this->response->setJSON(['message' => 'Not implemented'])->setStatusCode(501);
    }

    #[Cache(for: 5 * MINUTE)]
    public function show($id = null): ResponseInterface
    {
        return $this->response->setJSON(['message' => 'Not implemented'])->setStatusCode(501);
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'permission', having: ['posts.create', 'posts.manage'])]
    public function create(): ResponseInterface
    {
        return $this->response->setJSON(['message' => 'Not implemented'])->setStatusCode(501);
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'permission', having: ['posts.edit', 'posts.manage'])]
    public function update($id = null): ResponseInterface
    {
        return $this->response->setJSON(['message' => 'Not implemented'])->setStatusCode(501);
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'permission', having: ['posts.delete', 'posts.manage'])]
    public function delete($id = null): ResponseInterface
    {
        return $this->response->setJSON(['message' => 'Not implemented'])->setStatusCode(501);
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'permission', having: ['posts.manage'])]
    public function publish($id = null): ResponseInterface
    {
        return $this->response->setJSON(['message' => 'Not implemented'])->setStatusCode(501);
    }

    #[Cache(for: 5 * MINUTE)]
    public function comments($id = null): ResponseInterface
    {
        return $this->response->setJSON(['message' => 'Not implemented'])->setStatusCode(501);
    }
}

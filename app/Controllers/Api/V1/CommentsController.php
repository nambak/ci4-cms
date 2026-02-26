<?php

declare(strict_types=1);

namespace App\Controllers\Api\V1;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Router\Attributes\Filter;

/**
 * Comments API 컨트롤러
 *
 * - index: 인증 불필요 (공개 조회)
 * - create, reply: tokens 인증 + comments.create 또는 comments.manage 권한 필요
 * - update, delete: tokens 인증 필요 (작성자/관리자 확인은 컨트롤러 로직에서 처리)
 * - moderate: tokens 인증 + comments.manage 권한 필요
 */
class CommentsController extends BaseApiController
{
    public function index(): ResponseInterface
    {
        return $this->response->setJSON(['message' => 'Not implemented'])->setStatusCode(501);
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'permission', having: ['comments.create', 'comments.manage'])]
    public function create(): ResponseInterface
    {
        return $this->response->setJSON(['message' => 'Not implemented'])->setStatusCode(501);
    }

    #[Filter(by: 'tokens')]
    public function update($id = null): ResponseInterface
    {
        return $this->response->setJSON(['message' => 'Not implemented'])->setStatusCode(501);
    }

    #[Filter(by: 'tokens')]
    public function delete($id = null): ResponseInterface
    {
        return $this->response->setJSON(['message' => 'Not implemented'])->setStatusCode(501);
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'permission', having: ['comments.create', 'comments.manage'])]
    public function reply($id = null): ResponseInterface
    {
        return $this->response->setJSON(['message' => 'Not implemented'])->setStatusCode(501);
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'permission', having: ['comments.manage'])]
    public function moderate($id = null): ResponseInterface
    {
        return $this->response->setJSON(['message' => 'Not implemented'])->setStatusCode(501);
    }
}

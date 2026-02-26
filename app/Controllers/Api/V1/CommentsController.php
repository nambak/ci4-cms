<?php

declare(strict_types=1);

namespace App\Controllers\Api\V1;

use App\Transformers\CommentTransformer;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Router\Attributes\Cache;
use CodeIgniter\Router\Attributes\Filter;

/**
 * Comments API 컨트롤러
 *
 * - index: 인증 불필요, 60초 캐싱 적용 (댓글 변경 메서드 구현 시 캐시 무효화 필요)
 * - create, reply: tokens 인증 + comments.create 또는 comments.manage 권한 필요
 * - update, delete: tokens 인증 필요 (작성자/관리자 확인은 컨트롤러 로직에서 처리)
 * - moderate: tokens 인증 + comments.manage 권한 필요
 */
class CommentsController extends BaseApiController
{
    protected CommentTransformer $transformer;

    public function __construct()
    {
        $this->transformer = new CommentTransformer();
    }

    #[Cache(for: 60)]
    public function index(): ResponseInterface
    {
        // TODO: $comments = model('CommentModel')->where('status', 'approved')->findAll();
        // return $this->respond($this->transformer->transformMany($comments));
        return $this->fail('Not Implemented', 501);
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'permission', having: ['comments.create', 'comments.manage'])]
    public function create(): ResponseInterface
    {
        // TODO: validate, model save, return transformer result
        // return $this->respondCreated($this->transformer->transform($comment));
        return $this->fail('Not Implemented', 501);
    }

    #[Filter(by: 'tokens')]
    public function update($id = null): ResponseInterface
    {
        // TODO: ownership check, validate, model update
        // return $this->respond($this->transformer->transform($comment));
        return $this->fail('Not Implemented', 501);
    }

    #[Filter(by: 'tokens')]
    public function delete($id = null): ResponseInterface
    {
        // TODO: ownership check, model delete
        // return $this->respondDeleted(['id' => $id]);
        return $this->fail('Not Implemented', 501);
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'permission', having: ['comments.create', 'comments.manage'])]
    public function reply($id = null): ResponseInterface
    {
        // TODO: validate, model save with parent_id
        // return $this->respondCreated($this->transformer->transform($reply));
        return $this->fail('Not Implemented', 501);
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'permission', having: ['comments.manage'])]
    public function moderate($id = null): ResponseInterface
    {
        // TODO: validate status, model update
        // return $this->respond($this->transformer->transform($comment));
        return $this->fail('Not Implemented', 501);
    }
}

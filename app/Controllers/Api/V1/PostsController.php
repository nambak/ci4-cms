<?php

declare(strict_types=1);

namespace App\Controllers\Api\V1;

use App\Transformers\PostTransformer;
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
    protected PostTransformer $transformer;

    public function __construct()
    {
        $this->transformer = new PostTransformer();
    }

    #[Cache(for: 5 * MINUTE)]
    public function index(): ResponseInterface
    {
        // TODO: $posts = model('PostModel')->findAll();
        // return $this->respond($this->transformer->transformMany($posts));
        return $this->respond([]);
    }

    #[Cache(for: 5 * MINUTE)]
    public function show($id = null): ResponseInterface
    {
        // TODO: $post = model('PostModel')->find($id);
        // if ($post === null) { return $this->failNotFound(); }
        // return $this->respond($this->transformer->transform($post));
        return $this->failNotFound('Not implemented');
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'permission', having: ['posts.create', 'posts.manage'])]
    public function create(): ResponseInterface
    {
        // TODO: validate, model save, return transformer result
        // return $this->respondCreated($this->transformer->transform($post));
        return $this->failServerError('Not implemented');
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'permission', having: ['posts.edit', 'posts.manage'])]
    public function update($id = null): ResponseInterface
    {
        // TODO: validate, model update, return transformer result
        // return $this->respond($this->transformer->transform($post));
        return $this->failServerError('Not implemented');
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'permission', having: ['posts.delete', 'posts.manage'])]
    public function delete($id = null): ResponseInterface
    {
        // TODO: model delete
        // return $this->respondDeleted(['id' => $id]);
        return $this->failServerError('Not implemented');
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'permission', having: ['posts.manage'])]
    public function publish($id = null): ResponseInterface
    {
        // TODO: model status update to published
        // return $this->respond($this->transformer->transform($post));
        return $this->failServerError('Not implemented');
    }

    #[Cache(for: 5 * MINUTE)]
    public function comments($id = null): ResponseInterface
    {
        // TODO: $comments = model('CommentModel')->where('post_id', $id)->findAll();
        // return $this->respond((new CommentTransformer())->transformMany($comments));
        return $this->respond([]);
    }
}

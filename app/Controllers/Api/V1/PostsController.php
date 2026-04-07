<?php

declare(strict_types=1);

namespace App\Controllers\Api\V1;

use App\Entities\PostEntity;
use App\Enums\UserRole;
use App\Models\PostModel;
use App\Transformers\PostTransformer;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Router\Attributes\Filter;

/**
 * Posts API 컨트롤러
 *
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
    protected $modelName = PostModel::class;
    protected $format = 'json';

    public function __construct()
    {
        $this->transformer = new PostTransformer();
    }

    /**
     * 게시글 목록 조회
     * 공개된 게시글만 노출, 다른 상태의 게시글은 관리자 전용 엔드포인트에서 확인 가능
     *
     * @see docs/openapi.yaml GET /posts
     * @see docs/openapi.yaml#PerPageParam, PaginationMeta
     */
    public function index(): ResponseInterface
    {
        $rules = [
            'per_page'     => 'permit_empty|integer|greater_than_equal_to[1]|less_than_equal_to[100]',
            'search'     => 'permit_empty|string|max_length[255]',
            'category_id' => 'permit_empty|integer',
        ];

        $per_page = $this->request->getGet('per_page');
        $search = $this->request->getGet('search');
        $category_id = $this->request->getGet('category_id');
        $data = compact('per_page', 'search', 'category_id');

        if (!$this->validateData($data, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        if ($search) {
            $this->model->search($search);
        }

        if ($category_id) {
            $this->model->where('category_id', $category_id);
        }

        // 공개 설정한 게시글만 조회
        $this->model->where('state', 'published');

        $per_page = (int)$per_page ?: 10;

        $posts = $this->model->paginate($per_page);

        return $this->responseWith($this->transformer->transformMany($posts), $this->model->pager);
    }

    /**
     * 게시글 조회
     *
     */
    public function show($id = null): ResponseInterface
    {
        $post = $this->model->find($id);

        if ($post === null) {
            return $this->failNotFound();
        }

        return $this->responseWithItem($this->transformer->transform($post));
    }

    /**
     * 게시글 생성
     *
     * @see docs/openapi.yaml POST /posts
     * @see docs/openapi.yaml#PostInput
     */
    #[Filter(by: 'tokens')]
    #[Filter(by: 'apipermission', having: ['posts.create', 'posts.manage'])]
    public function create(): ResponseInterface
    {
        $rules = [
            'title'       => 'required|min_length[3]|max_length[255]',
            'content'     => 'required|min_length[10]',
            'category_id' => 'required|integer|is_not_unique[categories.id]',
        ];

        $payload = $this->request->getJSON(true);

        if (!$payload) {
            return $this->failValidationErrors('Invalid payload');
        }

        if (!$this->validateData($payload, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $payload['writer_id'] = auth()->id();
        $payload['state'] = 'draft';
        $payload['tenant_id'] = auth()->user()->tenant_id;
        $result = $this->model->insert($payload);

        if (!$result) {
            if (empty($this->model->errors())) {
                return $this->failServerError($this->model->db->error());
            } else {
                return $this->failValidationErrors($this->model->errors());
            }
        }

        $createdPost = $this->model->find($this->model->getInsertID());

        return $this->responseWithItem($this->transformer->transform($createdPost), 201);
    }

    /**
     * 게시글 수정
     *
     */
    #[Filter(by: 'tokens')]
    #[Filter(by: 'apipermission', having: ['posts.edit', 'posts.manage'])]
    public function update($id = null): ResponseInterface
    {
        $post = $this->model->find($id);

        if (!$post) {
            return $this->failNotFound('No post found with id: ' . $id);
        }

        if (!$this->isAuthorized($post)) {
            return $this->failForbidden('You are not authorized to update this post');
        }

        $rules = [
            'title'       => 'required|min_length[3]|max_length[255]',
            'content'     => 'required|min_length[10]',
            'category_id' => 'required|integer|is_not_unique[categories.id]',
        ];

        $payload = $this->request->getJSON(true);

        if (!$this->validateData($payload, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $allowedPayload = array_intersect_key($payload, $rules);

        if (!$allowedPayload) {
            return $this->failValidationErrors('No valid data provided');
        }

        $result = $this->model->update($id, $allowedPayload);

        if (!$result) {
            return $this->failValidationErrors($this->model->errors());
        }

        $updatedPost = $this->model->find($id);

        return $this->responseWithItem($this->transformer->transform($updatedPost));
    }

    /**
     * 게시글 삭제
     */
    #[Filter(by: 'tokens')]
    #[Filter(by: 'apipermission', having: ['posts.delete', 'posts.manage'])]
    public function delete($id = null): ResponseInterface
    {
        $post = $this->model->find($id);

        if (!$post) {
            return $this->failNotFound('No post found with id: ' . $id);
        }

        if (!$this->isAuthorized($post)) {
            return $this->failForbidden('You are not authorized to delete this post');
        }

        $result = $this->model->delete($id);

        if (!$result) {
            return $this->failServerError('Failed to delete post with id: ' . $id);
        }

        return $this->respondNoContent();
    }

    /**
     * 게시글 공개
     *
     */
    #[Filter(by: 'tokens')]
    #[Filter(by: 'apipermission', having: ['posts.manage'])]
    public function publish($id = null): ResponseInterface
    {
        $post = $this->model->find($id);

        if (!$post) {
            return $this->failNotFound('No post found with id: ' . $id);
        }

        if (!$this->isAuthorized($post)) {
            return $this->failForbidden('You are not authorized to publish this post');
        }

        if ($post->isPublished()) {
            return $this->respond([
                'status'  => 'error',
                'code'    => 409,
                'message' => 'Post is already published',
            ], 409);
        }

        $result = $this->model->update($id, ['state' => 'published']);

        if (!$result) {
            return $this->failServerError('Failed to publish post with id: ' . $id);
        }

        return $this->responseWithMessage('Post published successfully');
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'apipermission', having: ['posts.manage'])]
    public function unpublish($id = null): ResponseInterface
    {
        $post = $this->model->find($id);

        if (!$post) {
            return $this->failNotFound('No post found with id: ' . $id);
        }

        if (!$this->isAuthorized($post)) {
            return $this->failForbidden('You are not authorized to unpublish this post');
        }

        if ($post->isDraft()) {
            return $this->respond([
                'status'  => 'error',
                'code'    => 409,
                'message' => 'Post is already draft',
            ], 409);
        }

        $result = $this->model->update($id, ['state' => 'draft']);

        if (!$result) {
            return $this->failServerError('Failed to unpublish post with id: ' . $id);
        }

        return $this->responseWithMessage('Post unpublished successfully');
    }

    public function comments($id = null): ResponseInterface
    {
        // TODO: $comments = model('CommentModel')->where('post_id', $id)->findAll();
        // return $this->respond((new CommentTransformer())->transformMany($comments));
        return $this->respond([]);
    }

    /**
     * Post의 수정, 삭제, 발행 권한 확인.
     * SuperAdmin은 모든 게시물의 수정, 삭제, 발행, 발행취소 가능.
     * Admin은 본인이 작성한 게시물의 수정, 삭제, 발생, 발행취소 가능.
     *
     * @param PostEntity $post
     * @return bool
     */
    private function isAuthorized(PostEntity $post): bool
    {
        $user = auth()->user();

        if (is_null($user)) {
            return false;
        }

        return $post->isOwnedBy((int)$user->id) === true || $user->inGroup(UserRole::Superadmin->value) === true;
    }
}

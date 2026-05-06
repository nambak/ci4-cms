<?php

declare(strict_types=1);

namespace App\Controllers\Api\V1;

use App\Enums\CommentState;
use App\Models\CommentModel;
use App\Transformers\CommentTransformer;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\ResponseInterface;
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
    protected $modelName = CommentModel::class;
    protected $format = 'json';

    public function __construct()
    {
        $this->transformer = new CommentTransformer();
    }

    public function index(): ResponseInterface
    {
        $rules = [
            'per_page' => 'permit_empty|integer|greater_than_equal_to[1]|less_than_equal_to[100]',
            'post_id'  => 'permit_empty|integer|is_natural_no_zero',
        ];

        $per_page = $this->request->getGet('per_page');
        $post_id = $this->request->getGet('post_id');
        $data = compact('per_page', 'post_id');

        if (!$this->validateData($data, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $per_page = max(1, min(100, (int)$per_page ?: 10));

        $builder = $this->model->where('state', CommentState::APPROVED->value);

        if ($post_id) {
            $builder->where('post_id', $post_id);
        }

        $comments = $builder->paginate($per_page);

        return $this->responseWith($this->transformer->transformMany($comments), $this->model->pager);
    }

    public function show($id = null): ResponseInterface
    {
        $comment = $this->model->find($id);

        if (!$comment || $comment->state !== CommentState::APPROVED) {
            return $this->failNotFound();
        }

        return $this->responseWithItem($this->transformer->transform($comment));
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'apipermission', having: ['comments.create', 'comments.manage'])]
    public function create(): ResponseInterface
    {
        $rules = [
            'post_id' => 'required|integer|is_natural_no_zero',
            'content' => 'required|min_length[1]',
        ];

        $payload = $this->request->getJson(true);

        if (!$payload) {
            return $this->failValidationErrors('Invalid payload');
        }

        if (!$this->validateData($payload, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $payload['user_id'] = auth()->id();
        $payload['state'] = CommentState::PENDING->value;

        try {
            $result = $this->model->insert($payload, true);

            if ($result === false) {
                return $this->failValidationErrors($this->model->errors());
            }

            $comment = $this->model->find($result);
        } catch (DatabaseException $exception) {
            log_message('error', $exception->getMessage());

            return $this->failServerError('Database error');
        }

        if (!$comment) {
            return $this->failServerError('Failed to create comment');
        }

        return $this->responseWithItem($this->transformer->transform($comment), 201);
    }

    #[Filter(by: 'tokens')]
    public function update($id = null): ResponseInterface
    {
        $rules = ['content' => 'required|min_length[1]'];

        $comment = $this->model->find($id);

        if (!$comment) {
            return $this->failNotFound("Comment not found with id: {$id}");
        }

        if (auth()->id() !== $comment->user_id) {
            return $this->failForbidden('You are not authorized to update this comment');
        }

        $payload = $this->request->getJSON(true);

        if (!$payload) {
            return $this->failValidationErrors(['payload' => 'Missing payload']);
        }

        if (!$this->validateData($payload, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        try {
            if (!$this->model->update($id, ['content' => $payload['content']])) {
                return $this->failValidationErrors($this->model->errors());
            }

            $comment = $this->model->find($id);

            return $this->responseWithItem($this->transformer->transform($comment));
        } catch (DatabaseException $exception) {
            log_message('error', $exception->getMessage());

            return $this->failServerError('Database error');
        }
    }

    #[Filter(by: 'tokens')]
    public function delete($id = null): ResponseInterface
    {
        $comment = $this->model->find($id);

        if (!$comment) {
            return $this->failNotFound("Comment not found with id: {$id}");
        }

        if (auth()->id() !== $comment->user_id) {
            return $this->failForbidden('You are not authorized to delete this comment');
        }

        try {
            $this->model->delete($id);

            return $this->respondNoContent();
        } catch (DatabaseException $exception) {
            log_message('error', $exception->getMessage());

            return $this->failServerError('Database error');
        }
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'apipermission', having: ['comments.create', 'comments.manage'])]
    public function reply($id = null): ResponseInterface
    {
        $rules = ['content' => 'required|min_length[1]'];

        $parentComment = $this->model->find($id);

        if (!$parentComment) {
            return $this->failNotFound("Comment not found with id: {$id}");
        }

        $payload = $this->request->getJSON(true);

        if (!$payload) {
            return $this->failValidationErrors(['payload' => 'Missing payload']);
        }

        if (!$this->validateData($payload, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $payload['parent_id'] = $parentComment->id;
        $payload['post_id'] = $parentComment->post_id;
        $payload['user_id'] = auth()->id();
        $payload['state'] = CommentState::PENDING->value;

        try {
            $result = $this->model->insert($payload, true);

            if ($result === false) {
                return $this->failValidationErrors($this->model->errors());
            }

            $comment = $this->model->find($result);

            return $this->responseWithItem($this->transformer->transform($comment), 201);
        } catch (DatabaseException $exception) {
            log_message('error', $exception->getMessage());

            return $this->failServerError('Database error');
        }
    }

    #[Filter(by: 'tokens')]
    #[Filter(by: 'apipermission', having: ['comments.manage'])]
    public function moderate($id = null): ResponseInterface
    {
        $state = implode(',', array_column(CommentState::cases(), 'value'));
        $rules = ['state' => "required|in_list[{$state}]"];

        $comment = $this->model->find($id);

        if (!$comment) {
            return $this->failNotFound("Comment not found with id: {$id}");
        }

        $payload = $this->request->getJSON(true);

        if (!$payload) {
            return $this->failValidationErrors(['payload' => 'Missing payload']);
        }

        if (!$this->validateData($payload, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        try {
            if (!$this->model->update($id, ['state' => $payload['state']])) {
                return $this->failValidationErrors($this->model->errors());
            }

            $comment = $this->model->find($id);

            return $this->responseWithItem($this->transformer->transform($comment));
        } catch (DatabaseException $exception) {
            log_message('error', $exception->getMessage());

            return $this->failServerError('Database error');
        }
    }
}

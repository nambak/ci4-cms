<?php

namespace App\Controllers\Api\V1;

use App\Transformers\UserTransformer;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

class UsersController extends BaseApiController
{
    protected UserTransformer $transformer;

    public function __construct()
    {
        $this->transformer = new UserTransformer();
    }

    /**
     * 사용자 목록
     *
     */
    public function index(): ResponseInterface
    {
        $data = auth()->getProvider()->findAll();

        return $this->respond($this->transformer->transformMany($data));
    }

    /**
     * 사용자 상세
     *
     * @param $id
     */
    public function show($id = null): ResponseInterface
    {
        try {
            $user = $this->findUserOrFail($id);
        } catch (PageNotFoundException $e) {
            return $this->failNotFound('No user found with id: ' . $id);
        }


        return $this->respond($this->transformer->transform($user));
    }

    /**
     * 사용자 수정
     *
     * @param $id
     */
    public function update($id = null): ResponseInterface
    {
        $payload = $this->request->getJSON(true);

        try {
            $user = $this->findUserOrFail($id);
        } catch (PageNotFoundException $e) {
            return $this->failNotFound('No user found with id: ' . $id);
        }

        // 허용 필드(username, password)만 추출
        $allowedFields = array_flip(['username', 'password']);
        $allowedPayload = array_intersect_key($payload, $allowedFields);

        // 빈 값(null) 제거
        $filteredPayload = array_filter($allowedPayload, function ($value) {
            return $value !== null;
        });

        $user->fill($filteredPayload);
        $result = auth()->getProvider()->update($id, $user);

        if (!$result) {
            return $this->failValidationErrors(auth()->getProvider()->errors());
        }

        $updatedUser = auth()->getProvider()->findById($id);

        return $this->respond($this->transformer->transform($updatedUser));
    }

    /**
     * 사용자 삭제
     *
     * @param $id
     */
    public function delete($id = null): ResponseInterface
    {
        try {
            $this->findUserOrFail($id);
        } catch (PageNotFoundException $e) {
            return $this->failNotFound('No user found with id: ' . $id);
        }

        $result = auth()->getProvider()->delete($id);

        if (!$result) {
            return $this->failServerError('Failed to delete user with id: ' . $id);
        }

        return $this->respondDeleted(['id' => $id]);
    }
}

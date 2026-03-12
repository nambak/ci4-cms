<?php

namespace App\Controllers\Api\V1;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Shield\Authorization\AuthorizationException;

class UserGroupsController extends BaseApiController
{
    /**
     * 해당 사용자의 현재 그룹 목록
     *
     * $@param $id
     */
    public function index($id = null): ResponseInterface
    {
        try {
            $user = $this->findUserOrFail($id);
        } catch (PageNotFoundException $e) {
            return $this->failNotFound('No user found with id: ' . $id);
        }

        $groups = $user->getGroups();

        return $this->respond($groups);
    }


    /**
     * 그룹 추가
     *
     * @param $id
     */
    public function update($id = null): ResponseInterface
    {
        $payload = $this->request->getJSON(true);

        if (!isset($payload['group_id'])) {
            return $this->failValidationErrors('group_id is required');
        }

        try {
            $user = $this->findUserOrFail($id);
            $user->addGroup($payload['group_id']);
        } catch (PageNotFoundException $e) {
            return $this->failNotFound($e->getMessage());
        } catch (AuthorizationException $e) {
            return $this->fail($e->getMessage());
        }

        return $this->respondUpdated();
    }

    /**
     * 그룹 해제
     *
     * @param $id
     */
    public function delete($id = null): ResponseInterface
    {
        $payload = $this->request->getJSON(true);

        if (!isset($payload['group_id'])) {
            return $this->failValidationErrors('group_id is required');
        }

        try {
            $user = $this->findUserOrFail($id);
            $user->removeGroup($payload['group_id']);
        } catch (PageNotFoundException $e) {
            return $this->failNotFound($e->getMessage());
        } catch (AuthorizationException $e) {
            return $this->fail($e->getMessage());
        }

        return $this->respondNoContent();
    }
}

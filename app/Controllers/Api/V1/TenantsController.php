<?php

namespace App\Controllers\Api\V1;

use App\Models\TenantModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class TenantsController extends ResourceController
{

    protected $modelName = TenantModel::class;
    protected $format = 'json';

    /**
     * 테넌트(subdomain) 목록
     *
     */
    public function index(): ResponseInterface
    {
        $data = $this->model->findAll();

        return $this->respond($data);
    }

    /**
     * 테넌트(subdomain) 조회
     *
     * @param $id
     */
    public function show($id = null): ResponseInterface
    {
        $tenant = $this->model->find($id);

        if (!$tenant) {
            return $this->failNotFound('No tenant found with id: ' . $id);
        }

        return $this->respond($tenant);
    }


    /**
     * 테넌트(subdomain) 생성
     *
     */
    public function create(): ResponseInterface
    {
        $data = $this->request->getJSON(true);
        $result = $this->model->insert($data);

        if (!$result) {
            return $this->failValidationErrors($this->model->errors());
        }

        return $this->respondCreated(['id' => $this->model->getInsertID()]);
    }

    /**
     * 테넌트(subdomain) 수정
     *
     * @param $id
     */
    public function update($id = null): ResponseInterface
    {
        $tenant = $this->model->find($id);

        if (!$tenant) {
            return $this->failNotFound('No tenant found with id: ' . $id);
        }

        $data = $this->request->getJSON(true);
        $result = $this->model->update($id, $data);

        if (!$result) {
            return $this->failValidationErrors($this->model->errors());
        }

        $updatedTenant = $this->model->find($id);

        return $this->respond($updatedTenant);

    }

    /**
     * 테넌트(subdomain) 삭제
     *
     * @param $id
     */
    public function delete($id = null): ResponseInterface
    {
        $tenant = $this->model->find($id);

        if (!$tenant) {
            return $this->failNotFound('No tenant found with id: ' . $id);
        }

        $result = $this->model->delete($id);

        if (!$result) {
            return $this->failServerError('Failed to delete tenant with id: ' . $id);
        }

        return $this->respondDeleted(['id' => $id]);
    }
}

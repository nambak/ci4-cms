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
        $rules = [
            'name' => 'required|min_length[3]|max_length[255]|is_unique[tenants.name]',
            'subdomain' => 'required|alpha_dash|min_length[3]|max_length[50]|is_unique[tenants.subdomain]',
        ];

        $payload = $this->request->getJSON(true);

        if (!$payload) {
            return $this->failValidationErrors('Invalid payload');
        }

        if (!$this->validateData($payload, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $result = $this->model->insert($payload);

        if (!$result) {
            return $this->failValidationErrors($this->model->errors());
        }

        return $this->respondCreated(['id' => $this->model->getInsertID()]);
    }

    /**
     * 테넌트(subdomain) 수정
     *
     */
    public function update($id = null): ResponseInterface
    {
        $tenant = $this->model->find($id);

        if (!$tenant) {
            return $this->failNotFound('No tenant found with id: ' . $id);
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[255]|is_unique[tenants.name,id,' . $id . ']',
            'subdomain' => 'required|alpha_dash|min_length[3]|max_length[50]|is_unique[tenants.subdomain,id,' . $id . ']',
        ];

        $payload = $this->request->getJSON(true);

        if (!$payload) {
            return $this->failValidationErrors('Invalid payload');
        }

        $allowedPayload = array_intersect_key($payload, $rules);
        $filteredRules = array_intersect_key($rules, $payload);

        if (!$this->validateData($allowedPayload, $filteredRules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $result = $this->model->update($id, $allowedPayload);

        if (!$result) {
            return $this->failValidationErrors($this->model->errors());
        }

        $updatedTenant = $this->model->find($id);

        return $this->respond($updatedTenant);

    }

    /**
     * 테넌트(subdomain) 삭제
     *
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

        return $this->respondNoContent();
    }
}

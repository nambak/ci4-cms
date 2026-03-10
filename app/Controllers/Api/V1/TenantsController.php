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
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        $data = $this->model->findAll();

        return $this->respond($data);
    }

    /**
     * Return the properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function show($id = null)
    {
        $tenant = $this->model->find($id);

        if (!$tenant) {
            return $this->failNotFound('No tenant found with id: ' . $id);
        }

        return $this->respond($tenant);
    }


    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        $data = $this->request->getJSON(true);
        $result = $this->model->insert($data);

        if (!$result) {
            return $this->failValidationErrors($this->model->errors());
        }

        return $this->respondCreated(['id' => $this->model->getInsertID()]);
    }

    /**
     * Add or update a model resource, from "posted" properties.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function update($id = null)
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
     * Delete the designated resource object from the model.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function delete($id = null)
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

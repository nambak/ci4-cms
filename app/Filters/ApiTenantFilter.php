<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiTenantFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $slug = $request->getHeaderLine('X-Tenant-Slug');

        if (!$slug) {
            return response()
                ->setStatusCode(400)
                ->setJSON([
                    'code'    => 400,
                    'status'  => 'error',
                    'message' => 'X-Tenant-Slug header is required'
                ]);
        }

        $model = model('TenantModel');
        $tenant = $model->where('subdomain', $slug)->first();

        if (!$tenant) {
            return response()
                ->setStatusCode(404)
                ->setJSON([
                    'code'    => 404,
                    'status'  => 'error',
                    'message' => 'Tenant not found'
                ]);
        }

        service('tenant')->setTenant($tenant);
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
        //
    }
}

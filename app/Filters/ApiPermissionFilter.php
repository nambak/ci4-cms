<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiPermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null): ResponseInterface|null
    {
        if (empty($arguments)) {
            return null;
        }

        if (!auth()->loggedIn()) {
            return response()->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        foreach ($arguments as $permission) {
            if (auth()->user()->can($permission)) {
                return null;
            }
        }

        return response()->setJSON(['error' => 'Forbidden'])->setStatusCode(403);

    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
        //
    }
}

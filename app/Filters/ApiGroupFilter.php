<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiGroupFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null): ResponseInterface|null
    {
        if (empty($arguments)) {
            return null;
        }

        if (!auth()->loggedIn()) {
            $response = [
                'status'  => 'error',
                'code'    => 401,
                'message' => 'Unauthorized',
            ];

            return response()
                ->setJSON($response)
                ->setStatusCode(401);
        }

        if (!auth()->user()->inGroup(...$arguments)) {
            $response = [
                'status'  => 'error',
                'code'    => 403,
                'message' => 'Forbidden',
            ];

            return response()
                ->setJson($response)
                ->setStatusCode(403);
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
        //
    }
}

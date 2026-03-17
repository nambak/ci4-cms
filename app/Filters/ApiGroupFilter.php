<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiGroupFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null): ResponseInterface|null
    {
        if (!auth()->loggedIn()) {
            return response()->setJson(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        if (!auth()->user()->inGroup(...$arguments)) {
            return response()->setJson(['error' => 'Forbidden'])->setStatusCode(403);
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
        //
    }
}

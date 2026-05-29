<?php

namespace App\Controllers\Tenant\Admin;

use App\Controllers\BaseController;

class BaseAdminController extends BaseController
{
    protected function adminView(string $viewPath, array $data = []): string
    {
        $data['tenant'] = service('tenant')->getTenant();

        return view($viewPath, $data);
    }
}

<?php

namespace App\Controllers\Tenant;

use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index(): string
    {
        $tenant = service('tenant')->getTenant();

        return view('tenant/home', compact('tenant'));
    }
}

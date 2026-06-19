<?php

namespace App\Controllers\Tenant\Admin;

class MediaController extends BaseAdminController
{
    public function index(): string
    {
        return $this->adminView('tenant/admin/media/index', [
            'activeMenu' => 'media',
            'pageTitle'  => '미디어 라이브러리',
        ]);
    }
}

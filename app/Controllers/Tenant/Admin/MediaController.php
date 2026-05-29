<?php

namespace App\Controllers\Tenant\Admin;

use App\Models\MediaModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

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

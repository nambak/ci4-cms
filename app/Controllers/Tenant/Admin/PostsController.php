<?php

namespace App\Controllers\Tenant\Admin;

use App\Models\PostModel;

class PostsController extends BaseAdminController
{
    public function index(): string
    {
        $tenant = service('tenant')->getTenant();

        $posts = model(PostModel::class)
            ->where('tenant_id', $tenant->id)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return $this->adminView('tenant/admin/posts/index', [
            'posts'      => $posts,
            'activeMenu' => 'posts',
            'pageTitle'  => '포스트',
        ]);
    }
}

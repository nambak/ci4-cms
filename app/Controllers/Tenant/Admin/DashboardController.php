<?php

namespace App\Controllers\Tenant\Admin;

use App\Models\CommentModel;
use App\Models\PostModel;
use App\Models\UserModel;

class DashboardController extends BaseAdminController
{
    public function index(): string
    {
        $tenant = service('tenant')->getTenant();

        $postCount = model(PostModel::class)
            ->where('tenant_id', $tenant->id)
            ->countAllResults();

        $userCount = model(UserModel::class)
            ->where('tenant_id', $tenant->id)
            ->countAllResults();

        $commentCount = model(CommentModel::class)
            ->join('posts', 'posts.id = comments.post_id')
            ->where('posts.tenant_id', $tenant->id)
            ->countAllResults();

        return $this->adminView(
            'tenant/admin/dashboard/index',
            [
                'activeMenu'   => 'dashboard',
                'pageTitle'    => 'Dashboard',
                'postCount'    => $postCount,
                'userCount'    => $userCount,
                'commentCount' => $commentCount,
            ]
        );
    }
}

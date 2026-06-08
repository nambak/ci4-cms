<?php

namespace App\Controllers\Tenant\Admin;

use App\Models\CommentModel;
use App\Models\PostModel;
use App\Models\UserModel;
use CodeIgniter\I18n\Time;

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

        $popularPosts = model(PostModel::class)
            ->popularByComments($tenant->id, 5);

        $recentPosts = model(PostModel::class)
            ->recent($tenant->id, 5);

        $recentComments = model(CommentModel::class)
            ->recent($tenant->id, 5);

        $trend = $this->buildTrend($tenant->id);

        return $this->adminView(
            'tenant/admin/dashboard/index',
            [
                'activeMenu'     => 'dashboard',
                'pageTitle'      => 'Dashboard',
                'postCount'      => $postCount,
                'userCount'      => $userCount,
                'commentCount'   => $commentCount,
                'popularPosts'   => $popularPosts,
                'tenant'         => $tenant,
                'recentPosts'    => $recentPosts,
                'recentComments' => $recentComments,
                'trend'          => $trend,
            ]
        );
    }

    private function buildTrend(int $tenantId, int $days = 30): array
    {
        $postMap = model(PostModel::class)->dailyCountsByTenant($tenantId, $days);
        $commentsMap = model(CommentModel::class)->dailyCountsByTenant($tenantId, $days);

        $labels = [];
        $posts = [];
        $comments = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Time::now()->subDays($i)->format('Y-m-d');
            $labels[] = $date;
            $posts[] = $postMap[$date] ?? 0;
            $comments[] = $commentsMap[$date] ?? 0;
        }

        return compact('labels', 'posts', 'comments');
    }
}

<?php

namespace App\Controllers\Tenant;

use App\Controllers\BaseController;
use App\Enums\PostState;
use App\Models\PostModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class PostsController extends BaseController
{
    public function index(): string
    {
        $tenantService = service('tenant');

        $model = model(PostModel::class);
        $posts = $model
            ->where('tenant_id', $tenantService->getId())
            ->where('state', PostState::PUBLISHED->value)
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        return view('tenant/posts/index', [
            'tenant' => $tenantService->getTenant(),
            'posts'  => $posts,
            'pager'  => $model->pager,
        ]);
    }

    public function show(string $tenantSlug, string $postSlug): string
    {
        $tenant = service('tenant')->getTenant();
        $model = model(PostModel::class);

        $post = $model
            ->where('tenant_id', $tenant->id)
            ->where('slug', $postSlug)
            ->where('state', PostState::PUBLISHED->value)
            ->first();

        if ($post === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('tenant/posts/show', compact('post', 'tenant'));
    }
}

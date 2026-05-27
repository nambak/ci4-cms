<?php

namespace App\Controllers\Tenant;

use App\Controllers\BaseController;
use App\Enums\PostState;
use App\Models\CategoryModel;
use App\Models\PostModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class CategoriesController extends BaseController
{
    public function posts(string $tenantSlug, string $categorySlug): string
    {
        $tenant = service('tenant')->getTenant();
        $postModel = model(PostModel::class);
        $categoryModel = model(CategoryModel::class);

        $category = $categoryModel
            ->where('tenant_id', $tenant->id)
            ->where('slug', $categorySlug)
            ->first();

        if ($category === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $posts = $postModel
            ->where('tenant_id', $tenant->id)
            ->where('category_id', $category->id)
            ->where('state', PostState::PUBLISHED->value)
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        return view('tenant/categories/posts', [
            'tenant'   => $tenant,
            'category' => $category,
            'posts'    => $posts,
            'pager'    => $postModel->pager,
        ]);
    }
}

<?php

namespace App\Controllers\Tenant\Admin;

use App\Models\CategoryModel;
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
            'subdomain'  => $tenant->subdomain,
            'posts'      => $posts,
            'activeMenu' => 'posts',
            'pageTitle'  => '포스트',
        ]);
    }

    public function new()
    {
        $tenant = service('tenant')->getTenant();

        $categories = model(CategoryModel::class)
            ->where('tenant_id', $tenant->id)
            ->findAll();

        return $this->adminView('tenant/admin/posts/new', [
            'subdomain'  => $tenant->subdomain,
            'categories' => $categories,
            'activeMenu' => 'posts',
            'pageTitle'  => '포스트 작성',
        ]);
    }

    public function create()
    {
        $rules = [
            'title'       => 'required',
            'content'     => 'required',
            'category_id' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $model = model(PostModel::class);

        $result = $model->insert([
            'title'       => $this->request->getPost('title'),
            'content'     => $this->request->getPost('content'),
            'tenant_id'   => service('tenant')->getTenant()->id,
            'category_id' => $this->request->getPost('category_id'),
            'writer_id'   => auth()->id(),
        ]);

        if ($result === false) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $model->errors());
        }

        $subdomain = service('tenant')->getTenant()->subdomain;

        return redirect()->to("/{$subdomain}/admin/posts");
    }
}

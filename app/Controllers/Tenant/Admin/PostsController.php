<?php

namespace App\Controllers\Tenant\Admin;

use App\Models\CategoryModel;
use App\Models\PostModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class PostsController extends BaseAdminController
{
    protected $tenant;
    protected PostModel $postModel;
    protected $rules = [
        'title'       => 'required',
        'content'     => 'required',
        'category_id' => 'required',
    ];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->tenant = service('tenant')->getTenant();
        $this->postModel = model(PostModel::class);
    }

    public function index(): string
    {
        $posts = $this->postModel
            ->where('tenant_id', $this->tenant->id)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return $this->adminView('tenant/admin/posts/index', [
            'subdomain'  => $this->tenant->subdomain,
            'posts'      => $posts,
            'activeMenu' => 'posts',
            'pageTitle'  => '포스트',
        ]);
    }

    /**
     * 게시글 작성 Form
     *
     * @return string
     */
    public function new()
    {
        $categories = $this->getCategories();

        return $this->adminView('tenant/admin/posts/new', [
            'subdomain'  => $this->tenant->subdomain,
            'categories' => $categories,
            'activeMenu' => 'posts',
            'pageTitle'  => '포스트 작성',
        ]);
    }

    /**
     * 게시글 작성
     *
     * @return RedirectResponse
     */
    public function create(): RedirectResponse
    {
        if (!$this->validate($this->rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        if (! $this->isOwnedCategory($this->request->getPost('category_id'))) {
            return redirect()->back()->withInput()
                ->with('errors', ['category_id' => '유효하지 않은 카테고리입니다.']);
        }

        $result = $this->postModel->insert([
            'title'       => $this->request->getPost('title'),
            'content'     => $this->request->getPost('content'),
            'tenant_id'   => $this->tenant->id,
            'category_id' => $this->request->getPost('category_id'),
            'writer_id'   => auth()->id(),
        ]);

        if ($result === false) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->postModel->errors());
        }

        return redirect()->to("/{$this->tenant->subdomain}/admin/posts");
    }

    /**
     * 게시글 수정 Form
     *
     * @return string
     */
    public function edit($id)
    {
        $post = $this->getPost($id);

        $categories = $this->getCategories();

        return $this->adminView('tenant/admin/posts/edit', [
            'subdomain'  => $this->tenant->subdomain,
            'categories' => $categories,
            'activeMenu' => 'posts',
            'pageTitle'  => '포스트 수정',
            'post'       => $post,
        ]);
    }

    /**
     * 게시글 수정
     */
    public function update($id)
    {
        $this->getPost($id);

        if (!$this->validate($this->rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        if (! $this->isOwnedCategory($this->request->getPost('category_id'))) {
            return redirect()->back()->withInput()
                ->with('errors', ['category_id' => '유효하지 않은 카테고리입니다.']);
        }

        $result = $this->postModel->update($id, [
            'title'       => $this->request->getPost('title'),
            'content'     => $this->request->getPost('content'),
            'category_id' => $this->request->getPost('category_id'),
        ]);

        if ($result === false) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->postModel->errors());
        }

        return redirect()->to("/{$this->tenant->subdomain}/admin/posts");
    }

    /**
     * 게시글 삭제
     */
    public function delete($id)
    {
        $this->getPost($id);

        $result = $this->postModel->delete($id);

        if ($result === false) {
            return redirect()->back()->with('errors', ['삭제에 실패했습니다.']);
        }

        return redirect()->to("/{$this->tenant->subdomain}/admin/posts");
    }

    protected function getPost($id)
    {
        $post = model(PostModel::class)
            ->where('tenant_id', $this->tenant->id)
            ->find($id);

        if (is_null($post)) {
            throw new PageNotFoundException();
        }

        return $post;
    }

    protected function isOwnedCategory($categoryId): bool
    {
        return model(CategoryModel::class)
                ->where('tenant_id', $this->tenant->id)
                ->find($categoryId) !== null;
    }

    protected function getCategories()
    {
        return model(CategoryModel::class)
            ->where('tenant_id', $this->tenant->id)
            ->findAll();
    }
}

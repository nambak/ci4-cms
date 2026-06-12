<?php

namespace App\Controllers\Tenant\Admin;

use App\Models\CategoryModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use ReflectionException;

class CategoriesController extends BaseAdminController
{
    protected $tenant;
    protected CategoryModel $categoryModel;
    protected $indexPage;
    protected $rules = [
        'name'        => 'required|max_length[255]',
        'description' => 'permit_empty|string',
    ];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->tenant = service('tenant')->getTenant();
        $this->indexPage = "/{$this->tenant->subdomain}/admin/categories";
        $this->categoryModel = model(CategoryModel::class);
    }


    /**
     * 카테고리 목록
     * @return string
     */
    public function index(): string
    {
        $categories = $this->categoryModel
            ->where('tenant_id', $this->tenant->id)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return $this->adminView('tenant/admin/categories/index', [
            'subdomain'  => $this->tenant->subdomain,
            'categories' => $categories,
            'activeMenu' => 'categories',
            'pageTitle'  => '카테고리',
        ]);
    }


    /**
     * 카테고리 생성 Form
     * @return string
     */
    public function new(): string
    {
        return $this->adminView('tenant/admin/categories/new', [
            'subdomain' => $this->tenant->subdomain,
            'activeMenu' => 'categories',
            'pageTitle'  => '카테고리 생성',
        ]);
    }

    /**
     * 카테고리 생성
     * @return RedirectResponse
     * @throws ReflectionException
     */
    public function create(): RedirectResponse
    {
        if (!$this->validate($this->rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $result = $this->categoryModel->insert([
            'tenant_id'   => $this->tenant->id,
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ]);

        if ($result === false) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->categoryModel->errors());
        }

        return redirect()->to($this->indexPage);
    }

    /**
     * 카테고리 수정 Form
     * @param $id
     * @return string
     */
    public function edit($id): string
    {
        $category = $this->getCategory($id);

        return $this->adminView('tenant/admin/categories/edit', [
            'subdomain'    => $this->tenant->subdomain,
            'activeMenu'   => 'categories',
            'pageTitle'    => '카테고리 수정',
            'category'     => $category,
        ]);
    }

    /**
     * 카테고리 수정
     * @param $id
     * @return RedirectResponse
     * @throws ReflectionException
     */
    public function update($id): RedirectResponse
    {
        $this->getCategory($id);

        if (!$this->validate($this->rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $result = $this->categoryModel->update($id, [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ]);

        if ($result === false) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->categoryModel->errors());
        }

        return redirect()->to($this->indexPage);
    }

    /**
     * 카테고리 삭제
     * @param $id
     * @return RedirectResponse
     */
    public function delete($id): RedirectResponse
    {
        $this->getCategory($id);

        $result = $this->categoryModel->delete($id);

        if ($result === false) {
            return redirect()
                ->back()
                ->with('errors', ['삭제에 실패했습니다.']);
        }

        return redirect()->to($this->indexPage);
    }

    /**
     * 카테고리 조회 및 소유권 검증 (없으면 404)
     * @param $id
     * @return object
     * @throws PageNotFoundException
     */
    protected function getCategory($id): object
    {
        $category = $this->categoryModel
            ->where('tenant_id', $this->tenant->id)
            ->find($id);

        if (is_null($category)) {
            throw new PageNotFoundException();
        }

        return $category;
    }
}

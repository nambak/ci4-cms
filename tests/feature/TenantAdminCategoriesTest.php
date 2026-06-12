<?php

namespace Tests\Feature;

use App\Models\CategoryModel;
use App\Models\TenantModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Shield\Test\AuthenticationTesting;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

class TenantAdminCategoriesTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;
    use AuthenticationTesting;

    protected $refresh = true;
    protected $namespace = null;

    protected function setUp(): void
    {
        parent::setUp();

        // 예외(PageNotFoundException)를 던지는 테스트가 공유 서비스(routes/request)를
        // 더럽혀 뒤따르는 _method 스푸핑 테스트가 깨지는 것을 막는다.
        $this->resetServices();
    }

    /**
     * @test 목록에 카테고리가 표시된다
     */
    public function test_index_lists_categories(): void
    {
        // Given:
        $tenant = $this->createTenant('acme');
        $category = $this->createCategory($tenant, '여행');
        $this->actingAs($this->createUser($tenant));

        // When:
        $response = $this->get("{$tenant->subdomain}/admin/categories");

        // Then:
        $response->assertStatus(200);
        $response->assertSee('여행');
    }

    /**
     * @test 목록은 다른 테넌트의 카테고리를 노출하지 않는다 (격리)
     */
    public function test_index_isolates_other_tenant_categories(): void
    {
        // Given:
        $tenantA = $this->createTenant('acme');
        $tenantB = $this->createTenant('example');

        $this->createCategory($tenantA, '내카테고리');
        $this->createCategory($tenantB, '남의카테고리');

        $this->actingAs($this->createUser($tenantA));

        // When:
        $response = $this->get("{$tenantA->subdomain}/admin/categories");

        // Then:
        $response->assertStatus(200);
        $response->assertSee('내카테고리');        // 양성
        $response->assertDontSee('남의카테고리');  // 음성
    }

    /**
     * @test 작성 폼이 로드된다
     */
    public function test_new_form_loads(): void
    {
        // Given:
        $tenant = $this->createTenant('acme');
        $this->actingAs($this->createUser($tenant));

        // When:
        $response = $this->get("{$tenant->subdomain}/admin/categories/new");

        // Then:
        $response->assertStatus(200);
        $response->assertSee('카테고리 생성');
    }

    /**
     * @test 카테고리를 생성하면 DB에 저장된다
     */
    public function test_create_persists_category(): void
    {
        // Given:
        $tenant = $this->createTenant('acme');
        $this->actingAs($this->createUser($tenant));

        // When:
        $response = $this->post("{$tenant->subdomain}/admin/categories", [
            'name'        => '새 카테고리',
            'description' => '설명입니다',
        ]);

        // Then:
        $response->assertRedirect();
        $this->seeInDatabase('categories', [
            'tenant_id'   => $tenant->id,
            'name'        => '새 카테고리',
            'description' => '설명입니다',
        ]);
    }

    /**
     * @test 이름이 없으면 생성에 실패한다 (검증)
     */
    public function test_create_fails_without_name(): void
    {
        // Given:
        $tenant = $this->createTenant('acme');
        $this->actingAs($this->createUser($tenant));

        // When:
        $response = $this->post("{$tenant->subdomain}/admin/categories", [
            'name'        => '',
            'description' => '이름 없음',
        ]);

        // Then:
        $response->assertRedirect();
        $this->dontSeeInDatabase('categories', [
            'tenant_id'   => $tenant->id,
            'description' => '이름 없음',
        ]);
    }

    /**
     * @test 수정 폼에 기존 값이 채워진다
     */
    public function test_edit_form_shows_existing_value(): void
    {
        // Given:
        $tenant = $this->createTenant('acme');
        $category = $this->createCategory($tenant, '수정전이름');
        $this->actingAs($this->createUser($tenant));

        // When:
        $response = $this->get("{$tenant->subdomain}/admin/categories/{$category->id}/edit");

        // Then:
        $response->assertStatus(200);
        $response->assertSee('수정전이름');
    }

    /**
     * @test 다른 테넌트의 카테고리 수정 폼은 404 (IDOR 방어)
     */
    public function test_edit_other_tenant_returns_404(): void
    {
        // Given:
        $tenantA = $this->createTenant('acme');
        $tenantB = $this->createTenant('example');
        $categoryB = $this->createCategory($tenantB, '남의것');

        $this->actingAs($this->createUser($tenantA));

        // Then:
        $this->expectException(PageNotFoundException::class);

        // When: A 테넌트로 B 카테고리 수정 시도
        $this->get("{$tenantA->subdomain}/admin/categories/{$categoryB->id}/edit");
    }

    /**
     * @test 카테고리를 수정하면 DB가 갱신된다
     */
    public function test_update_changes_category(): void
    {
        // Given:
        $tenant = $this->createTenant('acme');
        $category = $this->createCategory($tenant, '수정전');
        $this->actingAs($this->createUser($tenant));

        // When: 실제 폼 흐름(POST + _method=PUT 스푸핑)을 재현
        $response = $this->submitPut("{$tenant->subdomain}/admin/categories/{$category->id}", [
            'name'        => '수정후',
            'description' => '갱신됨',
        ]);

        // Then:
        $response->assertRedirect();
        $this->seeInDatabase('categories', [
            'id'   => $category->id,
            'name' => '수정후',
        ]);
    }

    /**
     * @test 다른 테넌트의 카테고리 수정은 404 (IDOR 방어)
     */
    public function test_update_other_tenant_returns_404(): void
    {
        // Given:
        $tenantA = $this->createTenant('acme');
        $tenantB = $this->createTenant('example');
        $categoryB = $this->createCategory($tenantB, '남의것');

        $this->actingAs($this->createUser($tenantA));

        // Then:
        $this->expectException(PageNotFoundException::class);

        // When:
        $this->put("{$tenantA->subdomain}/admin/categories/{$categoryB->id}", [
            'name' => '탈취시도',
        ]);
    }

    /**
     * @test 카테고리를 삭제하면 DB에서 제거된다
     */
    public function test_delete_removes_category(): void
    {
        // Given:
        $tenant = $this->createTenant('acme');
        $category = $this->createCategory($tenant, '삭제대상');
        $this->actingAs($this->createUser($tenant));

        // When:
        $response = $this->delete("{$tenant->subdomain}/admin/categories/{$category->id}");

        // Then:
        $response->assertRedirect();
        $this->dontSeeInDatabase('categories', ['id' => $category->id]);
    }

    /**
     * @test 다른 테넌트의 카테고리 삭제는 404 (IDOR 방어)
     */
    public function test_delete_other_tenant_returns_404(): void
    {
        // Given:
        $tenantA = $this->createTenant('acme');
        $tenantB = $this->createTenant('example');
        $categoryB = $this->createCategory($tenantB, '남의것');

        $this->actingAs($this->createUser($tenantA));

        // Then:
        $this->expectException(PageNotFoundException::class);

        // When:
        $this->delete("{$tenantA->subdomain}/admin/categories/{$categoryB->id}");

        // And: B의 카테고리는 그대로 남아있다
        $this->seeInDatabase('categories', ['id' => $categoryB->id]);
    }

    /**
     * helper methods
     */
    private function createTenant($subdomain): object
    {
        return fake(TenantModel::class, ['subdomain' => $subdomain]);
    }

    private function createCategory($tenant, $name): object
    {
        return fake(CategoryModel::class, ['tenant_id' => $tenant->id, 'name' => $name]);
    }

    private function createUser($tenant): object
    {
        return fake(UserModel::class, ['tenant_id' => $tenant->id])->addGroup('admin');
    }

    /**
     * HTML 폼의 PUT 제출을 재현한다.
     *
     * 브라우저는 POST + hidden _method=PUT 로 보내고, CI4가 메서드를 PUT 으로 스푸핑한다.
     * 스푸핑 후 Validation::withRequest 는 본문을 getRawInput()(원시 본문)에서 읽으므로,
     * post 글로벌(getPost용)과 urlencoded 원시 본문(검증용)을 모두 채워야 실제와 동일하다.
     */
    private function submitPut(string $url, array $data)
    {
        $payload = ['_method' => 'PUT'] + $data;

        return $this->withBody(http_build_query($payload))->post($url, $payload);
    }
}

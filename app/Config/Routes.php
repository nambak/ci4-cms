<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =====================================================
// 홈 & 문서
// =====================================================
$routes->get('/', 'Home::index');
$routes->get('docs/api', 'Docs::api');

// =====================================================
// Shield 인증 라우트
// =====================================================
service('auth')->routes($routes);

// =====================================================
// API v1 라우트
// =====================================================
$routes->group('api/v1', static function ($routes): void {
    // 인증 (공개 - 필터 없음)
    $routes->post('auth/register', 'Api\V1\AuthController::register');
    $routes->post('auth/login', 'Api\V1\AuthController::login');

    // 인증 필요 엔드포인트 (tokens 필터)
    $routes->group('', ['filter' => 'tokens'], static function ($routes): void {
        // 인증
        $routes->get('auth/me', 'Api\V1\AuthController::me');
        $routes->post('auth/logout', 'Api\V1\AuthController::logout');
        $routes->post('auth/refresh', 'Api\V1\AuthController::refresh');

        $routes->group('' , ['filter' => 'group:superadmin,admin'], static function ($routes): void {
            // 테넌트 관리
            $routes->resource('tenants', ['controller' => 'Api\V1\TenantsController']);
            $routes->get('tenants/(:num)/users', 'Api\V1\TenantsController::users/$1');
        });

        // 사용자 관리 (#8)
        $routes->resource('users', ['controller' => 'Api\V1\UsersController']);
        $routes->get('users/(:num)/roles', 'Api\V1\UsersController::roles/$1');
        $routes->put('users/(:num)/roles', 'Api\V1\UsersController::updateRoles/$1');

        // 포스트 (#9)
        $routes->resource('posts', ['controller' => 'Api\V1\PostsController']);
        $routes->post('posts/(:num)/publish', 'Api\V1\PostsController::publish/$1');
        $routes->post('posts/(:num)/unpublish', 'Api\V1\PostsController::unpublish/$1');
        $routes->get('posts/(:num)/comments', 'Api\V1\PostsController::comments/$1');

        // 페이지 (#9)
        $routes->resource('pages', ['controller' => 'Api\V1\PagesController']);
        $routes->post('pages/(:num)/publish', 'Api\V1\PagesController::publish/$1');

        // 카테고리 (#10)
        $routes->resource('categories', ['controller' => 'Api\V1\CategoriesController']);
        $routes->get('categories/(:num)/posts', 'Api\V1\CategoriesController::posts/$1');

        // 태그 (#10)
        $routes->resource('tags', ['controller' => 'Api\V1\TagsController']);
        $routes->get('tags/(:num)/posts', 'Api\V1\TagsController::posts/$1');

        // 댓글 (#11)
        $routes->resource('comments', ['controller' => 'Api\V1\CommentsController']);
        $routes->post('comments/(:num)/replies', 'Api\V1\CommentsController::reply/$1');
        $routes->post('comments/(:num)/moderate', 'Api\V1\CommentsController::moderate/$1');

        // 미디어 (#12)
        $routes->resource('media', ['controller' => 'Api\V1\MediaController']);
        $routes->post('media/upload', 'Api\V1\MediaController::upload');

        // SEO (#13)
        $routes->get('seo/(:num)', 'Api\V1\SeoController::show/$1');
        $routes->put('seo/(:num)', 'Api\V1\SeoController::update/$1');
        $routes->get('seo/sitemap', 'Api\V1\SeoController::sitemap');

        // 통계 (#15)
        $routes->get('stats/dashboard', 'Api\V1\StatsController::dashboard');
        $routes->get('stats/posts', 'Api\V1\StatsController::posts');
        $routes->get('stats/comments', 'Api\V1\StatsController::comments');
        $routes->get('stats/users', 'Api\V1\StatsController::users');
    });
});

// =====================================================
// 슈퍼관리자 패널 (전역 테넌트/사용자 관리)
// filter: group:superadmin (#8)
// =====================================================
$routes->group('admin', ['filter' => 'group:superadmin'], static function ($routes): void {
    $routes->get('/', 'Admin\DashboardController::index');
    $routes->resource('tenants', ['controller' => 'Admin\TenantsController']);
    $routes->resource('users', ['controller' => 'Admin\UsersController']);
});

// =====================================================
// 테넌트 라우트 (반드시 마지막에 위치)
// (:segment) 가 /admin, /api, /docs 등과 충돌하지 않도록
// filter: tenant (#7 에서 구현)
// =====================================================
$routes->group('([a-z0-9][a-z0-9\-]{0,61})', ['filter' => 'tenant'], static function ($routes): void {
    // 공개 페이지 (#14)
    $routes->get('/', 'Tenant\HomeController::index/$1');
    $routes->get('posts', 'Tenant\PostsController::index/$1');
    $routes->get('posts/(:segment)', 'Tenant\PostsController::show/$1/$2');
    $routes->get('pages/(:segment)', 'Tenant\PagesController::show/$1/$2');
    $routes->get('categories/(:segment)', 'Tenant\CategoriesController::posts/$1/$2');
    $routes->get('tags/(:segment)', 'Tenant\TagsController::posts/$1/$2');
    $routes->get('search', 'Tenant\SearchController::index/$1');
    $routes->get('sitemap.xml', 'Tenant\SeoController::sitemap/$1');
    $routes->get('robots.txt', 'Tenant\SeoController::robots/$1');

    // 테넌트 관리자 패널 (#15)
    // filter: group:admin,superadmin (#8 에서 구현)
    $routes->group('admin', ['filter' => 'group:admin,superadmin'], static function ($routes): void {
        $routes->get('/', 'Tenant\Admin\DashboardController::index/$1');
        $routes->resource('posts', ['controller' => 'Tenant\Admin\PostsController']);
        $routes->resource('pages', ['controller' => 'Tenant\Admin\PagesController']);
        $routes->resource('categories', ['controller' => 'Tenant\Admin\CategoriesController']);
        $routes->resource('tags', ['controller' => 'Tenant\Admin\TagsController']);
        $routes->resource('comments', ['controller' => 'Tenant\Admin\CommentsController']);
        $routes->resource('media', ['controller' => 'Tenant\Admin\MediaController']);
        $routes->resource('users', ['controller' => 'Tenant\Admin\UsersController']);
        $routes->get('settings', 'Tenant\Admin\SettingsController::index/$1');
        $routes->post('settings', 'Tenant\Admin\SettingsController::update/$1');
        $routes->get('seo', 'Tenant\Admin\SeoController::index/$1');
        $routes->post('seo/update', 'Tenant\Admin\SeoController::update/$1');
        $routes->get('stats', 'Tenant\Admin\StatsController::index/$1');
    });
});

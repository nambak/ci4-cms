<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('docs/api', 'Docs::api');

service('auth')->routes($routes);

// API v1 라우트
$routes->group('api/v1', static function ($routes): void {
    // 인증
    $routes->post('auth/register', 'Api\V1\AuthController::register');
    $routes->post('auth/login', 'Api\V1\AuthController::login');
    $routes->get('auth/me', 'Api\V1\AuthController::me');
    $routes->post('auth/logout', 'Api\V1\AuthController::logout');
    $routes->post('auth/refresh', 'Api\V1\AuthController::refresh');

    // 포스트
    $routes->resource('posts', ['controller' => 'Api\V1\PostsController']);
    $routes->post('posts/(:num)/publish', 'Api\V1\PostsController::publish/$1');
    $routes->get('posts/(:num)/comments', 'Api\V1\PostsController::comments/$1');

    // 카테고리
    $routes->resource('categories', ['controller' => 'Api\V1\CategoriesController']);
    $routes->get('categories/(:num)/posts', 'Api\V1\CategoriesController::posts/$1');

    // 댓글
    $routes->resource('comments', ['controller' => 'Api\V1\CommentsController']);
    $routes->post('comments/(:num)/replies', 'Api\V1\CommentsController::reply/$1');
    $routes->post('comments/(:num)/moderate', 'Api\V1\CommentsController::moderate/$1');
});

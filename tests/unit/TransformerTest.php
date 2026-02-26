<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\CommentStatus;
use App\Enums\MediaType;
use App\Enums\PostStatus;
use App\Transformers\AuthUserTransformer;
use App\Transformers\CategoryTransformer;
use App\Transformers\CommentTransformer;
use App\Transformers\MediaTransformer;
use App\Transformers\PostTransformer;
use App\Transformers\TagTransformer;
use App\Transformers\UserTransformer;
use CodeIgniter\Test\CIUnitTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

/**
 * API Transformer 단위 테스트
 *
 * 관련 이슈: #48
 */
#[Group('unit')]
class TransformerTest extends CIUnitTestCase
{
    // -------------------------------------------------------------------------
    // PostTransformer
    // -------------------------------------------------------------------------

    #[Test]
    public function post_transformer_returns_expected_fields(): void
    {
        $resource = [
            'id'           => 1,
            'title'        => '테스트 포스트',
            'slug'         => 'test-post',
            'excerpt'      => '요약',
            'content'      => '본문',
            'status'       => PostStatus::Published,
            'category_id'  => 2,
            'author_id'    => 3,
            'published_at' => '2025-01-01T00:00:00Z',
            'created_at'   => '2025-01-01T00:00:00Z',
            'updated_at'   => '2025-01-01T00:00:00Z',
        ];

        $result = (new PostTransformer())->transform($resource);

        $this->assertSame(1, $result['id']);
        $this->assertSame('테스트 포스트', $result['title']);
        $this->assertSame('test-post', $result['slug']);
        $this->assertSame('published', $result['status']);
        $this->assertArrayHasKey('category_id', $result);
        $this->assertArrayHasKey('author_id', $result);
        $this->assertArrayNotHasKey('password', $result);
    }

    #[Test]
    public function post_transformer_serializes_enum_status_to_string(): void
    {
        $resource = [
            'id' => 1, 'title' => 'T', 'slug' => 's', 'content' => 'c',
            'status'      => PostStatus::Draft,
            'author_id'   => 1,
            'created_at'  => null, 'updated_at' => null,
        ];

        $result = (new PostTransformer())->transform($resource);

        $this->assertSame('draft', $result['status']);
        $this->assertIsString($result['status']);
    }

    #[Test]
    public function post_transformer_accepts_string_status(): void
    {
        $resource = [
            'id' => 1, 'title' => 'T', 'slug' => 's', 'content' => 'c',
            'status'     => 'archived',
            'author_id'  => 1,
            'created_at' => null, 'updated_at' => null,
        ];

        $result = (new PostTransformer())->transform($resource);

        $this->assertSame('archived', $result['status']);
    }

    #[Test]
    public function post_transformer_allowed_fields_covers_all_public_fields(): void
    {
        $transformer   = new PostTransformer();
        $reflection    = new \ReflectionMethod($transformer, 'getAllowedFields');
        $allowedFields = $reflection->invoke($transformer);

        $this->assertContains('id', $allowedFields);
        $this->assertContains('title', $allowedFields);
        $this->assertContains('slug', $allowedFields);
        $this->assertContains('content', $allowedFields);
        $this->assertContains('status', $allowedFields);
        $this->assertNotContains('password', $allowedFields);
    }

    #[Test]
    public function post_transformer_allowed_includes(): void
    {
        $transformer     = new PostTransformer();
        $reflection      = new \ReflectionMethod($transformer, 'getAllowedIncludes');
        $allowedIncludes = $reflection->invoke($transformer);

        $this->assertContains('category', $allowedIncludes);
        $this->assertContains('tags', $allowedIncludes);
        $this->assertContains('author', $allowedIncludes);
    }

    // -------------------------------------------------------------------------
    // CategoryTransformer
    // -------------------------------------------------------------------------

    #[Test]
    public function category_transformer_returns_expected_fields(): void
    {
        $resource = [
            'id'          => 1,
            'name'        => '기술',
            'slug'        => 'tech',
            'description' => '기술 관련',
            'created_at'  => '2025-01-01T00:00:00Z',
            'updated_at'  => '2025-01-01T00:00:00Z',
        ];

        $result = (new CategoryTransformer())->transform($resource);

        $this->assertSame(1, $result['id']);
        $this->assertSame('기술', $result['name']);
        $this->assertSame('tech', $result['slug']);
        $this->assertArrayHasKey('description', $result);
    }

    #[Test]
    public function category_transformer_disables_includes_until_implemented(): void
    {
        $transformer = new CategoryTransformer();
        $reflection  = new \ReflectionMethod($transformer, 'getAllowedIncludes');

        $this->assertSame([], $reflection->invoke($transformer));
    }

    // -------------------------------------------------------------------------
    // TagTransformer
    // -------------------------------------------------------------------------

    #[Test]
    public function tag_transformer_returns_expected_fields(): void
    {
        $resource = [
            'id' => 1, 'name' => 'PHP', 'slug' => 'php',
            'created_at' => null, 'updated_at' => null,
        ];

        $result = (new TagTransformer())->transform($resource);

        $this->assertSame(1, $result['id']);
        $this->assertSame('PHP', $result['name']);
        $this->assertSame('php', $result['slug']);
    }

    // -------------------------------------------------------------------------
    // CommentTransformer
    // -------------------------------------------------------------------------

    #[Test]
    public function comment_transformer_returns_expected_fields(): void
    {
        $resource = [
            'id'         => 1,
            'post_id'    => 10,
            'user_id'    => 5,
            'parent_id'  => null,
            'content'    => '댓글 내용',
            'status'     => CommentStatus::Approved,
            'created_at' => '2025-01-01T00:00:00Z',
            'updated_at' => '2025-01-01T00:00:00Z',
        ];

        $result = (new CommentTransformer())->transform($resource);

        $this->assertSame(1, $result['id']);
        $this->assertSame('approved', $result['status']);
        $this->assertNull($result['parent_id']);
        $this->assertArrayHasKey('post_id', $result);
    }

    #[Test]
    public function comment_transformer_disables_includes_until_implemented(): void
    {
        $transformer = new CommentTransformer();
        $reflection  = new \ReflectionMethod($transformer, 'getAllowedIncludes');

        $this->assertSame([], $reflection->invoke($transformer));
    }

    // -------------------------------------------------------------------------
    // MediaTransformer
    // -------------------------------------------------------------------------

    #[Test]
    public function media_transformer_returns_expected_fields(): void
    {
        $resource = [
            'id'            => 1,
            'filename'      => 'image.jpg',
            'original_name' => '원본이미지.jpg',
            'mime_type'     => 'image/jpeg',
            'file_size'     => 102400,
            'path'          => '/uploads/2025/01/image.jpg',
            'type'          => MediaType::Image,
            'created_at'    => null,
            'updated_at'    => null,
        ];

        $result = (new MediaTransformer())->transform($resource);

        $this->assertSame(1, $result['id']);
        $this->assertSame('image.jpg', $result['filename']);
        $this->assertSame('원본이미지.jpg', $result['original_name']);
        $this->assertSame(102400, $result['size']);
        $this->assertSame('image', $result['type']);
        $this->assertArrayNotHasKey('file_size', $result);
    }

    #[Test]
    public function media_transformer_accepts_string_type(): void
    {
        $resource = [
            'id' => 1, 'filename' => 'f', 'original_name' => 'o',
            'mime_type' => 'video/mp4', 'path' => '/p',
            'type'       => 'video',
            'created_at' => null, 'updated_at' => null,
        ];

        $result = (new MediaTransformer())->transform($resource);

        $this->assertSame('video', $result['type']);
    }

    // -------------------------------------------------------------------------
    // UserTransformer (공개용 - email 미포함)
    // -------------------------------------------------------------------------

    #[Test]
    public function user_transformer_excludes_email_and_sensitive_fields(): void
    {
        $resource = [
            'id'         => 1,
            'email'      => 'user@example.com',
            'username'   => '홍길동',
            'password'   => 'hashed_secret',
            'created_at' => '2025-01-01T00:00:00Z',
            'updated_at' => '2025-01-01T00:00:00Z',
        ];

        $result = (new UserTransformer())->transform($resource);

        $this->assertSame(1, $result['id']);
        $this->assertArrayNotHasKey('email', $result);
        $this->assertArrayNotHasKey('password', $result);
    }

    #[Test]
    public function user_transformer_allowed_fields_excludes_email_and_password(): void
    {
        $transformer   = new UserTransformer();
        $reflection    = new \ReflectionMethod($transformer, 'getAllowedFields');
        $allowedFields = $reflection->invoke($transformer);

        $this->assertNotContains('email', $allowedFields);
        $this->assertNotContains('password', $allowedFields);
        $this->assertContains('id', $allowedFields);
        $this->assertContains('name', $allowedFields);
    }

    // -------------------------------------------------------------------------
    // AuthUserTransformer (인증 사용자 전용 - email 포함)
    // -------------------------------------------------------------------------

    #[Test]
    public function auth_user_transformer_includes_email(): void
    {
        $resource = [
            'id'         => 1,
            'email'      => 'user@example.com',
            'username'   => '홍길동',
            'password'   => 'hashed_secret',
            'created_at' => '2025-01-01T00:00:00Z',
            'updated_at' => '2025-01-01T00:00:00Z',
        ];

        $result = (new AuthUserTransformer())->transform($resource);

        $this->assertSame('user@example.com', $result['email']);
        $this->assertArrayNotHasKey('password', $result);
    }

    #[Test]
    public function auth_user_transformer_allowed_fields_includes_email(): void
    {
        $transformer   = new AuthUserTransformer();
        $reflection    = new \ReflectionMethod($transformer, 'getAllowedFields');
        $allowedFields = $reflection->invoke($transformer);

        $this->assertContains('email', $allowedFields);
        $this->assertNotContains('password', $allowedFields);
    }

    // -------------------------------------------------------------------------
    // transformMany
    // -------------------------------------------------------------------------

    #[Test]
    public function transform_many_returns_collection(): void
    {
        $resources = [
            ['id' => 1, 'name' => 'PHP', 'slug' => 'php', 'created_at' => null, 'updated_at' => null],
            ['id' => 2, 'name' => 'CI4', 'slug' => 'ci4', 'created_at' => null, 'updated_at' => null],
        ];

        $result = (new TagTransformer())->transformMany($resources);

        $this->assertCount(2, $result);
        $this->assertSame(1, $result[0]['id']);
        $this->assertSame(2, $result[1]['id']);
    }
}

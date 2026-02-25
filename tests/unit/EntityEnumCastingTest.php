<?php

namespace Tests\Unit;

use App\Entities\Comment;
use App\Entities\Media;
use App\Entities\Post;
use App\Enums\CommentStatus;
use App\Enums\MediaType;
use App\Enums\PostStatus;
use CodeIgniter\Test\CIUnitTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

/**
 * Entity Enum Casting 동작 테스트
 *
 * DB 문자열 → Enum 인스턴스 자동 변환, 역방향 할당 검증
 *
 * 관련 이슈: #50
 */
#[Group('unit')]
class EntityEnumCastingTest extends CIUnitTestCase
{
    // -------------------------------------------------------------------------
    // Post Entity
    // -------------------------------------------------------------------------

    #[Test]
    public function post_entity_casts_string_to_post_status_enum(): void
    {
        $post = new Post(['status' => 'published']);
        $this->assertInstanceOf(PostStatus::class, $post->status);
        $this->assertSame(PostStatus::Published, $post->status);
    }

    #[Test]
    public function post_entity_accepts_enum_assignment(): void
    {
        $post = new Post();
        $post->status = PostStatus::Draft;
        $this->assertSame(PostStatus::Draft, $post->status);
    }

    #[Test]
    public function post_entity_is_published_helper(): void
    {
        $post = new Post(['status' => 'published']);
        $this->assertTrue($post->isPublished());
        $this->assertFalse($post->isDraft());
    }

    #[Test]
    public function post_entity_nullable_status_is_null_when_missing(): void
    {
        $post = new Post();
        $this->assertNull($post->status);
    }

    // -------------------------------------------------------------------------
    // Comment Entity
    // -------------------------------------------------------------------------

    #[Test]
    public function comment_entity_casts_string_to_comment_status_enum(): void
    {
        $comment = new Comment(['status' => 'approved']);
        $this->assertInstanceOf(CommentStatus::class, $comment->status);
        $this->assertSame(CommentStatus::Approved, $comment->status);
    }

    #[Test]
    public function comment_entity_is_visible_helper(): void
    {
        $comment = new Comment(['status' => 'approved']);
        $this->assertTrue($comment->isVisible());

        $pending = new Comment(['status' => 'pending']);
        $this->assertFalse($pending->isVisible());
    }

    // -------------------------------------------------------------------------
    // Media Entity
    // -------------------------------------------------------------------------

    #[Test]
    public function media_entity_casts_string_to_media_type_enum(): void
    {
        $media = new Media(['type' => 'image']);
        $this->assertInstanceOf(MediaType::class, $media->type);
        $this->assertSame(MediaType::Image, $media->type);
    }

    #[Test]
    public function media_entity_is_image_helper(): void
    {
        $media = new Media(['type' => 'image']);
        $this->assertTrue($media->isImage());

        $video = new Media(['type' => 'video']);
        $this->assertFalse($video->isImage());
    }
}

<?php

namespace Tests\Unit;

use App\Enums\CommentStatus;
use App\Enums\MediaType;
use App\Enums\PostStatus;
use App\Enums\UserRole;
use CodeIgniter\Test\CIUnitTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

/**
 * Enum 정의 및 비즈니스 로직 테스트
 *
 * 관련 이슈: #50
 */
#[Group('unit')]
class EnumTest extends CIUnitTestCase
{
    // -------------------------------------------------------------------------
    // PostStatus
    // -------------------------------------------------------------------------

    #[Test]
    public function post_status_has_correct_backing_values(): void
    {
        $this->assertSame('draft',     PostStatus::Draft->value);
        $this->assertSame('published', PostStatus::Published->value);
        $this->assertSame('archived',  PostStatus::Archived->value);
    }

    #[Test]
    public function post_status_is_public_only_when_published(): void
    {
        $this->assertTrue(PostStatus::Published->isPublic());
        $this->assertFalse(PostStatus::Draft->isPublic());
        $this->assertFalse(PostStatus::Archived->isPublic());
    }

    #[Test]
    public function post_status_can_be_created_from_string(): void
    {
        $this->assertSame(PostStatus::Published, PostStatus::from('published'));
        $this->assertSame(PostStatus::Draft,     PostStatus::from('draft'));
    }

    // -------------------------------------------------------------------------
    // UserRole
    // -------------------------------------------------------------------------

    #[Test]
    public function user_role_has_correct_backing_values(): void
    {
        $this->assertSame('superadmin', UserRole::Superadmin->value);
        $this->assertSame('admin',      UserRole::Admin->value);
        $this->assertSame('developer',  UserRole::Developer->value);
        $this->assertSame('user',       UserRole::User->value);
        $this->assertSame('beta',       UserRole::Beta->value);
    }

    #[Test]
    public function user_role_admin_access_check(): void
    {
        $this->assertTrue(UserRole::Superadmin->hasAdminAccess());
        $this->assertTrue(UserRole::Admin->hasAdminAccess());
        $this->assertTrue(UserRole::Developer->hasAdminAccess());
        $this->assertFalse(UserRole::User->hasAdminAccess());
        $this->assertFalse(UserRole::Beta->hasAdminAccess());
    }

    // -------------------------------------------------------------------------
    // CommentStatus
    // -------------------------------------------------------------------------

    #[Test]
    public function comment_status_is_visible_only_when_approved(): void
    {
        $this->assertTrue(CommentStatus::Approved->isVisible());
        $this->assertFalse(CommentStatus::Pending->isVisible());
        $this->assertFalse(CommentStatus::Spam->isVisible());
    }

    #[Test]
    public function comment_status_has_correct_backing_values(): void
    {
        $this->assertSame('pending',  CommentStatus::Pending->value);
        $this->assertSame('approved', CommentStatus::Approved->value);
        $this->assertSame('spam',     CommentStatus::Spam->value);
    }

    // -------------------------------------------------------------------------
    // MediaType
    // -------------------------------------------------------------------------

    #[Test]
    public function media_type_has_correct_backing_values(): void
    {
        $this->assertSame('image',    MediaType::Image->value);
        $this->assertSame('video',    MediaType::Video->value);
        $this->assertSame('document', MediaType::Document->value);
    }

    #[Test]
    public function media_type_can_be_created_from_string(): void
    {
        $this->assertSame(MediaType::Image, MediaType::from('image'));
        $this->assertSame(MediaType::Video, MediaType::from('video'));
    }
}

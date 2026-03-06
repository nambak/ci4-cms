<?php

namespace Tests\Unit;

use App\Enums\MediaType;
use App\Enums\PostState;
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
    // PostState
    // -------------------------------------------------------------------------

    #[Test]
    public function post_state_has_correct_backing_values(): void
    {
        $this->assertSame('draft',     PostState::Draft->value);
        $this->assertSame('published', PostState::Published->value);
        $this->assertSame('archived',  PostState::Archived->value);
    }

    #[Test]
    public function post_state_is_public_only_when_published(): void
    {
        $this->assertTrue(PostState::Published->isPublic());
        $this->assertFalse(PostState::Draft->isPublic());
        $this->assertFalse(PostState::Archived->isPublic());
    }

    #[Test]
    public function post_state_can_be_created_from_string(): void
    {
        $this->assertSame(PostState::Published, PostState::from('published'));
        $this->assertSame(PostState::Draft,     PostState::from('draft'));
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

    // -------------------------------------------------------------------------
    // label() 매핑 회귀 테스트
    // -------------------------------------------------------------------------

    #[Test]
    public function each_enum_label_returns_expected_string(): void
    {
        $this->assertSame('임시저장', PostState::Draft->label());
        $this->assertSame('게시됨',   PostState::Published->label());
        $this->assertSame('보관됨',   PostState::Archived->label());

        $this->assertSame('Admin',     UserRole::Admin->label());
        $this->assertSame('Super Admin', UserRole::Superadmin->label());
        $this->assertSame('Beta User', UserRole::Beta->label());

        $this->assertSame('이미지',  MediaType::Image->label());
        $this->assertSame('동영상', MediaType::Video->label());
        $this->assertSame('문서',   MediaType::Document->label());
    }

    #[Test]
    public function enum_labels_are_unique_across_all_enums(): void
    {
        $labels = [
            PostState::Draft->label(),
            PostState::Published->label(),
            PostState::Archived->label(),
            MediaType::Image->label(),
            MediaType::Video->label(),
            MediaType::Document->label(),
        ];

        $this->assertSame(count($labels), count(array_unique($labels)));
    }
}

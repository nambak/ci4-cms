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
        $this->assertSame('draft', PostState::DRAFT->value);
        $this->assertSame('published', PostState::PUBLISHED->value);
        $this->assertSame('archived', PostState::ARCHIVED->value);
    }

    #[Test]
    public function post_state_is_public_only_when_published(): void
    {
        $this->assertTrue(PostState::PUBLISHED->isPublic());
        $this->assertFalse(PostState::DRAFT->isPublic());
        $this->assertFalse(PostState::ARCHIVED->isPublic());
    }

    #[Test]
    public function post_state_can_be_created_from_string(): void
    {
        $this->assertSame(PostState::PUBLISHED, PostState::from('published'));
        $this->assertSame(PostState::DRAFT, PostState::from('draft'));
    }

    // -------------------------------------------------------------------------
    // UserRole
    // -------------------------------------------------------------------------

    #[Test]
    public function user_role_has_correct_backing_values(): void
    {
        $this->assertSame('superadmin', UserRole::Superadmin->value);
        $this->assertSame('admin', UserRole::Admin->value);
        $this->assertSame('user', UserRole::User->value);
    }

    #[Test]
    public function user_role_admin_access_check(): void
    {
        $this->assertTrue(UserRole::Superadmin->hasAdminAccess());
        $this->assertTrue(UserRole::Admin->hasAdminAccess());
        $this->assertFalse(UserRole::User->hasAdminAccess());
    }

    // -------------------------------------------------------------------------
    // MediaType
    // -------------------------------------------------------------------------

    #[Test]
    public function media_type_has_correct_backing_values(): void
    {
        $this->assertSame('image', MediaType::Image->value);
        $this->assertSame('video', MediaType::Video->value);
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
        $this->assertSame('임시저장', PostState::DRAFT->label());
        $this->assertSame('게시됨', PostState::PUBLISHED->label());
        $this->assertSame('보관됨', PostState::ARCHIVED->label());

        $this->assertSame('Admin', UserRole::Admin->label());
        $this->assertSame('Super Admin', UserRole::Superadmin->label());

        $this->assertSame('이미지', MediaType::Image->label());
        $this->assertSame('동영상', MediaType::Video->label());
        $this->assertSame('문서', MediaType::Document->label());
    }

    #[Test]
    public function enum_labels_are_unique_across_all_enums(): void
    {
        $labels = [
            PostState::DRAFT->label(),
            PostState::PUBLISHED->label(),
            PostState::ARCHIVED->label(),
            MediaType::Image->label(),
            MediaType::Video->label(),
            MediaType::Document->label(),
        ];

        $this->assertSame(count($labels), count(array_unique($labels)));
    }
}

<?php

namespace Tests\Unit;

use App\Entities\Post;
use App\Enums\PostState;
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
        $post = new Post(['state' => 'published']);
        $this->assertInstanceOf(PostState::class, $post->state);
        $this->assertSame(PostState::Published, $post->state);
    }

    #[Test]
    public function post_entity_accepts_enum_assignment(): void
    {
        $post = new Post();
        $post->state = PostState::Draft;
        $this->assertSame(PostState::Draft, $post->state);
        $this->assertSame(PostState::Draft->value, $post->toRawArray()['state']);
    }

    #[Test]
    public function post_entity_is_published_helper(): void
    {
        $post = new Post(['state' => 'published']);
        $this->assertTrue($post->isPublished());
        $this->assertFalse($post->isDraft());
    }

    #[Test]
    public function post_entity_nullable_status_is_null_when_missing(): void
    {
        $post = new Post();
        $this->assertNull($post->state);
    }


}

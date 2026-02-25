<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

/**
 * 홈페이지 외부 링크 접근성 테스트
 *
 * 검증 항목:
 * - target="_blank" 링크에 rel="noopener noreferrer" 보안 속성
 * - 새 탭에서 열림을 스크린 리더에 알리는 aria-label
 *
 * 관련 이슈: #31
 */
#[Group('unit')]
class HomePageExternalLinksTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected $namespace;
    private string $body;

    protected function setUp(): void
    {
        parent::setUp();
        $this->body = (string) $this->get('/')->response()->getBody();
    }

    // -------------------------------------------------------------------------
    // GitHub 링크
    // -------------------------------------------------------------------------

    #[Test]
    public function github_link_opens_in_new_tab(): void
    {
        $this->assertStringContainsString('href="https://github.com/nambak/ci4-cms"', $this->body);
        $this->assertStringContainsString('target="_blank"', $this->body);
    }

    #[Test]
    public function github_link_has_security_attributes(): void
    {
        $this->assertStringContainsString('rel="noopener noreferrer"', $this->body);
    }

    #[Test]
    public function github_link_aria_label_includes_new_tab_notice(): void
    {
        $this->assertStringContainsString('GitHub 저장소 (새 탭에서 열림)', $this->body);
    }

    // -------------------------------------------------------------------------
    // LinkedIn 링크
    // -------------------------------------------------------------------------

    #[Test]
    public function linkedin_link_opens_in_new_tab(): void
    {
        $this->assertStringContainsString('href="https://www.linkedin.com/in/nambak80/"', $this->body);
        $this->assertStringContainsString('target="_blank"', $this->body);
    }

    #[Test]
    public function linkedin_link_aria_label_includes_new_tab_notice(): void
    {
        $this->assertStringContainsString('LinkedIn 프로필 (새 탭에서 열림)', $this->body);
    }
}

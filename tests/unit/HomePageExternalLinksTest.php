<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use DOMDocument;
use DOMElement;
use DOMXPath;
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
    private DOMXPath $xpath;

    protected function setUp(): void
    {
        parent::setUp();

        $body = (string) $this->get('/')->response()->getBody();

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($body);
        libxml_clear_errors();

        $this->xpath = new DOMXPath($dom);
    }

    private function findAnchorByHref(string $href): DOMElement
    {
        $nodes = $this->xpath->query('//a[@href="' . $href . '"]');
        $this->assertGreaterThan(0, $nodes->length, "No <a> found with href=\"{$href}\"");

        return $nodes->item(0);
    }

    // -------------------------------------------------------------------------
    // GitHub 링크
    // -------------------------------------------------------------------------

    #[Test]
    public function github_link_opens_in_new_tab(): void
    {
        $anchor = $this->findAnchorByHref('https://github.com/nambak/ci4-cms');
        $this->assertSame('_blank', $anchor->getAttribute('target'));
    }

    #[Test]
    public function github_link_has_security_attributes(): void
    {
        $anchor = $this->findAnchorByHref('https://github.com/nambak/ci4-cms');
        $this->assertSame('noopener noreferrer', $anchor->getAttribute('rel'));
    }

    #[Test]
    public function github_link_aria_label_includes_new_tab_notice(): void
    {
        $anchor = $this->findAnchorByHref('https://github.com/nambak/ci4-cms');
        $this->assertStringContainsString('새 탭에서 열림', $anchor->getAttribute('aria-label'));
    }

    // -------------------------------------------------------------------------
    // LinkedIn 링크
    // -------------------------------------------------------------------------

    #[Test]
    public function linkedin_link_opens_in_new_tab(): void
    {
        $anchor = $this->findAnchorByHref('https://www.linkedin.com/in/nambak80/');
        $this->assertSame('_blank', $anchor->getAttribute('target'));
    }

    #[Test]
    public function linkedin_link_aria_label_includes_new_tab_notice(): void
    {
        $anchor = $this->findAnchorByHref('https://www.linkedin.com/in/nambak80/');
        $this->assertStringContainsString('새 탭에서 열림', $anchor->getAttribute('aria-label'));
    }
}

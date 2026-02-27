<!DOCTYPE html>
<html lang="ko" data-theme="nord">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CI4 CMS - 멀티테넌시 콘텐츠 관리 플랫폼</title>
    <link rel="shortcut icon" href="<?= base_url('logo.svg') ?>" />
    <link rel="stylesheet" as="style" crossorigin href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.min.css" />
    <link href="<?= base_url('/assets/css/output.css') ?>" rel="stylesheet">
    <meta name="description" content="CodeIgniter 4 기반의 강력한 멀티테넌시 CMS 플랫폼. 여러 사이트를 하나의 시스템으로 관리하세요.">
</head>
<body class="min-h-screen">

    <!-- Navigation -->
    <nav class="navbar bg-nord-1/95 backdrop-blur-sm shadow-nord sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex-1">
                <a href="/" class="btn btn-ghost normal-case text-xl text-nord-8 hover:text-nord-7">
                    <img src="<?= base_url('assets/images/logo.svg') ?>" alt="CI4 CMS Logo" class="h-6 inline-block align-middle mr-2">
                    CI4 CMS
                </a>
            </div>
            <!-- Desktop Menu -->
            <div class="flex-none hidden md:block">
                <ul class="menu menu-horizontal px-1">
                    <li><a href="#features" class="text-nord-6 hover:text-nord-8">기능</a></li>
                    <li><a href="#architecture" class="text-nord-6 hover:text-nord-8">아키텍처</a></li>
                    <li><a href="/docs/api" class="text-nord-6 hover:text-nord-8">API 문서</a></li>
                    <li><a href="/login" class="btn-login">로그인</a></li>
                </ul>
            </div>
            <!-- Mobile Hamburger Button -->
            <div class="flex-none md:hidden">
                <button
                    id="mobile-menu-toggle"
                    class="hamburger-btn"
                    type="button"
                    aria-label="메뉴 열기"
                    aria-expanded="false"
                    aria-controls="mobile-menu"
                >
                    <span class="hamburger-line hamburger-line-1"></span>
                    <span class="hamburger-line hamburger-line-2"></span>
                    <span class="hamburger-line hamburger-line-3"></span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu-overlay" class="mobile-overlay" aria-hidden="true"></div>

    <!-- Mobile Menu -->
    <nav id="mobile-menu" class="mobile-menu" aria-label="모바일 메뉴" aria-hidden="true" inert>
        <ul class="mobile-menu-list">
            <li><a href="#features" class="mobile-menu-link">기능</a></li>
            <li><a href="#architecture" class="mobile-menu-link">아키텍처</a></li>
            <li><a href="/docs/api" class="mobile-menu-link">API 문서</a></li>
            <li class="mt-4 px-2">
                <a href="/login" class="btn btn-primary btn-block">로그인</a>
            </li>
        </ul>
    </nav>

    <!-- Hero Section -->
    <section class="hero-gradient py-16 md:py-24 lg:py-32">
        <div class="container mx-auto px-4">
            <div class="hero-content flex-col lg:flex-row-reverse gap-12">
                <div class="flex-1 text-center lg:text-left">
                    <div class="badge-nord mb-4">
                        PHP 8.x | 고성능 CMS
                    </div>
                    <h1 class="text-5xl md:text-6xl font-bold leading-tight mb-6">
                        <span class="text-gradient-nord">멀티테넌시</span><br>
                        <span class="text-nord-6">CMS 플랫폼</span>
                    </h1>
                    <p class="text-lg md:text-xl text-nord-4 leading-relaxed mb-8 max-w-2xl">
                        하나의 시스템으로 여러 사이트를 관리하세요.<br>
                        CodeIgniter 4와 Shield RBAC 기반의 강력한 멀티테넌시 콘텐츠 관리 플랫폼입니다.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="/register" class="btn btn-primary btn-lg shadow-lg hover:shadow-xl">
                            시작하기
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                        <a href="#features" class="btn btn-ghost btn-lg text-nord-4 hover:text-nord-6 hover:bg-nord-2">
                            자세히 보기
                        </a>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="bg-[#edf1f5] rounded-2xl py-8 shadow-2xl max-w-3xl mx-auto">
                        <img src="/assets/images/dashboard.png"
                             alt="Dashboard Mockup"
                             class="w-full rounded-lg">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-12 md:py-16 lg:py-20 bg-nord-6">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold leading-tight text-nord-1 mb-4">핵심 기능</h2>
                <p class="text-xl text-nord-3 leading-normal max-w-2xl mx-auto">
                    현대적인 CMS에 필요한 모든 기능을 제공합니다
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-5xl mx-auto">
                <!-- Feature 1: Multi-Tenancy -->
                <div class="feature-item">
                    <div class="feature-number">01</div>
                    <div class="feature-content">
                        <h3>멀티테넌시</h3>
                        <p>URL 기반 테넌트 분리로 하나의 시스템에서 여러 사이트를 독립적으로 운영할 수 있습니다.</p>
                        <div class="badge badge-primary badge-sm">yoursite.com/tenant-slug</div>
                    </div>
                </div>

                <!-- Feature 2: RBAC -->
                <div class="feature-item">
                    <div class="feature-number">02</div>
                    <div class="feature-content">
                        <h3>Shield RBAC</h3>
                        <p>CodeIgniter Shield 기반의 강력한 역할 기반 접근 제어로 세밀한 권한 관리가 가능합니다.</p>
                        <div class="badge badge-secondary badge-sm">Role-Based Access Control</div>
                    </div>
                </div>

                <!-- Feature 3: RESTful API -->
                <div class="feature-item">
                    <div class="feature-number">03</div>
                    <div class="feature-content">
                        <h3>RESTful API</h3>
                        <p>OpenAPI 3.0 스펙 기반의 완전한 RESTful API를 제공하여 헤드리스 CMS로도 활용 가능합니다.</p>
                        <div class="badge badge-accent badge-sm">OpenAPI 3.0</div>
                    </div>
                </div>

                <!-- Feature 4: Testing -->
                <div class="feature-item">
                    <div class="feature-number">04</div>
                    <div class="feature-content">
                        <h3>자동화된 테스트</h3>
                        <p>PHPUnit 기반의 통합 테스트로 안정적인 코드베이스를 유지합니다.</p>
                        <div class="badge badge-success badge-sm">PHPUnit 10.5</div>
                    </div>
                </div>

                <!-- Feature 5: Modern Stack -->
                <div class="feature-item">
                    <div class="feature-number">05</div>
                    <div class="feature-content">
                        <h3>최신 기술 스택</h3>
                        <p>PHP 8.x, CodeIgniter 4, Tailwind CSS, DaisyUI로 구성된 모던한 개발 환경을 제공합니다.</p>
                        <div class="badge badge-warning badge-sm">PHP 8.x</div>
                    </div>
                </div>

                <!-- Feature 6: Nord Theme -->
                <div class="feature-item">
                    <div class="feature-number">06</div>
                    <div class="feature-content">
                        <h3>Nord 테마</h3>
                        <p>Nord 색상 팔레트 기반의 세련된 UI로 일관된 디자인 시스템을 제공합니다.</p>
                        <div class="badge badge-info badge-sm">Nord Color Palette</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Architecture Section -->
    <section id="architecture" class="py-12 md:py-16 lg:py-20 bg-architecture">
        <div class="container mx-auto px-4">
            <div class="text-center mb-8">
                <h2 class="text-4xl md:text-5xl font-bold leading-tight text-nord-6 mb-4">아키텍처</h2>
                <p class="text-xl text-nord-4 leading-normal max-w-2xl mx-auto">
                    확장 가능하고 유지보수하기 쉬운 구조
                </p>
            </div>

            <!-- Architecture Diagram -->
            <div class="arch-diagram-container max-w-5xl mx-auto mb-12 md:mb-16 p-4 md:p-8">
                <svg role="img" aria-labelledby="arch-title arch-desc"
                     viewBox="0 0 1180 140" xmlns="http://www.w3.org/2000/svg"
                     class="h-auto arch-diagram" focusable="false">
                    <title id="arch-title">CI4 CMS 요청 처리 흐름</title>
                    <desc id="arch-desc">사용자의 요청이 보안 검사, 요청 처리, 데이터 조회를 거쳐 화면에 응답되는 5단계 흐름도</desc>

                    <defs>
                        <marker id="arrow" markerWidth="10" markerHeight="7" refX="10" refY="3.5" orient="auto">
                            <polygon points="0 0, 10 3.5, 0 7" fill="#d8dee9"/>
                        </marker>
                    </defs>

                    <!-- Flow Lines -->
                    <line x1="210" y1="70" x2="248" y2="70" stroke="#d8dee9" stroke-width="2" marker-end="url(#arrow)"/>
                    <line x1="450" y1="70" x2="488" y2="70" stroke="#d8dee9" stroke-width="2" marker-end="url(#arrow)"/>
                    <line x1="690" y1="70" x2="728" y2="70" stroke="#d8dee9" stroke-width="2" marker-end="url(#arrow)"/>
                    <line x1="930" y1="70" x2="968" y2="70" stroke="#d8dee9" stroke-width="2" marker-end="url(#arrow)"/>

                    <!-- 1. 사용자 -->
                    <g class="arch-node">
                        <rect x="10" y="20" width="200" height="100" rx="12" fill="#88c0d0" opacity="0.9"/>
                        <text x="110" y="64" text-anchor="middle" fill="#2e3440" font-size="24" font-weight="600">사용자</text>
                        <text x="110" y="92" text-anchor="middle" fill="#2e3440" font-size="15" opacity="0.6">브라우저 · 앱</text>
                    </g>

                    <!-- 2. 보안 검사 -->
                    <g class="arch-node">
                        <rect x="250" y="20" width="200" height="100" rx="12" fill="#bf616a" opacity="0.9"/>
                        <text x="350" y="64" text-anchor="middle" fill="#eceff4" font-size="24" font-weight="600">보안 검사</text>
                        <text x="350" y="92" text-anchor="middle" fill="#eceff4" font-size="15" opacity="0.7">악성 요청 차단</text>
                    </g>

                    <!-- 3. 요청 처리 -->
                    <g class="arch-node">
                        <rect x="490" y="20" width="200" height="100" rx="12" fill="#81a1c1" opacity="0.9"/>
                        <text x="590" y="64" text-anchor="middle" fill="#eceff4" font-size="24" font-weight="600">요청 처리</text>
                        <text x="590" y="92" text-anchor="middle" fill="#eceff4" font-size="15" opacity="0.7">분석 및 실행</text>
                    </g>

                    <!-- 4. 데이터 -->
                    <g class="arch-node">
                        <rect x="730" y="20" width="200" height="100" rx="12" fill="#a3be8c" opacity="0.9"/>
                        <text x="830" y="64" text-anchor="middle" fill="#2e3440" font-size="24" font-weight="600">데이터</text>
                        <text x="830" y="92" text-anchor="middle" fill="#2e3440" font-size="15" opacity="0.6">정보 조회 · 저장</text>
                    </g>

                    <!-- 5. 응답 -->
                    <g class="arch-node">
                        <rect x="970" y="20" width="200" height="100" rx="12" fill="#b48ead" opacity="0.9"/>
                        <text x="1070" y="64" text-anchor="middle" fill="#eceff4" font-size="24" font-weight="600">응답</text>
                        <text x="1070" y="92" text-anchor="middle" fill="#eceff4" font-size="15" opacity="0.7">결과 화면 전달</text>
                    </g>
                </svg>
            </div>

            <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                <!-- Architecture Card 1 -->
                <div class="bg-white/90 border border-white/40 rounded-2xl p-6">
                    <h3 class="text-2xl font-bold leading-tight text-nord-10 mb-4">멀티테넌시 구조</h3>
                    <ul class="space-y-3 text-nord-2 leading-normal">
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-nord-14 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>URL 기반 테넌트 분리</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-nord-14 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>독립적인 콘텐츠 관리</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-nord-14 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>테넌트 필터 기반 데이터 격리</span>
                        </li>
                    </ul>
                </div>

                <!-- Architecture Card 2 -->
                <div class="bg-white/90 border border-white/40 rounded-2xl p-6">
                    <h3 class="text-2xl font-bold leading-tight text-nord-10 mb-4">인증 & 권한</h3>
                    <ul class="space-y-3 text-nord-2 leading-normal">
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-nord-14 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>CodeIgniter Shield 통합</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-nord-14 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Bearer Token API 인증</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-nord-14 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>그룹 및 권한 기반 접근 제어</span>
                        </li>
                    </ul>
                </div>

                <!-- Architecture Card 3 -->
                <div class="bg-white/90 border border-white/40 rounded-2xl p-6">
                    <h3 class="text-2xl font-bold leading-tight text-nord-10 mb-4">API 구조</h3>
                    <ul class="space-y-3 text-nord-2 leading-normal">
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-nord-14 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>RESTful 설계 원칙</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-nord-14 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>OpenAPI 3.0 문서화</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-nord-14 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>JSON 응답 표준화</span>
                        </li>
                    </ul>
                </div>

                <!-- Architecture Card 4 -->
                <div class="bg-white/90 border border-white/40 rounded-2xl p-6">
                    <h3 class="text-2xl font-bold leading-tight text-nord-10 mb-4">테스트 환경</h3>
                    <ul class="space-y-3 text-nord-2 leading-normal">
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-nord-14 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>PHPUnit 10.5 통합 테스트</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-nord-14 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>OpenAPI 스펙 기반 테스트</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-nord-14 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>코드 커버리지 리포트</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-12 md:py-16 lg:py-20 bg-gradient-to-r from-nord-4 to-nord-5">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-4xl md:text-5xl font-bold leading-tight text-nord-1 mb-6">
                    지금 바로 시작하세요
                </h2>
                <p class="text-xl text-nord-2 leading-relaxed mb-8 max-w-2xl mx-auto">
                    강력한 멀티테넌시 CMS 플랫폼으로 여러 사이트를 효율적으로 관리하세요.<br>
                    무료로 시작할 수 있습니다.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="/register" class="btn btn-primary btn-lg shadow-lg hover:shadow-xl">
                        시작하기
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                    <a href="/docs/api" class="btn btn-ghost btn-lg text-nord-2 hover:text-nord-0 hover:bg-nord-6">
                        API 문서 보기
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </a>
                </div>
                <!-- 로그인 링크 추가 -->
                <p class="mt-6 text-base text-nord-3">
                    이미 계정이 있으신가요?
                    <a href="/login" class="text-nord-10 hover:text-nord-9 font-semibold underline underline-offset-2 transition-colors">로그인</a>
                </p>

                <div class="mt-12 grid grid-cols-1 sm:grid-cols-3 gap-8 max-w-2xl mx-auto">
                    <div class="text-center">
                        <div class="text-5xl font-bold leading-tight text-nord-10 mb-2">100%</div>
                        <div class="text-nord-3 text-sm uppercase tracking-wide">오픈소스</div>
                    </div>
                    <div class="text-center">
                        <div class="text-5xl font-bold leading-tight text-nord-10 mb-2">PHP 8.x</div>
                        <div class="text-nord-3 text-sm uppercase tracking-wide">최적화</div>
                    </div>
                    <div class="text-center">
                        <div class="text-5xl font-bold leading-tight text-nord-10 mb-2">RESTful</div>
                        <div class="text-nord-3 text-sm uppercase tracking-wide">API 제공</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-nord-0 text-nord-4">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- 브랜드 -->
                <div>
                    <div class="flex items-center mb-3">
                        <img src="<?= base_url('assets/images/logo.svg') ?>" alt="logo" class="w-8 h-8 mr-2">
                        <span class="text-lg font-bold text-nord-6">CI4 CMS</span>
                    </div>
                    <p class="text-sm text-nord-4 leading-relaxed">
                        CodeIgniter 4 기반의 멀티테넌시 CMS 플랫폼
                    </p>
                </div>
            </div>
            <!-- 소셜 미디어 -->
            <div class="flex gap-4 mt-4">
                <a href="https://github.com/nambak/ci4-cms" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center text-nord-3 hover:text-nord-6 transition-colors" aria-label="GitHub 저장소 (새 탭에서 열림)">
                    <svg class="size-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                    </svg>
                </a>
                <a href="https://www.linkedin.com/in/nambak80/" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center text-nord-3 hover:text-nord-6 transition-colors" aria-label="LinkedIn 프로필 (새 탭에서 열림)">
                    <svg class="size-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                    </svg>
                </a>
                <a href="mailto:nambak80@gmail.com"
                   class="flex items-center gap-2 text-sm text-nord-3 hover:text-nord-6 transition-colors"
                   aria-label="Email nambak80@gmail.com">
                    <svg class="size-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- 저작권 -->
        <div class="border-t border-nord-3/30">
            <div class="container mx-auto px-4 py-6">
                <p class="text-center text-sm text-nord-3">
                    Copyright &copy; <?= date('Y') ?> CI4 CMS - All rights reserved
                </p>
            </div>
        </div>
    </footer>

    <!-- Scroll Effect & Mobile Menu Script -->
    <script>
        (function() {
            // === Navbar Scroll Effect ===
            const nav = document.querySelector('.navbar');
            if (nav) {
                let ticking = false;
                function updateNavbar() {
                    if (window.scrollY > 50) {
                        nav.classList.add('scrolled');
                    } else {
                        nav.classList.remove('scrolled');
                    }
                    ticking = false;
                }
                window.addEventListener('scroll', function() {
                    if (!ticking) {
                        window.requestAnimationFrame(updateNavbar);
                        ticking = true;
                    }
                });
                updateNavbar();
            }

            // === Mobile Menu ===
            const toggle = document.getElementById('mobile-menu-toggle');
            const menu = document.getElementById('mobile-menu');
            const overlay = document.getElementById('mobile-menu-overlay');
            if (!toggle || !menu || !overlay) return;

            function openMenu() {
                toggle.classList.add('active');
                menu.classList.add('active');
                overlay.classList.add('active');
                toggle.setAttribute('aria-expanded', 'true');
                toggle.setAttribute('aria-label', '메뉴 닫기');
                menu.setAttribute('aria-hidden', 'false');
                overlay.setAttribute('aria-hidden', 'false');
                document.getElementById('mobile-menu').inert = false;
                document.body.style.overflow = 'hidden';
            }

            function closeMenu() {
                toggle.classList.remove('active');
                menu.classList.remove('active');
                overlay.classList.remove('active');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.setAttribute('aria-label', '메뉴 열기');
                menu.setAttribute('aria-hidden', 'true');
                overlay.setAttribute('aria-hidden', 'true');
                document.getElementById('mobile-menu').inert = true;
                document.body.style.overflow = '';
            }

            function isOpen() {
                return menu.classList.contains('active');
            }

            toggle.addEventListener('click', function() {
                isOpen() ? closeMenu() : openMenu();
            });

            overlay.addEventListener('click', closeMenu);

            // ESC 키로 닫기
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && isOpen()) {
                    closeMenu();
                    toggle.focus();
                }
            });

            // 모바일 메뉴 링크 클릭 시 닫기
            menu.querySelectorAll('a').forEach(function(link) {
                link.addEventListener('click', closeMenu);
            });

            // 화면 크기 변경 시 메뉴 닫기
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768 && isOpen()) {
                    closeMenu();
                }
            });
        })();
    </script>

</body>
</html>

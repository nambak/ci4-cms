<!DOCTYPE html>
<html lang="ko" data-theme="nord">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CI4 CMS - 멀티테넌시 콘텐츠 관리 플랫폼</title>
    <link rel="shortcut icon" href="<?= base_url('logo.svg') ?>" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            <div class="flex-none">
                <ul class="menu menu-horizontal px-1">
                    <li><a href="#features" class="text-nord-6 hover:text-nord-8">기능</a></li>
                    <li><a href="#architecture" class="text-nord-6 hover:text-nord-8">아키텍처</a></li>
                    <li><a href="/docs/api-docs.html" class="text-nord-6 hover:text-nord-8">API 문서</a></li>
                    <li><a href="/login" class="btn-login">로그인</a></li>
                </ul>
            </div>
        </div>
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
    <section id="architecture" class="py-12 md:py-16 lg:py-20 bg-nord-5">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold leading-tight text-nord-1 mb-4">아키텍처</h2>
                <p class="text-xl text-nord-3 leading-normal max-w-2xl mx-auto">
                    확장 가능하고 유지보수하기 쉬운 구조
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                <!-- Architecture Card 1 -->
                <div class="bg-white/60 border border-nord-8/20 rounded-2xl p-6">
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
                <div class="bg-white/60 border border-nord-8/20 rounded-2xl p-6">
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
                <div class="bg-white/60 border border-nord-8/20 rounded-2xl p-6">
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
                <div class="bg-white/60 border border-nord-8/20 rounded-2xl p-6">
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
                    <a href="/docs/api-docs.html" class="btn btn-ghost btn-lg text-nord-2 hover:text-nord-0 hover:bg-nord-6">
                        API 문서 보기
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </a>
                </div>
                <!-- 로그인 링크 추가 -->
                <p class="mt-6 text-base text-nord-2">
                    이미 계정이 있으신가요?
                    <a href="/login" class="text-nord-10 hover:text-nord-9 font-semibold underline underline-offset-2 transition-colors">
                        로그인
                    </a>
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
    <footer class="footer footer-center p-10 bg-nord-1 text-nord-4">
        <div>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" style="stop-color:#6FA8BC;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#4A7C91;stop-opacity:1" />
                    </linearGradient>
                    <linearGradient id="grad2" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" style="stop-color:#2C4152;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#3D5A6C;stop-opacity:1" />
                    </linearGradient>
                </defs>
                <rect x="1" y="1" width="10" height="10" rx="3" fill="url(#grad1)"/>
                <rect x="13" y="1" width="10" height="10" rx="3" fill="url(#grad2)"/>
                <rect x="1" y="13" width="10" height="10" rx="3" fill="url(#grad2)"/>
                <rect x="13" y="13" width="10" height="10" rx="3" fill="url(#grad1)"/>
            </svg>
            <p class="font-bold leading-tight text-nord-6">
                CI4 CMS <br/>
                <span class="text-nord-4 font-normal leading-normal">CodeIgniter 4 기반 멀티테넌시 CMS 플랫폼</span>
            </p>
            <p class="text-sm leading-normal">Copyright &copy; <?= date('Y') ?> - All rights reserved</p>
        </div>
        <div>
            <div class="grid grid-flow-col gap-4">
                <a href="https://github.com/nambak/ci4-cms" class="link-nord">GitHub</a>
                <a href="/docs/api-docs.html" class="link-nord">API 문서</a>
            </div>
        </div>
    </footer>

    <!-- Scroll Effect Script -->
    <script>
        (function() {
            const nav = document.querySelector('.navbar');
            if (!nav) return;

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

            // Initial check
            updateNavbar();
        })();
    </script>

</body>
</html>

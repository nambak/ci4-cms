<!DOCTYPE html>
<html lang="ko" data-theme="nord">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CI4 CMS - 멀티테넌시 콘텐츠 관리 플랫폼</title>
    <link href="/assets/css/output.css" rel="stylesheet">
    <meta name="description" content="CodeIgniter 4 기반의 강력한 멀티테넌시 CMS 플랫폼. 여러 사이트를 하나의 시스템으로 관리하세요.">
</head>
<body class="min-h-screen">

    <!-- Navigation -->
    <nav class="navbar bg-nord-1 shadow-nord sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex-1">
                <a href="/" class="btn btn-ghost normal-case text-xl text-nord-8 hover:text-nord-7">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z" />
                    </svg>
                    CI4 CMS
                </a>
            </div>
            <div class="flex-none">
                <ul class="menu menu-horizontal px-1">
                    <li><a href="#features" class="text-nord-6 hover:text-nord-8">기능</a></li>
                    <li><a href="#architecture" class="text-nord-6 hover:text-nord-8">아키텍처</a></li>
                    <li><a href="/docs/api-docs.html" class="text-nord-6 hover:text-nord-8">API 문서</a></li>
                    <li><a href="/login" class="btn btn-primary btn-sm ml-2">로그인</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-gradient py-20 md:py-32">
        <div class="container mx-auto px-4">
            <div class="hero-content flex-col lg:flex-row-reverse gap-12">
                <div class="flex-1 text-center lg:text-left">
                    <div class="badge-nord mb-4">
                        CodeIgniter 4 Powered
                    </div>
                    <h1 class="text-5xl md:text-6xl font-bold mb-6">
                        <span class="text-gradient-nord">멀티테넌시</span><br>
                        <span class="text-nord-6">CMS 플랫폼</span>
                    </h1>
                    <p class="text-lg md:text-xl text-nord-4 mb-8 max-w-2xl">
                        하나의 시스템으로 여러 사이트를 관리하세요.
                        CodeIgniter 4와 Shield RBAC 기반의 강력한 멀티테넌시 콘텐츠 관리 플랫폼입니다.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="/register" class="btn btn-primary btn-lg">
                            시작하기
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                        <a href="#features" class="btn btn-secondary btn-outline btn-lg">
                            자세히 보기
                        </a>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="card-nord p-8">
                        <div class="flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-64 w-64 text-nord-8 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-nord-1">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-nord-6 mb-4">핵심 기능</h2>
                <p class="text-xl text-nord-4 max-w-2xl mx-auto">
                    현대적인 CMS에 필요한 모든 기능을 제공합니다
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1: Multi-Tenancy -->
                <div class="feature-card">
                    <div class="icon-container">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-nord-6 mb-3">멀티테넌시</h3>
                    <p class="text-nord-4 mb-4">
                        URL 기반 테넌트 분리로 하나의 시스템에서 여러 사이트를 독립적으로 운영할 수 있습니다.
                    </p>
                    <div class="badge badge-primary badge-sm">yoursite.com/tenant-slug</div>
                </div>

                <!-- Feature 2: RBAC -->
                <div class="feature-card">
                    <div class="icon-container">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-nord-6 mb-3">Shield RBAC</h3>
                    <p class="text-nord-4 mb-4">
                        CodeIgniter Shield 기반의 강력한 역할 기반 접근 제어로 세밀한 권한 관리가 가능합니다.
                    </p>
                    <div class="badge badge-secondary badge-sm">Role-Based Access Control</div>
                </div>

                <!-- Feature 3: RESTful API -->
                <div class="feature-card">
                    <div class="icon-container">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-nord-6 mb-3">RESTful API</h3>
                    <p class="text-nord-4 mb-4">
                        OpenAPI 3.0 스펙 기반의 완전한 RESTful API를 제공하여 헤드리스 CMS로도 활용 가능합니다.
                    </p>
                    <div class="badge badge-accent badge-sm">OpenAPI 3.0</div>
                </div>

                <!-- Feature 4: Testing -->
                <div class="feature-card">
                    <div class="icon-container">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-nord-6 mb-3">자동화된 테스트</h3>
                    <p class="text-nord-4 mb-4">
                        PHPUnit 기반의 통합 테스트로 안정적인 코드베이스를 유지합니다.
                    </p>
                    <div class="badge badge-success badge-sm">PHPUnit 10.5</div>
                </div>

                <!-- Feature 5: Modern Stack -->
                <div class="feature-card">
                    <div class="icon-container">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-nord-6 mb-3">최신 기술 스택</h3>
                    <p class="text-nord-4 mb-4">
                        PHP 8.1+, CodeIgniter 4, Tailwind CSS, DaisyUI로 구성된 모던한 개발 환경을 제공합니다.
                    </p>
                    <div class="badge badge-warning badge-sm">PHP 8.1+</div>
                </div>

                <!-- Feature 6: Nord Theme -->
                <div class="feature-card">
                    <div class="icon-container">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-nord-6 mb-3">Nord 테마</h3>
                    <p class="text-nord-4 mb-4">
                        일관된 디자인 시스템을 위한 Nord 색상 팔레트 기반의 세련된 UI를 제공합니다.
                    </p>
                    <div class="badge badge-info badge-sm">Nord Color Palette</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Architecture Section -->
    <section id="architecture" class="py-20 bg-nord-0">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-nord-6 mb-4">아키텍처</h2>
                <p class="text-xl text-nord-4 max-w-2xl mx-auto">
                    확장 가능하고 유지보수하기 쉬운 구조
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                <!-- Architecture Card 1 -->
                <div class="card-nord p-6">
                    <h3 class="text-2xl font-bold text-nord-8 mb-4">멀티테넌시 구조</h3>
                    <ul class="space-y-3 text-nord-4">
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
                <div class="card-nord p-6">
                    <h3 class="text-2xl font-bold text-nord-8 mb-4">인증 & 권한</h3>
                    <ul class="space-y-3 text-nord-4">
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
                <div class="card-nord p-6">
                    <h3 class="text-2xl font-bold text-nord-8 mb-4">API 구조</h3>
                    <ul class="space-y-3 text-nord-4">
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
                            <span>Base URL: /api/v1</span>
                        </li>
                    </ul>
                </div>

                <!-- Architecture Card 4 -->
                <div class="card-nord p-6">
                    <h3 class="text-2xl font-bold text-nord-8 mb-4">테스트 환경</h3>
                    <ul class="space-y-3 text-nord-4">
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
    <section class="py-20 bg-gradient-to-r from-nord-1 to-nord-2">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-4xl md:text-5xl font-bold text-nord-6 mb-6">
                    지금 바로 시작하세요
                </h2>
                <p class="text-xl text-nord-4 mb-8 max-w-2xl mx-auto">
                    강력한 멀티테넌시 CMS 플랫폼으로 여러 사이트를 효율적으로 관리하세요.
                    무료로 시작할 수 있습니다.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="/register" class="btn btn-primary btn-lg">
                        무료로 시작하기
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                    <a href="/docs/api-docs.html" class="btn btn-secondary btn-outline btn-lg">
                        API 문서 보기
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </a>
                </div>

                <div class="mt-12 grid grid-cols-3 gap-8 max-w-2xl mx-auto">
                    <div class="text-center">
                        <div class="text-4xl font-bold text-nord-8 mb-2">100%</div>
                        <div class="text-nord-4">오픈소스</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-nord-8 mb-2">PHP 8.1+</div>
                        <div class="text-nord-4">최신 기술</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-nord-8 mb-2">RESTful</div>
                        <div class="text-nord-4">API 제공</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer footer-center p-10 bg-nord-1 text-nord-4">
        <div>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-nord-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z" />
            </svg>
            <p class="font-bold text-nord-6">
                CI4 CMS <br/>
                <span class="text-nord-4 font-normal">CodeIgniter 4 기반 멀티테넌시 CMS 플랫폼</span>
            </p>
            <p>Copyright &copy; 2024 - All rights reserved</p>
        </div>
        <div>
            <div class="grid grid-flow-col gap-4">
                <a href="#" class="link-nord">문서</a>
                <a href="#" class="link-nord">GitHub</a>
                <a href="#" class="link-nord">API</a>
            </div>
        </div>
    </footer>

</body>
</html>
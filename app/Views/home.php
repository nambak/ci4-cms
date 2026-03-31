<!DOCTYPE html>
<html class="light" lang="ko">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>CI4 CMS - 멀티테넌시 콘텐츠 관리 플랫폼</title>
    <link rel="shortcut icon" href="<?= base_url('logo.svg') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/css/output.css') ?>"/>
    <link rel="stylesheet" as="style" crossorigin="anonymous" integrity="sha384-uGEvnSEpW2nM9xJFsrxrwakwrk9QdDTQIBJh0hVMu90OaVyMAMpAK1rIn0/Kh1/k" href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <meta name="description" content="CodeIgniter 4 기반의 강력한 멀티테넌시 CMS 플랫폼. 여러 사이트를 하나의 시스템으로 관리하세요.">
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        @media (prefers-reduced-motion: no-preference) {
            html {
                scroll-behavior: smooth;
            }
        }
    </style>
</head>
<body class="bg-surface text-on-surface antialiased">

<!-- Top Navigation -->
<nav class="fixed top-0 w-full z-50 bg-slate-50/70 backdrop-blur-xl shadow-sm">
    <div class="flex justify-between items-center max-w-7xl mx-auto px-6 h-16">
        <div class="flex items-center gap-8">
            <a href="/" class="flex items-center gap-2">
                <img src="<?= base_url('assets/images/logo.svg') ?>" alt="CI4 CMS Logo" class="h-6">
                <span class="text-xl font-bold tracking-tighter text-slate-900">CI4 CMS</span>
            </a>
            <div class="hidden md:flex items-center gap-6">
                <a class="text-sm font-semibold tracking-tight text-cyan-700 border-b-2 border-cyan-600" href="#features">기능</a>
                <a class="text-sm font-medium tracking-tight text-slate-600 hover:text-slate-900 transition-colors" href="#architecture">아키텍처</a>
                <a class="text-sm font-medium tracking-tight text-slate-600 hover:text-slate-900 transition-colors" href="/docs/api">API 문서</a>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <a href="/login" class="hidden md:block px-4 py-2 text-sm font-medium tracking-tight text-slate-600 hover:text-slate-900 transition-colors">로그인</a>
            <a href="/register" class="hero-gradient px-5 py-2.5 text-white font-bold text-sm rounded-xl shadow-lg shadow-md-primary/20 active:scale-95 transition-all">시작하기</a>
            <!-- Mobile Hamburger -->
            <button id="mobile-menu-toggle" class="md:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-lg transition-all" type="button" aria-label="메뉴 열기" aria-expanded="false" aria-controls="mobile-menu">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path id="menu-icon-open" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    <path id="menu-icon-close" class="hidden" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-white/95 backdrop-blur-xl border-t border-slate-200/50" aria-hidden="true">
        <div class="max-w-7xl mx-auto px-6 py-4 space-y-2">
            <a href="#features" class="block py-3 px-4 text-sm font-medium text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">기능</a>
            <a href="#architecture" class="block py-3 px-4 text-sm font-medium text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">아키텍처</a>
            <a href="/docs/api" class="block py-3 px-4 text-sm font-medium text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">API 문서</a>
            <hr class="border-slate-200">
            <a href="/login" class="block py-3 px-4 text-sm font-medium text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">로그인</a>
            <a href="/register" class="block py-3 px-4 text-sm font-bold text-white bg-md-primary rounded-lg text-center">시작하기</a>
        </div>
    </div>
</nav>

<main class="pt-16">

    <!-- Hero Section -->
    <section class="relative overflow-hidden pt-20 pb-32 px-6">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 items-center">
            <div class="space-y-8">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-secondary-container text-on-secondary-container text-xs font-bold">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-md-primary opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-md-primary"></span>
                        </span>
                        PHP 8.x | 고성능 CMS
                    </div>
                    <h1 class="text-5xl md:text-7xl font-black tracking-tight text-on-background leading-[1.1]">
                        <span class="text-transparent bg-clip-text hero-gradient">멀티테넌시</span><br>
                        CMS 플랫폼
                    </h1>
                </div>
                <p class="text-xl text-on-surface-variant max-w-xl leading-relaxed">
                    하나의 시스템으로 여러 사이트를 관리하세요.<br>
                    CodeIgniter 4와 Shield RBAC 기반의 강력한 멀티테넌시 콘텐츠 관리 플랫폼입니다.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="/register" class="hero-gradient px-8 py-4 text-white font-bold rounded-xl shadow-xl shadow-md-primary/20 hover:opacity-90 transition-all flex items-center gap-2">
                        시작하기 <span class="material-symbols-outlined">arrow_forward</span>
                    </a>
                    <a href="#features" class="bg-surface-container-high text-md-primary px-8 py-4 font-bold rounded-xl hover:bg-surface-container-highest transition-all">
                        자세히 보기
                    </a>
                </div>
            </div>
            <div class="relative">
                <div class="absolute -inset-4 bg-md-primary/5 blur-3xl rounded-full"></div>
                <div class="relative bg-surface-container-lowest rounded-xl shadow-2xl shadow-on-surface/5 border border-outline-variant/20 overflow-hidden transform lg:rotate-2 hover:rotate-0 transition-transform duration-700">
                    <div class="h-8 bg-surface-container-low flex items-center px-4 gap-1.5">
                        <div class="w-3 h-3 rounded-full bg-red-400/40"></div>
                        <div class="w-3 h-3 rounded-full bg-amber-400/40"></div>
                        <div class="w-3 h-3 rounded-full bg-emerald-400/40"></div>
                    </div>
                    <img alt="대시보드 인터페이스" class="w-full h-auto" src="<?= base_url('assets/images/dashboard.png') ?>"/>
                </div>
            </div>
        </div>
    </section>

    <!-- Social Proof / Metrics -->
    <section class="bg-surface-container-low py-12 border-y border-outline-variant/10 text-center">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="flex flex-col">
                    <span class="text-3xl font-black text-md-primary">100%</span>
                    <span class="font-bold text-on-secondary-container mt-1">오픈소스</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-3xl font-black text-md-primary">PHP 8.x</span>
                    <span class="font-bold text-on-secondary-container mt-1">최적화 엔진</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-3xl font-black text-md-primary">RESTful</span>
                    <span class="font-bold text-on-secondary-container mt-1">API 제공</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-3xl font-black text-md-primary">Zero</span>
                    <span class="font-bold text-on-secondary-container mt-1">기술 부채</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Grid -->
    <section id="features" class="bg-nord-5 py-32 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="mb-20 space-y-4">
                <span class="text-md-primary font-bold tracking-[0.2em] uppercase text-xs">핵심 기능</span>
                <h2 class="text-4xl font-black tracking-tight text-on-background">현대적인 CMS에 필요한 모든 기능</h2>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="group p-8 rounded-xl bg-surface-container-lowest hover:bg-surface-container-highest transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-md-primary/10 flex items-center justify-center text-md-primary mb-6 group-hover:bg-md-primary group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined">groups</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3">멀티테넌시</h3>
                    <p class="text-on-surface-variant leading-relaxed">URL 기반 테넌트 분리로 하나의 시스템에서 여러 사이트를 독립적으로 운영할 수 있습니다.</p>
                </div>
                <!-- Feature 2 -->
                <div class="group p-8 rounded-xl bg-surface-container-lowest hover:bg-surface-container-highest transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-md-primary/10 flex items-center justify-center text-md-primary mb-6 group-hover:bg-md-primary group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined">api</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3">RESTful API</h3>
                    <p class="text-on-surface-variant leading-relaxed">OpenAPI 3.0 스펙 기반의 완전한 RESTful API를 제공하여 헤드리스 CMS로도 활용 가능합니다.</p>
                </div>
                <!-- Feature 3 -->
                <div class="group p-8 rounded-xl bg-surface-container-lowest hover:bg-surface-container-highest transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-md-primary/10 flex items-center justify-center text-md-primary mb-6 group-hover:bg-md-primary group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined">stack</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3">최신 기술 스택</h3>
                    <p class="text-on-surface-variant leading-relaxed">PHP 8.x, CodeIgniter 4, Tailwind CSS, DaisyUI로 구성된 모던한 개발 환경을 제공합니다.</p>
                </div>
                <!-- Feature 4 -->
                <div class="group p-8 rounded-xl bg-surface-container-lowest hover:bg-surface-container-highest transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-md-primary/10 flex items-center justify-center text-md-primary mb-6 group-hover:bg-md-primary group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined">shield_person</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Shield RBAC</h3>
                    <p class="text-on-surface-variant leading-relaxed">CodeIgniter Shield 기반의 강력한 역할 기반 접근 제어로 세밀한 권한 관리가 가능합니다.</p>
                </div>
                <!-- Feature 5 -->
                <div class="group p-8 rounded-xl bg-surface-container-lowest hover:bg-surface-container-highest transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-md-primary/10 flex items-center justify-center text-md-primary mb-6 group-hover:bg-md-primary group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined">terminal</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3">자동화된 테스트</h3>
                    <p class="text-on-surface-variant leading-relaxed">PHPUnit 기반의 통합 테스트로 안정적인 코드베이스를 유지합니다.</p>
                </div>
                <!-- Feature 6 -->
                <div class="group p-8 rounded-xl bg-surface-container-lowest hover:bg-surface-container-highest transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-md-primary/10 flex items-center justify-center text-md-primary mb-6 group-hover:bg-md-primary group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined">palette</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Nord 테마</h3>
                    <p class="text-on-surface-variant leading-relaxed">Nord 색상 팔레트 기반의 세련된 UI로 일관된 디자인 시스템을 제공합니다.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Architecture Section -->
    <section id="architecture" class="bg-nord-4 py-32 px-6 overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20 space-y-4">
                <h2 class="text-4xl font-black">아키텍처</h2>
                <p class="text-on-surface-variant max-w-2xl mx-auto">확장 가능하고 유지보수하기 쉬운 구조로, 모든 요청이 검증되고 최대 효율로 처리됩니다.</p>
            </div>
            <div class="relative flex flex-col md:flex-row justify-between items-center gap-4 md:gap-0">
                <div class="z-10 bg-surface-container-lowest p-6 rounded-xl shadow-lg border border-outline-variant/10 w-full md:w-48 text-center group hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-md-primary text-4xl mb-4">person</span>
                    <p class="font-bold uppercase">사용자</p>
                </div>
                <div class="hidden md:block h-px flex-1 bg-gradient-to-r from-md-primary/40 to-md-primary/40 mx-2"></div>
                <div class="z-10 bg-surface-container-lowest p-6 rounded-xl shadow-lg border border-outline-variant/10 w-full md:w-48 text-center group hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-md-primary text-4xl mb-4">lock</span>
                    <p class="font-bold uppercase">보안 검사</p>
                </div>
                <div class="hidden md:block h-px flex-1 bg-gradient-to-r from-md-primary/40 to-md-primary/40 mx-2"></div>
                <div class="z-10 bg-md-primary p-6 rounded-xl shadow-xl w-full md:w-48 text-center transform scale-110">
                    <span class="material-symbols-outlined text-white text-4xl mb-4">settings</span>
                    <p class="font-bold uppercase text-white">요청 처리</p>
                </div>
                <div class="hidden md:block h-px flex-1 bg-gradient-to-r from-md-primary/40 to-md-primary/40 mx-2"></div>
                <div class="z-10 bg-surface-container-lowest p-6 rounded-xl shadow-lg border border-outline-variant/10 w-full md:w-48 text-center group hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-md-primary text-4xl mb-4">database</span>
                    <p class="font-bold uppercase">데이터</p>
                </div>
                <div class="hidden md:block h-px flex-1 bg-gradient-to-r from-md-primary/40 to-md-primary/40 mx-2"></div>
                <div class="z-10 bg-surface-container-lowest p-6 rounded-xl shadow-lg border border-outline-variant/10 w-full md:w-48 text-center group hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-md-primary text-4xl mb-4">send</span>
                    <p class="font-bold uppercase">응답</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Developer Experience -->
    <section class="py-32 px-6">
        <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-12">
            <div class="bg-surface-container-highest p-12 rounded-xl relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-8 opacity-10 transform translate-x-4 -translate-y-4 group-hover:translate-x-0 group-hover:translate-y-0 transition-transform">
                    <span class="material-symbols-outlined text-9xl">code</span>
                </div>
                <span class="text-md-primary font-bold text-sm block">RESTful 설계</span>
                <h3 class="text-3xl font-black mb-6">API 구조</h3>
                <p class="text-on-surface-variant mb-8 leading-relaxed">RESTful 설계 원칙에 따른 자동 라우트 감지, 리소스 컨트롤러, 응답 트레이트 관리로 빠른 모바일/웹 백엔드를 구축합니다.</p>
                <ul class="space-y-3">
                    <li class="flex items-center gap-3 text-sm font-semibold"><span class="material-symbols-outlined text-md-primary text-sm">check_circle</span> OpenAPI 3.0 문서화</li>
                    <li class="flex items-center gap-3 text-sm font-semibold"><span class="material-symbols-outlined text-md-primary text-sm">check_circle</span> Bearer Token API 인증</li>
                    <li class="flex items-center gap-3 text-sm font-semibold"><span class="material-symbols-outlined text-md-primary text-sm">check_circle</span> JSON 응답 표준화</li>
                </ul>
            </div>
            <div class="bg-surface-container-highest p-12 rounded-xl text-white relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-8 opacity-10 transform translate-x-4 -translate-y-4 group-hover:translate-x-0 group-hover:translate-y-0 transition-transform">
                    <span class="material-symbols-outlined text-9xl">cases</span>
                </div>
                <span class="text-md-primary font-bold text-sm block">Shield RBAC</span>
                <h3 class="text-3xl font-black mb-6">인증 & 권한</h3>
                <p class="text-slate-400 mb-8 leading-relaxed">CodeIgniter Shield 통합으로 강력한 CI/CD 파이프라인과 프로덕션 멀티테넌시 환경을 미러링하는 완벽한 테스트 하니스를 제공합니다.</p>
                <ul class="space-y-3">
                    <li class="flex items-center gap-3 text-sm font-semibold"><span class="material-symbols-outlined text-primary-container text-sm">check_circle</span> PHPUnit 10.5 통합 테스트</li>
                    <li class="flex items-center gap-3 text-sm font-semibold"><span class="material-symbols-outlined text-primary-container text-sm">check_circle</span> 그룹 및 권한 기반 접근 제어</li>
                    <li class="flex items-center gap-3 text-sm font-semibold"><span class="material-symbols-outlined text-primary-container text-sm">check_circle</span> 코드 커버리지 리포트</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="py-32 px-6 bg-nord-5">
        <div class="max-w-5xl mx-auto hero-gradient rounded-3xl p-12 md:p-20 text-center text-white relative overflow-hidden shadow-2xl">
            <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
                <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100">
                    <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white"></path>
                </svg>
            </div>
            <div class="relative z-10 space-y-8">
                <h2 class="text-4xl md:text-6xl font-black">지금 바로<br/>시작하세요</h2>
                <p class="text-white/80 text-lg max-w-2xl mx-auto">
                    강력한 멀티테넌시 CMS 플랫폼으로 여러 사이트를 효율적으로 관리하세요.<br>
                    무료로 시작할 수 있습니다.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center pt-4">
                    <a href="/register" class="bg-white text-md-primary px-10 py-5 rounded-xl font-black text-lg hover:bg-slate-50 transition-all shadow-xl">시작하기</a>
                    <a href="/docs/api" class="bg-md-primary/20 backdrop-blur-md border border-white/20 px-10 py-5 rounded-xl font-black text-lg hover:bg-white/10 transition-all">API 문서 보기</a>
                </div>
                <p class="text-white/60 text-sm">
                    이미 계정이 있으신가요?
                    <a href="/login" class="text-white font-semibold underline underline-offset-2 hover:text-white/90 transition-colors">로그인</a>
                </p>
            </div>
        </div>
    </section>

</main>

<!-- Footer -->
<footer class="w-full border-t border-slate-200/20 bg-nord-4">
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8 max-w-7xl mx-auto py-16 px-6">
        <div class="col-span-2 space-y-3">
            <div class="flex items-center gap-2">
                <img src="<?= base_url('assets/images/logo.svg') ?>" alt="CI4 CMS Logo" class="w-6 h-6">
                <span class="text-2xl font-black text-slate-900">CI4 CMS</span>
            </div>
            <p class="text-slate-500 text-xs leading-relaxed max-w-xs">
                CodeIgniter 4 기반의 멀티테넌시 CMS 플랫폼.<br>
                확장 가능한 구조로 현대적인 웹 애플리케이션을 구축하세요.
            </p>
            <div class="flex gap-4">
                <a href="https://github.com/nambak/ci4-cms" target="_blank" rel="noopener noreferrer" aria-label="GitHub 저장소" class="text-slate-400 hover:text-md-primary transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                    </svg>
                </a>
                <a href="https://www.linkedin.com/in/nambak80/" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn 프로필" class="text-slate-400 hover:text-md-primary transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                    </svg>
                </a>
                <a href="mailto:nambak80@gmail.com" aria-label="이메일" class="text-slate-400 hover:text-md-primary transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </a>
            </div>
        </div>
        <div class="flex flex-col gap-4">
            <span class="tracking-widest font-bold text-cyan-700">리소스</span>
            <div class="flex flex-col gap-2">
                <a class="text-sm text-slate-500 hover:text-cyan-600 transition-colors" href="/docs/api">API 문서</a>
                <span class="text-sm text-slate-400 cursor-default">변경 내역</span>
            </div>
        </div>
        <div class="flex flex-col gap-4">
            <span class="tracking-widest font-bold text-cyan-700">플랫폼</span>
            <div class="flex flex-col gap-2">
                <a class="text-sm text-slate-500 hover:text-cyan-600 transition-colors" href="#architecture">아키텍처</a>
                <a class="text-sm text-slate-500 hover:text-cyan-600 transition-colors" href="#features">기능</a>
            </div>
        </div>
        <div class="flex flex-col gap-4">
            <span class="tracking-widest font-bold text-cyan-700">법적 고지</span>
            <div class="flex flex-col gap-2">
                <span class="text-sm text-slate-400 cursor-default">개인정보처리방침</span>
                <span class="text-sm text-slate-400 cursor-default">이용약관</span>
            </div>
        </div>
        <div class="flex flex-col gap-4">
            <span class="tracking-widest font-bold text-cyan-700">계정</span>
            <div class="flex flex-col gap-2">
                <a class="text-sm text-slate-500 hover:text-cyan-600 transition-colors" href="/login">로그인</a>
                <a class="text-sm text-slate-500 hover:text-cyan-600 transition-colors" href="/register">회원가입</a>
            </div>
        </div>
    </div>
    <div class="max-w-7xl mx-auto px-6 py-8 border-t border-slate-200/10">
        <p class="text-slate-500 text-xs uppercase tracking-widest font-bold text-center md:text-left">
            Copyright &copy; <?= date('Y') ?> CI4 CMS - All rights reserved
        </p>
    </div>
</footer>

<!-- Mobile Menu Script -->
<script>
(function() {
    const toggle = document.getElementById('mobile-menu-toggle');
    const menu = document.getElementById('mobile-menu');
    const iconOpen = document.getElementById('menu-icon-open');
    const iconClose = document.getElementById('menu-icon-close');
    if (!toggle || !menu) return;

    function openMenu() {
        menu.classList.remove('hidden');
        menu.setAttribute('aria-hidden', 'false');
        iconOpen.classList.add('hidden');
        iconClose.classList.remove('hidden');
        toggle.setAttribute('aria-expanded', 'true');
        toggle.setAttribute('aria-label', '메뉴 닫기');
        document.body.style.overflow = 'hidden';
    }

    function closeMenu() {
        menu.classList.add('hidden');
        menu.setAttribute('aria-hidden', 'true');
        iconOpen.classList.remove('hidden');
        iconClose.classList.add('hidden');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', '메뉴 열기');
        document.body.style.overflow = '';
    }

    function isOpen() {
        return !menu.classList.contains('hidden');
    }

    toggle.addEventListener('click', function() {
        isOpen() ? closeMenu() : openMenu();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isOpen()) {
            closeMenu();
            toggle.focus();
        }
    });

    menu.querySelectorAll('a').forEach(function(link) {
        link.addEventListener('click', closeMenu);
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768 && isOpen()) {
            closeMenu();
        }
    });
})();
</script>

</body>
</html>

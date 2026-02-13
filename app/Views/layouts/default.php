<!DOCTYPE html>
<html lang="ko" data-theme="nord">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> - CI4 CMS</title>
    <link rel="shortcut icon" href="<?= base_url('logo.svg') ?>" />
    <link rel="stylesheet" as="style" crossorigin href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.min.css" />
    <link href="<?= base_url('/assets/css/output.css') ?>" rel="stylesheet">
    <?= $this->renderSection('head') ?>
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
                    <li><a href="/#features" class="text-nord-6 hover:text-nord-8">기능</a></li>
                    <li><a href="/#architecture" class="text-nord-6 hover:text-nord-8">아키텍처</a></li>
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
            <li><a href="/#features" class="mobile-menu-link">기능</a></li>
            <li><a href="/#architecture" class="mobile-menu-link">아키텍처</a></li>
            <li><a href="/docs/api" class="mobile-menu-link">API 문서</a></li>
            <li class="mt-4 px-2">
                <a href="/login" class="btn btn-primary btn-block">로그인</a>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <?= $this->renderSection('content') ?>

    <!-- Footer -->
    <footer class="footer footer-center p-10 bg-nord-1 text-nord-4">
        <div>
            <div class="font-bold leading-tight text-nord-6">
                <div class="flex items-center justify-center">
                    <img src="<?= base_url('assets/images/logo.svg') ?>" alt="logo" class="w-10 h-10 mx-auto inline-block mr-2">
                    <span class="text-lg">CI4 CMS</span>
                </div>
            </div>
            <p class="text-sm leading-normal">Copyright &copy; <?= date('Y') ?> - All rights reserved</p>
        </div>
    </footer>

    <!-- Navbar Scroll Effect & Mobile Menu Script -->
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

    <?= $this->renderSection('scripts') ?>
</body>
</html>

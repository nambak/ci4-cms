
<!DOCTYPE html>
<html lang="ko" data-theme="nord">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> - <?= esc($tenant->name ?? 'CI4 CMS') ?></title>
    <link rel="shortcut icon" href="<?= base_url('logo.svg') ?>" />
    <link href="<?= base_url('/assets/css/output.css') ?>" rel="stylesheet">
    <?= $this->renderSection('head') ?>
    <style>
        /* nord DaisyUI 테마의 베이스 토큰 (base-100/200/300, base-content) */
        :root {
            --base-100: #eceff4;   /* 페이지 베이스 */
            --base-200: #e5e9f0;   /* 살짝 들어간 면 */
            --base-300: #d8dee9;   /* 경계/구분선 */
            --base-content: #2e3440;
            --primary: #5e81ac;    /* nord primary */
        }
        html, body { height: 100%; }
        body {
            background-color: var(--base-200);
            color: var(--base-content);
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }
        /* 사이드바 네비게이션 항목 — DaisyUI .menu 항목에 대응 */
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 0.875rem;
            border-radius: 0.5rem;
            font-size: 0.9375rem;
            font-weight: 500;
            color: #434c5e;                 /* nord-2 */
            transition: background-color .15s ease, color .15s ease;
        }
        .nav-link:hover {
            background-color: #e5e9f0;      /* nord-5 */
            color: #2e3440;                 /* nord-0 */
        }
        .nav-link .nav-icon { color: #4c566a; transition: color .15s ease; }
        .nav-link:hover .nav-icon { color: #5e81ac; }
        /* 활성 상태 — 채워진 primary pill */
        .nav-link.is-active {
            background-color: #5e81ac;      /* nord-10 */
            color: #eceff4;                 /* nord-6 */
            box-shadow: 0 1px 2px rgba(46,52,64,.18);
        }
        .nav-link.is-active .nav-icon { color: #eceff4; }
        .nav-link.is-active:hover { background-color: #5e81ac; color: #eceff4; }

        .nav-section-label {
            padding: 0 0.875rem;
            font-size: 0.6875rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #9aa3b2;                 /* nord-3 muted */
        }

        /* 사용자 드롭다운 */
        .user-menu[data-open="false"] .user-dropdown { display: none; }
        .user-menu[data-open="true"] .user-chevron { transform: rotate(180deg); }
        .user-chevron { transition: transform .18s ease; }

        /* 콘텐츠 영역 스크롤 */
        .content-scroll { scrollbar-width: thin; scrollbar-color: #c3ccda transparent; }
        .content-scroll::-webkit-scrollbar { width: 10px; }
        .content-scroll::-webkit-scrollbar-thumb { background: #c3ccda; border-radius: 8px; border: 3px solid transparent; background-clip: padding-box; }
    </style>
</head>

<body>
<div class="flex h-screen overflow-hidden">

    <!-- ============================================================
         SIDEBAR (좌측)
         ============================================================ -->
    <aside class="hidden md:flex w-64 shrink-0 flex-col bg-nord-6 border-r border-nord-4" data-screen-label="사이드바">

        <!-- 브랜드 -->
        <div class="h-16 flex items-center gap-2.5 px-5 border-b border-nord-4 shrink-0">
      <span class="grid place-items-center w-9 h-9 rounded-lg bg-nord-10 text-nord-6 shrink-0">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M4 12h16M4 17h10"/></svg>
      </span>
            <div class="leading-tight">
                <div class="text-[15px] font-bold text-nord-0">CI4 CMS</div>
                <div class="text-[11px] font-medium text-nord-3">관리자</div>
            </div>
        </div>

        <!-- 네비게이션 -->
        <nav class="flex-1 overflow-y-auto content-scroll px-3 py-5 space-y-6">

            <!-- 그룹: 일반 -->
            <div class="space-y-1">
                <a href="<?= site_url("{$tenant->subdomain}/admin/") ?>" class="nav-link <?= ($activeMenu ?? '') === 'dashboard' ? 'is-active' : '' ?>" aria-current="page">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>
                    <span>대시보드</span>
                </a>
            </div>

            <!-- 그룹: 콘텐츠 -->
            <div class="space-y-1">
                <div class="nav-section-label mb-2">콘텐츠</div>
                <a href="<?= site_url("{$tenant->subdomain}/admin/posts") ?>" class="nav-link <?= ($activeMenu ?? '') === 'posts' ? 'is-active' : '' ?>">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z"/><path d="M9 9h1M9 13h6M9 17h6"/></svg>
                    <span>포스트</span>
                </a>
                <a href="<?= site_url("{$tenant->subdomain}/admin/categories") ?>" class="nav-link <?= ($activeMenu ?? '') === 'categories' ? 'is-active' : '' ?>">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h16a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1h-7.6a1 1 0 0 1-.8-.4L9.8 4.4a1 1 0 0 0-.8-.4H4a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1z"/></svg>
                    <span>카테고리</span>
                </a>
                <a href="<?= site_url("{$tenant->subdomain}/admin/tags") ?>" class="nav-link <?= ($activeMenu ?? '') === 'tags' ? 'is-active' : '' ?>">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.6 13.4 13.4 20.6a2 2 0 0 1-2.8 0l-7-7A2 2 0 0 1 3 12.2V5a2 2 0 0 1 2-2h7.2a2 2 0 0 1 1.4.6l7 7a2 2 0 0 1 0 2.8z"/><circle cx="7.5" cy="7.5" r="1.5"/></svg>
                    <span>태그</span>
                </a>
                <a href="<?= site_url("{$tenant->subdomain}/admin/comments") ?>" class="nav-link <?= ($activeMenu ?? '') === 'comments' ? 'is-active' : '' ?>">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-8.5 8.5 8.5 8.5 0 0 1-3.6-.8L3 21l1.8-5.4a8.5 8.5 0 0 1-.8-3.6A8.38 8.38 0 0 1 12.5 3a8.38 8.38 0 0 1 8.5 8.5z"/></svg>
                    <span>댓글</span>
                </a>
                <a href="<?= site_url("{$tenant->subdomain}/admin/media") ?>" class="nav-link <?= ($activeMenu ?? '') === 'media' ? 'is-active' : '' ?>">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                    <span>미디어</span>
                </a>
            </div>

            <!-- 그룹: 관리 -->
            <div class="space-y-1">
                <div class="nav-section-label mb-2">관리</div>
                <a href="<?= site_url("{$tenant->subdomain}/admin/users") ?>" class="nav-link <?= ($activeMenu ?? '') === 'users' ? 'is-active' : '' ?>">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <span>사용자</span>
                </a>
            </div>
        </nav>
    </aside>

    <!-- ============================================================
         메인 영역 (Topbar + Content)
         ============================================================ -->
    <div class="flex-1 flex flex-col min-w-0">

        <!-- TOPBAR (상단) -->
        <header class="h-16 shrink-0 flex items-center gap-4 px-5 sm:px-6 bg-nord-6/90 backdrop-blur-sm border-b border-nord-4" data-screen-label="상단바">

            <!-- 모바일 메뉴 버튼 -->
            <button type="button" class="md:hidden grid place-items-center w-10 h-10 -ml-1.5 rounded-lg text-nord-2 hover:bg-nord-5" aria-label="메뉴">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <!-- 브레드크럼 (현재 위치) -->
            <nav class="flex items-center gap-2 min-w-0" aria-label="브레드크럼">
                <a href="<?= site_url("{$tenant->subdomain}/admin") ?>" class="flex items-center gap-1.5 text-sm text-nord-3 hover:text-nord-10 shrink-0">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/></svg>
                    <span>관리자</span>
                </a>
                <svg class="text-nord-4 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                <span class="text-sm font-semibold text-nord-0 truncate">
                    <?= esc($pageTitle ?? '') ?>
                </span>
            </nav>

            <!-- 우측: 사용자 드롭다운 -->
            <div class="ml-auto flex items-center gap-1.5 sm:gap-2">
                <div class="user-menu relative" data-open="false">
                    <button type="button" class="user-toggle flex items-center gap-2.5 pl-1.5 pr-2 py-1.5 rounded-lg hover:bg-nord-5 transition-colors" aria-haspopup="true" aria-expanded="false">
                        <span class="grid place-items-center w-8 h-8 rounded-full bg-nord-10 text-nord-6 text-[13px] font-bold shrink-0">
                            <?= esc(mb_substr(auth()->user()->username, 0, 1)) ?>
                        </span>
                        <span class="hidden sm:flex flex-col items-start leading-tight">
                            <span class="text-[13px] font-semibold text-nord-0">
                                <?= esc(auth()->user()->username) ?>
                            </span>
                            <span class="text-[11px] text-nord-3">
                                <?= esc(auth()->user()->email) ?>
                            </span>
                        </span>
                        <svg class="user-chevron text-nord-3 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                    </button>

                    <!-- 드롭다운 패널 -->
                    <div class="user-dropdown absolute right-0 mt-2 w-56 origin-top-right rounded-xl bg-nord-6 border border-nord-4 shadow-lg shadow-nord-0/10 py-1.5 z-50">
                        <div class="px-4 py-3 border-b border-nord-4">
                            <div class="text-sm font-semibold text-nord-0">
                                <?= esc(auth()->user()->username) ?>
                            </div>
                            <div class="text-xs text-nord-3 mt-0.5">
                                <?= esc(auth()->user()->email) ?>
                            </div>
                        </div>
                        <div class="py-1">
                            <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-nord-2 hover:bg-nord-5">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                <span>프로필</span>
                            </a>
                            <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-nord-2 hover:bg-nord-5">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.2 2h-.4a2 2 0 0 0-2 2v.2a2 2 0 0 1-1 1.7l-.4.2a2 2 0 0 1-2 0l-.2-.1a2 2 0 0 0-2.7.7l-.3.5a2 2 0 0 0 .7 2.7l.2.1a2 2 0 0 1 1 1.7v.5a2 2 0 0 1-1 1.7l-.2.1a2 2 0 0 0-.7 2.7l.3.5a2 2 0 0 0 2.7.7l.2-.1a2 2 0 0 1 2 0l.4.2a2 2 0 0 1 1 1.7v.2a2 2 0 0 0 2 2h.4a2 2 0 0 0 2-2v-.2a2 2 0 0 1 1-1.7l.4-.2a2 2 0 0 1 2 0l.2.1a2 2 0 0 0 2.7-.7l.3-.5a2 2 0 0 0-.7-2.7l-.2-.1a2 2 0 0 1-1-1.7v-.5a2 2 0 0 1 1-1.7l.2-.1a2 2 0 0 0 .7-2.7l-.3-.5a2 2 0 0 0-2.7-.7l-.2.1a2 2 0 0 1-2 0l-.4-.2a2 2 0 0 1-1-1.7V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                                <span>설정</span>
                            </a>
                        </div>
                        <div class="py-1 border-t border-nord-4">
                            <a href="<?= url_to('logout') ?>" class="flex items-center gap-3 px-4 py-2 text-sm text-nord-11 hover:bg-nord-11/10">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5M21 12H9"/></svg>
                                <span>로그아웃</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- CONTENT (중앙) -->
        <main class="flex-1 overflow-y-auto content-scroll" data-screen-label="콘텐츠">
            <div class="max-w-6xl mx-auto px-5 sm:px-8 py-8">
                <?= $this->renderSection('content') ?>
            </div>
        </main>
    </div>
</div>

<script>
    // 사용자 드롭다운 토글
    (function () {
        const menu = document.querySelector('.user-menu');
        if (!menu) return;
        const toggle = menu.querySelector('.user-toggle');

        function setOpen(open) {
            menu.dataset.open = String(open);
            toggle.setAttribute('aria-expanded', String(open));
        }
        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            setOpen(menu.dataset.open !== 'true');
        });
        document.addEventListener('click', function (e) {
            if (!menu.contains(e.target)) setOpen(false);
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') setOpen(false);
        });
    })();
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>

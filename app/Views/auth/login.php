<?= $this->extend('layouts/auth') ?>

<?= $this->section('title') ?>로그인<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!--
    Frost Layer - Login
    Minimalist Pure 기반에서 Frost 계열 색상과 질감을 겹친 단계.

    변경 축소지도:
    1. 카드: bg-nord-1/60 (단일 반투명)
           -> bg-frost-card (수직 그라데이션) + frost-card-highlight (상단 하이라이트 선)
              frost-card-enter (진입 시 안개 페이드)
    2. 입력 필드: bg-nord-2 (단색)
                -> bg-frost-input (저부 frost 탄트)
                   focus: frost-input-glow (box-shadow 기반 glow) + frost-input-pulse (1회 펄스)
                   placeholder: placeholder-frost (nord-7 기반 40%)
    3. 버튼: btn-primary hover shadow -> frost-btn-glow로 frost 색조의 깊은 glow
    4. 로고 타이틀: text-nord-8 -> text-gradient-nord (기존 유틸리티 재활용)
                    frost 분위기와 타이틀의 시각적 연속성을 강화
    5. 체크박스: border-nord-3 -> border-nord-9/40, focus ring -> frost 색상으로 통일
-->

<div class="min-h-screen flex items-center justify-center px-4 py-16 sm:px-6 lg:px-8 relative z-10">
    <div class="w-full max-w-sm">

        <!-- Logo & Title -->
        <div class="text-center mb-10">
            <a href="/" class="inline-flex items-center justify-center gap-3 hover:opacity-80 transition-opacity duration-300">
                <img src="<?= base_url('/assets/images/logo.svg') ?>" alt="CI4 CMS" class="h-12 w-12" />
                <span class="text-5xl font-semibold text-gradient-nord tracking-tight">CI4 CMS</span>
            </a>
            <p class="mt-3 text-nord-4 opacity-60 text-sm tracking-wide">계정에 로그인하세요</p>
        </div>

        <!-- Login Card -->
        <!--
            Frost Card 구조:
            - relative + overflow-hidden: frost-card-highlight의 ::before가
              카드 경계 밖으로 나가지 않도록.
            - bg-frost-card: nord-1 기반의 수직 그라데이션 (하단에 nord-9 미세 탄트)
            - frost-card-highlight: 상단 1px 하이라이트 선 (빛이 위에서 떨어지는 질감)
            - backdrop-blur-md: 배경 안개와의 분리감 유지
            - shadow-2xl: 기존과 동일한 깊이 정의
            - frost-card-enter: 진입 시 위에서 안개처럼 내려오는 페이드
            - hover: bg-frost-card-hover로 카드 배경이 미세하게 밝아짐
        -->
        <div class="relative overflow-hidden rounded-2xl bg-frost-card backdrop-blur-md shadow-2xl
                    frost-card-highlight frost-card-enter
                    transition-all duration-500 hover:bg-frost-card-hover">
            <div class="p-8 sm:p-10">

                <!-- Error Messages -->
                <!--
                    에러 영역: nord-11 기반 미세 배경 유지.
                    Frost Layer에서는 에러의 색상 강도를 높이지 않음.
                    안기 있는 분위기 속에서 강한 색상은 오히려 불편함을 줌.
                -->
                <?php if (session('error') !== null) : ?>
                    <div class="mb-5 px-4 py-3 rounded-xl bg-nord-11/10 text-nord-11 text-sm leading-relaxed">
                        <?= esc(session('error')) ?>
                    </div>
                <?php elseif (session('errors') !== null) : ?>
                    <div class="mb-5 px-4 py-3 rounded-xl bg-nord-11/10 text-nord-11 text-sm leading-relaxed">
                        <?php if (is_array(session('errors'))) : ?>
                            <?php foreach (session('errors') as $error) : ?>
                                <p><?= esc($error) ?></p>
                            <?php endforeach ?>
                        <?php else : ?>
                            <?= esc(session('errors')) ?>
                        <?php endif ?>
                    </div>
                <?php endif ?>

                <!-- Success Message -->
                <?php if (session('message') !== null) : ?>
                    <div class="mb-5 px-4 py-3 rounded-xl bg-nord-14/10 border-l-2 border-nord-14/40 text-nord-14 text-sm leading-relaxed flex gap-2 items-start">
                        <svg class="w-4 h-4 mt-0.5 shrink-0 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <div><?= esc(session('message')) ?></div>
                    </div>
                <?php endif ?>

                <!-- Login Form -->
                <form action="<?= url_to('login') ?>" method="post">
                    <?= csrf_field() ?>

                    <!-- Email -->
                    <!--
                        입력 필드 Frost Layer:
                        - bg-frost-input: 단색 nord-2 대신 저부에 frost 탄트 있는 그라데이션
                        - placeholder-frost: nord-7(청록) 기반 40% 불투명 -> 차가운 힌트
                        - frost-input-glow: focus 시 ring + 외부 glow + 내부 하이라이트
                        - frost-input-pulse: focus 진입 순간 glow가 은은하게 펄스 (1회)
                        - focus:bg-frost-input-focus: 배경도 frost 색조로 밝아짐
                        - transition-all: ring과 background 변화가 동시에 부드럽게
                    -->
                    <div class="mb-5">
                        <label for="email" class="block text-nord-4 text-sm font-medium mb-2 tracking-tight">
                            이메일
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="w-full px-4 py-3 rounded-xl bg-frost-input text-nord-1 placeholder-nord-3 text-sm
                                   focus:bg-frost-input-focus focus:outline-none
                                   frost-input-glow frost-input-pulse
                                   transition-all duration-300"
                            placeholder="name@example.com"
                            inputmode="email"
                            autocomplete="email"
                            value="<?= esc(old('email')) ?>"
                            required
                        >
                    </div>

                    <!-- Password -->
                    <div class="mb-7">
                        <label for="password" class="block text-nord-4 text-sm font-medium mb-2 tracking-tight">
                            비밀번호
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="w-full px-4 py-3 pr-12 rounded-xl bg-frost-input text-nord-1 placeholder-nord-3 text-sm
                                       focus:bg-frost-input-focus focus:outline-none
                                       frost-input-glow frost-input-pulse
                                       transition-all duration-300"
                                placeholder="비밀번호를 입력하세요"
                                inputmode="text"
                                autocomplete="current-password"
                                required
                            >
                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-nord-4 opacity-50 hover:opacity-80 transition-opacity" onclick="togglePassword('password', this)">
                                <svg class="w-5 h-5 eye-off" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                                <svg class="w-5 h-5 eye-on hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <!--
                        체크박스: border와 focus ring 모두 frost 색상으로 통일.
                        기존 border-nord-3은 카드 배경과 대비가 약하므로,
                        nord-9 기반의 미세한 테두리로 바꿔 frost 분위기와 연속됨.
                    -->
                    <div class="flex items-center justify-between mb-6">
                        <?php if (setting('Auth.sessionConfig')['allowRemembering']): ?>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    class="w-4 h-4 rounded border-nord-9/40 bg-nord-2 text-nord-8
                                           focus:ring-2 focus:ring-nord-8/60 focus:ring-offset-1 focus:ring-offset-nord-1
                                           transition-colors duration-200"
                                    <?php if (old('remember')): ?> checked<?php endif ?>
                                >
                                <span class="text-nord-4 opacity-60 text-xs group-hover:opacity-80 transition-opacity">로그인 상태 유지 <span class="opacity-60">(30일)</span></span>
                            </label>
                        <?php else: ?>
                            <div></div>
                        <?php endif; ?>

                        <?php if (setting('Auth.allowMagicLinkLogins')) : ?>
                            <a href="<?= url_to('magic-link') ?>" class="text-nord-8 opacity-70 text-xs hover:opacity-100 transition-opacity duration-200">
                                비밀번호를 잊으셨나요?
                            </a>
                        <?php endif ?>
                    </div>

                    <!-- Submit Button -->
                    <!--
                        Frost Button:
                        - btn btn-primary: DaisyUI nord 테마의 기본 배경(nord-8) + shimmer 효과 유지
                        - border-0: 테두리 제거 (기존과 동일)
                        - frost-btn-glow: hover 시 frost 색조의 깊은 glow (shadow-lg 대체)
                          active 시 glow 축소로 눌림 피드백
                        - hover:scale-[1.02] active:scale-95: 기존 마이크로 인터랙션 유지
                        - shadow-lg: 비활성 상태의 기본 깊이 유지
                    -->
                    <button type="submit" class="btn btn-primary w-full border-0 text-sm font-semibold tracking-tight shadow-lg
                                                 frost-btn-glow
                                                 transition-all duration-300 hover:scale-[1.02] active:scale-95">
                        로그인
                    </button>
                </form>

                <!-- Register Link -->
                <p class="text-center text-nord-4 opacity-50 text-xs mt-8">
                    계정이 없으신가요?
                    <?php if (setting('Auth.allowRegistration')) : ?>
                        <a href="<?= url_to('register') ?>" class="text-nord-8 opacity-100 font-medium hover:opacity-80 transition-opacity duration-200">
                            회원가입
                        </a>
                    <?php endif ?>
                </p>

            </div>
        </div>

        <!-- Back to Home -->
        <div class="text-center mt-7">
            <a href="/" class="inline-flex items-center gap-1.5 text-nord-4 opacity-40 hover:opacity-70 transition-opacity duration-300 text-xs group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition-transform duration-300 group-hover:-translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span class="tracking-wide">홈으로 돌아가기</span>
            </a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// 비밀번호 가시성 토글
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const eyeOff = btn.querySelector('.eye-off');
    const eyeOn = btn.querySelector('.eye-on');

    if (input.type === 'password') {
        input.type = 'text';
        eyeOff.classList.add('hidden');
        eyeOn.classList.remove('hidden');
    } else {
        input.type = 'password';
        eyeOff.classList.remove('hidden');
        eyeOn.classList.add('hidden');
    }
}

// 폼 제출 시 버튼 로딩 상태
document.querySelector('form').addEventListener('submit', function(e) {
    const btn = this.querySelector('button[type="submit"]');
    btn.classList.add('loading', 'pointer-events-none');
    btn.disabled = true;
});
</script>
<?= $this->endSection() ?>

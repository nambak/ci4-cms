<?= $this->extend('layouts/auth') ?>

<?= $this->section('title') ?>회원가입<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!--
    Frost Layer - Register
    login.php와 동일한 Frost Layer 디자인 언어를 적용.

    회원가입 폼의 특성상 필드가 4개로 많으므로:
    - 필드 간 간격(mb-4)을 login보다 약간 좁혀 카드 내부의 밀폐감을 줄임 (기존 유지)
    - 마지막 필드(password_confirm) 이후에는 mb-6으로 버튼과의 간격을 열음 (기존 유지)
    - 힌트 텍스트: 기존 nord-3/opacity-50에서 frost 색상(nord-7)으로 미세하게 바꿈.
      정보 계층이 카드 배경 -> 입력 필드 -> 라벨 -> 힌트 순으로
      frost 색상의 강도에 따라 자연스럽게 연결됨.
-->

<div class="min-h-screen flex items-center justify-center px-4 py-16 sm:px-6 lg:px-8 relative z-10">
    <div class="w-full max-w-sm">

        <!-- Logo & Title -->
        <div class="text-center mb-10">
            <a href="/" class="inline-flex items-center justify-center gap-3 hover:opacity-80 transition-opacity duration-300">
                <img src="<?= base_url('/assets/images/logo.svg') ?>" alt="CI4 CMS" class="w-12 h-12" />
                <span class="text-5xl font-semibold text-gradient-nord tracking-tight">CI4 CMS</span>
            </a>
            <p class="mt-3 text-nord-4 opacity-60 text-sm tracking-wide">새 계정을 만드세요</p>
        </div>

        <!-- Register Card -->
        <!--
            Frost Card: login.php와 동일한 구조.
            relative + overflow-hidden, bg-frost-card, frost-card-highlight,
            frost-card-enter, backdrop-blur-md, shadow-2xl.
        -->
        <div class="relative overflow-hidden rounded-2xl bg-frost-card backdrop-blur-md shadow-2xl
                    frost-card-highlight frost-card-enter
                    transition-all duration-500 hover:bg-frost-card-hover">
            <div class="p-8 sm:p-10">

                <!-- Error Messages -->
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

                <!-- Register Form -->
                <form action="<?= url_to('register') ?>" method="post">
                    <?= csrf_field() ?>

                    <!-- Email -->
                    <div class="mb-4">
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
                            value="<?= old('email') ?>"
                            required
                        >
                    </div>

                    <!-- Username -->
                    <div class="mb-4">
                        <label for="username" class="block text-nord-4 text-sm font-medium mb-2 tracking-tight">
                            사용자명
                        </label>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            class="w-full px-4 py-3 rounded-xl bg-frost-input text-nord-1 placeholder-nord-3 text-sm
                                   focus:bg-frost-input-focus focus:outline-none
                                   frost-input-glow frost-input-pulse
                                   transition-all duration-300"
                            placeholder="username"
                            inputmode="text"
                            autocomplete="username"
                            value="<?= old('username') ?>"
                            required
                        >
                        <!--
                            힌트 텍스트: nord-3/opacity-50에서 nord-7(청록) 기반으로 변경.
                            frost 입력 필드의 저부 탄트 색상과 연속되어
                            "이 텍스트는 해당 필드에 속한다"는 시각적 연결이 강화됨.
                            opacity-45로 라벨(nord-4)과의 위계 차이를 유지.
                        -->
                        <p class="mt-1.5 text-nord-7 opacity-45 text-xs tracking-wide">영문, 숫자, 마침표만 사용 가능 (3-30자)</p>
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
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
                                autocomplete="new-password"
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
                        <p class="mt-1.5 text-nord-7 opacity-45 text-xs tracking-wide">최소 8자 이상</p>
                    </div>

                    <!-- Password Confirm -->
                    <div class="mb-6">
                        <label for="password_confirm" class="block text-nord-4 text-sm font-medium mb-2 tracking-tight">
                            비밀번호 확인
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="password_confirm"
                                name="password_confirm"
                                class="w-full px-4 py-3 pr-12 rounded-xl bg-frost-input text-nord-1 placeholder-nord-3 text-sm
                                       focus:bg-frost-input-focus focus:outline-none
                                       frost-input-glow frost-input-pulse
                                       transition-all duration-300"
                                placeholder="비밀번호를 다시 입력하세요"
                                inputmode="text"
                                autocomplete="new-password"
                                required
                            >
                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-nord-4 opacity-50 hover:opacity-80 transition-opacity" onclick="togglePassword('password_confirm', this)">
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

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-full border-0 text-sm font-semibold tracking-tight shadow-lg
                                                 frost-btn-glow
                                                 transition-all duration-300 hover:scale-[1.02] active:scale-95">
                        회원가입
                    </button>
                </form>

                <!-- Login Link -->
                <p class="text-center text-nord-4 opacity-50 text-xs mt-8">
                    이미 계정이 있으신가요?
                    <a href="<?= url_to('login') ?>" class="text-nord-8 opacity-100 font-medium hover:opacity-80 transition-opacity duration-200">
                        로그인
                    </a>
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

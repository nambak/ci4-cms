<!DOCTYPE html>
<html lang="ko" data-theme="nord">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> - CI4 CMS</title>
    <link rel="shortcut icon" href="<?= base_url('logo.svg') ?>" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= base_url('/assets/css/output.css') ?>" rel="stylesheet">
</head>
<body class="min-h-screen bg-nord-0 relative">
    <!--
        Frost Layer - Background Atmosphere
        - nord-8 (#88c0d0) 포인트 2개: 좌상단(25%, 20%), 우하단(75%, 80%)
          강도 0.10으로 올려 안개 질감이 실제로 느껴지도록 함.
        - nord-7 (#8fbcbb) 포인트 1개: 중앙 하단(50%, 90%)
          가장 따뜻한 Frost 색상을 하단에 배치하여 위(차가운 파란색)
          -> 아래(따뜻한 청록색) 방향의 온도 그라데이션을 형성.
        - 세 포인트의 삼각형 배치로, 카드가 중앙에 올 때 주변 빛이
          균형 잡히며 카드의 shadow가 깨끗하게 떨어집니다.
        - spread 범위를 60%로 확장하여 안개가 넓게 퍼진 느낌을 강화.
    -->
    <div class="fixed inset-0 pointer-events-none"
         style="background:
            radial-gradient(circle at 25% 20%, rgba(136,192,208,0.10) 0%, transparent 60%),
            radial-gradient(circle at 75% 80%, rgba(129,161,193,0.10) 0%, transparent 60%),
            radial-gradient(circle at 50% 90%, rgba(143,188,187,0.07) 0%, transparent 55%);
         "></div>

    <?= $this->renderSection('main') ?>

    <?= $this->renderSection('scripts') ?>
</body>
</html>

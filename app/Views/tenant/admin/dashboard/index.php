<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>
대시보드
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-nord-0">대시보드</h1>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="stat shadow bg-nord-6 rounded-md">
            <div class="stat-title text-nord-3">총 사용자 수</div>
            <div class="stat-value text-right" data-testid="stat-users"><?= number_format($userCount) ?></div>
        </div>
        <div class="stat shadow bg-nord-6 rounded-md">
            <div class="stat-title text-nord-3">총 포스트 수</div>
            <div class="stat-value text-right" data-testid="stat-posts"><?= number_format($postCount) ?></div>
        </div>
        <div class="stat shadow bg-nord-6 rounded-md">
            <div class="stat-title text-nord-3">총 댓글 수</div>
            <div class="stat-value text-right" data-testid="stat-comments"><?= number_format($commentCount) ?></div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

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
        <div class="stat shadow bg-nord-6 rounded-md">
            <div class="stat-title text-nord-3">인기 포스트</div>
            <div class="stat-value" data-testid="widget-popular-posts">
                <?php foreach ($popularPosts as $post) : ?>
                    <div class="flex items-center justify-between">
                        <a href="<?= esc(site_url("{$tenant->subdomain}/posts/{$post->slug}"), 'attr') ?>"
                                class="text-nord-0 hover:text-nord-1"
                        >
                            <?= esc($post->title) ?>
                        </a>
                        <div class="text-right text-nord-3"><?= esc($post->comment_count) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="stat shadow bg-nord-6 rounded-md">
            <div class="stat-title text-nord-3">최신 포스트</div>
            <div class="stat-value" data-testid="widget-recent-posts">
                <?php foreach ($recentPosts as $post): ?>
                    <div class="flex items-center justify-between">
                        <a href="<?= esc(site_url("{$tenant->subdomain}/posts/{$post->slug}"), 'attr') ?>"
                                class="text-nord-0 hover:text-nord-1"
                        >
                            <?= esc($post->title) ?>
                        </a>
                        <div class="text-right text-nord-3"><?= esc($post->created_at->humanize()) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="stat shadow bg-nord-6 rounded-md">
            <div class="stat-title text-nord-3">최신 댓글</div>
            <div class="stat-value" data-testid="widget-recent-comments">
                <?php foreach ($recentComments as $comment): ?>
                    <div class="flex items-center justify-between">
                        <a href="<?= esc(site_url("{$tenant->subdomain}/posts/{$comment->post_slug}"), 'attr') ?>"
                                class="text-nord-0 hover:text-nord-1"
                        >
                            <?= esc($comment->post_title) ?>
                        </a>
                        <div class="text-right text-nord-3"><?= esc($comment->created_at->humanize()) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="shadow bg-nord-6 rounded-md p-4 md:col-span-3">
            <h2 class="text-nord-3 mb-2">최근 30일 활동 추이</h2>
            <canvas data-testid="chart-activity-trend"></canvas>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
    const ctx = document.querySelector('[data-testid="chart-activity-trend"]');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($trend['labels']) ?>,
            datasets: [
                { label: '포스트', data: <?= json_encode($trend['posts']) ?> },
                { label: '댓글', data: <?= json_encode($trend['comments']) ?> },
            ]
        }
    });
</script>
<?= $this->endSection() ?>

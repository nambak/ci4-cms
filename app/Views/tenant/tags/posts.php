<?= $this->extend('layouts/default') ?>

<?= $this->section('title') ?>
태그 - <?= esc($tag->name) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-10">
    <h1 class="text-3xl font-bold mb-8 text-nord-8">
    #<?= esc($tag->name) ?> 포스트
    </h1>

    <?php if (empty($posts)): ?>
        <div class="alert alert-info">아직 발행된 포스트가 없습니다.</div>
    <?php else: ?>
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($posts as $post): ?>
                <article class="card bg-base-100 shadow-nord">
                    <div class="card-body">
                        <h2 class="card-title text-nord-7">
                            <?= esc($post->title) ?>
                        </h2>
                        <p class="text-sm text-nord-4">
                            <?= esc(date('Y-m-d', strtotime($post->created_at))) ?>
                        </p>
                        <p class="line-clamp-3">
                            <?= esc(mb_substr(strip_tags($post->content), 0, 120)) ?>
                        </p>
                        <div class="card-actions justify-end">
                        <a href="<?= esc(site_url("{$tenant->subdomain}/posts/{$post->slug}"), 'attr') ?>"
                                class="btn btn-sm btn-primary">읽기</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="mt-10 flex justify-center">
            <?= $pager->links('default', 'default_full') ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>


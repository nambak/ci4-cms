<?= $this->extend('layouts/default') ?>

<?= $this->section('title') ?>
    <?= esc($post->title) ?> - <?= esc($tenant->name) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="container mx-auto px-4 py-10">
        <h1 class="text-3xl font-bold mb-2">
            <?= esc($post->title) ?>
        </h1>
        <time class="text-sm text-nord-4 mb-6" datetime="<?= esc(date('Y-m-d', strtotime($post->created_at))) ?>">
            <?= esc(date('Y-m-d', strtotime($post->created_at))) ?>
        </time>

        <article class="prose max-w-3xl">
            <?= nl2br(esc($post->content)) ?>
        </article>

        <a class="btn btn-ghost btn-sm" href="<?= esc(site_url("{$tenant->subdomain}/posts"), 'attr') ?>">
            ← 포스트 목록으로
        </a>
    </div>
<?= $this->endSection() ?>
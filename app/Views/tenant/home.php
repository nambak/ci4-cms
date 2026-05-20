<?= $this->extend('layouts/default') ?>

<?= $this->section('title') ?>

<?= esc($tenant->name) ?>

<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="hero min-h-[60vh] bg-base-200">
    <div class="hero-content text-center">
        <div class="max-w-2xl">
            <h1 class="text-5xl">
                <?= esc($tenant->name) ?>
            </h1>
            <p class="py-6 text-nord-4">
                <?= esc($tenant->subdomain) ?>.ci4-cms.test 에 오신 것을 환영합니다.
            </p>
            <a href="/<?= esc($tenant->subdomain, 'url') ?>/posts" class="btn btn-primary">
                포스트 보기
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>


<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>
    카테고리
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-nord-0">카테고리</h1>
            <p class="text-sm text-nord-3 mt-1">카테고리를 관리합니다.</p>
        </div>
        <a type="button" class="btn btn-primary" href="<?= site_url("{$subdomain}/admin/categories/new") ?>">
            새 카테고리
        </a>
    </div>

    <?php if (session('errors')): ?>
        <?php foreach (session('errors') as $error): ?>
            <div class="alert alert-error">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="inline stroke-current shrink-0 h-6 w-6"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span><?= esc($error) ?></span>
                </div>
            </div>
        <?php endforeach ?>
    <?php endif ?>

    <div class="card bg-nord-6 w-full shadow-sm">
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table [&_tr]:border-nord-4">
                    <!-- head -->
                    <thead class="text-nord-3">
                    <tr>
                        <th></th>
                        <th>이름</th>
                        <th>슬러그</th>
                        <th>설명</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($categories)) : ?>
                        <tr><td colspan="5"><div class="text-nord-3 text-center">등록된 카테고리가 없습니다</div></td></tr>
                    <?php else: ?>
                        <?php foreach ($categories as $category) : ?>
                            <tr>
                                <td class="text-nord-0"><?= $category->id ?></td>
                                <td class="text-nord-0"><?= esc($category->name) ?></td>
                                <td class="text-nord-3"><?= esc($category->slug) ?></td>
                                <td class="text-nord-3"><?= esc($category->description) ?></td>
                                <td>
                                    <a href="<?= site_url("{$subdomain}/admin/categories/{$category->id}/edit") ?>" class="btn btn-sm">수정</a>
                                    <form method="post" action="<?= site_url("{$subdomain}/admin/categories/{$category->id}") ?>">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <?= csrf_field() ?>
                                        <button onclick="return confirm('삭제할까요?')" class="btn btn-error btn-sm">삭제</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
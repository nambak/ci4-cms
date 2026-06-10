<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>
    포스트
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-nord-0">포스트</h1>
            <p class="text-sm text-nord-3 mt-1">작성된 포스트를 관리합니다.</p>
        </div>
        <a type="button" class="btn btn-primary" href="<?= site_url("{$subdomain}/admin/posts/new") ?>">
            새 포스트
        </a>
    </div>

    <div class="card bg-nord-6 w-full shadow-sm">
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table [&_tr]:border-nord-4">
                    <!-- head -->
                    <thead class="text-nord-3">
                    <tr>
                        <th></th>
                        <th>제목</th>
                        <th>작성일</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($posts)) : ?>
                        <tr><td colspan="3"><div class="text-nord-3 text-center">작성된 글이 없습니다</div></td></tr>
                    <?php else: ?>
                        <?php foreach ($posts as $post) : ?>
                            <tr>
                                <td class="text-nord-0"><?= $post->id ?></td>
                                <td class="text-nord-0"><?= esc($post->title) ?></td>
                                <td  class="text-nord-0"><?= $post->created_at->format('Y-m-d') ?></td>
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
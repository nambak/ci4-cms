<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>
    포스트 수정
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-nord-0">포스트 수정</h1>
        </div>
    </div>
    <div class="card bg-nord-6 w-full shadow-sm">
        <div class="card-body">
            <?php if (session('errors')): ?>
                <?php foreach (session('errors') as $error): ?>
                    <div class="alert alert-error">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="inline stroke-current shrink-0 h-6 w-6"
                                fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span><?= $error ?></span>
                        </div>
                    </div>
                <?php endforeach ?>
            <?php endif ?>
            <form
                method="post"
                class="form-control"
                action="<?= site_url("{$subdomain}/admin/posts/{$post->id}") ?>"
            >
                <input type="hidden" name="_method" value="PUT">
                <?= csrf_field() ?>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text text-nord-0">제목</span>
                    </label>
                    <input
                        type="text"
                        placeholder="제목을 입력하세요"
                        class="input input-bordered bg-nord-4"
                        name="title"
                        value="<?= old('title', $post->title) ?>"
                    >
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text text-nord-0">카테고리</span>
                    </label>
                    <select name="category_id" class="select select-bordered bg-nord-4">
                        <?php foreach ($categories as $category) : ?>
                            <option
                                value="<?= $category->id ?>"
                                <?= old('category_id', $post->category_id) == $category->id ? 'selected' : '' ?>
                            >
                                <?= esc($category->name) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text text-nord-0">내용</span>
                    </label>
                    <textarea
                        class="textarea textarea-bordered h-24 bg-nord-4"
                        placeholder="내용을 입력하세요"
                        name="content"
                    ><?= old('content', $post->content) ?></textarea>
                </div>
                <div class="form-control mt-4">
                    <button class="btn btn-primary">저장</button>
                </div>
            </form>
        </div>
    </div>
<?= $this->endSection() ?>

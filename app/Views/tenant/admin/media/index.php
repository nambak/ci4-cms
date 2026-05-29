<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>
    미디어
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-nord-0">미디어 라이브러리</h1>
                <p class="text-sm text-nord-3 mt-1">업로드된 이미지를 관리합니다.</p>
            </div>
            <button type="button" id="upload-btn" class="btn btn-primary">
                파일 업로드
            </button>
            <input type="file" id="upload-input" hidden accept="image/*">
        </div>

        <div id="media-grid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            <div class="text-nord-3">불러오는 중...</div>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script>
        (async function () {
            const grid = document.getElementById('media-grid');

            try {
                const response = await fetch('/api/v1/media', {
                    headers: {'Accept': 'application/json'}
                });

                if (!response.ok) {
                    grid.innerHTML = '<div class="col-span-full text-center text-nord-11 py-12">미디어를 불러오지 못했습니다.</div>';
                    return;
                }

                const json = await response.json();
                const items = json.data?.items ?? [];

                renderGrid(items);
            } catch (error) {
                console.error(error);
                grid.innerHTML = '<div class="col-span-full text-center text-nord-11 py-12">네트워크 오류</div>';
            }

            function renderGrid(items) {
                if (items.length === 0) {
                    grid.innerHTML = '<div class="col-span-full text-center text-nord-3 py-12">업로드된 미디어가 없습니다.</div>';
                    return;
                }

                grid.innerHTML = items.map(createCardHtml).join('');
            }

            function escapeHtml(s) {
                return String(s ?? '').replace(/[&<>"']/g, c => ({
                    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
                }[c]));
            }

            function escapeAttribute(s) {
                return String(s ?? '').replace(/[&<>"'`=\\]/g, c => '&#' + c.charCodeAt(0) + ';');
            }

            const uploadBtn = document.getElementById('upload-btn');
            const uploadInput = document.getElementById('upload-input');

            uploadBtn.addEventListener('click', () => uploadInput.click());

            uploadInput.addEventListener('change', async (e) => {
                const file = e.target.files[0];

                if (!file) return;

                uploadBtn.disabled = true;
                uploadBtn.textContent = '업로드 중...';

                try {
                    const formData = new FormData();
                    formData.append('file', file);

                    const response = await fetch('/api/v1/media/upload', {
                        method: 'POST',
                        headers: {'Accept': 'application/json'},
                        body: formData
                    });

                    if (!response.ok) {
                        const err = await response.json().catch(() => ({}));
                        alert('업로드 실패: ' + (err.message || response.status));
                        return;
                    }

                    const json = await response.json();
                    prependItem(json.data);
                } catch (err) {
                    console.error(err);
                    alert('네트워크 오류');
                } finally {
                    uploadBtn.disabled = false;
                    uploadBtn.textContent = '파일 업로드';
                    uploadInput.value = '';
                }
            });

            function prependItem(m) {
                if (grid.querySelector('.col-span-full')) {
                    grid.innerHTML = '';
                }

                grid.insertAdjacentHTML('afterbegin', createCardHtml(m));
            }

            function createCardHtml(m) {
                return `<div class="aspect-square rounded-lg overflow-hidden bg-nord-5 border border-nord-4 group relative" data-media-id="${m.id}">
                    <img src="${escapeAttribute(m.url)}" alt="${escapeHtml(m.original_name)}" class="w-full h-full object-cover" loading="lazy" />
                    <button type="button" class="delete-btn absolute top-1.5 right-1.5 w-7 h-7 rounded-full bg-nord-0/70 text-nord-6 grid place-items-center opacity-0 group-hover:opacity-100 transition hover:bg-nord-11" aria-label="삭제">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18M6 6l12 12"/>
                        </svg>
                    </button>
                    <div class="absolute inset-x-0 bottom-0 bg-nord-0/80 text-nord-6 text-xs px-2 py-1 truncate opacity-0 group-hover:opacity-100 transition">
                        ${escapeHtml(m.original_name)}
                    </div>
                </div>`;
            }

            grid.addEventListener('click', async (e) => {
                const deleteBtn = e.target.closest('.delete-btn');
                if (!deleteBtn) return;

                const card = deleteBtn.closest('[data-media-id]');
                const id = card.dataset.mediaId;

                if (!confirm('이 미디어를 삭제하시겠습니까?')) return;

                deleteBtn.disabled = true;

                try {
                    const response = await fetch(`/api/v1/media/${id}`, {
                        method: 'DELETE',
                        headers: {'Accept': 'application/json'}
                    });

                    if (response.ok) {
                        card.remove();
                        if (grid.children.length === 0) {
                            grid.innerHTML = '<div class="col-span-full text-center text-nord-3 py-12">업로드된 미디어가 없습니다.</div>';
                        }
                    } else {
                        const err = await response.json().catch(() => ({}));
                        alert('삭제 실패: ' + (err.message || response.status));
                        deleteBtn.disabled = false;
                    }
                } catch (err) {
                    console.error(err);
                    alert('네트워크 오류');
                    deleteBtn.disabled = false;
                }
            });
        })();
    </script>
<?= $this->endSection() ?>
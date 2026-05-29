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

                grid.innerHTML = items.map(m => `
                  <div class="aspect-square rounded-lg overflow-hidden bg-nord-5 border border-nord-4 group relative">
                      <img src="${m.url}" alt="${escapeHtml(m.original_name)}" class="w-full h-full object-cover" loading="lazy" />
                      <div class="absolute inset-x-0 bottom-0 bg-nord-0/80 text-nord-6 text-xs px-2 py-1 truncate opacity-0 group-hover:opacity-100 transition">
                          ${escapeHtml(m.original_name)}
                      </div>
                  </div>
                `).join('');
            }

            function escapeHtml(s) {
                return String(s ?? '').replace(/[&<>"']/g, c => ({
                    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
                }[c]));
            }
        })();
    </script>
<?= $this->endSection() ?>
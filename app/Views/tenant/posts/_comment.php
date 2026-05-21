<div><?= nl2br(esc($comment->content)) ?></div>
<div><?= esc(date('Y-m-d H:i', strtotime($comment->created_at))) ?></div>
<?php foreach ($comment->replies as $reply) : ?>
    <div class="ml-6 border-l">
        <?= view('tenant/posts/_comment', ['comment' => $reply]) ?>
    </div>
<?php endforeach; ?>


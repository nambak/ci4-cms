<?php

namespace App\Controllers\Tenant;

use App\Controllers\BaseController;
use App\Enums\PostState;
use App\Models\PostModel;
use App\Models\TagModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class TagsController extends BaseController
{
    public function posts(string $tenantSlug, string $tagSlug): string
    {
        $tenant = service('tenant')->getTenant();

        $postModel = model(PostModel::class);

        $tag = model(TagModel::class)
            ->where('tenant_id', $tenant->id)
            ->where('slug', $tagSlug)
            ->first();

        if ($tag === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $posts = $postModel
            ->select('posts.*')
            ->join('post_tags', 'post_tags.post_id = posts.id')
            ->where('post_tags.tag_id', $tag->id)
            ->where('posts.tenant_id', $tenant->id)
            ->where('posts.state', PostState::PUBLISHED->value)
            ->orderBy('posts.created_at', 'DESC')
            ->paginate(10);

        return view('tenant/tags/posts', [
            'tenant' => $tenant,
            'tag'    => $tag,
            'posts'  => $posts,
            'pager'  => $postModel->pager
        ]);
    }
}

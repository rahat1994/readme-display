<?php

namespace ReadmeDisplay\App\Http\Controllers;

use ReadmeDisplay\App\Models\Post;
use ReadmeDisplay\Framework\Request\Request;

class PostController extends Controller
{
    public function get(Request $request)
    {
        $status = $request->status ?: 'all';

        $useFilter = $status !== 'all';

        return Post::latest('ID')
            ->when($useFilter, function($query) use($status) {
                $query->where('post_status', $status);
            })
            ->paginate($request->per_page);
    }

    public function find($id)
    {
        return Post::findOrFail($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'post_title' => 'required|string',
            'post_content' => 'required|string',
        ]);

        return wp_insert_post(
            $request->all() + ['post_status' => 'publish']
        );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'post_title' => 'required|string',
            'post_content' => 'required|string',
        ]);

        return wp_update_post($request->all());
    }

    public function delete($id)
    {
        return Post::findOrFail($id)->delete();
    }
}

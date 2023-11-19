<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\StorePostUpdateRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostUpdateResource;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
// use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        // return Post::all();
        $orderColumn = request('order_column', 'created_at');
        if (!in_array($orderColumn, ['id', 'title', 'created_at'])) {
            $orderColumn = 'created_at';
        }
        $orderDirection = request('order_direction', 'desc');
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        $posts = Post::with('category')
            ->when(request('search_category'), function (Builder $query) {
                $query->where('category_id', request('search_category'));
            })
            ->when(request('search_id'), function (Builder $query) {
                $query->where('id', request('search_id'));
            })
            ->when(request('search_title'), function (Builder $query) {
                $query->where('title', 'like', '%' . request('search_title') . '%');
            })
            ->when(request('search_content'), function (Builder $query) {
                $query->where('content', 'like', '%' . request('search_content') . '%');
            })
            ->when(request('search_global'), function (Builder $query) {
                $query->where(function (Builder $q) {
                    $q->where('id', request('search_global'))
                        ->orWhere('title', 'like', '%' . request('search_global') . '%')
                        // ->orWhere('category', 'like', '%' . request('search_global') . '%')
                        ->orWhere('content', 'like', '%' . request('search_global') . '%');
                });
            })
            ->orderBy($orderColumn, $orderDirection)
            ->paginate(5);

        return PostResource::collection($posts);
    }

    public function store(StorePostRequest $request)
    {
        $this->authorize('posts.create');
        // dd($request->all()); 
        if ($request->hasFile('thumbnail')) {
            $filename = now() . '_' . uniqid() . '.' . $request->file('thumbnail')->getClientOriginalName();
            // info($filename);
        }
        $post = Post::create($request->safe()->merge([
            'thumbnail' => $filename,
        ])->all());

        return new PostResource($post);
    }

    public function show(Post $post)
    {
        return new PostUpdateResource($post);
    }

    public function update(Post $post, StorePostUpdateRequest $request)
    {
        $this->authorize('posts.update');

        $file =  $request->hasFile('thumbnail') ? $request->file('thumbnail')->store('Thumbnails', 'public') : $post->thumbnail;
        $post->update($request->safe()->merge([
            'thumbnail' => $file
        ])->all());
        return new PostResource($post);
    }

    public function destroy(Post $post)
    {
        $this->authorize('posts.delete');
        $post->delete();

        return response()->noContent();
    }
}

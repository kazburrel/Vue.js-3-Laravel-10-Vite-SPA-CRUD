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
            ->when(request('category'), function (Builder $query) {
                $query->where('category_id', request('category'));
            })
            ->orderBy($orderColumn, $orderDirection)
            ->paginate(5);

        return PostResource::collection($posts);
    }

    public function store(StorePostRequest $request)
    {
        dd($request->thumbnail); 
        if ($request->hasFile('thumbnail')) {
            $filename = now() . '_' . uniqid() . '.' . $request->file('thumbnail')->getClientOriginalName();
            info($filename);
        }
        $post = Post::create($request->safe()->merge([
            'thumbnail' => $filename,
        ])->all());

        return new PostResource($post);
    }

    public function show(Post $post)
    {
        // dd($post);
        // if (!$post) {
        //     return response()->json(['error' => 'Post not found'], 404);
        // }
        // return response()->json([
        //     'id' => $post->id,
        //     'title' => $post->title,
        //     'content' => substr($post->content, 0, 50) . '...',
        //     'category_id' => $post->category_id, 
        //     'category' => $post->category->name, 
        //     'thumbnail' => $post->thumbnail, 
        //     'created_at' => $post->created_at->toDateString()
        // ]);
        return new PostUpdateResource($post);
    }

    public function update(Post $post, StorePostUpdateRequest $request)
    {
        dd($request->all());    
        if ($request->hasFile('thumbnail')) {
            dd('hi');
            $file = now() . '_' . uniqid() . '.' . $request->file('thumbnail')->getClientOriginalName();
            $post->update($request->safe()->merge([
                'thumbnail' => $file
            ])->all());
            return new PostResource($post);
        }
    }
}

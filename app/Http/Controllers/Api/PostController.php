<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        // return Post::all();
        $posts = Post::with('category')->paginate(10);

        return PostResource::collection($posts);
    }
}

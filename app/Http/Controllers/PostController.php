<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostDetailResource;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();
        
        // return response()->json(['data' => $posts]);
        return PostResource::collection($posts);
    }

    public function show($id)
    {
        $post = Post::with('writer:id,username')->findOrFail($id);

        //why new because only single return meanwhile post resource usually return array
        return new PostDetailResource($post);
    }

    public function show2($id)
    {
        $post = Post::findOrFail($id);

        //why new because only single return meanwhile post resource usually return array
        return new PostDetailResource($post);
    }
}

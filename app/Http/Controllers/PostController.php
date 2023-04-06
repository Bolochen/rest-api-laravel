<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PostDetailResource;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('writer:id,username')->get();

        // return response()->json(['data' => $posts]);
        return PostDetailResource::collection($posts);
    }

    public function show($id)
    {
        $post = Post::with('writer:id,username')->findOrFail($id);

        //why new because only single return meanwhile post resource usually return array
        return new PostDetailResource($post);
    }

    // public function show2($id)
    // {
    //     $post = Post::findOrFail($id);

    //     //why new because only single return meanwhile post resource usually return array
    //     return new PostDetailResource($post);
    // }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'news_content' => 'required'
        ]);

        $validatedData['author'] = Auth::user()->id;
        $post = Post::create($validatedData);

        return new PostDetailResource($post->loadMissing('writer:id,username'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'news_content' => 'required'
        ]);

        $post = Post::findOrFail($id);
        $post->update($validatedData);

        return new PostDetailResource($post->loadMissing('writer:id,username'));
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return new PostDetailResource($post->loadMissing('writer:id,username'));
    }
}

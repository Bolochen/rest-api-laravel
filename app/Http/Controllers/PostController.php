<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PostDetailResource;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('writer:id,username', 'comments:id,post_id,user_id,comments_content')->get();

        // return response()->json(['data' => $posts]);
        return PostDetailResource::collection($posts);
    }

    public function show($id)
    {
        $post = Post::with('writer:id,username', 'comments:id,post_id,user_id,comments_content')->findOrFail($id);

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

        $image = null;
        if ($request->file) {

            $fileName = $this->generateRandomString();
            $extension = $request->file->extension();
            $image = $fileName . '.' . $extension;

            Storage::putFileAs('image', $request->file, $image);
        }

        $validatedData['author'] = Auth::user()->id;
        $validatedData['image'] = $image;
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

    function generateRandomString($length = 30)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

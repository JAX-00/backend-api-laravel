<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index(){
        return Post::with('user')->latest()->get();
    }

    public function store(Request $request){
        $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        $post = Post::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return response()->json($post, 201);
    }

    public function update(Request $request, Post $post){
     if ($request->user()->id !== $post->user_id) {
        return response()->json([
            'message' => 'Forbidden'
        ], 403);
     }

     $request->validate([
        'title' => 'required|string',
        'content' => 'required|string',
     ]);

     $post->update([
        'title' => $request->title,
        'content' => $request->content,
     ]);

     return response()->json($post);
    }

public function destroy(Post $post)
{
    // Cek kepemilikan post
    if ($post->user_id !== Auth::id()) {
        return response()->json([
            'message' => 'Forbidden'
        ], 403);
    }

    $post->delete();

    return response()->json([
        'message' => 'Post deleted successfully'
    ], 200);
}
}

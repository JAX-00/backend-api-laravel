<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request){
    return Post::where('user_id', $request->user()->id)
               ->with('user')
               ->latest()
               ->get();
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
     $this->authorize('update', $post);

     $request->validate([
        'title' => 'required|string',
        'content' => 'required|string',
     ]);

     $post->update($request->only('title', 'content'));

     return response()->json($post);
    }

public function destroy(Post $post)
{
    $this->authorize('delete', $post);

    $post->delete();

    return response()->json([
        'message' => 'Post deleted successfully'
    ], 200);
}
}

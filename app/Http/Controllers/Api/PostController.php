<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * List post milik user login
     */
    public function index(Request $request)
    {
   $query = Post::query();

    // 🔐 hanya post milik user login
    $query->where('user_id', $request->user()->id);

    // 🔍 SEARCH (optional)
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%");
        });
    }

    // 📄 pagination
    $posts = $query
        ->with('user')
        ->latest()
        ->paginate(5);

    // 🎁 response rapi
    return PostResource::collection($posts);
    }

    /**
     * Simpan post baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post = Post::create([
            'user_id' => $request->user()->id,
            'title'   => $validated['title'],
            'content' => $validated['content'],
        ]);

        return new PostResource($post);
    }

    /**
     * Update post
     */
    public function update(Request $request, Post $post)
    {
        // Authorization
        if ($request->user()->id !== $post->user_id) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post->update($validated);

        return new PostResource($post);
    }

    /**
     * Hapus post
     */
    public function destroy(Post $post)
    {
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

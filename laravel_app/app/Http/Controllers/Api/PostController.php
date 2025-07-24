<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PostReaction;
use App\Models\Reaction;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::with(['user', 'postfile', 'post_reactions'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'message' => '取得に成功しました',
            'data' => [
                'posts' => $posts
            ]
        ]);
    }

    public function show(Request $request)
    {
        $lastPostId = $request->query('last_post_id', 0);

        $posts = Post::where('id', '<', $lastPostId)
            ->with(['user', 'postfile', 'post_reactions']) // リレーションをロード
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'message' => '取得に成功しました',
            'data' => [
                'posts' => $posts
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'files.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,avi|max:51200',
        ]);

        try {
            DB::beginTransaction();

            // Create the post
            $post = Post::create([
                'user_id' => auth()->id(),
                'post_content' => $validated['content'],
            ]);

            // Handle file uploads if any
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $path = $file->store('post_files', 'public');
                    
                    // 既存のpostfileリレーションを使用
                    $post->postfile()->create([
                        'post_file_path' => $path,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            DB::commit();

            // 既存のリレーションを使用してデータをロード
            $post->load(['user', 'postfile', 'post_reactions']);

            return response()->json([
                'success' => true,
                'message' => '投稿に成功しました',
                'data' => [
                    'post' => [
                        'id' => $post->id,
                        'user_id' => $post->user_id,
                        'post_content' => $post->post_content,
                        'created_at' => $post->created_at,
                        'updated_at' => $post->updated_at,
                        'user' => $post->user,
                        'files' => $post->postfile,  // postfileリレーションを使用
                        'reactions' => $post->post_reactions,  // post_reactionsリレーションを使用
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Post creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '投稿に失敗しました',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function delete(Request $request, Post $post) {
        $post->delete();
        return response()->json([
            'success' => true,
            'message' => '投稿を削除しました',
            'data' => [
                'post' => $post
            ]
        ]);
    }

    public function reaction_ops(Request $request, $post_id)
    {
        $request->validate([
            'reaction_id' => 'required|exists:reactions,id'
        ]);

        $user = $request->user();
        $reaction = Reaction::findOrFail($request->reaction_id);
        
        // Check if the user has already reacted with this reaction type
        $existingReaction = PostReaction::where('post_id', $post_id)
            ->where('user_id', $user->id)
            ->where('reaction_id', $reaction->id)
            ->first();

        if ($existingReaction) {
            // Remove the reaction if it exists
            $existingReaction->delete();
            $message = 'リアクションを解除しました';
            $isReacted = false;
        } else {
            // Add the reaction if it doesn't exist
            PostReaction::create([
                'post_id' => $post_id,
                'user_id' => $user->id,
                'reaction_id' => $reaction->id,
            ]);
            $message = 'リアクションを追加しました';
            $isReacted = true;
        }

        // Get the updated reaction count for this post and reaction type
        $reactionCount = PostReaction::where('post_id', $post_id)
            ->where('reaction_id', $reaction->id)
            ->count();

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'is_reacted' => $isReacted,
                'reaction_count' => $reactionCount
            ]
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PostReaction;
use App\Models\Reaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
                        'post_file_type' => str_starts_with($file->getClientMimeType(), 'video/') ? 'video' : 'image',
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

    public function delete(Request $request, $id) {
        // 認証チェック
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => '認証が必要です'
            ], 401);
        }

        // 明示的にPostを取得
        $post = Post::find($id);
        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => '投稿が見つかりません'
            ], 404);
        }

        // 削除リクエストのログ
        Log::info('投稿削除リクエスト', [
            'post_id' => $post->id,
            'post_user_id' => $post->user_id,
            'auth_user_id' => auth()->id()
        ]);
        
        // user_idがnullまたは空の場合の特別処理
        if (empty($post->user_id)) {
            Log::error('投稿データが不正です - user_idが空', [
                'post_id' => $post->id,
                'post_user_id' => $post->user_id
            ]);
            return response()->json([
                'success' => false,
                'message' => '投稿データに問題があります'
            ], 500);
        }
        
        // 投稿の所有者のみが削除できるようにチェック（型安全な比較）
        if ((int)$post->user_id !== (int)auth()->id()) {
            Log::warning('Unauthorized delete attempt', [
                'post_id' => $post->id,
                'post_user_id' => $post->user_id,
                'auth_user_id' => auth()->id()
            ]);
            return response()->json([
                'success' => false,
                'message' => '投稿を削除する権限がありません'
            ], 403);
        }

        Log::info('権限チェック通過 - 削除処理を開始', [
            'post_id' => $post->id,
            'user_id' => auth()->id()
        ]);

        try {
            DB::beginTransaction();

            // 投稿に関連する物理ファイルを削除
            $postFiles = $post->postfile;
            foreach ($postFiles as $file) {
                if ($file->post_file_path && Storage::disk('public')->exists($file->post_file_path)) {
                    Storage::disk('public')->delete($file->post_file_path);
                }
            }

            // 投稿を削除（関連するpost_files、post_reactionsはcascadeで自動削除される）
            $post->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '投稿を削除しました',
                'data' => [
                    'deleted_post_id' => $post->id
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Post deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '投稿の削除に失敗しました',
                'errors' => [$e->getMessage()]
            ], 500);
        }
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

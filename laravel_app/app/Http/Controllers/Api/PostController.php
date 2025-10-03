<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Throwable;
use App\Models\Post;
use App\Models\PostFile;
use App\Models\PostReaction;
use App\Models\Reaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Requests\PostStoreRequest;
use App\Services\PostFilesTypeSetService;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::with(['user:id,name,user_icon', 'postfile', 'post_reactions.reaction'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // ファイルパスの処理（APIエンドポイント経由のURLを返す）
        $posts->getCollection()->transform(function ($post) {
            // 投稿ファイルのURLフィールドを追加
            if ($post->postfile) {
                $post->postfile->transform(function ($file) {
                    // APIエンドポイント経由のURLを返す
                    $file->post_file_url = url('api/storage/' . $file->post_file_path);
                    return $file;
                });
            }
            
            return $post;
        });

        return response()->json([
            'success' => true,
            'message' => '取得に成功しました',
            'data' => [
                'posts' => $posts
            ]
        ]);
    }

    public function show(Request $request, $id)
    {
        // $postsは単一のPostモデルインスタンス
        $post = Post::with(['user:id,name,user_icon', 'postfile', 'post_reactions.reaction'])
            ->findOrFail($id);
    
        // 単一モデル内のリレーションを直接操作する
        if ($post->postfile) {
            $post->postfile->transform(function ($file) {
                // ファイルのパスからAPI経由のURLを生成し、フィールドとして追加
                $file->post_file_url = url('api/storage/' . $file->post_file_path);
                return $file;
            });
        }
    
        return response()->json([
            'success' => true,
            'message' => '取得に成功しました',
            'data' => [
                // 変数名を$postsから$postに変更すると、より意図が明確になります
                'posts' => $post 
            ]
        ]);
    }
    public function store(PostStoreRequest $request)
    {
        // 1. ファイルアップロードとそのパスを、トランザクション開始前に完了させる
        $uploadedFiles = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if ($file && $file->isValid()) {
                    // ファイルオブジェクトと保存先パスをセットで保持
                    $uploadedFiles[] = [
                        'file' => $file,
                        'path' => $file->store('post_files', 'public'), // ここで外部ストレージに保存
                    ];
                }
            }
        }
        
        $uploadedPaths = array_column($uploadedFiles, 'path'); // 後でロールバック用にパスをリスト化
        
        try {
            // 2. DBトランザクションを開始し、レコードの書き込みだけを行う
            DB::beginTransaction();
    
            $post = Post::create([
                'user_id' => $request->user()->id,
                'post_content' => $request->validated()['content'],
            ]);
    
            foreach ($uploadedFiles as $data) {
                $file = $data['file'];
                $path = $data['path'];
                
                $fileType = PostFilesTypeSetService::setType($file);
                // 既にアップロード済みのパスをDBに記録
                PostFile::create([
                    'post_id' => $post->id,
                    'post_file_path' => $path, 
                    'post_file_type' => $fileType,
                    'file_size' => $file->getSize(),
                ]);
            }
            
            DB::commit(); // DB操作がすべて成功したらコミット
    
            // 3. 成功レスポンスの準備（コミット後）
            $post->load('postfile');
            return response()->json([
                'success' => true,
                'message' => '投稿に成功しました',
                'data' => $post
            ], 201);
    
        } catch (Throwable $th) {
            // 4. エラー発生時のクリーンアップ処理
            if (DB::transactionLevel() > 0) {
                DB::rollBack(); // DBへの変更をロールバック
            }
    
            // アップロードに成功していたファイルをすべて削除（ゴミファイル対策）
            if (!empty($uploadedPaths)) {
                Storage::disk('public')->delete($uploadedPaths);
            }
            
            return response()->json([
                'success' => false,
                'message' => '投稿に失敗しました',
                'errors' => [$th->getMessage()]
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

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
use App\Http\Requests\PostDeleteRequest;
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

            $reactionInfo = [];
            foreach ($post->post_reactions as $reaction) {
                $reactionName = $reaction->reaction->reaction_name;
                
                if (!isset($reactionInfo[$reactionName])) {
                    $reactionInfo[$reactionName] = [
                        'count' => 0,
                        'image' => $reaction->reaction->reaction_image,
                        'name' => $reactionName
                    ];
                }
                $reactionInfo[$reactionName]['count']++;
                $reaction->reaction_name = $reactionName;
            }
            $post->reaction_stats = array_values($reactionInfo);
            
            return $post;
        });

        return response()->json([
            'success' => true,
            'message' => '取得に成功しました',
            'posts' => $posts
        ]);
    }

    public function show(Request $request, $id)
    {
        $post = Post::with(['user:id,name,user_icon', 'postfile', 'post_reactions.reaction'])
            ->findOrFail($id);
    
        if ($post->postfile) {
            $post->postfile->transform(function ($file) {
                // ファイルのパスからAPI経由のURLを生成し、フィールドとして追加
                $file->post_file_url = url('api/storage/' . $file->post_file_path);
                return $file;
            });
        }

        $reactionInfo = [];
        foreach ($post->post_reactions as $reaction) {
            $reactionName = $reaction->reaction->reaction_name;
            
            if (!isset($reactionInfo[$reactionName])) {
                $reactionInfo[$reactionName] = [
                    'count' => 0,
                    'image' => $reaction->reaction->reaction_image,
                    'name' => $reactionName
                ];
            }
            $reactionInfo[$reactionName]['count']++;
            $reaction->reaction_name = $reactionName;
        }
        $post->reaction_stats = array_values($reactionInfo);
    
        return response()->json([
            'success' => true,
            'message' => '取得に成功しました',
            'data' => [
                'post' => $post 
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
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
    
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

    public function delete(PostDeleteRequest $request, $id) 
    {   
        $post = Post::findOrFail($id);
        $deletedPostId = $post->id;
        
        try {
            DB::transaction(function () use ($post) {
                
                foreach ($post->postfile as $file) {
                    $path = $file->post_file_path;
                    
                    if ($path && Storage::disk('public')->exists($path)) {
                        if (!Storage::disk('public')->delete($path)) {
                            // 強制的に例外をスローし、DB::transaction を失敗させる
                            throw new \Exception('ストレージからのファイルの削除に失敗しました。（ファイル権限の確認が必要かもしれません）'); 
                        }
                    }
                }
                
                $post->delete(); 
            }); 
            
            return response()->json([
                'success' => true,
                'message' => '投稿を削除しました',
                'data' => [
                    'deleted_post_id' => $deletedPostId
                ]
            ]);
    
        } catch (\Exception $e) {
            
            Log::error('Post deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => '投稿の削除に失敗しました',
                'errors' => [$e->getMessage()] 
            ], 500);
        }
    }
    
}

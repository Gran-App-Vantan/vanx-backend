<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Point;
use App\Models\PointLog;
use App\Http\Requests\AuthSignUpRequest;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AccountUpdateRequest;
use App\Http\Requests\WalletFillterRequest;
use App\Http\Requests\WalletUpdateRequest;

class AccountController extends Controller
{
    //
    public function login(AuthLoginRequest $request){
    $validated = $request->validated();
    $user_name = $validated['user']['name'];
    $password = $validated['user']['password'];
    if (Auth::attempt(['name' => $user_name, 'password' => $password])) {
        $authUser = Auth::user();
        return response()->json([
            'success' => true,
            'messages' => ['ログインに成功しました。'],
            'authToken' => $authUser->createToken('authToken', ['*'], now()->addDays(7))->plainTextToken,
        ]);
    }
    return response()->json(
        [
            'success' => false,
            'messages' => ['名前またはパスワードが正しくありません。'],
        ], 401);
    }
    public function register(AuthSignUpRequest $request)
    {
        $user_paths = User::all()->pluck('user_path')->toArray();
        $validated = $request->validated();
        $user = $validated['user'];
        $user_path_check = false;
        while ($user_path_check == false) {
            $user_path = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@_'), 0, 8); // 8文字のランダム文字列
            if (!in_array($user_path, $user_paths)) {
                $user_path_check = true;
            };
        }
        $user = User::create([
            'name' => $user['name'],
            'user_path' => $user_path,
            'password' => Hash::make($user['password']),
            'user_job' => 'player',
            'user_icon' => 'default_user_icon',
        ]);
        $user->points()->create([
            'point' => 10000,
        ]);

        $token = $user->createToken('authToken', ['*'], now()->addDays(7))->plainTextToken;

        return response()->json([
            'success' => true,
            'messages' => ['ユーザー登録が完了しました'],
            'authToken' => $token,
            'user' => [
                'id' => $user->id, 
                'name' => $user->name,
                'user_path' => $user->user_path,
                'user_job' => $user->user_job,
            ]
        ]);
    }
    public function update(AccountUpdateRequest $request){
        $user = $request->user();
        $updateData = [
            "name" => $request->input('name') ?: $user->name,
            "password" => Hash::make($request->input('password')) ?: $user->password,
            "user_icon" => $request->hasFile('user_icon') ? $request->file('user_icon')->storePublicly('user_icons','public') : $user->user_icon
        ];
        $user->update($updateData);
        
        return response()->json([
            'success' => true,
            'messages' => ['更新に成功しました'],
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'user_icon' => $user->user_icon
            ]
        ]);
    }
    public function profile(Request $request, $id)
    {
        $user = User::with([
            'points',
            'posts' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'posts.postfile',
            'posts.post_reactions.reaction'
        ])->findOrFail($id);
        
        $posts = $user->posts;

        $user->makeHidden(['password', 'remember_token','user_path','posts']);

        $user->point = $user->points->point ?? 0;
        unset($user->points); 

        // 投稿のファイルURLフィールドを追加
        $posts->transform(function ($post) {
            if ($post->postfile) {
                $post->postfile->transform(function ($file) {
                    $file->post_file_url = url('api/storage/' . $file->post_file_path);
                    return $file;
                });
            }
            return $post;
        });
        $posts->transform(function ($post) {
            // リアクションの種類ごとの情報を格納する配列
            $reactionInfo = [];
            
            // 各リアクションを処理
            foreach ($post->post_reactions as $reaction) {
                $reactionName = $reaction->reaction->reaction_name;
                
                // まだ存在しないリアクションタイプの場合は初期化
                if (!isset($reactionInfo[$reactionName])) {
                    $reactionInfo[$reactionName] = [
                        'count' => 0,
                        'image' => $reaction->reaction->reaction_image,
                        'name' => $reactionName
                    ];
                }
                
                // カウントをインクリメント
                $reactionInfo[$reactionName]['count']++;
                
                // 既存の変換処理
                $reaction->reaction_name = $reactionName;
            }
            
            // 配列のインデックスをリセットして連番の配列に変換
            $post->reaction_stats = array_values($reactionInfo);
            
            return $post;
        });

        return response()->json([
            'success' => true,
            'message' => '取得に成功しました',
            'data' => [
                'user' => $user,
                'posts' => $posts
            ]
        ]);
    }
    public function me(Request $request)
    {
        $user = $request->user()->load('points');
        $user = $user->only('id', 'name', 'user_icon');
        $user['point'] = $request->user()->points->point;
        return response()->json([
            'success' => true,
            'message' => '取得に成功しました',
            'data' => [
                'user' => $user
            ]
        ]);
    }
    public function wallet(WalletFillterRequest $request)
    {
        $user = $request->user()->load('points');
        $user = $user->only('id', 'name', 'user_icon');
        $user['point'] = $request->user()->points->point;
        if ($request->input('filter') == "all") {
            $pointlogs = $request->user()->pointlogs->toArray();
        } else if ($request->input('filter') == "plus") {
            $pointlogs = $request->user()->pointlogs()->whereIn('type', ['get', 'import'])->get()->toArray();
        } else if ($request->input('filter') == "minus") {
            $pointlogs = $request->user()->pointlogs()->whereIn('type', ['use', 'export'])->get()->toArray();
        }


        return response()->json([
            'success' => true,
            'message' => '取得に成功しました',
            'data' => [
                'user' => $user,
                'pointlogs' => $pointlogs
            ]
        ]);
    }
    public function wallet_update(WalletUpdateRequest $request){
        //設計の改善の必要あり、ブランチを変更して作成します。
        $user = $request->user();
        $point = $user->points;
        $point->update([
            'point' => $point->point + $request->input('point'),
        ]);
        $user->pointlogs()->create([
            'user_id' => $user->id,
            'point_amount' => $request->input('point'),
            'service_name' => $request->input('service_name'),
            'description' => $request->input('description'),
            'type' => $request->input('type'),
        ]);
        return response()->json([
            'success' => true,
            'message' => '更新に成功しました',
        ]);
    }
    public function ranking(Request $request){
        $users = User::where('user_job', 'player')
            ->leftJoin('points', 'users.id', '=', 'points.user_id')
            ->orderByDesc('points.point')
            ->select('users.*') // usersテーブルの全カラムを選択
            ->with('points') // レスポンスでpointsオブジェクトを使えるようにEager Loading
            ->get();

        $my_account = $request->user()->load('points');
        $my_account = $my_account->only('id', 'name', 'user_icon');
        $my_account['point'] = $request->user()->points->point;

        return response()->json([
            'success' => true,
            'message' => '取得に成功しました',
            'data' => [
                'my_account' => $my_account,
                'users' => $users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'user_icon' => $user->user_icon,
                        'point' => $user->points->point ?? 0,
                    ];
                })  
            ]
        ]); 
    }
}
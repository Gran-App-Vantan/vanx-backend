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

class AccountController extends Controller
{
    //
    public function login(AuthLoginRequest $request){
        $user_path = $request->input('user_path');
        $password = $request->input('password');
        if (Auth::attempt(['user_path' => $user_path, 'password' => $password])) {
            $authUser = $request->user();
            return response()->json([
                'success' => true,
                'messages' => ['ログインに成功しました。'],
                'authToken' => $authUser->createToken('authToken', ['*'], now()->addDays(7))->plainTextToken,
            ]);
        }
        return response()->json([
            'success' => false,
            'messages' => ['IDまたはパスワードが正しくありません。'],
        ], 401);
    }
    public function register(AuthSignUpRequest $request)
    {
        
        $validated = $request->validated();
        $user = $validated['user'];
        $user = User::create([
            'name' => $user['name'],
            'user_path' => $user['user_path'],
            'password' => Hash::make($user['password']),
            'user_job' => 'player',
            'user_icon' => 'default_icon.png',
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
    public function update(Request $request){
        $user = $request->user();
        $updateData = [
            "name" => $request->input('name') ?: $user->name,
            "user_path" => $request->input('user_path', "") ?: $user->user_path
        ];
        if ($request->hasFile('user_icon')) {
            try {
                // ファイルのMIMEタイプを確認
                $mimeType = $request->file('user_icon')->getMimeType();

                // パブリックディスクを使用してファイルを保存
                $filename = 'user_icon' . $user->id . '.png';
                $path = Storage::disk('public')->put(
                    'user_icons/' . $filename,
                    file_get_contents($request->file('user_icon'))
                );

                if (!$path) {
                    throw new \Exception('ファイルの保存に失敗しました');
                }

                $updateData["user_icon"] = "user_icons/" . $filename;

                // デバッグ用のログ
                \Log::info("File uploaded successfully:", [
                    'path' => $path,
                    'filename' => "user_icon{$user->id}.png",
                    'original_name' => $request->file('user_icon')->getClientOriginalName(),
                    'mime_type' => $mimeType
                ]);
            } catch (\Exception $e) {
                \Log::error("File upload failed:", [
                    'error' => $e->getMessage(),
                    'user_id' => $user->id,
                    'file_size' => $request->file('user_icon')->getSize()
                ]);
                return response()->json([
                    'success' => false,
                    'error' => 'アイコンのアップロードに失敗しました',
                    'message' => $e->getMessage(),
                    'code' => 'UPLOAD_ERROR'
                ], 400);
            }
        }

        try {
            $user->update($updateData);
            return response()->json([
                'success' => true,
                'message' => '更新に成功しました',
                'data' => [
                    'user' => [
                        'name' => $user->name,
                        'user_path' => $user->user_path,
                        'user_icon' => $user->user_icon
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error("User update failed:", [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
            return response()->json([
                'success' => false,
                'error' => 'ユーザー情報の更新に失敗しました',
                'message' => $e->getMessage(),
                'code' => 'UPDATE_ERROR'
            ], 400);
        }
    }
    public function profile($id)
    {
        $user = User::with('points')->findOrFail($id);
        
        $response = [
            'success' => true,
            'message' => '取得に成功しました',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'user_path' => $user->user_path,
                    'user_job' => $user->user_job,
                    'user_icon' => $user->user_icon,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ],
                'point' => null
            ]
        ];

        // ポイント情報が存在する場合のみ追加
        if ($user->points && $user->points->isNotEmpty()) {
            $point = $user->points->first();
            $response['data']['point'] = [
                'id' => $point->id,
                'user_id' => $point->user_id,
                'point_balance' => $point->point_balance,
                'created_at' => $point->created_at,
                'updated_at' => $point->updated_at,
            ];
        }

        return response()->json($response);
    }
    public function wallet(Request $request)
    {
        $user = $request->user();
        $point = Point::where('user_id', $user->id)->first();
        return response()->json([
            'success' => true,
            'message' => '取得に成功しました',
            'data' => [
                'point' => $point->point
            ]
        ]);
    }
    public function wallet_update(Request $request){
        $user = $request->user();
        $point = Point::where('user_id', $user->id)->first();
        $point->update([
            'point' => $point->point + $request['point'],
        ]);
        PointLog::create([
            'user_id' => $user->id,
            'point_amount' => $request['point'],
            'service_name' => $request['service_name'],
            'description' => $request['description'],
            'type' => $request['type'],
        ]);
        return response()->json([
            'success' => true,
            'message' => '更新に成功しました',
            'data' => [
                'point' => $point->point
            ]
        ]);
    }
    public function ranking(Request $request){
        $users = User::all();
        return response()->json([
            'success' => true,
            'message' => '取得に成功しました',
            'data' => [
                'users' => $users
            ]
        ]); 
    }
}
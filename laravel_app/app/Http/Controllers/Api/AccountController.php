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
            'user_icon' => 'default_icon.png',
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
            "user_path" => $request->input('user_path') ?: $user->user_path
        ];
        

        if (isset($_FILES['user_icon']) && $_FILES['user_icon']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['user_icon'];
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'user_icon_' . $user->id . '.' . $extension;
            $path = $file['tmp_name'];
            
            $destinationDir = public_path('storage/user_icons/');
            if (!file_exists($destinationDir)) {
                mkdir($destinationDir, 0777, true);
            }
            
            if (move_uploaded_file($path, $destinationDir . $filename)) {
                $updateData['user_icon'] = 'storage/user_icons/' . $filename;
            }
        }
        
        $user->update($updateData);
        return response()->json([
            'success' => true,
            'messages' => ['更新に成功しました'],
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'user_path' => $user->user_path,
                'user_icon' => $user->user_icon
            ]
        ]);
    }
    public function profile(Request $request, $id)
    {
        $user = User::with(['points', 'posts.postfile'])->findOrFail($id);
        $posts = $user->posts;

        $user->makeHidden(['password', 'remember_token','posts']);

        $user->point = $user->points->point ?? 0;
        unset($user->points); 

        return response()->json([
            'success' => true,
            'message' => '取得に成功しました',
            'data' => [
                'user' => $user,
                'posts' => $posts
            ]
        ]);
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
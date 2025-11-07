<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\RuleBook;
use App\Models\GameLoginSession;
use App\Models\Device;
use App\Models\Game;
use App\Http\Requests\CreateUrlRequest;
use App\Http\Requests\DeleteUrlRequest;
use App\Http\Requests\CheckUrlRequest;



class GameController extends Controller
{
    public function create_url(CreateUrlRequest $request){
        $device_number = $request->input('device_number');
        $game_type = $request->input('game_type');
        $game_id = Game::where('name', $game_type)->first()->id;
        $device = Device::where([
            ['device_number', $device_number],
            ['game_id', $game_id]
        ])->first();
        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'デバイスが見つかりませんでした',
            ], 404);
        }
        $url = GameLoginSession::create([
            'token' => Str::random(60),
            'device_id' => $device->id,
            'expires_at' => now()->addHours(2),
        ]);

        return response()->json([
            'success' => true,
            'message' => '取得に成功しました',
            'data' => [
                'token' => $url->token,
                'game_type' => $game_type
            ]
        ]);
    }
    public function token_check(CheckUrlRequest $request)
    {
        $token = $request->input('token');
        $user_id = $request->user()->id;
        $user_point = $request->user()->points->point;
        $game_login_session = GameLoginSession::with('device')->where('token', $token)->first();
        
        if (!$game_login_session) {
            return response()->json([
                'success' => false,
                'message' => 'トークンは存在しません',
            ], 404);
        }

        if ($game_login_session->expires_at < now()) {
            return response()->json([
                'success' => false,
                'message' => 'トークンの有効期限が切れています',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'トークンは存在します',
            'data' => [
                'user_id' => $user_id,
                'has_point' => $user_point,
                'token' => $game_login_session->token,
                'device_number' => $game_login_session->device->device_number,
                'expires_at' => $game_login_session->expires_at
            ]
        ]);
    }
    public function token_delete(DeleteUrlRequest $request){
        $token = $request->input('token');
        $game_login_session = GameLoginSession::where('token', $token)->first();
        if (!$game_login_session) {
            return response()->json([
                'success' => false,
                'message' => 'トークンが見つかりませんでした',
            ], 404);
        }
        $game_login_session->delete();
        return response()->json([
            'success' => true,
            'message' => '削除に成功しました',
        ]);
    }

    public function game_rule(Request $request, RuleBook $game_id){
        return response()->json([
            'success' => true,
            'message' => '取得に成功しました',
            'data' => [
                'rule_book' => $game_id
            ]
        ]);
    }
}

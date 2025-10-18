<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RuleBook;
use App\Models\GameLoginSession;

class GameController extends Controller
{
    public function game_url(Request $request){
        $device = $request->input('device');
        $game_type = $request->input('game_type');
        $url = GameLoginSession::create([
            'device' => $device,
            'game_id' => $game_type,
            
        ]);
        return response()->json([
            'success' => true,
            'message' => '取得に成功しました',
            'data' => [
                'url' => $url->token
            ]
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

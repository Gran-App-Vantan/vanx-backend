<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RuleBook;

class GameController extends Controller
{
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

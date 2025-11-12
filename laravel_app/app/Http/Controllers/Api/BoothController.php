<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booth;

class BoothController extends Controller
{
    public function floor_map(Request $request){
        $booths = Booth::all();
        return response()->json([
            'success' => true,
            'message' => '取得に成功しました',
            'data' => [
                'booths' => $booths
            ]
        ]);
    }
}

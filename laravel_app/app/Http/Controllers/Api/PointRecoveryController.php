<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Models\PointRecoverySession;
use App\Http\Requests\UseRecoveryRequest;
use App\Http\Requests\QrCreateRequest;
use App\Http\Requests\QrDeleteRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class PointRecoveryController extends Controller
{
    
    public function use_recovery(UseRecoveryRequest $request)
    {
        $user = $request->user();
        $token = $request->input('token');
        
        try {
            $session = PointRecoverySession::where('token', $token)->firstOrFail();

            // トークンが既に使用済みかチェック
            if ($user->point_recovery_users()->where('point_recovery_session_id', $session->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'このトークンは既に使用されています',
                ], 400);
            }

            DB::beginTransaction();
            
            try {
                // ユーザーのポイントを更新（Pointモデルを経由）
                $user->points()->increment('point', $session->amount);
                
                // ポイントログを作成
                $user->pointlogs()->create([
                    'point_amount' => $session->amount,
                    'service_name' => $session->service_name,
                    'description' => $session->description,
                    'type' => $session->type,
                ]);
                
                // 使用済みトークンを記録
                $user->point_recovery_users()->attach($session->id);
                
                DB::commit();
                
                // 更新後のポイントを取得
                $updatedPoint = $user->points()->first()->point;
                
                return response()->json([
                    'success' => true,
                    'message' => 'ポイント回復に成功しました',
                    'data' => [
                        'new_balance' => $updatedPoint
                    ]
                ], 200);
                
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Point recovery transaction error: ' . $e->getMessage());
                \Log::error($e->getTraceAsString());
                
                return response()->json([
                    'success' => false,
                    'message' => 'ポイントの更新中にエラーが発生しました',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => '有効なトークンが見つかりません',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Point recovery error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => '予期せぬエラーが発生しました',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
    
    public function index()
    {
        $sessions = PointRecoverySession::all();
        return response()->json([
            'success' => true,
            'data' => $sessions,
        ], 200);
    }
    
    public function create(QrCreateRequest $request)
    {
        $validated = $request->validated();
        $token = bin2hex(string: random_bytes(16));
        if($validated['expires_at']){
            $expires_at = now()->addMinutes($validated['expires_at']);
        }
        $session = PointRecoverySession::create([
            'token' => $token,
            'amount' => $validated['amount'],
            'service_name' => $validated['service_name'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'expires_at' => $expires_at ?? null,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'QRコードの作成に成功しました',
            'data' => $session,
        ], 200);
    }
    
    public function delete(QrDeleteRequest $request)
    {
        $id = $request->id;
        $session = PointRecoverySession::findOrFail($id);
        $session->delete();
        return response()->json([
            'success' => true,
            'message' => 'QRコードの削除に成功しました',
        ], 200);
    }
}

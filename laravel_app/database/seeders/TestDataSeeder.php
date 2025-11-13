<?php

namespace Database\Seeders;

use App\Models\PointLog;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::find(1);
        
        if (!$user) {
            $user = User::create([
                'id' => 1,
                'name' => 'テストユーザー',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'user_icon' => 'default_user_icon',
                'user_job' => 'player'
            ]);

            $user->points()->create([
                'point' => 10000,
            ]);
        }

        // テストデータの作成
        $now = now();
        $pointLogs = [];

        for ($i = 0; $i < 100; $i++) {
            // ランダムにポイントを生成（-1000 〜 1000の範囲）
            $pointAmount = rand(-1000, 1000);
            
            $pointLogs[] = [
                'user_id' => 1,
                'service_name' => 'テストサービス' . ($i % 5 + 1), // 5つのサービス名をローテーション
                'description' => 'テストデータ ' . ($i + 1),
                'point_amount' => $pointAmount,
                'created_at' => $now->copy()->subDays(rand(0, 30))->addHours(rand(0, 23))->addMinutes(rand(0, 59)),
                'updated_at' => now(),
            ];
        }

        // 一括挿入
        PointLog::insert($pointLogs);

        // ユーザーのポイントを更新
        $totalPoints = PointLog::where('user_id', 1)->sum('point_amount');
        $user->points()->update(['point' => 10000 + $totalPoints]);
    }
}
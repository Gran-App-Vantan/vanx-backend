<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // リアクションの初期データを設定
        $this->call(ReactionsSeeder::class);

        User::factory()->create([
            'name' => 'Test User',
            'user_path' => 'test_user',
            'password' => Hash::make('password'),
            'user_icon' => 'default_user_icon',
        ]);
    }
}

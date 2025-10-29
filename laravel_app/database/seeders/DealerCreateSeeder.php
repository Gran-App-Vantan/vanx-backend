<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DealerCreateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filepath = storage_path('app/public/DealerToken.txt');
        $user = User::create([
            'name' => 'Dealer',
            'user_path' => '12dea34ler4',
            'password' => Hash::make('dealer1234'),
            'user_icon' => 'default_user_icon',
            'biography' => null,
            'game_play_flag' => 0,
            'user_job' => 'dealer',
        ]);

        $token = $user->createToken('authToken')->plainTextToken;
        file_put_contents($filepath, $token);

    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Game;
use App\Models\Device;

class GameAndDeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        $game_settings = [
            ['name' => 'IndianPoker', 'device_number' => 4],
            ['name' => 'Roulette', 'device_number' => 1],
            ['name' => 'Slot', 'device_number' => 4],
            ['name' => 'Blackjack', 'device_number' => 4],
        ];
        foreach ($game_settings as $game_setting) {
            Game::create([
                'name' => $game_setting['name'],
            ]);
            $game_id = Game::where('name', $game_setting['name'])->first()->id;
            foreach (range(1, $game_setting['device_number']) as $device_number) {
                Device::create([
                    'game_id' => $game_id,
                    'device_number' => $device_number,
                ]);
            }
        }
    }
}

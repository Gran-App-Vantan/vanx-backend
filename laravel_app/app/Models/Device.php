<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    //
    protected $fillable = [
        'game_id',
        'device_number'
    ];
    public function game()
    {
        return $this->hasMany(Game::class);
    }
    public function loginSession()
    {
        return $this->hasMany(GameLoginSession::class);
    }
}

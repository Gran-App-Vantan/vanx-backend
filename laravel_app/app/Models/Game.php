<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    //
    protected $fillable = [
        'name'
    ];
    public function devices()
    {
        return $this->hasMany(Device::class);
    }
    public function rulebooks()
    {
        return $this->hasOne(RuleBook::class);
    }
}

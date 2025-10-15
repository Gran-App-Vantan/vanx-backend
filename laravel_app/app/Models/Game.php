<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    //
    protected $fillable = [
        'name'
    ];
    public function devises()
    {
        return $this->hasMany(Devise::class);
    }
    public function rulebooks()
    {
        return $this->hasOne(RuleBook::class);
    }
}

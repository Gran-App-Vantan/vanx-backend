<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RuleImageFile extends Model
{
    //
    protected $fillable = [
        'game_id',
        'rule_image_path'
    ];
}

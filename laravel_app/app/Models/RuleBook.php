<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RuleBook extends Model
{
    //
    protected $fillable = [
        'game_name',
        'rule_content'
    ];

    public function ruleimagefiles()
    {
        return $this->hasMany(RuleImageFile::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reaction extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'reaction_name',
        'reaction_image',
        'reaction_type'
    ];

    public function used_reactions()
    {
        return $this->hasMany(UserReactionPost::class);
    }
}

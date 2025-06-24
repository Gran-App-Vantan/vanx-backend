<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserReactionPost extends Model
{
    //
    protected $fillable = [
        'user_id',
        'post_id',
        'reaction_id'
    ];
}

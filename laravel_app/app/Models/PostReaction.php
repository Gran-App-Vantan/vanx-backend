<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostReaction extends Model
{
    //
    protected $fillable = [
        "user_id",
        "post_id",
        "reaction_id"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function reaction()
    {
        return $this->belongsTo(Reaction::class);
    }
}

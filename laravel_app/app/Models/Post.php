<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'user_id',
        'post_content'
    ];

    public function postfile()
    {
        return $this->hasMany(PostFile::class);
    }
    public function used_reactions()
    {
        return $this->hasMany(UserReactionPost::class);
    }
}

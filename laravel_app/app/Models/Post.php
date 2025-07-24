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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function files()
    {
        return $this->hasMany(PostFile::class);
    }

    public function postfile()
    {
        return $this->hasMany(PostFile::class);
    }

    public function post_reactions()
    {
        return $this->hasMany(PostReaction::class);
    }

    public function reactions()
    {
        return $this->hasMany(PostReaction::class);
    }
}

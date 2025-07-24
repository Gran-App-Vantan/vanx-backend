<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostFile extends Model
{
    //
    protected $fillable = [
        'post_id',
        'post_file_path',
        'post_file_type'
    ];
}

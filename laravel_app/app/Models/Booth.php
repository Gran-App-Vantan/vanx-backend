<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booth extends Model
{
    protected $fillable = [
        'booth_name',
        'booth_floor',
        'created_group',
        'booth_content',
        'booth_image'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointLog extends Model
{
    //
    protected $fillable = [
        'user_id',
        'service_name',
        'description',
        'point_amount',
        'type'
    ];
}

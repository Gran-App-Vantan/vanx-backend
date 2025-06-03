<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointSession extends Model
{
    //
    protected $table = [
        'service_name',
        'description',
        'point_amount',
        'start_time',
        'end_time',
    ];
}

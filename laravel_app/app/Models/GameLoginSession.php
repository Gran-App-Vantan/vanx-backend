<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameLoginSession extends Model
{
    //
    protected $primaryKey = 'token';

    public $incrementing = false;

    protected $keyType = 'string';
    
    protected $fillable = [
        'token',
        'device_id',
        'expires_at',
    ];
    public function device()
    {
        return $this->belongsTo(Devise::class);
    }
}

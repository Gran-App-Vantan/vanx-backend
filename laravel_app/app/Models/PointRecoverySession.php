<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointRecoverySession extends Model
{
    //
    protected $fillable = [
        'token',
        'amount',
        'service_name',
        'description',
        'type',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',  // 日付文字列をCarbonインスタンスに変換
    ];
    
    public function users()
    {
        return $this->belongsToMany(User::class, 'point_recovery_users', 'point_recovery_session_id', 'user_id')
                    ->withTimestamps();
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'user_path',
        'password',
        'user_icon',
        'biography',
        'game_play_flag',
        'user_job',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function points()
    {
        return $this->hasOne(Point::class);
    }
    
    public function pointlogs()
    {
        return $this->hasMany(Pointlog::class);
    }
    
    public function point_sessions()
    {
        return $this->hasMany(PointSession::class);
    }
    
    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    
    /**
     * ユーザーが作成した投稿へのリアクションを取得
     */
    public function post_reactions()
    {
        return $this->hasMany(PostReaction::class);
    }

    /**
     * ユーザーアイコンの絶対URLを取得
     */
    public function getUserIconAttribute($value): ?string
    {
        if ($value) {
            if ($value == 'default_user_icon' || $value == 'assets/images/default_user_icon.svg') {
                return url('/assets/images/default_user_icon.svg');
            }
            if (preg_match('/^https?:\/\//', $value) || str_starts_with($value, '/api/storage/') || str_starts_with($value, 'api/storage/')) {
                return $value;
            }
            // 独自の/storage/{path}エンドポイントを使用
            return url('/api/storage/' . $value);
        }
        return $value;
    }
}

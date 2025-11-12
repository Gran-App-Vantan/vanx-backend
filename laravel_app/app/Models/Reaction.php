<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Reaction extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'reaction_name',
        'reaction_image',
        'reaction_type'
    ];

    public function post_reactions()
    {
        return $this->hasMany(PostReaction::class);
    }

    /**
     * Get the reaction image URL.
     */
    protected function reactionImage(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => asset('storage/' . $value),
        );
    }
}

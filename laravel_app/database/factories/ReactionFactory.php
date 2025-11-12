<?php

namespace Database\Factories;

use App\Models\Reaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReactionFactory extends Factory
{
    protected $model = Reaction::class;

    public function definition(): array
    {
        return [
            'reaction_name' => fake()->word(),
            'reaction_image' => fake()->imageUrl(),
            'reaction_type' => fake()->randomElement(['face', 'nature', 'food', 'activity', 'travel', 'object', 'symbol']),
        ];
    }
} 
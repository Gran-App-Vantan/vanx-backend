<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\UserReactionPost;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModelRelationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_post_relation()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Post::class, $user->posts->first());
        $this->assertEquals($user->id, $post->user_id);
    }

    public function test_post_reaction_relation()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $reaction = Reaction::factory()->create();
        
        $userReactionPost = UserReactionPost::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'reaction_id' => $reaction->id
        ]);

        $this->assertInstanceOf(UserReactionPost::class, $post->used_reactions->first());
        $this->assertEquals($post->id, $userReactionPost->post_id);
    }

    public function test_user_reaction_relation()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $reaction = Reaction::factory()->create();
        
        $userReactionPost = UserReactionPost::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'reaction_id' => $reaction->id
        ]);

        $this->assertInstanceOf(UserReactionPost::class, $user->used_reactions->first());
        $this->assertEquals($user->id, $userReactionPost->user_id);
    }
} 
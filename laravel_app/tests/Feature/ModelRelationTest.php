<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\PostReaction;
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
        
        $postReaction = PostReaction::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'reaction_id' => $reaction->id
        ]);

        $this->assertInstanceOf(PostReaction::class, $post->post_reactions->first());
        $this->assertEquals($post->id, $postReaction->post_id);
    }

    public function test_user_reaction_relation()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $reaction = Reaction::factory()->create();
        
        $postReaction = PostReaction::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'reaction_id' => $reaction->id
        ]);
    
        // ユーザーが作成したリアクションを取得
        $userPostReaction = $user->post_reactions()->first();
        
        $this->assertInstanceOf(PostReaction::class, $userPostReaction);
        $this->assertEquals($user->id, $userPostReaction->user_id);
        $this->assertEquals($post->id, $userPostReaction->post_id);
        $this->assertEquals($reaction->id, $userPostReaction->reaction_id);
    }
}
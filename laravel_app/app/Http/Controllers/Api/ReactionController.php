<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PostReaction;
use App\Models\Reaction;
use App\Http\Requests\ReactionsGetRequest;

class ReactionController extends Controller
{
    public function reactions(ReactionsGetRequest $request)
    {   
        $validated = $request->validated();
        $category = $validated['category'];
        if ($category == 'all'){
            $reactions = Reaction::paginate(25);
        } else {
            $reactions = Reaction::where('reaction_type',$category)->paginate(25);
        }
        return response()->json([
            'success' => true,
            'message' => $category.'のリアクションを取得しました',
            'reactions' => $reactions
        ]);
    }


    public function reaction(Request $request, $post_id)
    {
        $request->validate([
            'reaction_id' => 'required|exists:reactions,id'
        ]);

        $user = $request->user();
        $reaction = Reaction::findOrFail($request->reaction_id);
        
        // Check if the user has already reacted with this reaction type
        $existingReaction = PostReaction::where('post_id', $post_id)
            ->where('user_id', $user->id)
            ->where('reaction_id', $reaction->id)
            ->first();

        if ($existingReaction) {
            // Remove the reaction if it exists
            $existingReaction->delete();
            $message = 'リアクションを解除しました';
            $isReacted = false;
        } else {
            // Add the reaction if it doesn't exist
            PostReaction::create([
                'post_id' => $post_id,
                'user_id' => $user->id,
                'reaction_id' => $reaction->id,
            ]);
            $message = 'リアクションを追加しました';
            $isReacted = true;
        }

        // Get the updated reaction count for this post and reaction type
        $reactionCount = PostReaction::where('post_id', $post_id)
            ->where('reaction_id', $reaction->id)
            ->count();

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'is_reacted' => $isReacted,
                'reaction_count' => $reactionCount
            ]
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PostReaction;
use App\Models\Reaction;
use App\Models\Post;
use App\Http\Requests\ReactionsGetRequest;
use App\Http\Requests\ReactionRequest;

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


    public function reaction(ReactionRequest $request, $post_id)
    {
        $user = $request->user();
        $reaction = $request->input('reaction_id');
        $searchConditions = [
            'post_id' => $post_id,
            'user_id' => $user->id,
            'reaction_id' => $reaction,
        ];

        $reaction = PostReaction::firstOrCreate($searchConditions);
        if ($reaction->wasRecentlyCreated) {
            $total_count = PostReaction::where('post_id', $post_id)->count();
            return response()->json([
                'success' => true,
                'message' => 'リアクションを追加しました',
                'action' => 'created',
                'data' => [
                    'is_reacted' => true,
                    'reaction_count' => $total_count
                ]
            ]);
        } else {
            $user->post_reactions()->where($searchConditions)->delete();
            $total_count = PostReaction::where('post_id', $post_id)->count();
            return response()->json([
                'success' => true,
                'message' => 'リアクションを解除しました',
                'action' => 'deleted',
                'data' => [
                    'is_reacted' => false,
                    'reaction_count' => $total_count
                ]
            ]);
        }
    }
}

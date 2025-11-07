<?php

namespace App\Http\Controllers\TestControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;    
use App\Models\User;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\PostReaction;
use App\Models\Point;
use App\Models\PointLog;
use App\Models\Booth;
use App\Models\RuleBook;
use App\Models\RuleImageFile;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Provider\Node\FallbackNodeProvider; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;



class TopController extends Controller
{
    function index() {
        $user = auth()->user();
        return view('test.top' , compact("user"));
    }
    function register(Request $request) {
        $error = $request["error"];
        return view("test.account.register", ["error"=> $error]);
    }
    function register_store(Request $request) {
        $name = $request["name"];
        $password = $request["password"];
        $checked_password = $request["checked_password"];
        if ($password !== $checked_password) {
            return redirect()->route("register")->with("error", "Password does not match");
        }

        $user = User::create([
            'name' => $name,
            'password' => Hash::make($password), // パスワードをハッシュ化して保存
            'user_icon' => 'user_icons/default_icon.png',
        ]);
        Point::create([
            'user_id' => $user->id
        ]);
        auth()->login($user);
        return redirect()->route("test.top");
    }
    function login(Request $request) {
        $error = $request["error"];
        return view("test.account.login", ["error"=> $error]);
    }

    function login_store(Request $request) {
        $name = $request->name;
        $password = $request->password;
        $user = User::where('name', $name)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            $error = "ユーザーID、またはパスワードが間違っています";
            return view('test.account.login', compact("error"));
        }
        auth()->login($user);
        return redirect()->route("test.top");
    }

    function edit(Request $request, $id) {
        $user = User::find(auth()->user()->id);
        return view("test.account.profile_edit", compact("user"));
    }
    function update(Request $request, $id) {
        $user = User::find($id);
        $updateData = [
            "name" => $request->input('name') ?: $user->name,
        ];

        if ($request->hasFile('user_icon')) {
            try {
                // ファイルのMIMEタイプを確認
                $mimeType = $request->file('user_icon')->getMimeType();
                
                // パブリックディスクを使用してファイルを保存
                $filename = 'user_icon' . $id . '.png';
                $path = Storage::disk('public')->put(
                    'user_icons/' . $filename,
                    file_get_contents($request->file('user_icon'))
                );
                
                if (!$path) {
                    throw new \Exception('ファイルの保存に失敗しました');
                }
                
                $updateData["user_icon"] = "user_icons/" . $filename;
                
                // デバッグ用のログ
                \Log::info("File uploaded successfully:", [
                    'path' => $path,
                    'filename' => "user_icon{$id}.png",
                    'original_name' => $request->file('user_icon')->getClientOriginalName(),
                    'mime_type' => $mimeType
                ]);
            } catch (\Exception $e) {
                \Log::error("File upload failed:", [
                    'error' => $e->getMessage(),
                    'user_id' => $id,
                    'file_size' => $request->file('user_icon')->getSize()
                ]);
                return redirect()->back()->with('error', 'アイコンのアップロードに失敗しました: ' . $e->getMessage());
            }
        }
        
        $user->update($updateData);
        return redirect()->route("test.top",['id' => $id])->with("success", "プロフィールが更新されました");
    }

    function profile(Request $request, $id) {
        $user = User::find($id);
        $my_point = Point::where("user_id","=", $user->id)->first();
        $point_rank = Point::orderBy("point", "desc")->get();
        $posts = Post::where('user_id', $id)->get();
        return view("test.account.profile_page", compact("user", "posts", "my_point", "point_rank"));
    }

    function wallet(Request $request) {
        $user = auth()->user();
        $my_point = Point::where("user_id","=", $user->id)->first();
        $point_logs = PointLog::where("user_id", $user->id)->get();
        return view("test.account.wallet", compact("user", "my_point", "point_logs"));
    }

    function ranking(Request $request) {
        $users_data = User::where('game_play_flag', true)
            ->withSum('points', 'point')
            ->orderByDesc('points_sum_point')
            ->get()
            ->map(function ($user_data) {
                return [
                    'user_name' => $user_data->name,
                    'user_icon' => $user_data->user_icon,
                    'point' => $user_data->points->point,
                ];
            });
        $user = auth()->user();
        $my_point = Point::where("user_id","=", $user->id)->first();
        return view("test.home.ranking", compact("user","users_data", "my_point"));
    }
    
    function show_posts(Request $request) {
        $posts = Post::with(['user', 'post_reactions','postfile'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view("test.home.posts", compact("posts"));
    }

    function post_create(Request $request) {
        return view("test.home.post");
    }
    function post_store(Request $request) {
        // バリデーションルールの定義
        $rules = [
            'content' => 'required|string|max:1000',
            'file.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,svg,gif,mp4,mov,webm|max:51200' // 画像と動画のみを許可、最大50MB
        ];

        // バリデーションエラーのカスタムメッセージ
        $messages = [
            'file.*.mimes' => '画像（jpg, jpeg, png, gif,svg）または動画（mp4, mov, webm）のみをアップロードできます。',
            'file.*.max' => 'ファイルサイズは50MB以下にしてください。',
            'total_size' => 'アップロードするファイルの合計サイズは50MB以下にしてください。'
        ];

        // バリデーションの実行
        $validated = $request->validate($rules, $messages);
        
        // 合計ファイルサイズのチェック
        $totalSize = 0;
        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $file) {
                $totalSize += $file->getSize();
            }
            if ($totalSize > 51200 * 1024) { // 50MB
                return back()->withErrors([
                    'total_size' => $messages['total_size']
                ])->withInput();
            }
        }

        $post = Post::create([
            'post_content' => $validated['content'],
            'user_id' => auth()->user()->id,
        ]);
        
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            foreach ($request->file('file') as $file) {
                $path = $file->storeAs('post_files', $file->getClientOriginalName());
                $post->postfile()->create([
                    'post_file_path' => $path,
                'post_file_type' => $file->getClientOriginalExtension() == 'mp4' || $file->getClientOriginalExtension() == 'mov' || $file->getClientOriginalExtension() == 'webm' ? 'video' : 'image',
                ]);
            }
        }
        return redirect()->route("post.show");
    }

    public function post_delete($id) {
        $post = Post::findOrFail($id);
        $post->delete();
        return redirect()->route("post.show");
    }
    function reaction(Request $request, $id) {
        $reactions = Reaction::all();
        $user = auth()->user();
        $used_reactions = PostReaction::where('post_id', $id)->where('user_id', $user->id)->get();
        return view("test.home.reactions", compact("id", "reactions", "used_reactions"));
    }
    function reaction_store(Request $request, $id) {
        $post = Post::findOrFail($id);
        
        // 既存のリアクションがあれば更新、なければ新規作成
        $postReaction = PostReaction::firstOrNew([
            'post_id' => $id,
            'user_id' => auth()->id(),
            'reaction_id' => $request->reaction_id
        ]);
    
        // 既存のリアクションがあれば削除（トグル動作）
        if ($postReaction->exists) {
            $postReaction->delete();
        } else {
            $postReaction->save();
        }
    
        return redirect()->route("post.show");
    }
    
    function reaction_delete(Request $request, $postId, $reactionId) {
        // Delete using query builder to handle composite key
        $deleted = PostReaction::where('post_id', $postId)
            ->where('user_id', auth()->id())
            ->where('reaction_id', $reactionId)
            ->delete();

        if ($deleted === 0) {
            abort(404, 'Reaction not found');
        }

        return redirect()->route("post.show")->with('success', 'リアクションが削除されました');
    }
    function floor_map(Request $request, $id) {
        $booths = Booth::where('booth_floor', $id)->get();
        return view("test.home.floor_map", compact("booths"));
    }

    function game_rule(Request $request, $id) {
        $rule_books = RuleBook::with('ruleimagefiles')->find($id);
        return view("test.home.game_rule", compact("rule_books"));
    }
}

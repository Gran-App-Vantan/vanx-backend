<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Document</title>
</head>
<style>
    a:hover,button:hover{
	opacity: 0.5;	/*透明度*/
	transition-duration: 0.3s;	/*変化する時間（秒）*/
}
</style>
<body>  
    <table>
        <tr style="border: 1px solid black;">
            <th>user_path</th>
            <th>user_icon</th>
            <th>name</th>
            <th>content</th>
            <th>reaction</th>
            <th>file</th>
            <th>created_at</th>
            <th>delete_post</th>
        </tr>
            @foreach ($posts as $post)
            <tr style="border: 1px solid black;">
                <td><a href="{{ route('profile',$post->user->id) }}">{{ $post->user->user_path ?? '' }}<a></td>
                <td><img src="{{ asset('storage/' . $post->user->user_icon) }}" alt="User Icon" style="width: 100px; height: 100px; border-radius: 1000px;"></td>
                <td>{{ $post->user->name ?? '' }}</td>
                <td>{{ $post->post_content }}</td>
                <td>
                    <a href="{{ route('reaction', ['id' => $post->id]) }}">+</a>
                    @foreach($post->post_reactions as $post_reaction)
                    <form action="{{ route('reaction.delete', ['post' => $post->id, 'reaction' => $post_reaction->reaction_id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" name="reaction_id" value="{{ $post_reaction->reaction_id }}" style="width: 16px;">
                                <img src="{{ asset('storage/' . $post_reaction->reaction->reaction_image) }}" alt="Reaction" style="width: 16px;">
                            </button>
                        </form>
                    @endforeach
                </td>
                <td>
                    @foreach($post->postfile as $file)
                        @if ($file->post_file_type == 'image')
                        <img src="{{ asset('storage/' . $file->post_file_path) }}" alt="Post File">
                        @else
                        <video src="{{ asset('storage/' . $file->post_file_path) }}" controls></video>
                        @endif
                    @endforeach
                </td>
                <td>{{ $post->created_at }}</td>
                <td>
                    <form action="{{ route('post.delete', ['id' => $post->id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
    </table>
    <a href="{{ route('post.create') }}">Post</a>
</body>
</html>
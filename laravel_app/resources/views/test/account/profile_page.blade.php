<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Profile Page</h1>
    <p>{{ $user->id }}</p>
    <p>{{ $user->name }}</p>
    <p>{{ $user->user_path }}</p>
    <p>{{ $user->user_icon }}</p>
    <p>{{ $my_point->point }}</p>
    <img src="{{ asset('storage/' . $user->user_icon) }}" alt="User Icon">
    <a href="{{ route('edit', $user->id) }}">Edit</a>
    <a href="{{ route('test.top') }}">Top</a>
    <a href="{{ route('point_wallet') }}">Point Wallet</a>

    <hr>
    <table>
        <tr style="border: 1px solid black;">
            <th>user_path</th>
            <th>user_icon</th>
            <th>name</th>
            <th>content</th>
            <th>reaction</th>
            <th>file</th>
            <th>created_at</th>
        </tr>
            @foreach ($posts as $post)
            <tr style="border: 1px solid black;">
                <td><a href="{{ route('profile',$post->user->id) }}">{{ $post->user->user_path ?? '' }}<a></td>
                <td><img src="{{ asset('storage/' . $post->user->user_icon) }}" alt="User Icon"></td>
                <td>{{ $post->user->name ?? '' }}</td>
                <td>{{ $post->post_content }}</td>
                <td>
                    <a href="{{ route('reaction', ['id' => $post->id]) }}">+</a>
                        @foreach($post->post_reactions as $post_reaction)
                        <form action="{{ route('reaction.delete', $post_reaction->id) }}" method="POST" style="width: 16px;">
                                @csrf
                                @method('DELETE')
                            <button type=submit name="{{ $post_reaction->id }}" value="{{ $post_reaction->id }}" style="width: 16px;">
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
            </tr>
            @endforeach
    </table>

    <hr>
    <table>
        <tr style="border: 1px solid black;">
            <th>point</th>
        </tr>
            @foreach ($point_rank as $ranking)
            <tr style="border: 1px solid black;">
                <td>{{ $ranking->id }}</td>
            </tr>
            @endforeach
    </table>

</body>
</html>
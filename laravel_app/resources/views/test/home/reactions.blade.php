<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1>Reactions</h1>
    <dev style="display:flex;">
        @foreach ($reactions as $reaction)
            @if(in_array($reaction->id, $used_reactions->pluck('reaction_id')->toArray()))
                @php
                    $postReaction = $used_reactions->where('reaction_id', $reaction->id)->first();
                @endphp
                <form action="{{ route('reaction.delete', ['post' => $id, 'reaction' => $postReaction->reaction_id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" name="reaction_id" value="{{ $postReaction->reaction_id }}">
                        <img src="{{ asset('storage/' . $reaction->reaction_image) }}" alt="Reaction" style="width: 16px">
                    </button>
                </form>
            @else
                <form action="{{ route('reaction.store', ['id' => $id]) }}" method="POST">
                    @csrf
                    @method('POST')
                    <button type="submit" name="reaction_id" value="{{ $reaction->id }}">
                        <img src="{{ asset('storage/' . $reaction->reaction_image) }}" alt="Reaction" style="width: 16px">
                    </button>
                </form>
            @endif
        @endforeach
    </dev>
    <a href="{{ route('post.show') }}">戻る</a>
</body>
</html>
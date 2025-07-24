<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Top</title>
</head>
<body>
    <h1>Top Page</h1>
    あああああああ
    @if (auth()->user())
        <p>{{ auth()->user()->name }}</p>
        <p>{{ auth()->user()->user_path }}</p>
        <p>{{ auth()->user()->user_icon }}</p>
        <img src="{{ asset('storage/' . auth()->user()->user_icon) }}" alt="User Icon">
        <a href="{{ route('edit', auth()->user()->id) }}">Edit</a>
    @else
        <p>ログインしていません</p>
    @endif
    
    <a href="{{ route('post.show') }}">Post</a>
    <a href="{{ route('login') }}">Login</a>
    <a href="{{ route('register') }}">Register</a>
    <a href="{{ route('wallet') }}">Wallet</a>
    <a href="{{ route('ranking') }}">Ranking</a>
    <a href="{{ route('floor_map', 1) }}">Floor Map</a>
    <a href="{{ route('game_rule', 1) }}">Game Rule</a>
    
</body>
</html>
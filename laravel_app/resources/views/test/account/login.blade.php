<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Login</h1>
    <form action="{{route('login.store')}}" method="POST">
        @csrf
        <label for="user_path">user_path</label>
        <input type="text" name="user_path">

        <label for="password">password</label>
        <input type="password" name="password">

        <button type="submit">Login</button>
    </form>
    <a href="{{ route('register') }}">Register</a>
</body>
</html>
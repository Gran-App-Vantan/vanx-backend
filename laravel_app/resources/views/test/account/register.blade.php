<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Sign Up</h1>
    <form action="{{route('register.store')}}" method="POST">
        @csrf
        <label for="name">name</label>
        <input type="text" name="name">

        <label for="password">password</label>
        <input type="password" name="password">

        <label for="checked_password">checked_password</label>
        <input type="password" name="checked_password">
        
        <button type="submit">sign_up</button>
    </form>
</body>
</html>
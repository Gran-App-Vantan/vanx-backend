<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Wallet Page</h1>
    <p>{{ $user->id }}</p>
    <p>{{ $user->name }}</p>
    <p>{{ $user->user_path }}</p>
    <p>{{ $user->user_icon }}</p>
    <p>{{ $my_point->point }}</p>
    <img src="{{ asset('storage/' . $user->user_icon) }}" alt="User Icon">
    <hr>
    <table>
        <tr>
            <th>point</th>
            <th>user_icon</th>
            <th>user_name</th>
        </tr>
        @foreach ($users_data as $user_data)
        <tr>
            <td>{{ $user_data['point'] }}</td>
            <td><img src="{{ asset('storage/' . $user_data['user_icon']) }}" alt="User Icon"></td>
            <td>{{ $user_data['user_name'] }}</td>
        </tr>
        @endforeach
    </table>


</body>
</html>
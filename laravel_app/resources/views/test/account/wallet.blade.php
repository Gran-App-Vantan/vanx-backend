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
    <table>
        <tr>
            <th>log date</th>
            <th>service</th>
            <th>amount</th>
        </tr>
        @foreach ($point_logs as $point_log)
        <tr>
            <td>{{ $point_log->created_at }}</td>
            <td>{{ $point_log->service_name }}</td>
            <td>{{ $point_log->point_amount }}</td>
        </tr>
        @endforeach
    </table>


</body>
</html>
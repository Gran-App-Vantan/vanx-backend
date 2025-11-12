<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>floor_map</h1>
    <table>
        <tr>
            <th>booth_name</th>
            <th>booth_floor</th>
            <th>created_group</th>
            <th>booth_content</th>
            <th>booth_image</th>
        </tr>
        @foreach ($booths as $booth)
            <tr>
                <td>{{ $booth->booth_name }}</td>
                <td>{{ $booth->booth_floor }}</td>
                <td>{{ $booth->created_group }}</td>
                <td>{{ $booth->booth_content }}</td>
                <td>{{ $booth->booth_image }}</td>
            </tr>
        @endforeach
    </table>

</body>
</html>
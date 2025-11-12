<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>game_rule</h1>
    <table>
        <tr>
            <th>game_name</th>
            <th>rule_content</th>
            <th>rule_image</th>
        </tr>
        @if($rule_books)
        <tr>
            <td>{{ $rule_books->game_name ?? '' }}</td>
            <td>{{ $rule_books->rule_content ?? '' }}</td>
            <td>
                @foreach($rule_books->ruleimagefiles as $image)
                    <img src="{{ asset('storage/' . $image->rule_image_path) }}" alt="Rule Image">
                    <p>{{ $image->rule_image_path }}</p>
                @endforeach
            </td>
        </tr>
    @endif
    </table>

</body>
</html>
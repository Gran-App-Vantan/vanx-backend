<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Profile Edit</h1>
    <p>Current user_icon: {{ $user->user_icon }}</p>
    <p>Current user ID: {{ $user->id }}</p>
    <p>Expected file path: {{ 'user_icon' . $user->id . '.png' }}</p>
    {{-- <?php dd(['id' => $user["id"]]); ?> --}}
    <form action="{{route('edit.update', ['id' => $user["id"]])}}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="file" name="user_icon">
        <label for="name">name</label>
        <input type="text" name="name" value="{{ $user->name }}">

        <label for="user_path">user_id</label>
        <input type="text" name="user_path" value="{{ $user->user_path }}">

        <button type="submit">complete</button>
    </form>
    
</body>
</html>
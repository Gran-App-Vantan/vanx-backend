<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Post</h1>
    @if ($errors->any())
        <div class="error-messages">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif
    <form action="{{ route('post.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="content">content</label>
        <input type="text" name="content">
        <label for="file">file</label>
        <input type="file" name="file[]" accept="image/*,video/*" multiple>
        <button type="submit">+</button>
    </form>
    
    <a href="{{ route('post.show') }}">戻る</a>
</body>
</html>
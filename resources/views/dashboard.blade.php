<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">

<div class="container">

    <h2>Welcome 💍</h2>

    <div class="card p-3">
        <img src="{{ $user['avatar'] }}" width="80" class="rounded-circle">

        <h4>{{ $user['name'] }}</h4>
        <p>{{ $user['email'] }}</p>

        <a href="/logout" class="btn btn-danger">Logout</a>
    </div>

</div>


<div class="mt-4">

    <h4>Upload Wedding Gallery 💍</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="/upload-multiple" method="POST" enctype="multipart/form-data">
        @csrf

        <input type="file" name="files[]" class="form-control mb-3" multiple required>

        <button class="btn btn-success">
            Upload All to Google Drive
        </button>
    </form>

</div>

</body>
</html>
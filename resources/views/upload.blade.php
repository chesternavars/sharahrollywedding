<h2>💍 Navarroza Wedding Upload</h2>

@if(session('success'))
    <p style="color:green">{{ session('success') }}</p>
@endif

@if ($errors->any())
    <div style="color:red;">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<form action="/upload" method="POST" enctype="multipart/form-data">
    @csrf

    <label>Select photo/video:</label><br><br>
    <input type="file" name="file" required accept="image/*,video/*"><br><br>

    <button type="submit">📤 Upload to Wedding Album</button>
</form>

<hr>

<a href="/view" target="_blank">🖼️ View Wedding Album</a>
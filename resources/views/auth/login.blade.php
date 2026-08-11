<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Masuk</title>
</head>
<body>
    <h1>Masuk</h1>

    @if ($errors->any())
        <div style="color: red;">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}">
        @csrf
        <label>Email</label><br>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus><br><br>

        <label>Kata Sandi</label><br>
        <input type="password" name="password" required><br><br>

        <label><input type="checkbox" name="remember" value="1"> Ingat saya</label><br><br>
        <button type="submit">Masuk</button>
    </form>
</body>
</html>

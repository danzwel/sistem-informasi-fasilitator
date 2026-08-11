<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Import Data Pelatihan</title>
</head>
<body>
    <h1>Import Data Pelatihan dari Excel</h1>
    <a href="{{ route('fasilitator.index') }}">← Kembali ke Daftar Fasilitator</a>
    <br><br>

    @if (session('error'))
        <p style="color: red;">{{ session('error') }}</p>
    @endif

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('import.preview') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label>Pilih File Excel (.xlsx):</label><br>
        <input type="file" name="file" accept=".xlsx,.xls"><br><br>
        <button type="submit">Upload & Preview</button>
    </form>
</body>
</html>
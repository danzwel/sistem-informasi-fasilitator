<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Riwayat Pendidikan</title>
</head>
<body>
    <h1>Tambah Riwayat Pendidikan</h1>
    <p>Untuk: <strong>{{ $fasilitator->nama }}</strong></p>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('fasilitator.riwayat-pendidikan.store', $fasilitator) }}" method="POST">
        @csrf

        <label>Jenjang (contoh: S1 Administrasi Pendidikan):</label><br>
        <input type="text" name="jenjang" value="{{ old('jenjang') }}"><br><br>

        <label>Institusi/Universitas:</label><br>
        <input type="text" name="institusi" value="{{ old('institusi') }}"><br><br>

        <label>Kota:</label><br>
        <input type="text" name="kota" value="{{ old('kota') }}"><br><br>

        <label>Tahun Mulai:</label><br>
        <input type="number" name="tahun_mulai" value="{{ old('tahun_mulai') }}" placeholder="2019"><br><br>

        <label>Tahun Selesai:</label><br>
        <input type="number" name="tahun_selesai" value="{{ old('tahun_selesai') }}" placeholder="2023"><br><br>

        <button type="submit">Simpan</button>
    </form>

    <br>
    <a href="{{ route('fasilitator.show', $fasilitator) }}">← Kembali ke Detail Fasilitator</a>
</body>
</html>
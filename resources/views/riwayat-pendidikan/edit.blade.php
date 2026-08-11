<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Riwayat Pendidikan</title>
</head>
<body>
    <h1>Edit Riwayat Pendidikan</h1>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('riwayat-pendidikan.update', $riwayatPendidikan) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Jenjang:</label><br>
        <input type="text" name="jenjang" value="{{ old('jenjang', $riwayatPendidikan->jenjang) }}"><br><br>

        <label>Institusi/Universitas:</label><br>
        <input type="text" name="institusi" value="{{ old('institusi', $riwayatPendidikan->institusi) }}"><br><br>

        <label>Kota:</label><br>
        <input type="text" name="kota" value="{{ old('kota', $riwayatPendidikan->kota) }}"><br><br>

        <label>Tahun Mulai:</label><br>
        <input type="number" name="tahun_mulai" value="{{ old('tahun_mulai', $riwayatPendidikan->tahun_mulai) }}"><br><br>

        <label>Tahun Selesai:</label><br>
        <input type="number" name="tahun_selesai" value="{{ old('tahun_selesai', $riwayatPendidikan->tahun_selesai) }}"><br><br>

        <button type="submit">Update</button>
    </form>

    <br>
    <a href="{{ route('fasilitator.show', $riwayatPendidikan->fasilitator_id) }}">← Kembali ke Detail Fasilitator</a>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Fasilitator</title>
</head>
<body>
    <h1>Tambah Fasilitator</h1>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('fasilitator.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <label>Nama:</label><br>
        <input type="text" name="nama" value="{{ old('nama') }}"><br><br>

        <label>Gelar:</label><br>
        <input type="text" name="gelar" value="{{ old('gelar') }}"><br><br>

        <label>Tempat Lahir:</label><br>
        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}"><br><br>

        <label>Tanggal Lahir:</label><br>
        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"><br><br>

        <label>NIK:</label><br>
        <input type="text" name="nik" value="{{ old('nik') }}"><br><br>

        <label>NIP:</label><br>
        <input type="text" name="nip" value="{{ old('nip') }}"><br><br>

        <label>Pangkat/Gol.:</label><br>
        <input type="text" name="pangkat" value="{{ old('pangkat') }}"><br><br>

        <label>Jabatan:</label><br>
        <input type="text" name="jabatan" value="{{ old('jabatan') }}"><br><br>

        <label>Unit Kerja:</label><br>
        <input type="text" name="unit_kerja" value="{{ old('unit_kerja') }}"><br><br>

        <label>Alamat Kantor:</label><br>
        <textarea name="alamat_kantor">{{ old('alamat_kantor') }}</textarea><br><br>

        <label>Alamat Rumah:</label><br>
        <textarea name="alamat_rumah">{{ old('alamat_rumah') }}</textarea><br><br>

        <label>No. HP:</label><br>
        <input type="text" name="no_hp" value="{{ old('no_hp') }}"><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="{{ old('email') }}"><br><br>

        <label>Foto:</label><br>
        <input type="file" name="foto" accept="image/*"><br><br>

        <label>Tanda Tangan (TTD):</label><br>
        <input type="file" name="ttd" accept="image/*"><br><br>

        <button type="submit">Simpan</button>
    </form>

    <br>
    <a href="{{ route('fasilitator.index') }}">← Kembali ke Daftar</a>
</body>
</html>
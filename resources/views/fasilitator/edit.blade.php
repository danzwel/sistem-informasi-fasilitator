<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Fasilitator</title>
</head>
<body>
    <h1>Edit Fasilitator</h1>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('fasilitator.update', $fasilitator) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label>Nama:</label><br>
        <input type="text" name="nama" value="{{ old('nama', $fasilitator->nama) }}"><br><br>

        <label>Gelar:</label><br>
        <input type="text" name="gelar" value="{{ old('gelar', $fasilitator->gelar) }}"><br><br>

        <label>Tempat Lahir:</label><br>
        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $fasilitator->tempat_lahir) }}"><br><br>

        <label>Tanggal Lahir:</label><br>
        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $fasilitator->tanggal_lahir) }}"><br><br>

        <label>NIK:</label><br>
        <input type="text" name="nik" value="{{ old('nik', $fasilitator->nik) }}"><br><br>

        <label>NIP:</label><br>
        <input type="text" name="nip" value="{{ old('nip', $fasilitator->nip) }}"><br><br>

        <label>Pangkat/Gol.:</label><br>
        <input type="text" name="pangkat" value="{{ old('pangkat', $fasilitator->pangkat) }}"><br><br>

        <label>Jabatan:</label><br>
        <input type="text" name="jabatan" value="{{ old('jabatan', $fasilitator->jabatan) }}"><br><br>

        <label>Unit Kerja:</label><br>
        <input type="text" name="unit_kerja" value="{{ old('unit_kerja', $fasilitator->unit_kerja) }}"><br><br>

        <label>Alamat Kantor:</label><br>
        <textarea name="alamat_kantor">{{ old('alamat_kantor', $fasilitator->alamat_kantor) }}</textarea><br><br>

        <label>Alamat Rumah:</label><br>
        <textarea name="alamat_rumah">{{ old('alamat_rumah', $fasilitator->alamat_rumah) }}</textarea><br><br>

        <label>No. HP:</label><br>
        <input type="text" name="no_hp" value="{{ old('no_hp', $fasilitator->no_hp) }}"><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="{{ old('email', $fasilitator->email) }}"><br><br>

        <label>Status:</label><br>
        <select name="status">
            <option value="aktif" {{ old('status', $fasilitator->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="nonaktif" {{ old('status', $fasilitator->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
        </select><br><br>

        <label>Foto:</label><br>
        @if ($fasilitator->foto)
            <img src="{{ asset('storage/' . $fasilitator->foto) }}" width="100"><br>
        @endif
        <input type="file" name="foto" accept="image/*"><br><br>

        <label>Tanda Tangan (TTD):</label><br>
        @if ($fasilitator->ttd)
            <img src="{{ asset('storage/' . $fasilitator->ttd) }}" width="150"><br>
        @endif
        <input type="file" name="ttd" accept="image/*"><br><br>

        <button type="submit">Update</button>
    </form>

    <br>
    <a href="{{ route('fasilitator.index') }}">← Kembali ke Daftar</a>
</body>
</html>
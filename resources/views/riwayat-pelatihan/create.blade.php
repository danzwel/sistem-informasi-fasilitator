<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Riwayat Pelatihan</title>
</head>
<body>
    <h1>Tambah Riwayat Pelatihan</h1>
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

    <form action="{{ route('fasilitator.riwayat-pelatihan.store', $fasilitator) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <label>Kategori:</label><br>
        <select name="kategori">
            <option value="materi_diajarkan" {{ $kategori == 'materi_diajarkan' ? 'selected' : '' }}>Materi yang Diajarkan</option>
            <option value="pelatihan_terkait" {{ $kategori == 'pelatihan_terkait' ? 'selected' : '' }}>Pendidikan/Pelatihan Terkait Materi</option>
            <option value="pengalaman_mengajar" {{ $kategori == 'pengalaman_mengajar' ? 'selected' : '' }}>Pengalaman Melatih/Mengajar</option>
        </select><br><br>

        <label>Nama Kegiatan:</label><br>
        <input type="text" name="nama_kegiatan" value="{{ old('nama_kegiatan') }}"><br><br>

        <label>Penyelenggara:</label><br>
        <input type="text" name="penyelenggara" value="{{ old('penyelenggara') }}"><br><br>

        <label>Tanggal:</label><br>
        <input type="date" name="tanggal" value="{{ old('tanggal') }}"><br><br>

        <label>Keterangan:</label><br>
        <textarea name="keterangan">{{ old('keterangan') }}</textarea><br><br>

        <label>Sertifikat (PDF/JPG/PNG):</label><br>
        <input type="file" name="sertifikat"><br><br>

        <button type="submit">Simpan</button>
    </form>

    <br>
    <a href="{{ route('fasilitator.show', $fasilitator) }}">← Kembali ke Detail Fasilitator</a>
</body>
</html>
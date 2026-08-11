<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Riwayat Pelatihan</title>
</head>
<body>
    <h1>Edit Riwayat Pelatihan</h1>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('riwayat-pelatihan.update', $riwayatPelatihan) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label>Kategori:</label><br>
        <select name="kategori">
            <option value="materi_diajarkan" {{ $riwayatPelatihan->kategori == 'materi_diajarkan' ? 'selected' : '' }}>Materi yang Diajarkan</option>
            <option value="pelatihan_terkait" {{ $riwayatPelatihan->kategori == 'pelatihan_terkait' ? 'selected' : '' }}>Pendidikan/Pelatihan Terkait Materi</option>
            <option value="pengalaman_mengajar" {{ $riwayatPelatihan->kategori == 'pengalaman_mengajar' ? 'selected' : '' }}>Pengalaman Melatih/Mengajar</option>
        </select><br><br>

        <label>Nama Kegiatan:</label><br>
        <input type="text" name="nama_kegiatan" value="{{ old('nama_kegiatan', $riwayatPelatihan->nama_kegiatan) }}"><br><br>

        <label>Penyelenggara:</label><br>
        <input type="text" name="penyelenggara" value="{{ old('penyelenggara', $riwayatPelatihan->penyelenggara) }}"><br><br>

        <label>Tanggal:</label><br>
        <input type="date" name="tanggal" value="{{ old('tanggal', $riwayatPelatihan->tanggal) }}"><br><br>

        <label>Keterangan:</label><br>
        <textarea name="keterangan">{{ old('keterangan', $riwayatPelatihan->keterangan) }}</textarea><br><br>

        <label>Sertifikat saat ini:</label><br>
        @if ($riwayatPelatihan->sertifikat)
            <a href="{{ asset('storage/' . $riwayatPelatihan->sertifikat) }}" target="_blank">Lihat sertifikat</a>
        @else
            <span style="color:red;">Belum ada sertifikat</span>
        @endif
        <br>
        <label>Ganti Sertifikat (kosongkan jika tidak ingin ganti):</label><br>
        <input type="file" name="sertifikat"><br><br>

        <button type="submit">Update</button>
    </form>

    <br>
    <a href="{{ route('fasilitator.show', $riwayatPelatihan->fasilitator_id) }}">← Kembali ke Detail Fasilitator</a>
</body>
</html>
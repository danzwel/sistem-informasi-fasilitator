<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Fasilitator - {{ $fasilitator->nama }}</title>
</head>
<body>
    <a href="{{ route('fasilitator.index') }}">← Kembali ke Daftar</a>
    <br><br>

    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <h1>{{ $fasilitator->nama }}</h1>

    @if ($fasilitator->foto)
        <img src="{{ asset('storage/' . $fasilitator->foto) }}" width="120"><br><br>
    @endif

    <table border="1" cellpadding="6">
        <tr><td>Tempat/Tanggal Lahir</td><td>{{ $fasilitator->tempat_lahir ?? '-' }}, {{ $fasilitator->tanggal_lahir ? \Carbon\Carbon::parse($fasilitator->tanggal_lahir)->format('d-m-Y') : '-' }}</td></tr>
        <tr><td>NIK</td><td>{{ $fasilitator->nik ?? '-' }}</td></tr>
        <tr><td>NIP</td><td>{{ $fasilitator->nip ?? '-' }}</td></tr>
        <tr><td>Pangkat/Gol.</td><td>{{ $fasilitator->pangkat ?? '-' }}</td></tr>
        <tr><td>Jabatan</td><td>{{ $fasilitator->jabatan ?? '-' }}</td></tr>
        <tr><td>Unit Kerja</td><td>{{ $fasilitator->unit_kerja ?? '-' }}</td></tr>
        <tr><td>Alamat Kantor</td><td>{{ $fasilitator->alamat_kantor ?? '-' }}</td></tr>
        <tr><td>Alamat Rumah</td><td>{{ $fasilitator->alamat_rumah ?? '-' }}</td></tr>
        <tr><td>No. HP</td><td>{{ $fasilitator->no_hp ?? '-' }}</td></tr>
        <tr><td>Email</td><td>{{ $fasilitator->email ?? '-' }}</td></tr>
        <tr><td>Status</td><td>{{ $fasilitator->status }}</td></tr>
    </table>

    <br>

    {{-- Riwayat Pendidikan --}}
    <h2>Riwayat Pendidikan</h2>
    <a href="{{ route('fasilitator.riwayat-pendidikan.create', $fasilitator) }}">+ Tambah</a>
    <table border="1" cellpadding="6">
        <tr>
            <th>Jenjang</th>
            <th>Institusi</th>
            <th>Kota</th>
            <th>Tahun</th>
            <th>Aksi</th>
        </tr>
        @forelse ($riwayatPendidikan as $item)
            <tr>
                <td>{{ $item->jenjang }}</td>
                <td>{{ $item->institusi }}</td>
                <td>{{ $item->kota ?? '-' }}</td>
                <td>{{ $item->tahun_mulai ?? '-' }} - {{ $item->tahun_selesai ?? '-' }}</td>
                <td>
                    <a href="{{ route('riwayat-pendidikan.edit', $item) }}">Edit</a>
                    |
                    <form action="{{ route('riwayat-pendidikan.destroy', $item) }}" method="POST" style="display:inline" onsubmit="return confirm('Yakin hapus data ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">Belum ada data.</td></tr>
        @endforelse
    </table>

    <br>

    {{-- Materi yang Diajarkan --}}
    <h2>Materi yang Diajarkan</h2>
    <a href="{{ route('fasilitator.riwayat-pelatihan.create', [$fasilitator, 'kategori' => 'materi_diajarkan']) }}">+ Tambah</a>
    <table border="1" cellpadding="6">
        <tr>
            <th>Nama Kegiatan</th>
            <th>Penyelenggara</th>
            <th>Tanggal</th>
            <th>Sertifikat</th>
            <th>Aksi</th>
        </tr>
        @forelse ($materiDiajarkan as $item)
            <tr>
                <td>{{ $item->nama_kegiatan }}</td>
                <td>{{ $item->penyelenggara ?? '-' }}</td>
                <td>{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') : '-' }}</td>
                <td>
                    @if ($item->sertifikat)
                        <a href="{{ asset('storage/' . $item->sertifikat) }}" target="_blank">Lihat</a>
                    @else
                        <span style="color:red;">Belum ada</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('riwayat-pelatihan.edit', $item) }}">Edit</a>
                    |
                    <form action="{{ route('riwayat-pelatihan.destroy', $item) }}" method="POST" style="display:inline" onsubmit="return confirm('Yakin hapus data ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">Belum ada data.</td></tr>
        @endforelse
    </table>

    <br>

    {{-- Pendidikan/Pelatihan Terkait Materi --}}
    <h2>Pendidikan/Pelatihan yang Terkait Materi</h2>
    <a href="{{ route('fasilitator.riwayat-pelatihan.create', [$fasilitator, 'kategori' => 'pelatihan_terkait']) }}">+ Tambah</a>
    <table border="1" cellpadding="6">
        <tr>
            <th>Nama Kegiatan</th>
            <th>Penyelenggara</th>
            <th>Tanggal</th>
            <th>Sertifikat</th>
            <th>Aksi</th>
        </tr>
        @forelse ($pelatihanTerkait as $item)
            <tr>
                <td>{{ $item->nama_kegiatan }}</td>
                <td>{{ $item->penyelenggara ?? '-' }}</td>
                <td>{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') : '-' }}</td>
                <td>
                    @if ($item->sertifikat)
                        <a href="{{ asset('storage/' . $item->sertifikat) }}" target="_blank">Lihat</a>
                    @else
                        <span style="color:red;">Belum ada</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('riwayat-pelatihan.edit', $item) }}">Edit</a>
                    |
                    <form action="{{ route('riwayat-pelatihan.destroy', $item) }}" method="POST" style="display:inline" onsubmit="return confirm('Yakin hapus data ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">Belum ada data.</td></tr>
        @endforelse
    </table>

    <br>

    {{-- Pengalaman Melatih/Mengajar --}}
    <h2>Pengalaman Melatih/Mengajar</h2>
    <a href="{{ route('fasilitator.riwayat-pelatihan.create', [$fasilitator, 'kategori' => 'pengalaman_mengajar']) }}">+ Tambah</a>
    <table border="1" cellpadding="6">
        <tr>
            <th>Nama Kegiatan</th>
            <th>Penyelenggara</th>
            <th>Tanggal</th>
            <th>Sertifikat</th>
            <th>Aksi</th>
        </tr>
        @forelse ($pengalamanMengajar as $item)
            <tr>
                <td>{{ $item->nama_kegiatan }}</td>
                <td>{{ $item->penyelenggara ?? '-' }}</td>
                <td>{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') : '-' }}</td>
                <td>
                    @if ($item->sertifikat)
                        <a href="{{ asset('storage/' . $item->sertifikat) }}" target="_blank">Lihat</a>
                    @else
                        <span style="color:red;">Belum ada</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('riwayat-pelatihan.edit', $item) }}">Edit</a>
                    |
                    <form action="{{ route('riwayat-pelatihan.destroy', $item) }}" method="POST" style="display:inline" onsubmit="return confirm('Yakin hapus data ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">Belum ada data.</td></tr>
        @endforelse
    </table>
</body>
</html>
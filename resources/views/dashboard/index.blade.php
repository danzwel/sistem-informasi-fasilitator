<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        .ringkasan { display: flex; gap: 15px; margin-bottom: 20px; }
        .kotak { border: 1px solid #333; padding: 12px 20px; text-align: center; }
        .kotak .angka { font-size: 24px; font-weight: bold; }
        .filter a { margin-right: 10px; }
        .filter a.aktif { font-weight: bold; text-decoration: underline; }
        .ok { color: green; }
        .kurang { color: red; }
    </style>
</head>
<body>
    <h1>Dashboard</h1>
    <a href="{{ route('fasilitator.index') }}">→ Lihat Semua Data Fasilitator</a>

    <div class="ringkasan">
        <div class="kotak">
            <div class="angka">{{ $totalFasilitator }}</div>
            <div>Total Fasilitator</div>
        </div>
        <div class="kotak">
            <div class="angka">{{ $totalAktif }}</div>
            <div>Fasilitator Aktif</div>
        </div>
        <div class="kotak">
            <div class="angka" style="color: {{ $totalBelumLengkap > 0 ? 'red' : 'green' }}">{{ $totalBelumLengkap }}</div>
            <div>Data Belum Lengkap</div>
        </div>
    </div>

    <h2>Monitoring Kelengkapan Data</h2>

    <div class="filter">
        <a href="{{ route('dashboard') }}" class="{{ $filter == 'semua' ? 'aktif' : '' }}">Semua</a>
        <a href="{{ route('dashboard', ['filter' => 'belum_lengkap']) }}" class="{{ $filter == 'belum_lengkap' ? 'aktif' : '' }}">Belum Lengkap</a>
        <a href="{{ route('dashboard', ['filter' => 'foto']) }}" class="{{ $filter == 'foto' ? 'aktif' : '' }}">Belum Foto</a>
        <a href="{{ route('dashboard', ['filter' => 'ttd']) }}" class="{{ $filter == 'ttd' ? 'aktif' : '' }}">Belum TTD</a>
        <a href="{{ route('dashboard', ['filter' => 'pendidikan']) }}" class="{{ $filter == 'pendidikan' ? 'aktif' : '' }}">Belum Riwayat Pendidikan</a>
        <a href="{{ route('dashboard', ['filter' => 'pelatihan']) }}" class="{{ $filter == 'pelatihan' ? 'aktif' : '' }}">Belum Riwayat Pelatihan</a>
        <a href="{{ route('dashboard', ['filter' => 'sertifikat']) }}" class="{{ $filter == 'sertifikat' ? 'aktif' : '' }}">Sertifikat Belum Lengkap</a>
    </div>

    <br>

    <table border="1" cellpadding="8">
        <tr>
            <th>Nama</th>
            <th>Foto</th>
            <th>TTD</th>
            <th>Riwayat Pendidikan</th>
            <th>Riwayat Pelatihan</th>
            <th>Sertifikat Lengkap</th>
            <th>Aksi</th>
        </tr>
        @forelse ($ditampilkan as $item)
            <tr>
                <td>{{ $item['fasilitator']->nama }}</td>
                <td class="{{ $item['foto'] ? 'ok' : 'kurang' }}">{{ $item['foto'] ? '✔ Ada' : '✘ Belum' }}</td>
                <td class="{{ $item['ttd'] ? 'ok' : 'kurang' }}">{{ $item['ttd'] ? '✔ Ada' : '✘ Belum' }}</td>
                <td class="{{ $item['pendidikan'] ? 'ok' : 'kurang' }}">{{ $item['pendidikan'] ? '✔ Ada' : '✘ Belum' }}</td>
                <td class="{{ $item['pelatihan'] ? 'ok' : 'kurang' }}">{{ $item['pelatihan'] ? '✔ Ada' : '✘ Belum' }}</td>
                <td class="{{ $item['sertifikat'] ? 'ok' : 'kurang' }}">{{ $item['sertifikat'] ? '✔ Lengkap' : '✘ Kurang' }}</td>
                <td>
                    <a href="{{ route('fasilitator.show', $item['fasilitator']) }}">Lengkapi</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7">Tidak ada data untuk filter ini.</td>
            </tr>
        @endforelse
    </table>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CV - {{ $fasilitator->nama }}</title>
    <style>
        body {
            font-family: "Helvetica", Arial, sans-serif;
            font-size: 12px;
            color: #000;
        }

        table.header-layout {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table.header-layout > tr > td {
            vertical-align: top;
            padding: 0;
        }

        .kolom-kiri {
            width: auto;
        }

        .kolom-kanan {
            width: 110px;
            text-align: center;
        }

        .nama {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .foto-box {
            width: 100px;
            height: 130px;
            border: 1px solid #000;
            text-align: center;
            font-size: 10px;
            line-height: 130px;
            margin: 0 auto;
        }

        .foto-box img {
            width: 100px;
            height: 130px;
            object-fit: cover;
            vertical-align: top;
        }

        table.bio {
            width: 100%;
            border-collapse: collapse;
        }

        table.bio td {
            padding: 2px 4px;
            vertical-align: top;
        }

        table.bio .label {
            width: 150px;
        }

        table.bio .titik-dua {
            width: 15px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
            margin-top: 16px;
            margin-bottom: 8px;
        }

        .pendidikan-item {
            margin-bottom: 10px;
        }

        table.pendidikan-baris1 {
            width: 100%;
            border-collapse: collapse;
        }

        table.pendidikan-baris1 td {
            vertical-align: top;
        }

        .pendidikan-tahun {
            text-align: right;
            font-weight: bold;
            white-space: nowrap;
            width: 130px;
        }

        .pendidikan-jenjang {
            margin-top: 2px;
        }

        ul.list-kegiatan {
            margin: 0;
            padding-left: 18px;
        }

        ul.list-kegiatan li {
            margin-bottom: 4px;
        }

        .footer {
            margin-top: 40px;
            text-align: right;
        }

        .footer .ttd-space {
            height: 70px;
        }

        .footer img {
            height: 60px;
        }
    </style>
</head>
<body>

    @php
        $fotoSrc = $fasilitator->foto ? ($forPdf ? public_path('storage/' . $fasilitator->foto) : asset('storage/' . $fasilitator->foto)) : null;
        $ttdSrc = $fasilitator->ttd ? ($forPdf ? public_path('storage/' . $fasilitator->ttd) : asset('storage/' . $fasilitator->ttd)) : null;
    @endphp

    <table class="header-layout">
        <tr>
            <td class="kolom-kiri">
                <div class="nama">{{ $fasilitator->nama }}{{ $fasilitator->gelar ? ', ' . $fasilitator->gelar : '' }}</div>

                <table class="bio">
                    <tr>
                        <td class="label">Tempat/Tanggal Lahir</td>
                        <td class="titik-dua">:</td>
                        <td>{{ $fasilitator->tempat_lahir ?? '-' }}{{ $fasilitator->tanggal_lahir ? ', ' . \Carbon\Carbon::parse($fasilitator->tanggal_lahir)->locale('id')->translatedFormat('d F Y') : '' }}</td>
                    </tr>
                    <tr>
                        <td class="label">NIK</td>
                        <td class="titik-dua">:</td>
                        <td>{{ $fasilitator->nik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">NIP</td>
                        <td class="titik-dua">:</td>
                        <td>{{ $fasilitator->nip ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Pangkat/Gol.</td>
                        <td class="titik-dua">:</td>
                        <td>{{ $fasilitator->pangkat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jabatan</td>
                        <td class="titik-dua">:</td>
                        <td>{{ $fasilitator->jabatan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Unit Kerja</td>
                        <td class="titik-dua">:</td>
                        <td>{{ $fasilitator->unit_kerja ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Alamat Kantor</td>
                        <td class="titik-dua">:</td>
                        <td>{{ $fasilitator->alamat_kantor ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Alamat Rumah</td>
                        <td class="titik-dua">:</td>
                        <td>{{ $fasilitator->alamat_rumah ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">No. Hp</td>
                        <td class="titik-dua">:</td>
                        <td>{{ $fasilitator->no_hp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Email</td>
                        <td class="titik-dua">:</td>
                        <td>{{ $fasilitator->email ?? '-' }}</td>
                    </tr>
                </table>
            </td>
            <td class="kolom-kanan">
                <div class="foto-box">
                    @if ($fotoSrc)
                        <img src="{{ $fotoSrc }}">
                    @else
                        Foto
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">Riwayat Pendidikan</div>
    @forelse ($riwayatPendidikan as $item)
        <div class="pendidikan-item">
            <table class="pendidikan-baris1">
                <tr>
                    <td>
                        <b>{{ $item->institusi }}</b>{{ $item->kota ? ' – ' . $item->kota . ', Indonesia' : '' }}
                    </td>
                    <td class="pendidikan-tahun">
                        {{ $item->tahun_mulai ?? '?' }} – {{ $item->tahun_selesai ?? 'Sekarang' }}
                    </td>
                </tr>
            </table>
            <div class="pendidikan-jenjang">{{ $item->jenjang }}</div>
        </div>
    @empty
        <div class="pendidikan-item">-</div>
    @endforelse

    <div class="section-title">Materi yang Diajarkan</div>
    @if ($materiDiajarkan->count())
        <ul class="list-kegiatan">
            @foreach ($materiDiajarkan as $item)
                <li>{{ $item->nama_kegiatan }}</li>
            @endforeach
        </ul>
    @else
        <div>-</div>
    @endif

    <div class="section-title">Pendidikan/Pelatihan yang Terkait Materi</div>
    @if ($pelatihanTerkait->count())
        <ul class="list-kegiatan">
            @foreach ($pelatihanTerkait as $item)
                <li>{{ $item->nama_kegiatan }}</li>
            @endforeach
        </ul>
    @else
        <div>-</div>
    @endif

    <div class="section-title">Pengalaman Melatih/Mengajar</div>
    @if ($pengalamanMengajar->count())
        <ul class="list-kegiatan">
            @foreach ($pengalamanMengajar as $item)
                <li>{{ $item->nama_kegiatan }}</li>
            @endforeach
        </ul>
    @else
        <div>-</div>
    @endif

    @if ($kegiatans->count())
        <div class="section-title">Kegiatan</div>
        <ul class="list-kegiatan">
            @foreach ($kegiatans as $kegiatan)
                <li>{{ $kegiatan->nama }}{{ $kegiatan->pivot->peran ? ' — ' . $kegiatan->pivot->peran : '' }}</li>
            @endforeach
        </ul>
    @endif

    <div class="footer">
        <div>Bandung, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</div>
        <div class="ttd-space">
            @if ($ttdSrc)
                <img src="{{ $ttdSrc }}">
            @endif
        </div>
        <div>({{ $fasilitator->nama }})</div>
    </div>

</body>
</html>

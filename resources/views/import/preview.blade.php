<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Preview Import</title>
    <style>
        .card {
            border: 1px solid #c00;
            background: #fff5f5;
            padding: 12px;
            margin-bottom: 12px;
            max-width: 550px;
        }
        .card.dihapus {
            opacity: 0.4;
            background: #eee;
            border-color: #999;
        }
        .card .baris-label {
            font-weight: bold;
            margin-bottom: 6px;
        }
        .card .konteks {
            font-size: 12px;
            color: #555;
            margin-bottom: 8px;
        }
        .card label {
            display: block;
            font-size: 13px;
            margin-top: 6px;
        }
        .card input[type="text"] {
            width: 100%;
            padding: 4px;
            box-sizing: border-box;
        }
        .card .aksi-baris {
            margin-top: 8px;
        }
        table.valid-table {
            border-collapse: collapse;
        }
        table.valid-table th, table.valid-table td {
            border: 1px solid #999;
            padding: 4px 6px;
            font-size: 12px;
        }
        table.valid-table input[type="text"] {
            width: 100%;
            box-sizing: border-box;
            font-size: 12px;
            padding: 2px;
        }
        tr.dihapus {
            opacity: 0.4;
            background: #eee;
        }
        .scroll-box {
            max-height: 450px;
            overflow-y: auto;
            border: 1px solid #999;
        }
        .toolbar {
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <h1>Preview Hasil Import</h1>

    <table border="1" cellpadding="8">
        <tr><td>Baris valid</td><td>{{ count($ringkasan['valid']) }}</td></tr>
        <tr><td>Baris bermasalah</td><td style="color:red">{{ count($ringkasan['bermasalah']) }}</td></tr>
    </table>

    <br>

    <form action="{{ route('import.store') }}" method="POST" id="form-import" onsubmit="return confirm('Yakin mau import data ini ke database?')">
        @csrf
        <input type="hidden" name="path" value="{{ $path }}">
        <input type="hidden" name="import_batch_id" value="{{ $importBatch->id }}">

        @if (count($ringkasan['bermasalah']))
            <h2 style="color:#c00;">⚠ Baris Bermasalah ({{ count($ringkasan['bermasalah']) }})</h2>
            <p>Lengkapi data yang kosong, lalu centang "Sertakan" kalau mau baris ini ikut diimport.</p>

            @foreach ($ringkasan['bermasalah'] as $item)
                @php $b = $item['baris']; @endphp
                <div class="card" id="card-{{ $b }}">
                    <div class="baris-label">Baris Excel #{{ $b }}</div>
                    <div class="konteks">
                        {{ $item['bulan'] ?? '-' }} {{ $item['tahun'] ?? '' }}
                    </div>

                    <label>Nama Fasilitator:</label>
                    <input type="text" name="nama_fasilitator[{{ $b }}]" value="{{ $item['nama_fasilitator'] }}" placeholder="Ketik nama fasilitator...">

                    <label>Nama Pelatihan:</label>
                    <input type="text" name="nama_pelatihan[{{ $b }}]" value="{{ $item['nama_pelatihan'] }}" placeholder="Ketik nama pelatihan...">

                    <label>Materi:</label>
                    <input type="text" name="materi[{{ $b }}]" value="{{ $item['materi'] }}" placeholder="Materi (opsional)">

                    <div class="aksi-baris">
                        <input type="hidden" name="sertakan[{{ $b }}]" value="0">
                        <label style="display:inline;">
                            <input type="checkbox" name="sertakan[{{ $b }}]" value="1" class="chk-sertakan" data-target="card-{{ $b }}" onchange="toggleBaris(this)">
                            Sertakan baris ini saat import
                        </label>
                    </div>
                </div>
            @endforeach
        @endif

        @if (count($ringkasan['valid']))
            <h2>✅ Data Valid — Cek Ulang Sebelum Import ({{ count($ringkasan['valid']) }})</h2>

            <div class="toolbar">
                <button type="button" onclick="hapusTerpilih()">🗑 Hapus yang Dicentang</button>
                <button type="button" onclick="pilihSemua(true)">Pilih Semua</button>
                <button type="button" onclick="pilihSemua(false)">Batal Pilih Semua</button>
            </div>

            <div class="scroll-box">
                <table class="valid-table">
                    <tr>
                        <th>Pilih</th>
                        <th>Baris</th>
                        <th>Nama Fasilitator</th>
                        <th>Status</th>
                        <th>Nama Pelatihan</th>
                        <th>Materi</th>
                        <th>Bulan/Tahun</th>
                        <th>Aksi</th>
                    </tr>
                    @foreach ($ringkasan['valid'] as $item)
                        @php $b = $item['baris']; @endphp
                        <tr id="row-{{ $b }}">
                            <td><input type="checkbox" class="pilih-hapus"></td>
                            <td>{{ $b }}</td>
                            <td><input type="text" name="nama_fasilitator[{{ $b }}]" value="{{ $item['nama_fasilitator'] }}"></td>
                            <td>{{ $item['status_fasilitator'] == 'baru' ? '🆕 Baru' : 'Sudah ada' }}</td>
                            <td><input type="text" name="nama_pelatihan[{{ $b }}]" value="{{ $item['nama_pelatihan'] }}"></td>
                            <td><input type="text" name="materi[{{ $b }}]" value="{{ $item['materi'] }}"></td>
                            <td>{{ $item['bulan'] ?? '-' }} {{ $item['tahun'] ?? '' }}</td>
                            <td>
                                <input type="hidden" name="sertakan[{{ $b }}]" value="1" id="sertakan-{{ $b }}">
                                <a href="javascript:void(0)" onclick="hapusBaris({{ $b }})" id="link-hapus-{{ $b }}">Hapus</a>
                                <a href="javascript:void(0)" onclick="pulihkanBaris({{ $b }})" id="link-pulih-{{ $b }}" style="display:none;">Batalkan</a>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif

        <br>
        <label>
            <input type="checkbox" name="confirm_import" value="1" required>
            Saya sudah meninjau preview dan menyetujui import ini.
        </label>
        <br><br>
        <button type="submit">✔ Import Sekarang</button>
    </form>

    <br>
    <a href="{{ route('import.create') }}">← Batal, Upload Ulang</a>

    <script>
        // Dipakai buat card di section "Baris Bermasalah"
        function toggleBaris(checkbox) {
            const card = document.getElementById(checkbox.dataset.target);
            card.classList.toggle('dihapus', !checkbox.checked);
        }

        // Dipakai buat baris di tabel "Data Valid"
        function hapusBaris(baris) {
            document.getElementById('sertakan-' + baris).value = '0';
            document.getElementById('row-' + baris).classList.add('dihapus');
            document.getElementById('link-hapus-' + baris).style.display = 'none';
            document.getElementById('link-pulih-' + baris).style.display = 'inline';
        }

        function pulihkanBaris(baris) {
            document.getElementById('sertakan-' + baris).value = '1';
            document.getElementById('row-' + baris).classList.remove('dihapus');
            document.getElementById('link-hapus-' + baris).style.display = 'inline';
            document.getElementById('link-pulih-' + baris).style.display = 'none';
        }

        function hapusTerpilih() {
            document.querySelectorAll('.pilih-hapus:checked').forEach(function (checkbox) {
                const row = checkbox.closest('tr');
                const baris = row.id.replace('row-', '');
                hapusBaris(baris);
                checkbox.checked = false;
            });
        }

        function pilihSemua(status) {
            document.querySelectorAll('.pilih-hapus').forEach(function (checkbox) {
                checkbox.checked = status;
            });
        }
    </script>
</body>
</html>

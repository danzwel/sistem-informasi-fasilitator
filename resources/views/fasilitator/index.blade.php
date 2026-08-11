<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Fasilitator</title>
</head>
<body>
    <h1>Data Fasilitator</h1>

    <a href="{{ route('fasilitator.create') }}">+ Tambah Fasilitator</a>
    &nbsp;|&nbsp;
    <a href="{{ route('import.create') }}">📥 Import dari Excel</a>
    <br><br>

    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <table border="1" cellpadding="8">
        <tr>
            <th>Foto</th>
            <th>TTD</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        @forelse ($fasilitators as $fasilitator)
            <tr>
                <td>
                    @if ($fasilitator->foto)
                        <img src="{{ asset('storage/' . $fasilitator->foto) }}" width="50">
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if ($fasilitator->ttd)
                        <img src="{{ asset('storage/' . $fasilitator->ttd) }}" width="80">
                    @else
                        -
                    @endif
                </td>
                <td>{{ $fasilitator->nama }}</td>
                <td>{{ $fasilitator->email }}</td>
                <td>{{ $fasilitator->status }}</td>
                <td>
                    <a href="{{ route('fasilitator.show', $fasilitator) }}">Detail</a>
                    |
                    <a href="{{ route('fasilitator.edit', $fasilitator) }}">Edit</a>
                    |
                    <form action="{{ route('fasilitator.destroy', $fasilitator) }}" method="POST" style="display:inline" onsubmit="return confirm('Yakin hapus data ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6">Belum ada data fasilitator.</td>
            </tr>
        @endforelse
    </table>

    {{ $fasilitators->links() }}
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Daftar Buku</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 15px;
        }

        h1 {
            font-size: 24px;
        }

        table {
            width: 85%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #777;
            padding: 6px 4px;
            text-align: left;
            font-weight: normal;
        }
    </style>
</head>
<body>
    <h1>Daftar Buku</h1>
    <p><a href="{{ route('buku.create') }}">Tambah Buku</a></p>

    @if (session('status'))
        <p>{{ session('status') }}</p>
    @endif

    @if ($bukus->isEmpty())
        <p>Data buku tidak ditemukan.</p>
    @else
        <table border="1">
            <tr>
                <th>Kode</th>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Aksi</th>
            </tr>
            @foreach ($bukus as $buku)
                <tr>
                    <td>{{ $buku->kode }}</td>
                    <td>{{ $buku->judul }}</td>
                    <td>{{ $buku->kategori->nama }}</td>
                    <td>
                        <a href="{{ route('buku.edit', $buku) }}">Ubah</a>
                        <br>
                        <form action="{{ route('buku.destroy', $buku) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    @endif
</body>
</html>
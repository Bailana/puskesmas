<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Obat</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background-color: #f2f2f2; text-align: left; }
        h1 { text-align: center; }
    </style>
</head>
<body>
    <h1>Laporan Data Obat</h1>
    <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Obat</th>
                <th>Jenis Obat</th>
                <th>Dosis</th>
                <th>Bentuk Obat</th>
                <th>Stok</th>
                <th>Harga Satuan</th>
                <th>Tgl. Kadaluarsa</th>
                <th>Nama Pabrikan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($obats as $index => $obat)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $obat->nama_obat }}</td>
                    <td>{{ $obat->jenis_obat }}</td>
                    <td>{{ $obat->dosis }}</td>
                    <td>{{ $obat->bentuk_obat }}</td>
                    <td>{{ $obat->stok }}</td>
                    <td>Rp {{ number_format($obat->harga_satuan, 2, ',', '.') }}</td>
                    <td>{{ \Carbon\Carbon::parse($obat->tanggal_kadaluarsa)->format('d-m-Y') }}</td>
                    <td>{{ $obat->nama_pabrikan }}</td>
                    <td>{{ $obat->keterangan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center;">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

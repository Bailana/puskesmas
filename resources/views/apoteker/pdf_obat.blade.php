<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Obat PDF</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        h2 {
            text-align: center;
            margin-bottom: 0;
        }
        .header {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Data Obat</h2>
        <p>Tanggal Export: {{ \Carbon\Carbon::now()->format('d-m-Y H:i:s') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Obat</th>
                <th>Jenis Obat</th>
                <th>Dosis</th>
                <th>Bentuk Obat</th>
                <th>Stok</th>
                <th>Harga Satuan</th>
                <th>Tanggal Kadaluarsa</th>
                <th>Nama Pabrikan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($obats as $index => $obat)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $obat->nama_obat }}</td>
                <td>{{ $obat->jenis_obat }}</td>
                <td>{{ $obat->dosis }}</td>
                <td>{{ $obat->bentuk_obat }}</td>
                <td>{{ $obat->stok }}</td>
                <td>Rp. {{ number_format($obat->harga_satuan, 2, ',', '.') }}</td>
                <td>{{ \Carbon\Carbon::parse($obat->tanggal_kadaluarsa)->format('d-m-Y') }}</td>
                <td>{{ $obat->nama_pabrikan }}</td>
                <td>{{ $obat->keterangan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

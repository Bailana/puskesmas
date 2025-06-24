<!DOCTYPE html>
<html>
<head>
    <title>Data Obat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        h2 {
            text-align: center;
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <h2>Data Obat</h2>
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

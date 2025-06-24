<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export PDF - Data Obat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #444;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div style="text-align: center; margin-bottom: 20px;">
        @php
            $path = public_path('template/images/logo_puskesmas.png');
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        @endphp
        <img src="{{ $base64 }}" alt="Logo Puskesmas" style="width: 80px; height: 80px;">
        <h1 style="margin: 0;">UPT Puskesmas Pujud</h1>
        <p style="margin: 0;">Jl. Raya Pujud No. 123, Kecamatan Pujud, Kabupaten Rokan Hilir, Riau</p>
        <p style="margin: 0;">Telp: (0765) 1234567 | Email: info@puskesmaspujud.go.id</p>
        <hr style="margin-top: 10px; border: 1px solid #000;">
    </div>

    <h2 style="text-align: center;">Data Obat</h2>
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

    <div style="margin-top: 50px; text-align: right;">
        <p>Rokan Hilir, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('DD MMMM YYYY') }}</p>
        <p>Mengetahui,</p>
        <br><br><br>
        <p><strong>__________________________</strong></p>
        <p><em>UPT Puskesmas Pujud</em></p>
    </div>
</body>
</html>

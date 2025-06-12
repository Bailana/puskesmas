<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export PDF - Rekapan Tagihan Pasien</title>
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
        .status-lunas {
            color: green;
            font-weight: bold;
        }
        .status-belum-lunas {
            color: orange;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>Rekapan Tagihan Pasien</h2>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Hari/Tanggal</th>
                <th>No. RM</th>
                <th>Nama Pasien</th>
                <th>JamKes</th>
                <th>Total Biaya</th>
                <th>Status Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tagihans as $index => $tagihan)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($tagihan->created_at)->locale('id')->isoFormat('dddd, DD-MM-YYYY') }}</td>
                <td>{{ $tagihan->pasien->no_rekam_medis ?? '-' }}</td>
                <td>{{ $tagihan->pasien->nama_pasien ?? '-' }}</td>
                <td>{{ $tagihan->pasien->jaminan_kesehatan ?? '-' }}</td>
                <td>{{ 'Rp ' . number_format($tagihan->total_biaya * 1000, 2, ',', '.') }}</td>
                <td>
                    @if(strtolower($tagihan->status) == 'lunas')
                        <span class="status-lunas">Lunas</span>
                    @else
                        <span class="status-belum-lunas">Belum Lunas</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

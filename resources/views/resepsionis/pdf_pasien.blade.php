<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Pasien</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #333;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        h2 {
            text-align: center;
            margin-top: 0;
        }
    </style>
</head>
<body>
    <h2>Data Pasien</h2>
    <table>
        <thead>
            <tr>
                <th>No. Rekam Medis</th>
                <th>Nama Pasien</th>
                <th>Jenis Kelamin</th>
                <th>Tanggal Lahir</th>
                <th>Alamat</th>
                <th>Golongan Darah</th>
                <th>Jaminan Kesehatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pasiens as $pasien)
            <tr>
                <td>{{ $pasien->no_rekam_medis }}</td>
                <td>{{ $pasien->nama_pasien }}</td>
                <td>{{ $pasien->jenis_kelamin }}</td>
                <td>{{ $pasien->tanggal_lahir }}</td>
                <td>{{ $pasien->alamat_jalan }}, RT {{ $pasien->rt }}/RW {{ $pasien->rw }}, {{ $pasien->kelurahan }}, {{ $pasien->kecamatan }}, {{ $pasien->kabupaten }}, {{ $pasien->provinsi }}</td>
                <td>{{ $pasien->gol_darah ?? '-' }}</td>
                <td>{{ $pasien->jaminan_kesehatan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

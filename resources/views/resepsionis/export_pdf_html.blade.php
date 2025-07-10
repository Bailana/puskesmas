<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pasien - Export PDF</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 20mm 10mm 20mm 10mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0 auto;
            padding: 0 10mm 0 10mm;
            box-sizing: border-box;
            max-width: 277mm;
            min-height: 190mm;
            overflow-wrap: break-word;
            word-wrap: break-word;
            word-break: break-word;
        }
        .kop-surat {
            margin-bottom: 20px;
            border-bottom: 3px solid #666;
            padding-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .kop-surat .left-logo,
        .kop-surat .right-logo {
            width: 80px;
            height: 80px;
        }
        .kop-surat .left-logo img,
        .kop-surat .right-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .kop-surat .center-text {
            flex-grow: 1;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            line-height: 1.2;
        }
        .kop-surat .center-text .line1 {
            font-size: 16px;
        }
        .kop-surat .center-text .line2 {
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: auto;
            word-wrap: break-word;
            border-spacing: 0;
        }
        thead {
            display: table-header-group;
        }
        table, th, td {
            border: 1px solid #444;
            padding: 6px 4px;
            vertical-align: top;
            word-break: break-word;
            white-space: normal;
        }
        th, td {
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
        }
        /* Adjust width for No. column */
        th:nth-child(1), td:nth-child(1) {
            width: 30px;
            max-width: 30px;
            word-wrap: normal;
            white-space: nowrap;
            text-align: center;
        }
        /* Adjust width for Gol. Darah column */
        th:nth-child(6), td:nth-child(6) {
            width: 40px;
            max-width: 40px;
            word-wrap: normal;
            white-space: nowrap;
            text-align: center;
        }
        /* Adjust width for Jenis Kelamin column */
        th:nth-child(5), td:nth-child(5) {
            width: 70px;
            max-width: 70px;
            word-wrap: normal;
            white-space: nowrap;
            text-align: center;
        }
        /* Fix Jenis Kelamin header text wrapping */
        th:nth-child(5) {
            white-space: nowrap;
            padding: 6px 4px;
        }
        /* Fix Gol. Darah header text wrapping */
        th:nth-child(6) {
            white-space: normal;
            word-wrap: break-word;
            padding: 6px 2px;
        }
        .signature {
            margin-top: 50px;
            text-align: right;
            page-break-inside: avoid;
            height: 100px;
            padding-top: 20px;
            box-sizing: border-box;
        }
        tr {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="kop-surat">
        <div class="left-logo">
            @php
                $pathLeft = public_path('template/images/LogoRohil.png');
                $typeLeft = pathinfo($pathLeft, PATHINFO_EXTENSION);
                $dataLeft = file_get_contents($pathLeft);
                $base64Left = 'data:image/' . $typeLeft . ';base64,' . base64_encode($dataLeft);
            @endphp
            <img src="{{ $base64Left }}" alt="Logo Kiri">
        </div>
        <div class="center-text">
            <div class="line1">PEMERINTAH KABUPATEN ROKAN HILIR</div>
            <div class="line2">DINAS KESEHATAN UPT PUSKESMAS PUJUD</div>
            <div class="line2">KECAMATAN PUJUD</div>
            <div class="line2">Jl. Jend Sudirman, No. 002 PUJUD Kode Pos. 28983</div>
            <div class="line2" style="font-size: 12px; font-weight: normal;">email : puskesmaspujud@gmail.com</div>
        </div>
        <div class="right-logo">
            @php
                $pathRight = public_path('template/images/logo_puskesmas_cross.png');
                if (!file_exists($pathRight)) {
                    $pathRight = public_path('template/images/logo_puskesmas.png');
                }
                $typeRight = pathinfo($pathRight, PATHINFO_EXTENSION);
                $dataRight = file_get_contents($pathRight);
                $base64Right = 'data:image/' . $typeRight . ';base64,' . base64_encode($dataRight);
            @endphp
            <img src="{{ $base64Right }}" alt="Logo Kanan">
        </div>
    </div>

    <h2 style="text-align: center; text-transform: uppercase;">Data Pasien</h2>
    @php
        $counter = 1;
    @endphp
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>No. RM</th>
                <th>Nama Pasien</th>
                <th>NIK</th>
                <th style="white-space: normal;">Jenis<br>Kelamin</th>
                <th>Gol. Darah</th>
                <th>Tempat Lahir</th>
                <th>Tanggal Lahir</th>
                <th>Alamat</th>
                <th>JamKes</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pasiens as $pasien)
            <tr>
                <td>{{ $counter++ }}</td>
                <td>{{ $pasien->no_rekam_medis }}</td>
                <td>{{ $pasien->nama_pasien }}</td>
                <td>{{ $pasien->nik }}</td>
                <td>{{ $pasien->jenis_kelamin }}</td>
                <td>{{ $pasien->gol_darah }}</td>
                <td>{{ $pasien->tempat_lahir }}</td>
                <td>{{ \Carbon\Carbon::parse($pasien->tanggal_lahir)->format('d-m-Y') }}</td>
                <td>{{ $pasien->alamat_jalan }}, RT {{ $pasien->rt }}/RW {{ $pasien->rw }}, {{ $pasien->kelurahan }}, {{ $pasien->kecamatan }}, {{ $pasien->kabupaten }}, {{ $pasien->provinsi }}</td>
                <td>{{ $pasien->jaminan_kesehatan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature">
        <p>Rokan Hilir, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('DD MMMM YYYY') }}</p>
        <p>Mengetahui,</p>
        <br><br><br>
        <p><strong>__________________________</strong></p>
        <p><em>Kepala UPT Puskesmas Pujud</em></p>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var element = document.body;
            var opt = {
                margin:       0.5,
                filename:     'data_pasien.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2 },
                jsPDF:        { unit: 'in', format: 'a4', orientation: 'landscape' }
            };
            html2pdf().set(opt).from(element).save();
        });
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapan Tagihan Pasien - Cetak</title>
    <style>
        @page {
            size: A4;
            margin: 20mm 5mm 20mm 10mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0 auto;
            padding: 0 10mm 0 10mm;
            box-sizing: border-box;
            max-width: 190mm; /* A4 width minus margins */
            min-height: 297mm; /* A4 height */
            overflow-wrap: break-word;
            word-wrap: break-word;
            word-break: break-word;
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
        .signature {
            margin-top: 50px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="kop-surat">
        <div class="left-logo">
            @php
                $pathLeft = public_path('template/images/logo_puskesmas.png');
                $typeLeft = pathinfo($pathLeft, PATHINFO_EXTENSION);
                $dataLeft = file_get_contents($pathLeft);
                $base64Left = 'data:image/' . $typeLeft . ';base64,' . base64_encode($dataLeft);
            @endphp
            <img src="{{ $base64Left }}" alt="Logo Kiri">
        </div>
        <div class="center-text">
            <div class="line1">PEMERINTAH KABUPATEN ROKAN HILIR</div>
            <div class="line2">DINAS KESEHATAN</div>
            <div class="line2">UPT PUSKESMAS PUJUD</div>
            <div class="line2">KECAMATAN PUJUD</div>
            <div class="line2">Jl. Jend Sudirman, No. 002 PUJUD Kode Pos. 28983</div>
            <div class="line2" style="font-size: 12px; font-weight: normal;">email : puskesmaspujud@gmail.com</div>
        </div>
        <div class="right-logo">
            @php
                // Fallback to left logo if right logo file does not exist
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

    <div class="signature">
        <p>Rokan Hilir, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('DD MMMM YYYY') }}</p>
        <p>Mengetahui,</p>
        <br><br><br>
        <p><strong>__________________________</strong></p>
        <p><em>Kasir UPT Puskesmas Pujud</em></p>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var element = document.body;
            var opt = {
                margin:       0.5,
                filename:     'tagihan_pasien.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2 },
                jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        });
    </script>
</body>
</html>

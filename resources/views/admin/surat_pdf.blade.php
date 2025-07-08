<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Kesehatan</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 14px;
            line-height: 1.6;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .line {
            border-top: 2px solid black;
            margin-top: 4px;
        }

        .subline {
            border-top: 1px solid black;
            margin-bottom: 20px;
        }

        .title {
            text-align: center;
            text-decoration: underline;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .content {
            margin-left: 40px;
        }

        .footer {
            margin-top: 40px;
            text-align: right;
        }
    </style>
</head>

<body>

    <div class="header">
        <table width="100%">
            <tr>
                <td width="15%" align="left">
                    <img src="{{ public_path('template/images/logo_rohil.png') }}" style="height: 80px;">
                </td>
                <td width="70%" align="center" style="line-height: 1.5;">
                    <strong style="font-size: 16px;">PEMERINTAH KABUPATEN ROKAN HILIR</strong><br>
                    DINAS KESEHATAN<br>
                    <strong>UPT PUSKESMAS PUJUD</strong><br>
                    KECAMATAN PUJUD<br>
                    JL. Jend Sudirman, No. 002 PUJUD Kode Pos. 28983<br>
                    Email: puskesmaspujud@gmail.com
                </td>
                <td width="15%" align="right">
                    <img src="{{ public_path('template/images/logo_puskesmas.png') }}" style="height: 80px;">
                </td>
            </tr>
        </table>
    </div>

    <div class="line"></div>
    <div class="subline"></div>

    <div class="title">
        SURAT KETERANGAN KESEHATAN
    </div>

    <div class="content">
        <table style="width: 100%; line-height: 1.8;">
            <tr>
                <td style="width: 35%;">Nama</td>
                <td style="width: 5%;">:</td>
                <td>{{ $pasien->nama_pasien }}</td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>:</td>
                <td>{{ $pasien->jenis_kelamin }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $pasien->alamat_jalan }}</td>
            </tr>
            <tr>
                <td>Tempat/Tgl. Lahir</td>
                <td>:</td>
                <td>{{ $pasien->tempat_lahir }}, {{ $pasien->tanggal_lahir }}</td>
            </tr>
            <tr>
                <td>Pekerjaan</td>
                <td>:</td>
                <td>{{ $pasien->pekerjaan }}</td>
            </tr>
            <tr>
                <td>Tinggi Badan</td>
                <td>:</td>
                <td>{{ $pasien->tinggi_badan }} cm</td>
            </tr>
            <tr>
                <td>Berat Badan</td>
                <td>:</td>
                <td>{{ $pasien->berat_badan }} kg</td>
            </tr>
            <tr>
                <td>Golongan Darah</td>
                <td>:</td>
                <td>{{ $pasien->gol_darah }}</td>
            </tr>
            <tr>
                <td>Tekanan Darah</td>
                <td>:</td>
                <td>{{ $pasien->tekanan_darah }}</td>
            </tr>
            <tr>
                <td>Buta Warna</td>
                <td>:</td>
                <td>{{ $pasien->buta_warna }}</td>
            </tr>
            <tr>
                <td>Kondisi Fisik</td>
                <td>:</td>
                <td>{{ $pasien->kondisi_fisik }}</td>
            </tr>
            <tr>
                <td>Keadaan</td>
                <td>:</td>
                <td>{{ $pasien->keadaan }}</td>
            </tr>
            <tr>
                <td>Keperluan</td>
                <td>:</td>
                <td>{{ $keperluan }}</td>
            </tr>
            <tr>
                <td>Dokter yang Memeriksa</td>
                <td>:</td>
                <td>{{ $pasien->dokter }}</td>
            </tr>
            <tr>
                <td>Tanggal Pemeriksaan</td>
                <td>:</td>
                <td>{{ $pasien->tanggal_periksa }}</td>
            </tr>
        </table>
    </div>

    <div class="footer" style="text-align: center; margin-top: 20px; line-height: 1.4;">
        <p>Dikeluarkan di: Pujud</p>
        <p>Pada Tanggal: {{ $pasien->tanggal_periksa }}</p> 

        <div style="margin-top: 20px;">
            <p>KEPALA UPT PUSKESMAS PUJUD</p>
        </div>

        <div style="margin-top: 40px;">
            <p><strong>Ns. NURHAYATI. S.Kep</strong></p>
            <p>Penata TK.I / III.d</p>
            <p>NIP. 19791229 201001 2 009</p>
        </div>
    </div>

</body>

</html>

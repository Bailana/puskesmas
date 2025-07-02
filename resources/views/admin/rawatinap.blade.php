@extends('dashboardadmin')

@section('admin')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>Pasien Rawat Inap</strong></h1>
    <div class="row">
        <div class="col-12 col-lg-12 col-xxl-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <form class="d-flex flex-wrap align-items-center gap-2 m-0 p-0" style="width: 250%;">
                            <div class="input-group" style="width: 100%;">
                                <input type="text" id="searchInput" name="search" class="form-control" placeholder="Pencarian..." aria-label="Search" autocomplete="off">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover my-0" id="pasienTable" style="font-size: 14px;">
                        <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Hari/Tanggal Masuk</th>
                                    <th>No.Rekam Medis</th>
                                    <th>Nama Pasien</th>
                                    <th>Umur</th>
                                    <th>JamKes</th>
                                    <th>Status</th>
                                </tr>
                        </thead>
                        <tbody>
                            @if(count($pasiens_ugd) > 0)
                            @foreach ($pasiens_ugd as $index => $pasien)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            @php
                                            \Carbon\Carbon::setLocale('id');
                                            $tanggalMasuk = \Carbon\Carbon::parse($pasien->tanggal_masuk)->translatedFormat('l, d F Y');
                                            @endphp
                                            <td>{{ $tanggalMasuk }}</td>
                                            <td>{{ $pasien->pasien ? $pasien->pasien->no_rekam_medis : '-' }}</td>
                                            <td>{{ $pasien->nama_pasien }}</td>
                                            <td>
                                                @php
                                                    $umur = null;
                                                    if ($pasien->pasien_id) {
                                                        $pasienDb = \App\Models\Pasien::find($pasien->pasien_id);
                                                        if ($pasienDb && $pasienDb->tanggal_lahir) {
                                                            $umur = \Carbon\Carbon::parse($pasienDb->tanggal_lahir)->age;
                                                        }
                                                    }
                                                @endphp
                                                {{ $umur !== null ? $umur . ' tahun' : '-' }}
                                            </td>
                                            <td>{{ $pasien->pasien ? $pasien->pasien->jaminan_kesehatan : '-' }}</td>
                                            <td>
                                                @php
                                                    $status = $pasien->status ?: 'Rawat Inap';
                                                    $badgeClass = 'bg-secondary';
                                                    if (strtolower($status) === 'perlu analisa') {
                                                        $badgeClass = 'bg-warning text-dark';
                                                    } elseif (strtolower($status) === 'rawat inap') {
                                                        $badgeClass = 'bg-primary';
                                                    } elseif (strtolower($status) === 'rawat jalan') {
                                                        $badgeClass = 'bg-success';
                                                    }
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                            </td>
                                        </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data pasien unit gawat darurat</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        var filter = this.value.toLowerCase();
        var rows = document.querySelectorAll('#pasienTable tbody tr');

        rows.forEach(function(row) {
            var text = row.textContent.toLowerCase();
            row.style.display = text.indexOf(filter) > -1 ? '' : 'none';
        });
    });
</script>
<script>
    $(function() {
        $(document).on('click', '.btn-riwayat-analisa', function() {
            var pasienId = $(this).data('pasien-id');
            var namaPasien = $(this).data('nama-pasien');
            $('#riwayat_nama_pasien_display').text(namaPasien || '');
            var $content = $('#riwayatAnalisaContent');
            $content.html('<div class="text-center text-muted">Memuat data analisa...</div>');
            if (!pasienId) {
                $content.html('<div class="text-danger">ID pasien tidak ditemukan.</div>');
                return;
            }
            $.get('/rawatinap/hasilanalisa/riwayat/' + encodeURIComponent(pasienId), function(res) {
                if (res.success && res.data) {
                    var data = res.data;

                    function row(label, value) {
                        return '<div class="row mb-2">' +
                            '<div class="col-md-4 fw-bold">' + label + '</div>' +
                            '<div class="col-md-8">' + (value || '-') + '</div>' +
                            '</div>';
                    }

                    function arr(val) {
                        if (Array.isArray(val)) return val.join(', ');
                        if (typeof val === 'string') try {
                            var arr = JSON.parse(val);
                            if (Array.isArray(arr)) return arr.join(', ');
                        } catch (e) {};
                        return val || '-';
                    }
                    var html = '';
                    html += row('Tekanan Darah', data.tekanan_darah);
                    html += row('Frekuensi Nadi', data.frekuensi_nadi);
                    html += row('Suhu', data.suhu);
                    html += row('Frekuensi Nafas', data.frekuensi_nafas);
                    html += row('Skor Nyeri', data.skor_nyeri);
                    html += row('Skor Jatuh', data.skor_jatuh);
                    html += row('Berat Badan', data.berat_badan);
                    html += row('Tinggi Badan', data.tinggi_badan);
                    html += row('Lingkar Kepala', data.lingkar_kepala);
                    html += row('IMT', data.imt);
                    html += row('Alat Bantu', data.alat_bantu);
                    html += row('Prosthesa', data.prosthesa);
                    html += row('Cacat Tubuh', data.cacat_tubuh);
                    html += row('ADL Mandiri', data.adl_mandiri);
                    html += row('Riwayat Jatuh', data.riwayat_jatuh);
                    html += row('Status Psikologi', arr(data.status_psikologi));
                    html += row('Hambatan Edukasi', arr(data.hambatan_edukasi));
                    html += row('Alergi', data.alergi);
                    html += row('Catatan', data.catatan);
                    html += row('Ruangan', data.ruangan);
                    $content.html(html);
                } else {
                    $content.html('<div class="text-danger">Data analisa tidak ditemukan.</div>');
                }
            }).fail(function() {
                $content.html('<div class="text-danger">Gagal mengambil data analisa.</div>');
            });
        });
    });
</script>
@endsection
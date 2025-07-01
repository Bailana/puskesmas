@extends('dashboardrawatinap')

@section('rawatinap')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>Data Pasien Rawat Inap</strong></h1>
    <div class="row">
        <div class="col-12 col-lg-12 col-xxl-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <form method="GET" action="#" class="d-flex flex-wrap align-items-center gap-2 m-0 p-0">
                        <div class="input-group" style="width: 250px;">
                            <input type="text" name="search" class="form-control" id="searchInput" placeholder="Pencarian..." aria-label="Search" autocomplete="off">
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover my-0" id="pasienTable">
                        <thead>
                            <tr>
                                <th class="nowrap">No.</th>
                                <th class="nowrap">Hari/Tanggal Masuk</th>
                                <th class="nowrap">Nama Pasien</th>
                                <th class="nowrap">Umur</th>
                                <th class="nowrap">Ruangan</th>
                                <th class="nowrap">Status</th>
                                <th class="nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($pasiens_ugd) > 0)
                            @foreach ($pasiens_ugd as $index => $pasien)
                            <tr>
                                <td class="nowrap">{{ $index + 1 }}</td>
                                <td class="nowrap">{{ \Carbon\Carbon::parse($pasien->tanggal_masuk)->locale('id')->translatedFormat('l, d F Y') }}
                                </td>
                                <td class="nowrap">{{ $pasien->nama_pasien }}</td>
                                <td class="nowrap">
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
                                <td class="nowrap">{{ $pasien->ruangan ?? '-' }}</td>
                                <td class="nowrap">
                                    @php
                                    $status = $pasien->status ?: 'Rawat Inap';
                                    $badgeClass = 'bg-secondary';
                                    if (strtolower($status) === 'perlu analisa') {
                                    $badgeClass = 'bg-warning text-dark';
                                    } elseif (strtolower($status) === 'rawat inap') {
                                    $badgeClass = 'bg-danger';
                                    } elseif (strtolower($status) === 'rawat jalan') {
                                    $badgeClass = 'bg-success';
                                    }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                </td>
                                <td class="nowrap">
                                    <button class="btn btn-primary btn-sm rounded" data-bs-toggle="modal"
                                        data-bs-target="#modalDetailPasien"
                                        data-pasien-id="{{ $pasien->pasien_id }}">Selengkapnya</button>
                                    <button class="btn btn-success btn-sm ms-1 rounded btn-hasil-analisa" data-bs-toggle="modal"
                                        data-bs-target="#modalHasilAnalisa" data-pasien-id="{{ $pasien->pasien_id }}"
                                        data-nama-pasien="{{ $pasien->nama_pasien }}"
                                        data-pasien='@json(["pasien_id" => $pasien->pasien_id, "nama_pasien" => $pasien->nama_pasien])'>Hasil Analisa</button>
                                    <button type="button" class="btn btn-info btn-sm ms-1 rounded btn-hasil-periksa" data-bs-toggle="modal" data-bs-target="#modalHasilPeriksa" data-pasien-id="{{ $pasien->pasien_id }}">Hasil Periksa</button>
                                    <button type="button" class="btn btn-warning btn-sm ms-1 rounded" data-bs-toggle="modal" data-bs-target="#modalPeriksa" data-pasien-id="{{ $pasien->pasien_id }}" data-nama-pasien="{{ $pasien->nama_pasien }}">Periksa</button>

                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data pasien unit gawat darurat</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 mb-2">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="small text-muted mb-2 text-start ps-3 pagination-info-text">
                            Showing {{ $pasiens_ugd->firstItem() }} to {{ $pasiens_ugd->lastItem() }} of
                            {{ $pasiens_ugd->total() }} results
                        </div>
                        <nav class="d-flex justify-content-center">
                            <ul class="pagination d-flex flex-row flex-wrap gap-2"
                                style="list-style-type: none; padding-left: 0; margin-bottom: 0;">
                                {{-- Previous Page Link --}}
                                @if ($pasiens_ugd->onFirstPage())
                                <li class="page-item disabled" aria-disabled="true" aria-label="Previous">
                                    <span class="page-link" aria-hidden="true">&laquo;</span>
                                </li>
                                @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $pasiens_ugd->previousPageUrl() }}" rel="prev"
                                        aria-label="Previous">&laquo;</a>
                                </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @php
                                $totalPages = $pasiens_ugd->lastPage();
                                $currentPage = $pasiens_ugd->currentPage();
                                $maxButtons = 3;

                                if ($totalPages <= $maxButtons) {
                                    $start=1;
                                    $end=$totalPages;
                                    } else {
                                    if ($currentPage==1) {
                                    $start=1;
                                    $end=3;
                                    } elseif ($currentPage==$totalPages) {
                                    $start=$totalPages - 2;
                                    $end=$totalPages;
                                    } else {
                                    $start=$currentPage - 1;
                                    $end=$currentPage + 1;
                                    }
                                    }
                                    @endphp

                                    @for ($page=$start; $page <=$end; $page++)
                                    @if ($page==$currentPage)
                                    <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                                    @else
                                    <li class="page-item"><a class="page-link" href="{{ $pasiens_ugd->url($page) }}">{{ $page }}</a></li>
                                    @endif
                                    @endfor

                                    {{-- Next Page Link --}}
                                    @if ($pasiens_ugd->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $pasiens_ugd->nextPageUrl() }}" rel="next"
                                            aria-label="Next">&raquo;</a>
                                    </li>
                                    @else
                                    <li class="page-item disabled" aria-disabled="true" aria-label="Next">
                                        <span class="page-link" aria-hidden="true">&raquo;</span>
                                    </li>
                                    @endif
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hasil Periksa -->
<div class="modal fade" id="modalHasilPeriksa" tabindex="-1" aria-labelledby="modalHasilPeriksaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
        <div class="modal-content" style="overflow-x: hidden;">
            <div class="modal-header d-flex justify-content-between">
                <h3 class="modal-title" id="modalHasilPeriksaLabel"><strong>Hasil Periksa Pasien</strong></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3" style="max-height: 400px; overflow-y: auto;">
                <div id="hasilPeriksaContent">
                    <div class="text-center text-muted">Memuat data hasil periksa...</div>
                </div>
                <div id="hasilPeriksaDetail" style="display:none; margin-top: 15px;">
                    <div id="hasilPeriksaDetailContent"></div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-secondary rounded btn-sm" id="backToListBtn">Kembali ke Daftar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Pasien UGD -->
<div class="modal fade" id="modalDetailPasien" tabindex="-1" aria-labelledby="modalDetailPasienLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
        <div class="modal-content" style="overflow-x: hidden;">
            <div class="modal-header d-flex justify-content-between">
                <h3 class="modal-title" id="modalDetailPasienLabel"><strong>Detail Pasien</strong></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3" style="max-height: 400px; overflow-y: auto;">
                <form>
                    <div class="container-fluid">
                        <!-- Personal Info -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="modalNoRekamMedis" class="form-label">No. Rekam Medis</label>
                                <input type="text" class="form-control form-control-sm" id="modalNoRekamMedis" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalNikPasien" class="form-label">NIK</label>
                                <input type="text" class="form-control form-control-sm" id="modalNikPasien" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalNamaPasien" class="form-label">Nama Pasien</label>
                                <input type="text" class="form-control form-control-sm" id="modalNamaPasien" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="modalTempatLahir" class="form-label">Tempat Lahir</label>
                                <input type="text" class="form-control form-control-sm" id="modalTempatLahir" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalTanggalLahir" class="form-label">Tanggal Lahir</label>
                                <input type="text" class="form-control form-control-sm" id="modalTanggalLahir" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalJenisKelamin" class="form-label">Jenis Kelamin</label>
                                <input type="text" class="form-control form-control-sm" id="modalJenisKelamin" readonly>
                            </div>
                        </div>
                        <!-- Health Info -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="modalGolonganDarah" class="form-label">Golongan Darah</label>
                                <input type="text" class="form-control form-control-sm" id="modalGolonganDarah"
                                    readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalAgama" class="form-label">Agama</label>
                                <input type="text" class="form-control form-control-sm" id="modalAgama" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalPekerjaan" class="form-label">Pekerjaan</label>
                                <input type="text" class="form-control form-control-sm" id="modalPekerjaan" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="modalStatusPernikahan" class="form-label">Status Pernikahan</label>
                                <input type="text" class="form-control form-control-sm" id="modalStatusPernikahan"
                                    readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalKepalaKeluarga" class="form-label">Kepala Keluarga</label>
                                <input type="text" class="form-control form-control-sm" id="modalKepalaKeluarga"
                                    readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalNoHp" class="form-label">Nomor Telepon</label>
                                <input type="text" class="form-control form-control-sm" id="modalNoHp" readonly>
                            </div>
                        </div>
                        <!-- Address Info -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="modalAlamat" class="form-label">Alamat</label>
                                <input type="text" class="form-control form-control-sm" id="modalAlamat" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="modalRt" class="form-label">RT</label>
                                <input type="text" class="form-control form-control-sm" id="modalRt" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalRw" class="form-label">RW</label>
                                <input type="text" class="form-control form-control-sm" id="modalRw" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalKelurahan" class="form-label">Kelurahan</label>
                                <input type="text" class="form-control form-control-sm" id="modalKelurahan" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="modalKecamatan" class="form-label">Kecamatan</label>
                                <input type="text" class="form-control form-control-sm" id="modalKecamatan" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalKabupaten" class="form-label">Kabupaten</label>
                                <input type="text" class="form-control form-control-sm" id="modalKabupaten" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalProvinsi" class="form-label">Provinsi</label>
                                <input type="text" class="form-control form-control-sm" id="modalProvinsi" readonly>
                            </div>
                        </div>
                        <!-- Insurance Info -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="modalJaminan" class="form-label">Jaminan Kesehatan</label>
                                <input type="text" class="form-control form-control-sm" id="modalJaminan" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="modalNoKepesertaan" class="form-label">No. Kepesertaan</label>
                                <input type="text" class="form-control form-control-sm" id="modalNoKepesertaan"
                                    readonly>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<!-- Modal Analisa -->
<div class="modal fade" id="modalAnalisa" tabindex="-1" aria-labelledby="modalAnalisaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
        <div class="modal-content" style="overflow-x: hidden;">
            <div class="modal-header d-flex justify-content-between">
                <h3 class="modal-title" id="modalAnalisaLabel">
                    <strong>Analisa <span class="nama-pasien" id="nama_pasien_display"></span></strong>
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow-y: auto; padding: 10px;">
                @csrf
                <form id="formAnalisa" method="POST" action="{{ route('rawatinap.hasilanalisa.store') }}">
                    @csrf
                    <input type="hidden" id="pasien_id" name="pasien_id">
                    <!-- Removed hidden input no_rekam_medis, hanya gunakan pasien_id -->
                    <!-- Tanda Vital -->
                    <div class="mb-3">
                        <h5>Tanda Vital</h5>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="tekananDarah" class="form-label">Tekanan Darah (mmHg)</label>
                            <input type="text" class="form-control form-control-sm" id="tekananDarah"
                                name="tekanan_darah">
                        </div>
                        <div class="col-6">
                            <label for="frekuensiNadi" class="form-label">Frekuensi Nadi (/menit)</label>
                            <input type="text" class="form-control form-control-sm" id="frekuensiNadi"
                                name="frekuensi_nadi">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="suhu" class="form-label">Suhu (°C)</label>
                            <input type="text" class="form-control form-control-sm" id="suhu" name="suhu">
                        </div>
                        <div class="col-6">
                            <label for="frekuensiNafas" class="form-label">Frekuensi Nafas (/menit)</label>
                            <input type="text" class="form-control form-control-sm" id="frekuensiNafas"
                                name="frekuensi_nafas">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="skorNyeri" class="form-label">Skor Nyeri</label>
                            <input type="text" class="form-control form-control-sm" id="skorNyeri" name="skor_nyeri">
                        </div>
                        <div class="col-6">
                            <label for="skorJatuh" class="form-label">Skor Jatuh</label>
                            <input type="text" class="form-control form-control-sm" id="skorJatuh" name="skor_jatuh">
                        </div>
                    </div>
                    <hr>
                    <!-- Antropometri -->
                    <div class="mb-3">
                        <h5>Antropometri</h5>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="beratBadan" class="form-label">Berat Badan</label>
                            <input type="text" class="form-control form-control-sm" id="beratBadan" name="berat_badan">
                        </div>
                        <div class="col-6">
                            <label for="tinggiBadan" class="form-label">Tinggi Badan</label>
                            <input type="text" class="form-control form-control-sm" id="tinggiBadan"
                                name="tinggi_badan">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="lingkarKepala" class="form-label">Lingkar Kepala</label>
                            <input type="text" class="form-control form-control-sm" id="lingkarKepala"
                                name="lingkar_kepala">
                        </div>
                        <div class="col-6">
                            <label for="imt" class="form-label">IMT</label>
                            <input type="text" class="form-control form-control-sm" id="imt" name="imt">
                        </div>
                    </div>
                    <hr>
                    <!-- Fungsional -->
                    <div class="mb-3">
                        <h5>Fungsional</h5>

                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="alatBantu" class="form-label">Alat Bantu</label>
                            <input type="text" class="form-control form-control-sm" id="alatBantu" name="alat_bantu">
                        </div>
                        <div class="col-6">
                            <label for="prosthesa" class="form-label">Prosthesa</label>
                            <input type="text" class="form-control form-control-sm" id="prosthesa" name="prosthesa">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="cacatTubuh" class="form-label">Cacat Tubuh</label>
                            <input type="text" class="form-control form-control-sm" id="cacatTubuh" name="cacat_tubuh">
                        </div>
                        <div class="col-6">
                            <label for="adlMandiri" class="form-label">ADL Mandiri</label>
                            <input type="text" class="form-control form-control-sm" id="adlMandiri" name="adl_mandiri">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="riwayatJatuh" class="form-label">Riwayat Jatuh</label>
                            <input type="text" class="form-control form-control-sm" id="riwayatJatuh"
                                name="riwayat_jatuh">
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-6">
                            <h5>Status Psikologi</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxDepresi"
                                    name="status_psikologi[]" value="Depresi">
                                <label class="form-check-label" for="checkboxDepresi">Depresi</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxTakut"
                                    name="status_psikologi[]" value="Takut">
                                <label class="form-check-label" for="checkboxTakut">Takut</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxAgresif"
                                    name="status_psikologi[]" value="Agresif">
                                <label class="form-check-label" for="checkboxAgresif">Agresif</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxMelukaiDiri"
                                    name="status_psikologi[]" value="Melukai diri sendiri/Orang lain">
                                <label class="form-check-label" for="checkboxMelukaiDiri">Melukai diri sendiri/Orang
                                    lain</label>
                            </div>
                        </div>

                        <div class="col-6">
                            <h5>Hambatan Edukasi</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxBahasa"
                                    name="hambatan_edukasi[]" value="Bahasa">
                                <label class="form-check-label" for="checkboxBahasa">Bahasa</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxCacatFisik"
                                    name="hambatan_edukasi[]" value="Cacat Fisik">
                                <label class="form-check-label" for="checkboxCacatFisik">Cacat Fisik</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxCacatKognitif"
                                    name="hambatan_edukasi[]" value="Cacat Kognitif">
                                <label class="form-check-label" for="checkboxCacatKognitif">Cacat Kognitif</label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="alergi" class="form-label">Alergi</label>
                            <textarea type="text" class="form-control form-control-sm" id="alergi"
                                name="alergi"></textarea>
                        </div>
                        <div class="col-6">
                            <label for="catatan" class="form-label">Catatan</label>
                            <textarea type="text" class="form-control form-control-sm" id="catatan"
                                name="catatan"></textarea>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="checkboxRawatInap" name="rawat_inap"
                                    value="1">
                                <label class="form-check-label" for="checkboxRawatInap">Rawat Inap</label>
                            </div>
                            <h5>Ruangan</h5>
                            <input type="text" class="form-control" id="selectPoli" name="ruangan" placeholder="Masukkan Ruangan" disabled>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-success ms-2" id="btnSimpanAnalisa">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Periksa -->
<div class="modal fade" id="modalPeriksa" tabindex="-1" aria-labelledby="modalPeriksaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
        <div class="modal-content" style="overflow-x: hidden;">
            <div class="modal-header d-flex justify-content-between">
                <h3 class="modal-title" id="modalPeriksaLabel"><strong>Periksa Pasien <span id="periksa_nama_pasien_display"></span></strong></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3" style="max-height: 400px; overflow-y: auto;">
                <form id="formPeriksa" method="POST" action="{{ url('/rawatinap/periksa/store') }}">
                    @csrf
                    <input type="hidden" id="periksa_pasien_id" name="pasien_id">

                    <div class="mb-3">
                        <label for="nama_pasien" class="form-label">Nama Pasien</label>
                        <input type="text" class="form-control" id="nama_pasien" name="nama_pasien" readonly>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                        </div>
                        <div class="col-md-6">
                            <label for="waktu" class="form-label">Waktu</label>
                            <input type="time" class="form-control" id="waktu" name="waktu" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="soap" class="form-label">SOAP</label>
                        <textarea class="form-control" id="soap" name="soap" rows="4" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="intruksi_tenaga_kerja" class="form-label">Intruksi Tenaga Kerja</label>
                        <textarea class="form-control" id="intruksi_tenaga_kerja" name="intruksi_tenaga_kerja" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="penanggung_jawab" class="form-label">Penanggung Jawab</label>
                        <select class="form-select" id="penanggung_jawab" name="penanggung_jawab" required>
                            <option value="" disabled selected>Pilih Penanggung Jawab</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="checkboxPasienPulang" name="pasien_pulang" value="1">
                        <label class="form-check-label" for="checkboxPasienPulang">Pasien diperbolehkan pulang</label>
                    </div>
                    <div class="mb-3" id="divTanggalPulang" style="display:none;">
                        <label for="tanggal_pulang" class="form-label">Tanggal Pulang</label>
                        <input type="date" class="form-control" id="tanggal_pulang" name="tanggal_pulang">
                    </div>
                    <div class="mb-3" id="divWaktuPulang" style="display:none;">
                        <label for="waktu_pulang" class="form-label">Waktu Pulang</label>
                        <input type="time" class="form-control" id="waktu_pulang" name="waktu_pulang">
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-end mt-3">
                <!-- <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Tutup</button> -->
                <button type="submit" form="formPeriksa" class="btn btn-success">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hasil Analisa -->
<div class="modal fade" id="modalHasilAnalisa" tabindex="-1" aria-labelledby="modalHasilAnalisaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
        <div class="modal-content modal-analisa-position-relative" style="overflow-x: hidden;">
            <div class="modal-header d-flex justify-content-between">
                <h3 class="modal-title" id="modalAnalisaLabel">
                    <strong>Hasil Analisa <span class="nama-pasien" id="nama_pasien_display"></span></strong>
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-analisa-body-scroll" style="max-height: 400px; overflow-y: auto; padding: 16px 24px 16px 24px;">
                <form>
                    <input type="hidden" id="modalAnalisaNoRekamMedis" readonly>
                    <!-- Tanda Vital -->
                    <div class="mb-3">
                        <h5 class="mb-3">Tanda Vital</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Tekanan Darah (mmHg)</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaTekananDarah" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Frekuensi Nadi (/menit)</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaFrekuensiNadi" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Suhu (°C)</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaSuhu" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Frekuensi Nafas (/menit)</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaFrekuensiNafas" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Skor Nyeri</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaSkorNyeri" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Skor Jatuh</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaSkorJatuh" readonly>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!-- Antropometri -->
                    <div class="mb-3">
                        <h5 class="mb-3">Antropometri</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Berat Badan</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaBeratBadan" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tinggi Badan</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaTinggiBadan" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lingkar Kepala</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaLingkarKepala" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">IMT</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaIMT" readonly>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!-- Fungsional -->
                    <div class="mb-3">
                        <h5 class="mb-3">Fungsional</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Alat Bantu</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaAlatBantu" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Prosthesa</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaProsthesa" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Cacat Tubuh</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaCacatTubuh" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ADL Mandiri</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaADLMandiri" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Riwayat Jatuh</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaRiwayatJatuh" readonly>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!-- Status Psikologi & Hambatan Edukasi -->
                    <div class="mb-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <h5>Status Psikologi</h5>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaStatusPsikologi" readonly>
                            </div>
                            <div class="col-md-6">
                                <h5>Hambatan Edukasi</h5>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaHambatanEdukasi" readonly>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!-- Alergi & Catatan -->
                    <div class="mb-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Alergi</label>
                                <textarea class="form-control form-control-sm" id="modalAnalisaAlergi" readonly></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Catatan</label>
                                <textarea class="form-control form-control-sm" id="modalAnalisaCatatan" readonly></textarea>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!-- Poli Tujuan & Penanggung Jawab -->
                    <div class="mb-1">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Ruangan</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaPoliTujuan" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Penanggung Jawab</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaPenanggungJawab" readonly>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Modal footer dihapus agar tidak ada tombol tutup di bawah -->
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    $(function() {
        var pasienIdGlobal = null;

        function showList(pasienId) {
            var $content = $('#hasilPeriksaContent');
            var $detail = $('#hasilPeriksaDetail');
            $detail.hide();
            $content.show();
            $content.html('<div class="text-center text-muted">Memuat data hasil periksa...</div>');

            $.get('/rawatinap/hasilperiksa/data/' + encodeURIComponent(pasienId), function(res) {
                if (res.success && res.data && res.data.length > 0) {
                    var data = res.data;
                    var html = '<ul>';
                    data.forEach(function(item, index) {
                        html += '<li style="display:flex; justify-content:space-between; align-items:center; padding: 0.375rem 0;">';

                        function getIndonesianDayName(dateString) {
                            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                            const date = new Date(dateString);
                            return days[date.getDay()] || '';
                        }

                        function formatDateToDDMMYYYY(dateString) {
                            const date = new Date(dateString);
                            const day = String(date.getDate()).padStart(2, '0');
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const year = date.getFullYear();
                            return day + '-' + month + '-' + year;
                        }
                        var dayName = getIndonesianDayName(item.tanggal);
                        var formattedDate = formatDateToDDMMYYYY(item.tanggal);
                        html += '<div style="text-align:left;">' + (index + 1) + '. <strong>Hari/Tanggal:</strong> ' + dayName + ', ' + formattedDate + ' <strong>Waktu:</strong> ' + item.waktu + '</div>';
                        html += '<button class="btn btn-primary rounded btn-sm lihat-detail-btn" data-index="' + index + '">Lihat</button>';
                        html += '</li>';
                    });
                    html += '</ul>';
                    $content.html(html);
                } else {
                    $content.html('<div class="text-center text-muted">Tidak ada hasil periksa pasien.</div>');
                }
            }).fail(function() {
                $content.html('<div class="text-danger">Tidak ada data hasil periksa pasien</div>');
            });
        }

        function showDetail(index) {
            var $content = $('#hasilPeriksaContent');
            var $detail = $('#hasilPeriksaDetail');
            $content.hide();
            $detail.show();

            $.get('/rawatinap/hasilperiksa/data/' + encodeURIComponent(pasienIdGlobal), function(res) {
                if (res.success && res.data && res.data.length > index) {
                    var item = res.data[index];

                    function getIndonesianDayName(dateString) {
                        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                        const date = new Date(dateString);
                        return days[date.getDay()] || '';
                    }
                    var dayName = getIndonesianDayName(item.tanggal);

                    function formatDateToDDMMYYYY(dateString) {
                        const date = new Date(dateString);
                        const day = String(date.getDate()).padStart(2, '0');
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const year = date.getFullYear();
                        return day + '-' + month + '-' + year;
                    }
                    var formattedDate = dayName + ', ' + formatDateToDDMMYYYY(item.tanggal);

                    var html = '<table class="table table-bordered">';
                    html += '<tr><th>Hari/Tanggal</th><td>' + formattedDate + '</td></tr>';
                    html += '<tr><th>Waktu</th><td>' + item.waktu + '</td></tr>';
                    html += '<tr><th>SOAP</th><td>' + (item.soap || '-') + '</td></tr>';
                    html += '<tr><th>Intruksi Tenaga Kerja</th><td>' + (item.intruksi_tenagakerja || '-') + '</td></tr>';
                    html += '<tr><th>Penanggung Jawab</th><td>' + (item.penanggung_jawab_nama || item.penanggung_jawab || '-') + '</td></tr>';
                    html += '</table>';
                    $('#hasilPeriksaDetailContent').html(html);
                } else {
                    $('#hasilPeriksaDetailContent').html('<div class="text-center text-muted">Tidak ada detail hasil periksa.</div>');
                }
            }).fail(function() {
                $('#hasilPeriksaDetailContent').html('<div class="text-center text-muted">Tidak ada data hasil periksa pasien.</div>');
            });
        }

        $(document).on('click', '.btn-hasil-periksa', function() {
            pasienIdGlobal = $(this).data('pasien-id');
            showList(pasienIdGlobal);
        });

        $(document).on('click', '.lihat-detail-btn', function() {
            var index = $(this).data('index');
            showDetail(index);
        });

        $('#backToListBtn').on('click', function() {
            if (pasienIdGlobal) {
                showList(pasienIdGlobal);
            }
        });
    });
</script>

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
    document.addEventListener('DOMContentLoaded', function() {
        var modalDetailPasien = document.getElementById('modalDetailPasien');
        if (modalDetailPasien) {
            modalDetailPasien.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var pasienId = button.getAttribute('data-pasien-id');

                // Clear modal fields before loading new data
                var fields = [
                    'modalNoRekamMedis', 'modalNikPasien', 'modalNamaPasien', 'modalTempatLahir', 'modalTanggalLahir',
                    'modalJenisKelamin', 'modalGolonganDarah', 'modalAgama', 'modalPekerjaan', 'modalStatusPernikahan',
                    'modalKepalaKeluarga', 'modalNoHp', 'modalAlamat', 'modalRt', 'modalRw', 'modalKelurahan',
                    'modalKecamatan', 'modalKabupaten', 'modalProvinsi', 'modalJaminan', 'modalNoKepesertaan'
                ];
                fields.forEach(function(fieldId) {
                    var el = document.getElementById(fieldId);
                    if (el) el.value = '';
                });

                if (!pasienId) {
                    console.error('Pasien ID not found on button');
                    return;
                }

                console.log('Fetching pasien data for pasienId:', pasienId);
                fetch('/rawatinap/pasien/by-id/' + encodeURIComponent(pasienId))
                    .then(response => response.json())
                    .then(data => {
                        console.log('Fetch response data:', data);
                        if (data.success && data.data) {
                            var pasien = data.data;
                            document.getElementById('modalNoRekamMedis').value = pasien.no_rekam_medis || '';
                            document.getElementById('modalNikPasien').value = pasien.nik || '';
                            document.getElementById('modalNamaPasien').value = pasien.nama_pasien || '';
                            document.getElementById('modalTempatLahir').value = pasien.tempat_lahir || '';
                            document.getElementById('modalTanggalLahir').value = pasien.tanggal_lahir || '';
                            document.getElementById('modalJenisKelamin').value = pasien.jenis_kelamin || '';
                            document.getElementById('modalGolonganDarah').value = pasien.gol_darah || '';
                            document.getElementById('modalAgama').value = pasien.agama || '';
                            document.getElementById('modalPekerjaan').value = pasien.pekerjaan || '';
                            document.getElementById('modalStatusPernikahan').value = pasien.status_pernikahan || '';
                            document.getElementById('modalKepalaKeluarga').value = pasien.kepala_keluarga || '';
                            document.getElementById('modalNoHp').value = pasien.no_hp || '';
                            document.getElementById('modalAlamat').value = pasien.alamat_jalan || '';
                            document.getElementById('modalRt').value = pasien.rt || '';
                            document.getElementById('modalRw').value = pasien.rw || '';
                            document.getElementById('modalKelurahan').value = pasien.kelurahan || '';
                            document.getElementById('modalKecamatan').value = pasien.kecamatan || '';
                            document.getElementById('modalKabupaten').value = pasien.kabupaten || '';
                            document.getElementById('modalProvinsi').value = pasien.provinsi || '';
                            document.getElementById('modalJaminan').value = pasien.jaminan_kesehatan || '';
                            document.getElementById('modalNoKepesertaan').value = pasien.nomor_kepesertaan || '';
                        } else {
                            alert('Data pasien tidak ditemukan.');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching pasien data:', error);
                        alert('Gagal mengambil data pasien.');
                    });
            });
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalPeriksa = document.getElementById('modalPeriksa');
        if (!modalPeriksa) {
            console.error('modalPeriksa element not found');
            return;
        }
        modalPeriksa.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var pasienId = button.getAttribute('data-pasien-id');
            var namaPasien = button.getAttribute('data-nama-pasien');
            var pasienIdInput = modalPeriksa.querySelector('#periksa_pasien_id');
            var namaPasienInput = modalPeriksa.querySelector('#nama_pasien');
            var namaPasienDisplay = modalPeriksa.querySelector('#periksa_nama_pasien_display');
            if (pasienIdInput) {
                pasienIdInput.value = pasienId || '';
            }
            if (namaPasienInput) {
                namaPasienInput.value = namaPasien || '';
            }
            if (namaPasienDisplay) {
                namaPasienDisplay.textContent = namaPasien || '';
            }
            // Clear other form fields except nama_pasien on modal show
            var form = modalPeriksa.querySelector('#formPeriksa');
            if (form) {
                // Save current nama_pasien value
                var currentNama = namaPasienInput.value;
                form.reset();
                // Restore nama_pasien value after reset
                namaPasienInput.value = currentNama;

                // Set tanggal dan waktu sekarang
                var now = new Date();
                var yyyy = now.getFullYear();
                var mm = String(now.getMonth() + 1).padStart(2, '0');
                var dd = String(now.getDate()).padStart(2, '0');
                var hh = String(now.getHours()).padStart(2, '0');
                var min = String(now.getMinutes()).padStart(2, '0');
                var todayStr = yyyy + '-' + mm + '-' + dd;
                var timeStr = hh + ':' + min;
                var tanggalInput = form.querySelector('#tanggal');
                var waktuInput = form.querySelector('#waktu');
                if (tanggalInput) {
                    tanggalInput.value = todayStr;
                }
                if (waktuInput) {
                    waktuInput.value = timeStr;
                }
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalAnalisa = document.getElementById('modalAnalisa');
        if (!modalAnalisa) {
            console.error('modalAnalisa element not found');
            return;
        }
        var btnSimpanAnalisa = modalAnalisa.querySelector('#btnSimpanAnalisa');
        var checkboxRawatInap = document.getElementById('checkboxRawatInap');
        var inputRuangan = document.getElementById('selectPoli');

        // Modal show: set pasien_id dan nama pasien
        modalAnalisa.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var pasienData = button.getAttribute('data-pasien');
            var pasienId = button.getAttribute('data-pasien-id');
            var pasien = pasienData ? JSON.parse(pasienData) : null;

            // Set pasien_id
            var pasienIdInput = modalAnalisa.querySelector('#pasien_id');
            if (pasienIdInput) {
                pasienIdInput.value = pasienId || (pasien ? pasien.pasien_id : '');
            }

            // Set nama pasien di header
            var namaPasienDisplay = modalAnalisa.querySelector('#nama_pasien_display');
            if (namaPasienDisplay) {
                namaPasienDisplay.textContent = pasien ? (pasien.nama_pasien || '') : '';
            }

            // Aktifkan tombol simpan hanya jika pasien_id terisi
            if (btnSimpanAnalisa) {
                btnSimpanAnalisa.disabled = !(pasienIdInput && pasienIdInput.value);
            }
        });

        // Atur enable/disable ruangan sesuai checkbox Rawat Inap
        if (checkboxRawatInap && inputRuangan) {
            function toggleRuangan() {
                if (checkboxRawatInap.checked) {
                    inputRuangan.disabled = false;
                } else {
                    inputRuangan.value = '';
                    inputRuangan.disabled = true;
                }
            }
            checkboxRawatInap.addEventListener('change', toggleRuangan);
            toggleRuangan(); // initial state
        }

        // Gabungkan seluruh logic submit formAnalisa di sini (hanya satu handler!)
        $('#formAnalisa').off('submit').on('submit', function(e) {
            // Validasi: jika ruangan diisi, checkbox harus dicentang
            var ruangan = inputRuangan ? inputRuangan.value.trim() : '';
            if (ruangan && (!checkboxRawatInap || !checkboxRawatInap.checked)) {
                alert('Jika ingin mengisi ruangan, centang dulu Rawat Inap!');
                if (checkboxRawatInap) checkboxRawatInap.focus();
                e.preventDefault();
                return false;
            }
            e.preventDefault();
            var form = this;
            var formData = $(form).serialize();
            var url = $(form).attr('action');
            var btn = $(form).find('button[type="submit"]');
            btn.prop('disabled', true).text('Menyimpan...');
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                success: function(res) {
                    if (res.success) {
                        toastr.options = {
                            "positionClass": "toast-top-right",
                            "timeOut": "2000",
                            "closeButton": true,
                            "progressBar": true
                        };
                        toastr.success(res.message || 'Data analisa berhasil disimpan.');
                        $('#modalAnalisa').modal('hide');
                        // Reload tabel data pasien UGD (jika pakai DataTables, gunakan .ajax.reload(); jika tidak, reload halaman)
                        if ($.fn.DataTable && $('#pasienTable').hasClass('dataTable')) {
                            $('#pasienTable').DataTable().ajax.reload(null, false);
                        } else {
                            // fallback: reload halaman
                            setTimeout(function() {
                                location.reload();
                            }, 1200);
                        }
                        form.reset();
                    } else {
                        toastr.error(res.message || 'Gagal menyimpan data analisa.');
                    }
                },
                error: function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        var msg = Object.values(errors).map(function(e) {
                            return e[0];
                        }).join('\n');
                        toastr.error(msg);
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        toastr.error(xhr.responseJSON.message);
                    } else {
                        toastr.error('Terjadi kesalahan saat menyimpan data.');
                    }
                },
                complete: function() {
                    btn.prop('disabled', false).text('Simpan');
                }
            });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle Hasil Analisa button click
        var hasilAnalisaModal = document.getElementById('modalHasilAnalisa');
        if (!hasilAnalisaModal) {
            console.error('modalHasilAnalisa element not found');
            return;
        }

        document.querySelectorAll('.btn-hasil-analisa').forEach(function(button) {
            button.addEventListener('click', function(event) {
                var pasienData = button.getAttribute('data-pasien');
                var pasien = pasienData ? JSON.parse(pasienData) : null;

                if (!pasien) {
                    alert('Data pasien tidak tersedia.');
                    return;
                }

                // Fetch latest analysis data from server
                fetch('/rawatinap/hasilanalisa/riwayat/' + pasien.pasien_id)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success && data.data) {
                            var analisa = data.data;

                            // Show form and hide no data message
                            var modalBody = hasilAnalisaModal.querySelector('.modal-body');
                            var form = modalBody.querySelector('form');
                            if (form) {
                                form.style.display = 'block';
                            }
                            var messageContainer = modalBody.querySelector('#noAnalisaMessage');
                            if (messageContainer) {
                                messageContainer.style.display = 'none';
                            }

                            // Populate modal fields with fetched data
                            document.getElementById('modalAnalisaLabel').textContent = 'Hasil Analisa ' + (pasien.nama_pasien || '');
                            document.getElementById('modalAnalisaNoRekamMedis').value = analisa.no_rekam_medis || '';
                            document.getElementById('modalAnalisaTekananDarah').value = analisa.tekanan_darah || '';
                            document.getElementById('modalAnalisaFrekuensiNadi').value = analisa.frekuensi_nadi || '';
                            document.getElementById('modalAnalisaSuhu').value = analisa.suhu || '';
                            document.getElementById('modalAnalisaFrekuensiNafas').value = analisa.frekuensi_nafas || '';
                            document.getElementById('modalAnalisaSkorNyeri').value = analisa.skor_nyeri || '';
                            document.getElementById('modalAnalisaSkorJatuh').value = analisa.skor_jatuh || '';
                            document.getElementById('modalAnalisaBeratBadan').value = analisa.berat_badan || '';
                            document.getElementById('modalAnalisaTinggiBadan').value = analisa.tinggi_badan || '';
                            document.getElementById('modalAnalisaLingkarKepala').value = analisa.lingkar_kepala || '';
                            document.getElementById('modalAnalisaIMT').value = analisa.imt || '';
                            document.getElementById('modalAnalisaAlatBantu').value = analisa.alat_bantu || '';
                            document.getElementById('modalAnalisaProsthesa').value = analisa.prosthesa || '';
                            document.getElementById('modalAnalisaCacatTubuh').value = analisa.cacat_tubuh || '';
                            document.getElementById('modalAnalisaADLMandiri').value = analisa.adl_mandiri || '';
                            document.getElementById('modalAnalisaRiwayatJatuh').value = analisa.riwayat_jatuh || '';
                            var statusPsikologi = analisa.status_psikologi || '';
                            var hambatanEdukasi = analisa.hambatan_edukasi || '';

                            // Try to parse JSON array strings and join elements, fallback to original string
                            try {
                                var parsedStatus = JSON.parse(statusPsikologi);
                                if (Array.isArray(parsedStatus)) {
                                    statusPsikologi = parsedStatus.join(', ');
                                }
                            } catch (e) {
                                // Not a JSON array string, keep original
                            }

                            try {
                                var parsedHambatan = JSON.parse(hambatanEdukasi);
                                if (Array.isArray(parsedHambatan)) {
                                    hambatanEdukasi = parsedHambatan.join(', ');
                                }
                            } catch (e) {
                                // Not a JSON array string, keep original
                            }

                            document.getElementById('modalAnalisaStatusPsikologi').value = statusPsikologi;
                            document.getElementById('modalAnalisaHambatanEdukasi').value = hambatanEdukasi;
                            document.getElementById('modalAnalisaAlergi').value = analisa.alergi || '';
                            document.getElementById('modalAnalisaCatatan').value = analisa.catatan || '';
                            document.getElementById('modalAnalisaPoliTujuan').value = analisa.ruangan && analisa.ruangan.trim() !== '' ? analisa.ruangan : 'UGD';
                            document.getElementById('modalAnalisaPenanggungJawab').value = (analisa.penanggung_jawab_user && analisa.penanggung_jawab_user.name) || '';

                            // Show the modal
                            var modal = new bootstrap.Modal(hasilAnalisaModal);
                            modal.show();
                        } else {
                            // Hide form fields and show only message
                            var modalBody = hasilAnalisaModal.querySelector('.modal-body');
                            var form = modalBody.querySelector('form');
                            if (form) {
                                form.style.display = 'none';
                            }
                            // Create or show message container
                            var messageContainer = modalBody.querySelector('#noAnalisaMessage');
                            if (!messageContainer) {
                                messageContainer = document.createElement('p');
                                messageContainer.id = 'noAnalisaMessage';
                                messageContainer.className = 'text-center text-muted';
                                messageContainer.textContent = 'Tidak ada hasil analisa pasien.';
                                modalBody.appendChild(messageContainer);
                            } else {
                                messageContainer.style.display = 'block';
                            }
                            var modal = new bootstrap.Modal(hasilAnalisaModal);
                            modal.show();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching data analisa:', error);
                        // Hide form fields and show only message
                        var modalBody = hasilAnalisaModal.querySelector('.modal-body');
                        var form = modalBody.querySelector('form');
                        if (form) {
                            form.style.display = 'none';
                        }
                        // Create or show message container
                        var messageContainer = modalBody.querySelector('#noAnalisaMessage');
                        if (!messageContainer) {
                            messageContainer = document.createElement('p');
                            messageContainer.id = 'noAnalisaMessage';
                            messageContainer.className = 'text-center text-muted';
                            messageContainer.textContent = 'Tidak ada hasil analisa pasien.';
                            modalBody.appendChild(messageContainer);
                        } else {
                            messageContainer.style.display = 'block';
                        }
                        var modal = new bootstrap.Modal(hasilAnalisaModal);
                        modal.show();
                    });
            });
        });
    });
</script>
<script>
    $(function() {
        $('#btnCariPasien').on('click', function() {
            var nomor = $('#nomor_kepesertaan_cari').val().trim();
            if (!nomor) {
                alert('Nomor Kepesertaan wajib diisi!');
                return;
            }
            // AJAX cari pasien berdasarkan nomor kepesertaan
            $.get('/rawatinap/pasien/cari-nomor-kepesertaan', {
                nomor_kepesertaan: nomor
            }, function(res) {
                if (res && res.id) {
                    // Set input hidden pasien_id sesuai hasil pencarian
                    $('#pasien_id_cari').val(res.id);
                    $('#hasilCariPasien').show();
                    $('#formFieldUgd').show();
                    $('#nama_pasien_cari').val(res.nama_pasien);
                    $('#no_rekam_medis_cari').val(res.no_rekam_medis);
                    $('#nik_pasien_cari').val(res.nik);
                    $('#tgl_lahir_pasien_cari').val(res.tanggal_lahir);
                    $('#jk_pasien_cari').val(res.jenis_kelamin);
                    $('#alamat_pasien_cari').val(res.alamat_jalan);
                    $('#pasien_id_cari').val(res.id); // PENTING: set value pasien_id
                    console.log('DEBUG: pasien_id di-set ke', res.id);
                    $('#btnCariPasien').hide();
                    $('#btnTambahPasienUgd').show();
                    $('#nomor_kepesertaan_cari').prop('readonly', true);
                } else {
                    $('#pasien_id_cari').val(''); // Kosongkan jika tidak ditemukan
                    alert('Pasien tidak ditemukan!');
                    $('#hasilCariPasien').hide();
                    $('#formFieldUgd').hide();
                    $('#btnTambahPasienUgd').hide();
                }
            }, 'json').fail(function(xhr, status, error) {
                $('#pasien_id_cari').val(''); // Kosongkan jika error
                alert('Gagal mencari pasien!');
            });
        });
        // Reset modal saat dibuka
        $('#modalTambahPasien').on('show.bs.modal', function() {
            $('#formTambahPasienUgd')[0].reset();
            $('#hasilCariPasien').hide();
            $('#formFieldUgd').hide();
            $('#btnTambahPasienUgd').hide();
            $('#btnCariPasien').show();
            $('#nomor_kepesertaan_cari').prop('readonly', false);

            // Set tanggal masuk otomatis ke hari ini saat modal dibuka
            var today = new Date();
            var yyyy = today.getFullYear();
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var dd = String(today.getDate()).padStart(2, '0');
            var todayStr = yyyy + '-' + mm + '-' + dd;
            $('#tanggal_masuk').val(todayStr);
        });

        // Ganti submit form menjadi AJAX
        $('#formTambahPasienUgd').off('submit').on('submit', function(e) {
            var pasienId = $('#pasien_id_cari').val();
            if (!pasienId) {
                alert('Silakan cari dan pilih pasien terlebih dahulu!');
                e.preventDefault();
                return false;
            }
            // Debug: pastikan pasien_id benar-benar terkirim
            console.log('DEBUG SUBMIT: pasien_id yang dikirim:', pasienId);

            e.preventDefault();
            var form = this;
            var formData = $(form).serialize();
            var url = $(form).attr('action');
            var btn = $('#btnTambahPasienUgd');
            btn.prop('disabled', true).text('Menyimpan...');
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                success: function(res) {
                    if (res.success && res.data) {
                        // Tambahkan baris baru ke tabel
                        var data = res.data;
                        var rowCount = $('#pasienTable tbody tr').length;
                        var newRow = '<tr>' +
                            '<td>' + (rowCount + 1) + '</td>' +
                            '<td>' + (data.tanggal_masuk || '') + '</td>' +
                            '<td>' + (data.nama_pasien || '') + '</td>' +
                            '<td>' + (data.umur || '') + '</td>' +
                            '<td>' + (data.status || '') + '</td>' +
                            '<td>' +
                            '<button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalDetailPasien" data-pasien="' +
                            encodeURIComponent(data.pasien_json) + '">Selengkapnya</button> ' +
                            '<button class="btn btn-warning btn-sm ms-1" data-bs-toggle="modal" data-bs-target="#modalAnalisa" data-pasien="' +
                            encodeURIComponent(data.pasien_json) + '">Analisa</button>' +
                            '</td>' +
                            '</tr>';
                        $('#pasienTable tbody').append(newRow);
                        // Reset dan tutup modal
                        $('#modalTambahPasien').modal('hide');
                        form.reset();
                        $('#hasilCariPasien').hide();
                        $('#formFieldUgd').hide();
                        $('#btnTambahPasienUgd').hide();
                        $('#btnCariPasien').show();
                        $('#nomor_kepesertaan_cari').prop('readonly', false);
                        // Optional: scroll ke baris baru
                        // toastr.success('Pasien UGD berhasil ditambahkan!');
                    } else {
                        alert(res.message || 'Gagal menambah pasien UGD.');
                    }
                },
                error: function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        var msg = Object.values(errors).map(function(e) {
                            return e[0];
                        }).join('\n');
                        alert(msg);
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        alert(xhr.responseJSON.message);
                    } else {
                        alert('Terjadi kesalahan saat menyimpan data.');
                    }
                },
                complete: function() {
                    btn.prop('disabled', false).text('Tambah');
                }
            });
        });
    });

    // AJAX submit for formPeriksa
    $(document).ready(function() {
        $('#formPeriksa').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var btn = form.find('button[type="submit"]');
            btn.prop('disabled', true).text('Menyimpan...');
            $.ajax({
                url: url,
                method: 'POST',
                data: form.serialize(),
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.message || 'Data hasil periksa berhasil disimpan.');
                        $('#modalPeriksa').modal('hide');
                        // Optionally reload or refresh data table here
                        if ($.fn.DataTable && $('#pasienTable').hasClass('dataTable')) {
                            $('#pasienTable').DataTable().ajax.reload(null, false);
                        } else {
                            setTimeout(function() {
                                location.reload();
                            }, 1200);
                        }
                        form[0].reset();
                    } else {
                        toastr.error(res.message || 'Gagal menyimpan data hasil periksa.');
                    }
                },
                error: function(xhr) {
                    console.error('AJAX error response:', xhr);
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        var msg = Object.values(errors).map(function(e) {
                            return e[0];
                        }).join('\n');
                        toastr.error(msg);
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        toastr.error(xhr.responseJSON.message);
                    } else {
                        toastr.error('Terjadi kesalahan saat menyimpan data.');
                    }
                },
                complete: function() {
                    btn.prop('disabled', false).text('Simpan');
                }
            });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var checkboxPasienPulang = document.getElementById('checkboxPasienPulang');
        var divTanggalPulang = document.getElementById('divTanggalPulang');
        var divWaktuPulang = document.getElementById('divWaktuPulang');
        var inputTanggalPulang = document.getElementById('tanggal_pulang');
        var inputWaktuPulang = document.getElementById('waktu_pulang');

        function setCurrentDateTime() {
            var now = new Date();
            var yyyy = now.getFullYear();
            var mm = String(now.getMonth() + 1).padStart(2, '0');
            var dd = String(now.getDate()).padStart(2, '0');
            var hh = String(now.getHours()).padStart(2, '0');
            var min = String(now.getMinutes()).padStart(2, '0');
            inputTanggalPulang.value = yyyy + '-' + mm + '-' + dd;
            inputWaktuPulang.value = hh + ':' + min;
        }

        checkboxPasienPulang.addEventListener('change', function() {
            if (checkboxPasienPulang.checked) {
                divTanggalPulang.style.display = 'block';
                divWaktuPulang.style.display = 'block';
                setCurrentDateTime();
            } else {
                divTanggalPulang.style.display = 'none';
                divWaktuPulang.style.display = 'none';
                inputTanggalPulang.value = '';
                inputWaktuPulang.value = '';
            }
        });
    });
</script>
@endsection
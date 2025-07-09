@extends('dashboardadmin')

@section('admin')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>Data Pasien</strong></h1>
    <div class="row">
        <div class="col-12 col-lg-12 col-xxl-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <!-- Input Pencarian -->
                        <form method="GET" action="{{ route('admin.datapasien') }}" class="d-flex flex-wrap align-items-center gap-2 m-0 p-0">
                            <div class="input-group" style="width: 250px;">
                                <input type="text" name="search" class="form-control" id="searchInput"
                                    placeholder="Pencarian..." aria-label="Search" value="{{ request('search') }}"
                                    autocomplete="off">
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1"
                                id="filterButton" title="Filter Data Pasien" data-bs-toggle="modal"
                                data-bs-target="#filterModal">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="{{ url('/admin/datapasien/export/pdf') }}" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1" title="Export PDF" target="_blank" style="margin-left: 5px;">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </a>
                            <a href="{{ url('/admin/datapasien/export/csv') }}" class="btn btn-outline-success btn-sm d-flex align-items-center gap-1" style="margin-left: 5px;" title="Export CSV" target="_blank">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </a>
                        </form>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover my-0" id="dokterPasien">
                        <thead>
                            <tr>
                                <th style="white-space: nowrap;">No.</th>
                                <th style="white-space: nowrap;">No. RM</th>
                                <th style="white-space: nowrap;">Nama Pasien</th>
                                <th style="white-space: nowrap;">Tempat, Tanggal Lahir</th>
                                <th style="white-space: nowrap;">JamKes</th>
                                <th style="white-space: nowrap;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pasiens as $index => $pasien)
                            <tr>
                                <td style="white-space: nowrap;">{{ $pasiens->firstItem() + $index }}</td>
                                <td style="white-space: nowrap;">{{ $pasien->no_rekam_medis }}</td>
                                <td style="white-space: nowrap;">{{ $pasien->nama_pasien}}</td>
                                <td style="white-space: nowrap;">
                                    {{ $pasien->tempat_lahir }},
                                    {{ \Carbon\Carbon::parse($pasien->tanggal_lahir)->format('d-m-Y') ?? 'Tanggal tidak tersedia' }}
                                </td>

                                <td style="white-space: nowrap;">{{ $pasien->jaminan_kesehatan }}</td>
                                <td style="white-space: nowrap;">
                                    <button type="button" class="btn btn-primary btn-sm rounded" data-bs-toggle="modal"
                                        data-bs-target="#modalPasienDetail"
                                        data-id="{{ $pasien->id }}"
                                        data-no_rekam_medis="{{ $pasien->no_rekam_medis }}" data-nik="{{ $pasien->nik }}"
                                        data-nama="{{ $pasien->nama_pasien }}"
                                        data-tempat_lahir="{{ $pasien->tempat_lahir }}"
                                        data-tanggal_lahir="{{ $pasien->tanggal_lahir ? \Carbon\Carbon::parse($pasien->tanggal_lahir)->format('d-m-Y') : 'Tanggal tidak tersedia' }}"
                                        data-jenis_kelamin="{{ $pasien->jenis_kelamin }}"
                                        data-gol_darah="{{ $pasien->gol_darah }}" data-agama="{{ $pasien->agama }}"
                                        data-pekerjaan="{{ $pasien->pekerjaan }}"
                                        data-status_pernikahan="{{ $pasien->status_pernikahan }}"
                                        data-alamat="{{ $pasien->alamat_jalan }}" data-rt="{{ $pasien->rt }}"
                                        data-rw="{{ $pasien->rw }}" data-kelurahan="{{ $pasien->kelurahan }}"
                                        data-kecamatan="{{ $pasien->kecamatan }}" data-kabupaten="{{ $pasien->kabupaten }}"
                                        data-provinsi="{{ $pasien->provinsi }}"
                                        data-jaminan="{{ $pasien->jaminan_kesehatan }}"
                                        data-no_kepesertaan="{{ $pasien->nomor_kepesertaan }}"
                                        data-kepala_keluarga="{{ $pasien->kepala_keluarga}}"
                                        data-no_hp="{{ $pasien->no_hp}}">
                                        Selengkapnya
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm rounded btn-riwayat"
                                        data-rekam-medis="{{ $pasien->no_rekam_medis }}"
                                        data-nama="{{ $pasien->nama_pasien }}"
                                        data-bs-toggle="modal" data-bs-target="#modalRiwayatBerobat">
                                        Riwayat Berobat
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm rounded btn-cetak-surat" data-id="{{ $pasien->id }}" data-nama="{{ $pasien->nama_pasien }}" data-bs-toggle="modal" data-bs-target="#modalCetakSurat">Cetak Surat</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 mb-2">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="small text-muted mb-2 text-start ps-3 pagination-info-text">
                            Showing {{ $pasiens->firstItem() }} to {{ $pasiens->lastItem() }} of
                            {{ $pasiens->total() }} results
                        </div>
                        <nav class="d-flex justify-content-center">
                            <ul class="pagination d-flex flex-row flex-wrap gap-2"
                                style="list-style-type: none; padding-left: 0; margin-bottom: 0;">
                                {{-- Previous Page Link --}}
                                @if ($pasiens->onFirstPage())
                                <li class="page-item disabled" aria-disabled="true" aria-label="Previous">
                                    <span class="page-link" aria-hidden="true">&laquo;</span>
                                </li>
                                @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $pasiens->previousPageUrl() }}" rel="prev"
                                        aria-label="Previous">&laquo;</a>
                                </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @php
                                $totalPages = $pasiens->lastPage();
                                $currentPage = $pasiens->currentPage();
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
                                    <li class="page-item"><a class="page-link" href="{{ $pasiens->url($page) }}">{{ $page }}</a></li>
                                    @endif
                                    @endfor

                                    {{-- Next Page Link --}}
                                    @if ($pasiens->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $pasiens->nextPageUrl() }}" rel="next"
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
<!-- Modal Filter -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="GET" action="{{ route('admin.datapasien') }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter Data Pasien</h5>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="jenis_kelamin" class="form-select">
                                <option value="">Semua</option>
                                <option value="Laki-laki"
                                    {{ request('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>
                                    Laki-laki</option>
                                <option value="Perempuan"
                                    {{ request('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>
                                    Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="gol_darah" class="form-label">Golongan Darah</label>
                            <select name="gol_darah" id="gol_darah" class="form-select">
                                <option value="">Semua</option>
                                <option value="A" {{ request('gol_darah') == 'A' ? 'selected' : '' }}>A
                                </option>
                                <option value="B" {{ request('gol_darah') == 'B' ? 'selected' : '' }}>B
                                </option>
                                <option value="AB" {{ request('gol_darah') == 'AB' ? 'selected' : '' }}>AB
                                </option>
                                <option value="O" {{ request('gol_darah') == 'O' ? 'selected' : '' }}>O
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="jaminan_kesehatan" class="form-label">Jaminan Kesehatan</label>
                            <select name="jaminan_kesehatan" id="jaminan_kesehatan" class="form-select">
                                <option value="">Semua</option>
                                <option value="Umum"
                                    {{ request('jaminan_kesehatan') == 'Umum' ? 'selected' : '' }}>Umum
                                </option>
                                <option value="BPJS Kesehatan"
                                    {{ request('jaminan_kesehatan') == 'BPJS Kesehatan' ? 'selected' : '' }}>
                                    BPJS Kesehatan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control"
                                value="{{ request('tempat_lahir') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="kecamatan" class="form-label">Kecamatan</label>
                            <input type="text" name="kecamatan" id="kecamatan" class="form-control"
                                value="{{ request('kecamatan') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="kelurahan" class="form-label">Kelurahan</label>
                            <input type="text" name="kelurahan" id="kelurahan" class="form-control"
                                value="{{ request('kelurahan') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="status_pernikahan" class="form-label">Status Pernikahan</label>
                            <select name="status_pernikahan" id="status_pernikahan" class="form-select">
                                <option value="">Semua</option>
                                <option value="Belum Menikah"
                                    {{ request('status_pernikahan') == 'Belum Menikah' ? 'selected' : '' }}>
                                    Belum Menikah</option>
                                <option value="Menikah"
                                    {{ request('status_pernikahan') == 'Menikah' ? 'selected' : '' }}>
                                    Menikah</option>
                                <option value="Cerai"
                                    {{ request('status_pernikahan') == 'Cerai' ? 'selected' : '' }}>Cerai
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control"
                                value="{{ request('tanggal_lahir') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end mt-3" style="gap: 10px;">
                    <button type="button" class="btn btn-danger " data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Data Pasien -->
<div class="modal fade" id="modalPasienDetail" tabindex="-1" aria-labelledby="modalPasienDetailLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
        <div class="modal-content" style="overflow-x: hidden;">
            <div class="modal-header d-flex justify-content-between">
                <h3 class="modal-title" id="modalPasienDetailLabel"><strong>Detail Pasien</strong></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body with Scroll -->
            <div class="modal-body" style="max-height: 400px; overflow-y: auto; padding: 10px;">
                <form id="formPasienDetail">
                    <input type="hidden" id="modalPasienId" name="id" value="">
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="modalNoRekamMedis" class="form-label">No. Rekam Medis</label>
                            <input type="text" class="form-control form-control-sm" id="modalNoRekamMedis" name="no_rekam_medis" readonly>
                        </div>
                        <div class="col-6">
                            <label for="modalNikPasien" class="form-label">NIK</label>
                            <input type="text" class="form-control form-control-sm" id="modalNikPasien" name="nik" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="modalNamaPasien" class="form-label">Nama Pasien</label>
                            <input type="text" class="form-control form-control-sm" id="modalNamaPasien" name="nama_pasien" readonly>
                        </div>
                        <div class="col-6">
                            <label for="modalKepalaKeluarga" class="form-label">Nama Kepala Keluarga</label>
                            <input type="text" class="form-control form-control-sm" id="modalKepalaKeluarga" name="kepala_keluarga" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="modalTempatLahir" class="form-label">Tempat Lahir</label>
                            <input type="text" class="form-control form-control-sm" id="modalTempatLahir" name="tempat_lahir" readonly>
                        </div>
                        <div class="col-6">
                            <label for="modalNoHp" class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control form-control-sm" id="modalNoHp" name="no_hp" readonly>
                        </div>
                    </div>


                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="modalTanggalLahir" class="form-label">Tanggal Lahir</label>
                            <input type="text" class="form-control form-control-sm" id="modalTanggalLahir" readonly>
                        </div>
                        <div class="col-6">
                            <label for="modalJenisKelamin" class="form-label">Jenis Kelamin</label>
                            <select class="form-control form-control-sm" id="modalJenisKelamin" disable>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="modalGolonganDarah" class="form-label">Golongan Darah</label>
                            <select class="form-control form-control-sm" id="modalGolonganDarah" disabled>
                                <option value="">Pilih Golongan Darah</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="AB">AB</option>
                                <option value="O">O</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="modalAgama" class="form-label">Agama</label>
                            <select class="form-control form-control-sm" id="modalAgama" disabled>
                                <option value="">Pilih Agama</option>
                                <option value="Islam">Islam</option>
                                <option value="Kristen Protestan">Kristen Protestan</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Buddha">Buddha</option>
                                <option value="Konghucu">Konghucu</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="modalPekerjaan" class="form-label">Pekerjaan</label>
                            <input type="text" class="form-control form-control-sm" id="modalPekerjaan" readonly>
                        </div>
                        <div class="col-6">
                            <label for="modalStatusPernikahan" class="form-label">Status Pernikahan</label>
                            <select class="form-control form-control-sm" id="modalStatusPernikahan" disable>
                                <option value="Belum Menikah">Belum Menikah</option>
                                <option value="Menikah">Menikah</option>
                                <option value="Cerai">Cerai</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="modalAlamat" class="form-label">Alamat</label>
                        <input class="form-control form-control-sm" id="modalAlamat" readonly></input>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="modalRt" class="form-label">RT</label>
                            <input type="text" class="form-control form-control-sm" id="modalRt" readonly>
                        </div>
                        <div class="col-4">
                            <label for="modalRw" class="form-label">RW</label>
                            <input type="text" class="form-control form-control-sm" id="modalRw" readonly>
                        </div>
                        <div class="col-4">
                            <label for="modalKelurahan" class="form-label">Kelurahan</label>
                            <input type="text" class="form-control form-control-sm" id="modalKelurahan" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="modalKecamatan" class="form-label">Kecamatan</label>
                            <input type="text" class="form-control form-control-sm" id="modalKecamatan" readonly>
                        </div>
                        <div class="col-4">
                            <label for="modalKabupaten" class="form-label">Kabupaten</label>
                            <input type="text" class="form-control form-control-sm" id="modalKabupaten" readonly>
                        </div>
                        <div class="col-4">
                            <label for="modalProvinsi" class="form-label">Provinsi</label>
                            <input type="text" class="form-control form-control-sm" id="modalProvinsi" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="modalJaminan" class="form-label">Jaminan Kesehatan</label>
                            <select class="form-control form-control-sm" id="modalJaminan" disabled>
                                <option value="Umum">Umum</option>
                                <option value="BPJS Kesehatan">BPJS Kesehatan</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="modalNoKepesertaan" class="form-label">No. Kepesertaan</label>
                            <input type="text" class="form-control form-control-sm" id="modalNoKepesertaan" readonly>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-warning ms-2" id="btnEdit">Edit</button>
                <button type="button" class="btn btn-success ms-2" id="btnSimpan" style="display: none;">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Riwayat Berobat -->
<div class="modal fade" id="modalRiwayatBerobat" tabindex="-1" aria-labelledby="modalRiwayatBerobatLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
        <div class="modal-content" style="overflow-x: hidden;">
            <div class="modal-header d-flex justify-content-between">
                <h3 class="modal-title" id="modalRiwayatBerobatLabel"><strong>Riwayat Berobat Pasien</strong></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow-y: auto; padding: 10px;">
                <div id="riwayatList">
                    <!-- List of dates will be populated here -->
                </div>

                <div id="hasilPeriksaDetail" style="display:none;">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>Tanggal Periksa</th>
                                <td id="detailTanggal"></td>
                            </tr>
                        </tbody>
                    </table>

                    <h5 id="headingHasilPeriksa">Hasil Periksa</h5>
                    <table class="table table-bordered" id="tableHasilPeriksa">
                        <tbody>
                            <tr>
                                <th>Anamnesis</th>
                                <td id="detailAnamnesis"></td>
                            </tr>
                            <tr>
                                <th>Pemeriksaan Fisik</th>
                                <td id="detailPemeriksaanFisik"></td>
                            </tr>
                            <tr>
                                <th>Rencana dan Terapi</th>
                                <td id="detailRencanaTerapi"></td>
                            </tr>
                            <tr>
                                <th>Diagnosis</th>
                                <td id="detailDiagnosis"></td>
                            </tr>
                            <tr>
                                <th>Edukasi</th>
                                <td id="detailEdukasi"></td>
                            </tr>
                            <tr>
                                <th>Kode ICD</th>
                                <td id="detailKodeICD"></td>
                            </tr>
                            <tr>
                                <th>Kesan Status Gizi</th>
                                <td id="detailStatusGizi"></td>
                            </tr>
                            <tr>
                                <th>Penanggung Jawab</th>
                                <td id="detailPenanggungJawab"></td>
                            </tr>
                        </tbody>
                    </table>

                    <h5 id="headingHasilAnalisa">Hasil Analisa</h5>
                    <table class="table table-bordered" id="tableHasilAnalisa">
                        <tbody>
                            <tr>
                                <th>Tekanan Darah (mmHg)</th>
                                <td id="detailTekananDarah"></td>
                            </tr>
                            <tr>
                                <th>Frekuensi Nadi (/menit)</th>
                                <td id="detailFrekuensiNadi"></td>
                            </tr>
                            <tr>
                                <th>Suhu (°C)</th>
                                <td id="detailSuhu"></td>
                            </tr>
                            <tr>
                                <th>Frekuensi Nafas (/menit)</th>
                                <td id="detailFrekuensiNafas"></td>
                            </tr>
                            <tr>
                                <th>Skor Nyeri</th>
                                <td id="detailSkorNyeri"></td>
                            </tr>
                            <tr>
                                <th>Skor Jatuh</th>
                                <td id="detailSkorJatuh"></td>
                            </tr>
                            <tr>
                                <th>Berat Badan</th>
                                <td id="detailBeratBadan"></td>
                            </tr>
                            <tr>
                                <th>Tinggi Badan</th>
                                <td id="detailTinggiBadan"></td>
                            </tr>
                            <tr>
                                <th>Lingkar Kepala</th>
                                <td id="detailLingkarKepala"></td>
                            </tr>
                            <tr>
                                <th>IMT</th>
                                <td id="detailIMT"></td>
                            </tr>
                            <tr>
                                <th>Alat Bantu</th>
                                <td id="detailAlatBantu"></td>
                            </tr>
                            <tr>
                                <th>Prosthesa</th>
                                <td id="detailProsthesa"></td>
                            </tr>
                            <tr>
                                <th>Cacat Tubuh</th>
                                <td id="detailCacatTubuh"></td>
                            </tr>
                            <tr>
                                <th>ADL Mandiri</th>
                                <td id="detailADLMandiri"></td>
                            </tr>
                            <tr>
                                <th>Riwayat Jatuh</th>
                                <td id="detailRiwayatJatuh"></td>
                            </tr>
                            <tr>
                                <th>Status Psikologi</th>
                                <td id="detailStatusPsikologi"></td>
                            </tr>
                            <tr>
                                <th>Hambatan Edukasi</th>
                                <td id="detailHambatanEdukasi"></td>
                            </tr>
                            <tr>
                                <th>Alergi</th>
                                <td id="detailAlergi"></td>
                            </tr>
                            <tr>
                                <th>Catatan</th>
                                <td id="detailCatatan"></td>
                            </tr>
                            <tr>
                                <th>Poli Tujuan</th>
                                <td id="detailPoliTujuan"></td>
                            </tr>
                            <tr>
                                <th>Penanggung Jawab</th>
                                <td id="detailPenanggungJawabAnalisa"></td>
                            </tr>
                        </tbody>
                    </table>

                    <h5 id="headingHasilPeriksaAnak">Hasil Periksa Anak</h5>
                    <table class="table table-bordered" id="hasilPeriksaAnakTable">
                        <tbody>
                            <!-- Data hasil periksa anak akan dimasukkan di sini -->
                        </tbody>
                    </table>

                    <h5 id="headingHasilPeriksaGigi">Hasil Periksa Gigi</h5>
                    <table class="table table-bordered" id="tableHasilPeriksaGigi">
                        <tbody>
                            <tr>
                                <th>Odontogram</th>
                                <td id="detailOdontogramGigi"></td>
                            </tr>
                            <tr>
                                <th>Pemeriksaan Subjektif</th>
                                <td id="detailPemeriksaanSubjektifGigi"></td>
                            </tr>
                            <tr>
                                <th>Pemeriksaan Objektif</th>
                                <td id="detailPemeriksaanObjektifGigi"></td>
                            </tr>
                            <tr>
                                <th>Diagnosis Gigi</th>
                                <td id="detailDiagnosisGigi"></td>
                            </tr>
                            <tr>
                                <th>Terapi/Anjuran Gigi</th>
                                <td id="detailTerapiAnjuranGigi"></td>
                            </tr>
                            <tr>
                                <th>Catatan Gigi</th>
                                <td id="detailCatatanGigi"></td>
                            </tr>
                            <tr>
                                <th>Penanggung Jawab Gigi</th>
                                <td id="detailPenanggungJawabGigi"></td>
                            </tr>
                        </tbody>
                    </table>

                    <h5 id="headingHasilAnalisaRawatinap">Hasil Analisa Rawat Inap</h5>
                    <table class="table table-bordered" id="tableHasilAnalisaRawatinap">
                        <tbody>
                            <tr>
                                <th>Tekanan Darah (mmHg)</th>
                                <td id="detailTekananDarahRawatinap"></td>
                            </tr>
                            <tr>
                                <th>Frekuensi Nadi (/menit)</th>
                                <td id="detailFrekuensiNadiRawatinap"></td>
                            </tr>
                            <tr>
                                <th>Suhu (°C)</th>
                                <td id="detailSuhuRawatinap"></td>
                            </tr>
                            <tr>
                                <th>Frekuensi Nafas (/menit)</th>
                                <td id="detailFrekuensiNafasRawatinap"></td>
                            </tr>
                            <tr>
                                <th>Skor Nyeri</th>
                                <td id="detailSkorNyeriRawatinap"></td>
                            </tr>
                            <tr>
                                <th>Skor Jatuh</th>
                                <td id="detailSkorJatuhRawatinap"></td>
                            </tr>
                            <tr>
                                <th>Berat Badan</th>
                                <td id="detailBeratBadanRawatinap"></td>
                            </tr>
                            <tr>
                                <th>Tinggi Badan</th>
                                <td id="detailTinggiBadanRawatinap"></td>
                            </tr>
                            <tr>
                                <th>Lingkar Kepala</th>
                                <td id="detailLingkarKepalaRawatinap"></td>
                            </tr>
                            <tr>
                                <th>IMT</th>
                                <td id="detailIMTRawatinap"></td>
                            </tr>
                            <tr>
                                <th>Alat Bantu</th>
                                <td id="detailAlatBantuRawatinap"></td>
                            </tr>
                            <tr>
                                <th>Prosthesa</th>
                                <td id="detailProsthesaRawatinap"></td>
                            </tr>
                            <tr>
                                <th>Cacat Tubuh</th>
                                <td id="detailCacatTubuhRawatinap"></td>
                            </tr>
                            <tr>
                                <th>ADL Mandiri</th>
                                <td id="detailADLMandiriRawatinap"></td>
                            </tr>
                            <tr>
                                <th>Riwayat Jatuh</th>
                                <td id="detailRiwayatJatuhRawatinap"></td>
                            </tr>
                            <tr>
                                <th>Status Psikologi</th>
                                <td id="detailStatusPsikologiRawatinap"></td>
                            </tr>
                            <tr>
                                <th>Penanggung Jawab</th>
                                <td id="detailPenanggungJawabRawatinap"></td>
                            </tr>
                            <tr>
                                <th>Hambatan Edukasi</th>
                                <td id="detailHambatanEdukasiRawatinap"></td>
                            </tr>
                            <tr>
                                <th>Alergi</th>
                                <td id="detailAlergiRawatinap"></td>
                            </tr>
                            <tr>
                                <th>Catatan</th>
                                <td id="detailCatatanRawatinap"></td>
                            </tr>
                        </tbody>
                    </table>

                    <h5 id="headingHasilPeriksaUgd">Hasil Periksa UGD</h5>
                    <table class="table table-bordered" id="tableHasilPeriksaUgd">
                        <tbody>
                            <tr>
                                <th>Tanggal Periksa</th>
                                <td id="detailTanggalPeriksaUgd"></td>
                            </tr>
                            <tr>
                                <th>Waktu</th>
                                <td id="detailWaktuUgd"></td>
                            </tr>
                            <tr>
                                <th>SOAP</th>
                                <td id="detailSoapUgd"></td>
                            </tr>
                            <tr>
                                <th>Instruksi Tenaga Kerja</th>
                                <td id="detailIntruksiTenagaKerjaUgd"></td>
                            </tr>
                            <tr>
                                <th>Penanggung Jawab</th>
                                <td id="detailPenanggungJawabUgd"></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-danger btn-sm mt-2" id="btnTutupDetail">Tutup</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-end mt-3"></div>
        </div>
    </div>
</div>

<!-- Modal Cetak Surat -->
<div class="modal fade" id="modalCetakSurat" tabindex="-1" aria-labelledby="modalCetakSuratLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between">
                <h3 class="modal-title" id="modalCetakSuratLabel"><strong>Cetak Surat</strong></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="keperluan" class="form-label">Keperluan Surat</label>
                    <input type="text" class="form-control" id="keperluan" name="keperluan" required>
                </div>
                <div id="suratPreview" style="display:none; border: 1px solid #ccc; padding: 15px; max-height: 400px; overflow-y: auto;">
                    <!-- Surat content preview will be rendered here -->
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-end">
                <button type="button" class="btn btn-primary" id="btnGeneratePdf">Buat PDF</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        var originalData; // Menyimpan data awal

        // Function to reset modal inputs to originalData
        function resetModal() {
            var modal = $('#modalPasienDetail');
            modal.find('#modalNoRekamMedis').val(originalData.no_rekam_medis);
            modal.find('#modalNikPasien').val(originalData.nik);
            modal.find('#modalNamaPasien').val(originalData.nama);
            modal.find('#modalTempatLahir').val(originalData.tempat_lahir);
            modal.find('#modalTanggalLahir').val(originalData.tanggal_lahir);
            modal.find('#modalJenisKelamin').val(originalData.jenis_kelamin).prop('disabled', true);
            modal.find('#modalGolonganDarah').val(originalData.gol_darah).prop('disabled', true);
            modal.find('#modalAgama').val(originalData.agama).prop('disabled', true);
            modal.find('#modalPekerjaan').val(originalData.pekerjaan);
            modal.find('#modalStatusPernikahan').val(originalData.status_pernikahan).prop('disabled', true);
            modal.find('#modalAlamat').val(originalData.alamat);
            modal.find('#modalRt').val(originalData.rt);
            modal.find('#modalRw').val(originalData.rw);
            modal.find('#modalKelurahan').val(originalData.kelurahan);
            modal.find('#modalKecamatan').val(originalData.kecamatan);
            modal.find('#modalKabupaten').val(originalData.kabupaten);
            modal.find('#modalProvinsi').val(originalData.provinsi);
            modal.find('#modalJaminan').val(originalData.jaminan).prop('disabled', true);
            modal.find('#modalNoKepesertaan').val(originalData.no_kepesertaan);
            modal.find('#modalKepalaKeluarga').val(originalData.kepala_keluarga);
            modal.find('#modalNoHp').val(originalData.no_hp);

            // Remove validation error classes and messages
            modal.find('.is-invalid').removeClass('is-invalid');
            modal.find('.invalid-feedback').remove();

            // Reset readonly and disabled states to default (readonly and disabled)
            modal.find('input').prop('readonly', true);
            modal.find('select').prop('disabled', true);

            // Hide save button, show edit button
            $('#btnSimpan').hide();
            $('#btnEdit').show();

            // Reset close button mode
            $('#btnTutup').data('mode', '');
        }

        // Ketika modal ditampilkan
        $('#modalPasienDetail').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Tombol yang memicu modal

            // Reset mode of close button to empty (non-edit)
            $('#btnTutup').data('mode', '');

            // Ambil data dari atribut data-*
            var noRekamMedis = button.data('no_rekam_medis');
            var nik = button.data('nik');
            var nama = button.data('nama');
            var tempatLahir = button.data('tempat_lahir');
            var tanggalLahir = button.data('tanggal_lahir');
            var jenisKelamin = button.data('jenis_kelamin');
            var golDarah = button.data('gol_darah');
            var agama = button.data('agama');
            var pekerjaan = button.data('pekerjaan');
            var statusPernikahan = button.data('status_pernikahan');
            var alamat = button.data('alamat');
            var rt = button.data('rt');
            var rw = button.data('rw');
            var kelurahan = button.data('kelurahan');
            var kecamatan = button.data('kecamatan');
            var kabupaten = button.data('kabupaten');
            var provinsi = button.data('provinsi');
            var jaminan = button.data('jaminan');
            var noKepesertaan = button.data('no_kepesertaan');
            var kepalaKeluarga = button.data('kepala_keluarga');
            var noHp = button.data('no_hp');

            // Menyimpan data awal
            originalData = {
                no_rekam_medis: noRekamMedis,
                nik: nik,
                nama: nama,
                tempat_lahir: tempatLahir,
                tanggal_lahir: tanggalLahir,
                jenis_kelamin: jenisKelamin,
                gol_darah: golDarah,
                agama: agama,
                pekerjaan: pekerjaan,
                status_pernikahan: statusPernikahan,
                alamat: alamat,
                rt: rt,
                rw: rw,
                kelurahan: kelurahan,
                kecamatan: kecamatan,
                kabupaten: kabupaten,
                provinsi: provinsi,
                jaminan: jaminan,
                no_kepesertaan: noKepesertaan,
                kepala_keluarga: kepalaKeluarga,
                no_hp: noHp
            };

            // Menampilkan data di dalam modal
            var modal = $(this);
            modal.find('#modalNoRekamMedis').val(noRekamMedis);
            modal.find('#modalNikPasien').val(nik);
            modal.find('#modalNamaPasien').val(nama);
            modal.find('#modalTempatLahir').val(tempatLahir);
            modal.find('#modalTanggalLahir').val(tanggalLahir);
            modal.find('#modalJenisKelamin').val(jenisKelamin).prop('disabled',
                true); // Menonaktifkan dropdown Jenis Kelamin
            modal.find('#modalGolonganDarah').val(golDarah).prop('disabled', true);
            modal.find('#modalAgama').val(agama).prop('disabled', true);
            modal.find('#modalPekerjaan').val(pekerjaan);
            modal.find('#modalStatusPernikahan').val(statusPernikahan).prop('disabled',
                true); // Menonaktifkan dropdown Status Pernikahan
            modal.find('#modalAlamat').val(alamat);
            modal.find('#modalRt').val(rt);
            modal.find('#modalRw').val(rw);
            modal.find('#modalKelurahan').val(kelurahan);
            modal.find('#modalKecamatan').val(kecamatan);
            modal.find('#modalKabupaten').val(kabupaten);
            modal.find('#modalProvinsi').val(provinsi);
            modal.find('#modalJaminan').val(jaminan).prop('disabled',
                true); // Menonaktifkan dropdown Jaminan Kesehatan
            modal.find('#modalNoKepesertaan').val(noKepesertaan);
            modal.find('#modalKepalaKeluarga').val(kepalaKeluarga);
            modal.find('#modalNoHp').val(noHp);
        });

        // Reset modal when it is hidden (closed) to clear edit mode and validation errors
        $('#modalPasienDetail').on('hidden.bs.modal', function() {
            resetModal();
        });

        // Fungsi Edit
        $('#btnEdit').on('click', function() {
            // Aktifkan input untuk diedit, kecuali No. Rekam Medis tetap readonly
            $('#modalNikPasien, #modalNamaPasien, #modalTempatLahir, #modalTanggalLahir, #modalGolonganDarah, #modalAgama, #modalPekerjaan, #modalAlamat, #modalNoKepesertaan, #modalRt, #modalRw, #modalKelurahan, #modalKecamatan, #modalKabupaten, #modalProvinsi, #modalKepalaKeluarga, #modalNoHp')
                .prop('readonly', false); // Mengaktifkan input text menjadi editable
            $('#modalJenisKelamin, #modalJaminan, #modalStatusPernikahan, #modalGolonganDarah, #modalAgama')
                .prop('disabled',
                    false); // Mengaktifkan dropdown

            // Batasi input NIK hanya angka dan max length 16
            $('#modalNikPasien').attr('maxlength', 16).on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16);
            });

            // Batasi input Nomor Telepon hanya angka dan max length 14
            $('#modalNoHp').attr('maxlength', 14).on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 14);
            });

            // Batasi input No. Kepesertaan hanya angka dan max length 16
            $('#modalNoKepesertaan').attr('maxlength', 16).on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16);
            });

            $(this).hide(); // Sembunyikan tombol Edit
            $('#btnSimpan').show(); // Tampilkan tombol Simpan
            $('#btnTutup').data('mode', 'edit'); // Tandai tombol Tutup dalam mode edit
        });

        // Fungsi Simpan
        $('#btnSimpan').on('click', function() {
            // Clear previous errors
            $('#modalPasienDetail .is-invalid').removeClass('is-invalid');
            $('#modalPasienDetail .invalid-feedback').remove();

            // Client-side validation for required fields
            var isValid = true;
            var requiredFields = [
                '#modalNikPasien',
                '#modalNamaPasien',
                '#modalTempatLahir',
                '#modalTanggalLahir',
                '#modalJenisKelamin',
                '#modalGolonganDarah',
                '#modalAgama',
                '#modalPekerjaan',
                '#modalStatusPernikahan',
                '#modalAlamat',
                '#modalRt',
                '#modalRw',
                '#modalKelurahan',
                '#modalKecamatan',
                '#modalKabupaten',
                '#modalProvinsi',
                '#modalJaminan',
                '#modalNoKepesertaan',
                '#modalKepalaKeluarga',
                '#modalNoHp'
            ];

            requiredFields.forEach(function(selector) {
                var element = $(selector);
                var value = element.val();
                if (!value || value.trim() === '') {
                    isValid = false;
                    element.addClass('is-invalid');
                    if (element.next('.invalid-feedback').length === 0) {
                        element.after(
                            '<div class="invalid-feedback">Field ini wajib diisi.</div>');
                    }
                }
            });

            if (!isValid) {
                return; // Stop submission if validation fails
            }

            // Ambil tanggal lahir dari input
            var tanggalLahir = $('#modalTanggalLahir').val();

            // Ubah format tanggal dari DD-MM-YYYY ke YYYY-MM-DD
            var parts = tanggalLahir.split('-');
            var formattedTanggalLahir = parts[2] + '-' + parts[1] + '-' + parts[0];

            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin mengubah data pasien ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim data yang diubah ke server menggunakan AJAX
                    $.ajax({
                        url: `/admin/datapasien/${$('#modalPasienId').val()}`,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            nik: $('#modalNikPasien').val(),
                            nama_pasien: $('#modalNamaPasien').val(),
                            tempat_lahir: $('#modalTempatLahir').val(),
                            tanggal_lahir: formattedTanggalLahir,
                            jenis_kelamin: $('#modalJenisKelamin').val(),
                            gol_darah: $('#modalGolonganDarah').val(),
                            agama: $('#modalAgama').val(),
                            pekerjaan: $('#modalPekerjaan').val(),
                            status_pernikahan: $('#modalStatusPernikahan').val(),
                            alamat_jalan: $('#modalAlamat').val(),
                            rt: $('#modalRt').val(),
                            rw: $('#modalRw').val(),
                            kelurahan: $('#modalKelurahan').val(),
                            kecamatan: $('#modalKecamatan').val(),
                            kabupaten: $('#modalKabupaten').val(),
                            provinsi: $('#modalProvinsi').val(),
                            jaminan_kesehatan: $('#modalJaminan').val(),
                            nomor_kepesertaan: $('#modalNoKepesertaan').val(),
                            kepala_keluarga: $('#modalKepalaKeluarga').val(),
                            no_hp: $('#modalNoHp').val()
                        },
                        success: function(response) {
                            // Tutup modal setelah berhasil simpan
                            $('#modalPasienDetail').modal('hide');

                            // Atur toastr agar menghilang dengan smooth
                            toastr.options = {
                                "hideMethod": "fadeOut",
                                "hideDuration": 1000, // durasi fade out (1 detik)
                                "timeOut": 2000 // waktu muncul toastr sebelum mulai menghilang
                            };

                            // Tampilkan toastr success
                            toastr.success('Data pasien berhasil diperbarui!',
                                'Sukses');

                            // Tunggu hingga toastr selesai menghilang, baru reload halaman
                            setTimeout(function() {
                                    location.reload();
                                },
                                3000
                            ); // 2000ms untuk timeout toastr + 1000ms untuk hideDuration
                        },
                        error: function(xhr) {
                            if (xhr.status === 422 && xhr.responseJSON && xhr
                                .responseJSON.errors) {
                                var errors = xhr.responseJSON.errors;
                                // Show errors inline
                                $.each(errors, function(key, messages) {
                                    var inputField = $('#modal' + key
                                        .charAt(0).toUpperCase() + key
                                        .slice(1));
                                    inputField.addClass('is-invalid');
                                    if (inputField.next('.invalid-feedback')
                                        .length === 0) {
                                        inputField.after(
                                            '<div class="invalid-feedback">' +
                                            messages[0] + '</div>');
                                    }
                                });
                            } else {
                                // Tampilkan toastr error dengan efek smooth juga
                                toastr.options = {
                                    "hideMethod": "fadeOut",
                                    "hideDuration": 1000,
                                    "timeOut": 2000
                                };

                                toastr.error(
                                    'Terjadi kesalahan saat memperbarui data!',
                                    'Error');
                            }
                        }
                    });
                }
            });
        });

        // Fungsi Tutup Modal
        $('#btnTutup').on('click', function() {
            var mode = $(this).data('mode'); // Cek mode dari tombol Tutup

            if (mode === 'edit') {
                // Jika dalam mode edit, tampilkan konfirmasi
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Perubahan yang belum disimpan akan hilang. Apakah Anda yakin ingin membatalkan perubahan?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, batal',
                    cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Kembali ke kondisi awal
                        resetModal(); // Reset modal ke kondisi awal
                        $('#modalPasienDetail').modal('hide'); // Tutup modal
                    }
                });
            } else {
                // Jika tidak dalam mode edit, tutup modal tanpa konfirmasi
                resetModal(); // Reset modal ke kondisi awal
                $('#modalPasienDetail').modal('hide');
            }
        });
    });
</script>
<script>
    // Handle Riwayat Berobat button click in antrian view (copy dari antrian.blade.php)
    document.addEventListener('DOMContentLoaded', function() {
        // Event delegation untuk .btn-riwayat
        document.body.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-riwayat')) {
                const button = e.target;
                const noRekamMedis = button.getAttribute('data-rekam-medis');
                const namaPasien = button.getAttribute('data-nama');
                const modal = new bootstrap.Modal(document.getElementById('modalRiwayatBerobat'));
                const riwayatList = document.getElementById('riwayatList');
                const hasilPeriksaDetail = document.getElementById('hasilPeriksaDetail');

                // Clear previous content
                riwayatList.innerHTML = '';
                hasilPeriksaDetail.style.display = 'none';
                riwayatList.style.display = 'block';

                // Fetch riwayat berobat dates by pasien no_rekam_medis
                fetch(`/admin/riwayat-berobat/${noRekamMedis}/dates?type=rawatinap`, {
                        credentials: 'include'
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (!result.success) {
                            riwayatList.innerHTML = `<p>Error: ${result.message}</p>`;
                            return;
                        }
                        const data = result.data;
                        if ((!data.rawatinap || data.rawatinap.length === 0) && (!data.rawatjalan || data.rawatjalan.length === 0)) {
                            riwayatList.innerHTML = '<p>Tidak ada riwayat berobat.</p>';
                            return;
                        }

                        // Function to render a group of dates with header
                        function renderDateGroup(source, dateList) {
                            if (!dateList || dateList.length === 0) return;

                            const sourceHeader = document.createElement('h6');
                            if (source === 'rawatinap') {
                                sourceHeader.textContent = 'UGD & Rawat Inap';
                            } else if (source === 'rawatjalan') {
                                sourceHeader.textContent = 'Rawat Jalan';
                            } else {
                                sourceHeader.textContent = source;
                            }
                            sourceHeader.style.marginTop = '1rem';
                            riwayatList.appendChild(sourceHeader);

                            dateList.forEach((item, index) => {
                                const tanggal = item.date;
                                const sourceKey = item.source;
                                const dateObj = new Date(tanggal);
                                const options = {
                                    weekday: 'long',
                                    year: 'numeric',
                                    month: '2-digit',
                                    day: '2-digit'
                                };
                                const dateStr = dateObj.toLocaleDateString('id-ID', options);
                                const div = document.createElement('div');
                                div.classList.add('d-flex', 'justify-content-between', 'align-items-center');
                                if (index < dateList.length - 1) {
                                    div.style.borderBottom = '1px solid #dee2e6';
                                    div.style.paddingBottom = '0.5rem';
                                    div.style.marginBottom = '0.5rem';
                                }
                                div.innerHTML = `
                <span>${dateStr}</span>
                <button class="btn btn-primary btn-sm btnLihat" data-tanggal="${tanggal}" data-norm="${noRekamMedis}" data-source="${sourceKey}">Lihat</button>
            `;
                                riwayatList.appendChild(div);
                            });
                        }

                        renderDateGroup('rawatinap', data.rawatinap);
                        renderDateGroup('rawatjalan', data.rawatjalan);
                    })
                    .catch(error => {
                        riwayatList.innerHTML = `<p>Error: ${error.message}</p>`;
                    });

                modal.show();
            }
        });

        // Event delegation untuk btnLihat di #riwayatList
        document.getElementById('riwayatList').addEventListener('click', function(ev) {
            if (ev.target.classList.contains('btnLihat')) {
                const tanggal = ev.target.getAttribute('data-tanggal');
                const noRekamMedis = ev.target.getAttribute('data-norm');
                const hasilPeriksaDetail = document.getElementById('hasilPeriksaDetail');
                const riwayatList = document.getElementById('riwayatList');
                fetch(`/admin/riwayat-berobat/${noRekamMedis}/${tanggal}?source=${ev.target.getAttribute('data-source')}`, {
                        credentials: 'include'
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Data hasil periksa tidak ditemukan');
                        }
                        return response.json();
                    })
                    .then(data => {
                        hasilPeriksaDetail.style.display = 'block';
                        riwayatList.style.display = 'none';
                        const d = data.data || {};
                        const dateObj = new Date(d.tanggal_periksa);
                        const options = {
                            weekday: 'long',
                            year: 'numeric',
                            month: '2-digit',
                            day: '2-digit'
                        };
                        document.getElementById('detailTanggal').textContent = dateObj.toLocaleDateString('id-ID', options);
                        // Cek apakah ada data pada hasil periksa
                        const hasHasilPeriksa = d.anamnesis || d.pemeriksaan_fisik || d.rencana_dan_terapi || d.diagnosis || d.edukasi || d.kode_icd || d.status_gizi;
                        document.getElementById('headingHasilPeriksa').style.display = hasHasilPeriksa ? 'block' : 'none';
                        document.getElementById('tableHasilPeriksa').style.display = hasHasilPeriksa ? 'table' : 'none';
                        if (hasHasilPeriksa) {
                            document.getElementById('detailAnamnesis').textContent = d.anamnesis || '-';
                            document.getElementById('detailPemeriksaanFisik').textContent = d.pemeriksaan_fisik || '-';
                            document.getElementById('detailRencanaTerapi').textContent = d.rencana_dan_terapi || '-';
                            document.getElementById('detailDiagnosis').textContent = d.diagnosis || '-';
                            document.getElementById('detailEdukasi').textContent = d.edukasi || '-';
                            document.getElementById('detailKodeICD').textContent = d.kode_icd || '-';
                            document.getElementById('detailStatusGizi').textContent = d.status_gizi || '-';
                            if (document.getElementById('detailPenanggungJawab')) {
                                document.getElementById('detailPenanggungJawab').textContent = d.penanggung_jawab_periksa || '-';
                            }
                        }
                        // Cek hasil analisa
                        const hasHasilAnalisa = d.tekanan_darah || d.frekuensi_nadi || d.suhu || d.frekuensi_nafas || d.skor_nyeri || d.skor_jatuh || d.berat_badan || d.tinggi_badan || d.lingkar_kepala || d.imt || d.alat_bantu || d.prosthesa || d.cacat_tubuh || d.adl_mandiri || d.riwayat_jatuh || d.status_psikologi || d.hambatan_edukasi || d.alergi || d.catatan || d.poli_tujuan || d.penanggung_jawab_nama;
                        document.getElementById('headingHasilAnalisa').style.display = hasHasilAnalisa ? 'block' : 'none';
                        document.getElementById('tableHasilAnalisa').style.display = hasHasilAnalisa ? 'table' : 'none';
                        if (hasHasilAnalisa) {
                            document.getElementById('detailTekananDarah').textContent = d.tekanan_darah || '-';
                            document.getElementById('detailFrekuensiNadi').textContent = d.frekuensi_nadi || '-';
                            document.getElementById('detailSuhu').textContent = d.suhu || '-';
                            document.getElementById('detailFrekuensiNafas').textContent = d.frekuensi_nafas || '-';
                            document.getElementById('detailSkorNyeri').textContent = d.skor_nyeri || '-';
                            document.getElementById('detailSkorJatuh').textContent = d.skor_jatuh || '-';
                            document.getElementById('detailBeratBadan').textContent = d.berat_badan || '-';
                            document.getElementById('detailTinggiBadan').textContent = d.tinggi_badan || '-';
                            document.getElementById('detailLingkarKepala').textContent = d.lingkar_kepala || '-';
                            document.getElementById('detailIMT').textContent = d.imt || '-';
                            document.getElementById('detailAlatBantu').textContent = d.alat_bantu || '-';
                            document.getElementById('detailProsthesa').textContent = d.prosthesa || '-';
                            document.getElementById('detailCacatTubuh').textContent = d.cacat_tubuh || '-';
                            document.getElementById('detailADLMandiri').textContent = d.adl_mandiri || '-';
                            document.getElementById('detailRiwayatJatuh').textContent = d.riwayat_jatuh || '-';
                            document.getElementById('detailStatusPsikologi').textContent = d.status_psikologi || '-';
                            document.getElementById('detailHambatanEdukasi').textContent = d.hambatan_edukasi || '-';
                            document.getElementById('detailAlergi').textContent = d.alergi || '-';
                            document.getElementById('detailCatatan').textContent = d.catatan || '-';
                            document.getElementById('detailPoliTujuan').textContent = d.poli_tujuan || '-';
                            document.getElementById('detailPenanggungJawabAnalisa').textContent = d.penanggung_jawab_analisa || '-';
                        }
                        // Cek hasil periksa anak
                        const hasHasilPeriksaAnak = d.berat_badan_anak || d.makanan_anak || d.gejala_anak || d.nasehat_anak || d.pegobatan_anak;
                        document.getElementById('headingHasilPeriksaAnak').style.display = hasHasilPeriksaAnak ? 'block' : 'none';
                        document.getElementById('hasilPeriksaAnakTable').style.display = hasHasilPeriksaAnak ? 'table' : 'none';
                        if (hasHasilPeriksaAnak) {
                            const tbodyAnak = document.querySelector('#hasilPeriksaAnakTable tbody');
                            tbodyAnak.innerHTML = `
            <tr><th>Berat Badan Anak</th><td>${d.berat_badan_anak || '-'}</td></tr>
            <tr><th>Makanan Anak</th><td>${d.makanan_anak || '-'}</td></tr>
            <tr><th>Gejala Anak</th><td>${d.gejala_anak || '-'}</td></tr>
            <tr><th>Nasehat Anak</th><td>${d.nasehat_anak || '-'}</td></tr>
            <tr><th>Pengobatan Anak</th><td>${d.pegobatan_anak || '-'}</td></tr>
            <tr><th>Penanggung Jawab Anak</th><td>${d.penanggung_jawab_anak || '-'}</td></tr>
        `;
                        } else {
                            document.querySelector('#hasilPeriksaAnakTable tbody').innerHTML = '';
                        }
                        // Cek hasil periksa gigi
                        const hasHasilPeriksaGigi = d.odontogram || d.pemeriksaan_subjektif || d.pemeriksaan_objektif || d.diagnosa_gigi || d.terapi_anjuran_gigi || d.catatan_gigi;
                        document.getElementById('headingHasilPeriksaGigi').style.display = hasHasilPeriksaGigi ? 'block' : 'none';
                        document.getElementById('tableHasilPeriksaGigi').style.display = hasHasilPeriksaGigi ? 'table' : 'none';
                        if (hasHasilPeriksaGigi) {
                            document.getElementById('detailOdontogramGigi').textContent = d.odontogram || '-';
                            document.getElementById('detailPemeriksaanSubjektifGigi').textContent = d.pemeriksaan_subjektif || '-';
                            document.getElementById('detailPemeriksaanObjektifGigi').textContent = d.pemeriksaan_objektif || '-';
                            document.getElementById('detailDiagnosisGigi').textContent = d.diagnosa_gigi || '-';
                            document.getElementById('detailTerapiAnjuranGigi').textContent = d.terapi_anjuran_gigi || '-';
                            document.getElementById('detailCatatanGigi').textContent = d.catatan_gigi || '-';
                            document.getElementById('detailPenanggungJawabGigi').textContent = d.penanggung_jawab_gigi || '-';
                        }
                    })
                    .catch(error => {
                        alert(error.message);
                    });
            }
        });

        // Event delegation untuk btnLihat di #riwayatList
        document.getElementById('riwayatList').addEventListener('click', function(ev) {
            if (ev.target.classList.contains('btnLihat')) {
                const tanggal = ev.target.getAttribute('data-tanggal');
                const noRekamMedis = ev.target.getAttribute('data-norm');
                const hasilPeriksaDetail = document.getElementById('hasilPeriksaDetail');
                const riwayatList = document.getElementById('riwayatList');
                fetch(`/admin/riwayat-berobat/${noRekamMedis}/${tanggal}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Data hasil periksa tidak ditemukan');
                        }
                        return response.json();
                    })
                    .then(data => {
                        hasilPeriksaDetail.style.display = 'block';
                        riwayatList.style.display = 'none';
                        const d = data.data || {};
                        const dateObj = new Date(d.tanggal_periksa);
                        const options = {
                            weekday: 'long',
                            year: 'numeric',
                            month: '2-digit',
                            day: '2-digit'
                        };
                        document.getElementById('detailTanggal').textContent = dateObj.toLocaleDateString('id-ID', options);

                        // Cek apakah ada data pada hasil periksa
                        const hasHasilPeriksa = d.anamnesis || d.pemeriksaan_fisik || d.rencana_dan_terapi || d.diagnosis || d.edukasi || d.kode_icd || d.status_gizi;
                        document.getElementById('headingHasilPeriksa').style.display = hasHasilPeriksa ? 'block' : 'none';
                        document.getElementById('tableHasilPeriksa').style.display = hasHasilPeriksa ? 'table' : 'none';
                        if (hasHasilPeriksa) {
                            document.getElementById('detailAnamnesis').textContent = d.anamnesis || '-';
                            document.getElementById('detailPemeriksaanFisik').textContent = d.pemeriksaan_fisik || '-';
                            document.getElementById('detailRencanaTerapi').textContent = d.rencana_dan_terapi || '-';
                            document.getElementById('detailDiagnosis').textContent = d.diagnosis || '-';
                            document.getElementById('detailEdukasi').textContent = d.edukasi || '-';
                            document.getElementById('detailKodeICD').textContent = d.kode_icd || '-';
                            document.getElementById('detailStatusGizi').textContent = d.status_gizi || '-';
                            if (document.getElementById('detailPenanggungJawab')) {
                                document.getElementById('detailPenanggungJawab').textContent = d.penanggung_jawab_periksa || '-';
                            }
                        }

                        // Cek hasil analisa
                        const hasHasilAnalisa = d.tekanan_darah || d.frekuensi_nadi || d.suhu || d.frekuensi_nafas || d.skor_nyeri || d.skor_jatuh || d.berat_badan || d.tinggi_badan || d.lingkar_kepala || d.imt || d.alat_bantu || d.prosthesa || d.cacat_tubuh || d.adl_mandiri || d.riwayat_jatuh || d.status_psikologi || d.hambatan_edukasi || d.alergi || d.catatan || d.poli_tujuan || d.penanggung_jawab_nama;
                        document.getElementById('headingHasilAnalisa').style.display = hasHasilAnalisa ? 'block' : 'none';
                        document.getElementById('tableHasilAnalisa').style.display = hasHasilAnalisa ? 'table' : 'none';
                        if (hasHasilAnalisa) {
                            document.getElementById('detailTekananDarah').textContent = d.tekanan_darah || '-';
                            document.getElementById('detailFrekuensiNadi').textContent = d.frekuensi_nadi || '-';
                            document.getElementById('detailSuhu').textContent = d.suhu || '-';
                            document.getElementById('detailFrekuensiNafas').textContent = d.frekuensi_nafas || '-';
                            document.getElementById('detailSkorNyeri').textContent = d.skor_nyeri || '-';
                            document.getElementById('detailSkorJatuh').textContent = d.skor_jatuh || '-';
                            document.getElementById('detailBeratBadan').textContent = d.berat_badan || '-';
                            document.getElementById('detailTinggiBadan').textContent = d.tinggi_badan || '-';
                            document.getElementById('detailLingkarKepala').textContent = d.lingkar_kepala || '-';
                            document.getElementById('detailIMT').textContent = d.imt || '-';
                            document.getElementById('detailAlatBantu').textContent = d.alat_bantu || '-';
                            document.getElementById('detailProsthesa').textContent = d.prosthesa || '-';
                            document.getElementById('detailCacatTubuh').textContent = d.cacat_tubuh || '-';
                            document.getElementById('detailADLMandiri').textContent = d.adl_mandiri || '-';
                            document.getElementById('detailRiwayatJatuh').textContent = d.riwayat_jatuh || '-';
                            document.getElementById('detailStatusPsikologi').textContent = d.status_psikologi || '-';
                            document.getElementById('detailHambatanEdukasi').textContent = d.hambatan_edukasi || '-';
                            document.getElementById('detailAlergi').textContent = d.alergi || '-';
                            document.getElementById('detailCatatan').textContent = d.catatan || '-';
                            document.getElementById('detailPoliTujuan').textContent = d.poli_tujuan || '-';
                            document.getElementById('detailPenanggungJawabAnalisa').textContent = d.penanggung_jawab_analisa || '-';
                        }

                        // Cek hasil periksa anak
                        const hasHasilPeriksaAnak = d.berat_badan_anak || d.makanan_anak || d.gejala_anak || d.nasehat_anak || d.pegobatan_anak;
                        document.getElementById('headingHasilPeriksaAnak').style.display = hasHasilPeriksaAnak ? 'block' : 'none';
                        document.getElementById('hasilPeriksaAnakTable').style.display = hasHasilPeriksaAnak ? 'table' : 'none';
                        if (hasHasilPeriksaAnak) {
                            const tbodyAnak = document.querySelector('#hasilPeriksaAnakTable tbody');
                            tbodyAnak.innerHTML = `
                            <tr><th>Berat Badan Anak</th><td>${d.berat_badan_anak || '-'}</td></tr>
                            <tr><th>Makanan Anak</th><td>${d.makanan_anak || '-'}</td></tr>
                            <tr><th>Gejala Anak</th><td>${d.gejala_anak || '-'}</td></tr>
                            <tr><th>Nasehat Anak</th><td>${d.nasehat_anak || '-'}</td></tr>
                            <tr><th>Pengobatan Anak</th><td>${d.pegobatan_anak || '-'}</td></tr>
                            <tr><th>Penanggung Jawab Anak</th><td>${d.penanggung_jawab_anak || '-'}</td></tr>
                        `;
                        } else {
                            document.querySelector('#hasilPeriksaAnakTable tbody').innerHTML = '';
                        }

                        // Cek hasil periksa gigi
                        const hasHasilPeriksaGigi = d.odontogram || d.pemeriksaan_subjektif || d.pemeriksaan_objektif || d.diagnosa_gigi || d.terapi_anjuran_gigi || d.catatan_gigi;
                        document.getElementById('headingHasilPeriksaGigi').style.display = hasHasilPeriksaGigi ? 'block' : 'none';
                        document.getElementById('tableHasilPeriksaGigi').style.display = hasHasilPeriksaGigi ? 'table' : 'none';
                        if (hasHasilPeriksaGigi) {
                            document.getElementById('detailOdontogramGigi').textContent = d.odontogram || '-';
                            document.getElementById('detailPemeriksaanSubjektifGigi').textContent = d.pemeriksaan_subjektif || '-';
                            document.getElementById('detailPemeriksaanObjektifGigi').textContent = d.pemeriksaan_objektif || '-';
                            document.getElementById('detailDiagnosisGigi').textContent = d.diagnosa_gigi || '-';
                            document.getElementById('detailTerapiAnjuranGigi').textContent = d.terapi_anjuran_gigi || '-';
                            document.getElementById('detailCatatanGigi').textContent = d.catatan_gigi || '-';
                            document.getElementById('detailPenanggungJawabGigi').textContent = d.penanggung_jawab_gigi || '-';
                        }

                        // Populate Hasil Analisa Rawatinap
                        const hasHasilAnalisaRawatinap = d.tekanan_darah_rawatinap || d.frekuensi_nadi_rawatinap || d.suhu_rawatinap || d.frekuensi_nafas_rawatinap || d.skor_nyeri_rawatinap || d.skor_jatuh_rawatinap || d.berat_badan_rawatinap || d.tinggi_badan_rawatinap || d.lingkar_kepala_rawatinap || d.imt_rawatinap || d.alat_bantu_rawatinap || d.prosthesa_rawatinap || d.cacat_tubuh_rawatinap || d.adl_mandiri_rawatinap || d.riwayat_jatuh_rawatinap || d.status_psikologi_rawatinap || d.hambatan_edukasi_rawatinap || d.alergi_rawatinap || d.catatan_rawatinap;
                        document.getElementById('headingHasilAnalisaRawatinap').style.display = hasHasilAnalisaRawatinap ? 'block' : 'none';
                        document.getElementById('tableHasilAnalisaRawatinap').style.display = hasHasilAnalisaRawatinap ? 'table' : 'none';
                        if (hasHasilAnalisaRawatinap) {
                            document.getElementById('detailTekananDarahRawatinap').textContent = d.tekanan_darah_rawatinap || '-';
                            document.getElementById('detailFrekuensiNadiRawatinap').textContent = d.frekuensi_nadi_rawatinap || '-';
                            document.getElementById('detailSuhuRawatinap').textContent = d.suhu_rawatinap || '-';
                            document.getElementById('detailFrekuensiNafasRawatinap').textContent = d.frekuensi_nafas_rawatinap || '-';
                            document.getElementById('detailSkorNyeriRawatinap').textContent = d.skor_nyeri_rawatinap || '-';
                            document.getElementById('detailSkorJatuhRawatinap').textContent = d.skor_jatuh_rawatinap || '-';
                            document.getElementById('detailBeratBadanRawatinap').textContent = d.berat_badan_rawatinap || '-';
                            document.getElementById('detailTinggiBadanRawatinap').textContent = d.tinggi_badan_rawatinap || '-';
                            document.getElementById('detailLingkarKepalaRawatinap').textContent = d.lingkar_kepala_rawatinap || '-';
                            document.getElementById('detailIMTRawatinap').textContent = d.imt_rawatinap || '-';
                            document.getElementById('detailAlatBantuRawatinap').textContent = d.alat_bantu_rawatinap || '-';
                            document.getElementById('detailProsthesaRawatinap').textContent = d.prosthesa_rawatinap || '-';
                            document.getElementById('detailCacatTubuhRawatinap').textContent = d.cacat_tubuh_rawatinap || '-';
                            document.getElementById('detailADLMandiriRawatinap').textContent = d.adl_mandiri_rawatinap || '-';
                            document.getElementById('detailRiwayatJatuhRawatinap').textContent = d.riwayat_jatuh_rawatinap || '-';
                            document.getElementById('detailStatusPsikologiRawatinap').textContent = d.status_psikologi_rawatinap || '-';
                            document.getElementById('detailHambatanEdukasiRawatinap').textContent = d.hambatan_edukasi_rawatinap || '-';
                            document.getElementById('detailAlergiRawatinap').textContent = d.alergi_rawatinap || '-';
                            document.getElementById('detailCatatanRawatinap').textContent = d.catatan_rawatinap || '-';
                            document.getElementById('detailPenanggungJawabRawatinap').textContent = d.penanggung_jawab_rawatinap || '-';
                        } else {
                            document.getElementById('detailTekananDarahRawatinap').textContent = '-';
                            document.getElementById('detailFrekuensiNadiRawatinap').textContent = '-';
                            document.getElementById('detailSuhuRawatinap').textContent = '-';
                            document.getElementById('detailFrekuensiNafasRawatinap').textContent = '-';
                            document.getElementById('detailSkorNyeriRawatinap').textContent = '-';
                            document.getElementById('detailSkorJatuhRawatinap').textContent = '-';
                            document.getElementById('detailBeratBadanRawatinap').textContent = '-';
                            document.getElementById('detailTinggiBadanRawatinap').textContent = '-';
                            document.getElementById('detailLingkarKepalaRawatinap').textContent = '-';
                            document.getElementById('detailIMTRawatinap').textContent = '-';
                            document.getElementById('detailAlatBantuRawatinap').textContent = '-';
                            document.getElementById('detailProsthesaRawatinap').textContent = '-';
                            document.getElementById('detailCacatTubuhRawatinap').textContent = '-';
                            document.getElementById('detailADLMandiriRawatinap').textContent = '-';
                            document.getElementById('detailRiwayatJatuhRawatinap').textContent = '-';
                            document.getElementById('detailStatusPsikologiRawatinap').textContent = '-';
                            document.getElementById('detailHambatanEdukasiRawatinap').textContent = '-';
                            document.getElementById('detailAlergiRawatinap').textContent = '-';
                            document.getElementById('detailCatatanRawatinap').textContent = '-';
                            document.getElementById('detailPenanggungJawabRawatinap').textContent = '-';
                        }

                        // Populate Hasil Periksa UGD
                        const hasHasilPeriksaUgd = d.tanggal_periksa_ugd || d.waktu_ugd || d.soap_ugd || d.intruksi_tenaga_kerja_ugd || d.penanggung_jawab_ugd;
                        document.getElementById('headingHasilPeriksaUgd').style.display = hasHasilPeriksaUgd ? 'block' : 'none';
                        document.getElementById('tableHasilPeriksaUgd').style.display = hasHasilPeriksaUgd ? 'table' : 'none';
                        if (hasHasilPeriksaUgd) {
                            document.getElementById('detailTanggalPeriksaUgd').textContent = d.tanggal_periksa_ugd || '-';
                            document.getElementById('detailWaktuUgd').textContent = d.waktu_ugd || '-';
                            document.getElementById('detailSoapUgd').textContent = d.soap_ugd || '-';
                            document.getElementById('detailIntruksiTenagaKerjaUgd').textContent = d.intruksi_tenaga_kerja_ugd || '-';
                            document.getElementById('detailPenanggungJawabUgd').textContent = d.penanggung_jawab_ugd || '-';
                        } else {
                            document.getElementById('detailTanggalPeriksaUgd').textContent = '-';
                            document.getElementById('detailWaktuUgd').textContent = '-';
                            document.getElementById('detailSoapUgd').textContent = '-';
                            document.getElementById('detailIntruksiTenagaKerjaUgd').textContent = '-';
                            document.getElementById('detailPenanggungJawabUgd').textContent = '-';
                        }
                    })
                    .catch(error => {
                        alert(error.message);
                    });
            }
        });

        // Event listener tombol Tutup di detail view
        document.getElementById('btnTutupDetail').addEventListener('click', function() {
            document.getElementById('hasilPeriksaDetail').style.display = 'none';
            document.getElementById('riwayatList').style.display = 'block';
        });
    });
</script>
@endsection
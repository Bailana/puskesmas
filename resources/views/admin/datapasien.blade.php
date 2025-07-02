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
                                <td style="white-space: nowrap;">{{ $index + 1 }}</td>
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
                                    <button type="button" class="btn btn-success btn-sm rounded" data-bs-toggle="modal"
                                        data-bs-target="#modalRiwayatBerobat"
                                        data-no_rekam_medis="{{ $pasien->no_rekam_medis }}"
                                        data-nama="{{ $pasien->nama_pasien }}">
                                        Riwayat Berobat
                                    </button>
                                    <!-- <button type="button" class="btn btn-warning btn-sm rounded editPasienBtn" data-bs-toggle="modal" data-bs-target="#modalEditPasien"
                                        data-id="{{ $pasien->id }}"
                                        data-no_rekam_medis="{{ $pasien->no_rekam_medis }}"
                                        data-nik="{{ $pasien->nik }}"
                                        data-nama_pasien="{{ $pasien->nama_pasien }}"
                                        data-tempat_lahir="{{ $pasien->tempat_lahir }}"
                                        data-tanggal_lahir="{{ $pasien->tanggal_lahir }}"
                                        data-jenis_kelamin="{{ $pasien->jenis_kelamin }}"
                                        data-gol_darah="{{ $pasien->gol_darah }}"
                                        data-agama="{{ $pasien->agama }}"
                                        data-pekerjaan="{{ $pasien->pekerjaan }}"
                                        data-status_pernikahan="{{ $pasien->status_pernikahan }}"
                                        data-alamat_jalan="{{ $pasien->alamat_jalan }}"
                                        data-rt="{{ $pasien->rt }}"
                                        data-rw="{{ $pasien->rw }}"
                                        data-kelurahan="{{ $pasien->kelurahan }}"
                                        data-kecamatan="{{ $pasien->kecamatan }}"
                                        data-kabupaten="{{ $pasien->kabupaten }}"
                                        data-provinsi="{{ $pasien->provinsi }}"
                                        data-jaminan_kesehatan="{{ $pasien->jaminan_kesehatan }}"
                                        data-nomor_kepesertaan="{{ $pasien->nomor_kepesertaan }}"
                                        data-kepala_keluarga="{{ $pasien->kepala_keluarga }}"
                                        data-no_hp="{{ $pasien->no_hp }}">
                                        Edit
                                    </button> -->
                                    <!-- <form action="{{ url('/admin/pasien/delete/' . $pasien->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm rounded" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</button>
                                    </form> -->
                                    <a href="{{ route('cetak.surat', $pasien->id) }}" class="btn btn-warning btn-sm rounded" target="_blank">Cetak Surat</a>
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
<!-- Modal Edit Pasien
<div class="modal fade" id="modalEditPasien" tabindex="-1" aria-labelledby="modalEditPasienLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
        <div class="modal-content" style="overflow-x: hidden;">
            <div class="modal-header d-flex justify-content-between">
                <h3 class="modal-title" id="modalEditPasienLabel"><strong>Edit Data Pasien</strong></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formEditPasien" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body p-3" style="max-height: 600px; overflow-y: auto;">
                    <div class="container-fluid">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="editNoRekamMedis" class="form-label">No. Rekam Medis</label>
                                <input type="text" class="form-control form-control-sm" id="editNoRekamMedis" name="no_rekam_medis" required>
                            </div>
                            <div class="col-md-4">
                                <label for="editNik" class="form-label">NIK</label>
                                <input type="text" class="form-control form-control-sm" id="editNik" name="nik" required>
                            </div>
                            <div class="col-md-4">
                                <label for="editNamaPasien" class="form-label">Nama Pasien</label>
                                <input type="text" class="form-control form-control-sm" id="editNamaPasien" name="nama_pasien" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="editTempatLahir" class="form-label">Tempat Lahir</label>
                                <input type="text" class="form-control form-control-sm" id="editTempatLahir" name="tempat_lahir">
                            </div>
                            <div class="col-md-4">
                                <label for="editTanggalLahir" class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control form-control-sm" id="editTanggalLahir" name="tanggal_lahir">
                            </div>
                            <div class="col-md-4">
                                <label for="editJenisKelamin" class="form-label">Jenis Kelamin</label>
                                <select class="form-select form-select-sm" id="editJenisKelamin" name="jenis_kelamin">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="editGolDarah" class="form-label">Golongan Darah</label>
                                <select class="form-select form-select-sm" id="editGolDarah" name="gol_darah">
                                    <option value="">Pilih Golongan Darah</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="AB">AB</option>
                                    <option value="O">O</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="editAgama" class="form-label">Agama</label>
                                <input type="text" class="form-control form-control-sm" id="editAgama" name="agama">
                            </div>
                            <div class="col-md-4">
                                <label for="editPekerjaan" class="form-label">Pekerjaan</label>
                                <input type="text" class="form-control form-control-sm" id="editPekerjaan" name="pekerjaan">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="editStatusPernikahan" class="form-label">Status Pernikahan</label>
                                <select class="form-select form-select-sm" id="editStatusPernikahan" name="status_pernikahan">
                                    <option value="">Pilih Status Pernikahan</option>
                                    <option value="Belum Menikah">Belum Menikah</option>
                                    <option value="Menikah">Menikah</option>
                                    <option value="Cerai">Cerai</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="editAlamatJalan" class="form-label">Alamat</label>
                                <input type="text" class="form-control form-control-sm" id="editAlamatJalan" name="alamat_jalan">
                            </div>
                            <div class="col-md-4">
                                <label for="editRt" class="form-label">RT</label>
                                <input type="text" class="form-control form-control-sm" id="editRt" name="rt">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="editRw" class="form-label">RW</label>
                                <input type="text" class="form-control form-control-sm" id="editRw" name="rw">
                            </div>
                            <div class="col-md-4">
                                <label for="editKelurahan" class="form-label">Kelurahan</label>
                                <input type="text" class="form-control form-control-sm" id="editKelurahan" name="kelurahan">
                            </div>
                            <div class="col-md-4">
                                <label for="editKecamatan" class="form-label">Kecamatan</label>
                                <input type="text" class="form-control form-control-sm" id="editKecamatan" name="kecamatan">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="editKabupaten" class="form-label">Kabupaten</label>
                                <input type="text" class="form-control form-control-sm" id="editKabupaten" name="kabupaten">
                            </div>
                            <div class="col-md-4">
                                <label for="editProvinsi" class="form-label">Provinsi</label>
                                <input type="text" class="form-control form-control-sm" id="editProvinsi" name="provinsi">
                            </div>
                            <div class="col-md-4">
                                <label for="editJaminanKesehatan" class="form-label">Jaminan Kesehatan</label>
                                <input type="text" class="form-control form-control-sm" id="editJaminanKesehatan" name="jaminan_kesehatan">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="editNomorKepesertaan" class="form-label">No. Kepesertaan</label>
                                <input type="text" class="form-control form-control-sm" id="editNomorKepesertaan" name="nomor_kepesertaan">
                            </div>
                            <div class="col-md-6">
                                <label for="editKepalaKeluarga" class="form-label">Kepala Keluarga</label>
                                <input type="text" class="form-control form-control-sm" id="editKepalaKeluarga" name="kepala_keluarga">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="editNoHp" class="form-label">No. HP</label>
                                <input type="text" class="form-control form-control-sm" id="editNoHp" name="no_hp">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end mt-3" style="gap: 10px;">
                    <button type="button" class="btn btn-danger " data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div> -->

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
                                <option value="Perusahaan"
                                    {{ request('jaminan_kesehatan') == 'Perusahaan' ? 'selected' : '' }}>
                                    Perusahaan</option>
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
                                <input type="text" class="form-control form-control-sm" id="modalNoKepesertaan" readonly>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Riwayat Berobat -->
<div class="modal fade" id="modalRiwayatBerobat" tabindex="-1" aria-labelledby="modalRiwayatBerobatLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
        <div class="modal-content" style="overflow-x: hidden;">
            <div class="modal-header d-flex justify-content-between">
                <h3 class="modal-title" id="modalRiwayatBerobatLabel"><strong>Riwayat Berobat - <span id="riwayatNamaPasien"></span></strong></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body with Scroll -->
            <div class="modal-body p-3" style="max-height: 400px; overflow-y: auto;">
                <h5>Hasil Analisa Rawat Inap</h5>
                <form id="formHasilAnalisa">
                    <div class="container-fluid">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="tekanan_darah" class="form-label">Tekanan Darah</label>
                                <input type="text" class="form-control form-control-sm" id="tekanan_darah" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="frekuensi_nadi" class="form-label">Frekuensi Nadi</label>
                                <input type="text" class="form-control form-control-sm" id="frekuensi_nadi" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="suhu" class="form-label">Suhu</label>
                                <input type="text" class="form-control form-control-sm" id="suhu" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="frekuensi_nafas" class="form-label">Frekuensi Nafas</label>
                                <input type="text" class="form-control form-control-sm" id="frekuensi_nafas" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="skor_nyeri" class="form-label">Skor Nyeri</label>
                                <input type="text" class="form-control form-control-sm" id="skor_nyeri" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="skor_jatuh" class="form-label">Skor Jatuh</label>
                                <input type="text" class="form-control form-control-sm" id="skor_jatuh" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="berat_badan" class="form-label">Berat Badan</label>
                                <input type="text" class="form-control form-control-sm" id="berat_badan" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="tinggi_badan" class="form-label">Tinggi Badan</label>
                                <input type="text" class="form-control form-control-sm" id="tinggi_badan" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="lingkar_kepala" class="form-label">Lingkar Kepala</label>
                                <input type="text" class="form-control form-control-sm" id="lingkar_kepala" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="imt" class="form-label">IMT</label>
                                <input type="text" class="form-control form-control-sm" id="imt" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="alat_bantu" class="form-label">Alat Bantu</label>
                                <input type="text" class="form-control form-control-sm" id="alat_bantu" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="prosthesa" class="form-label">Prosthesa</label>
                                <input type="text" class="form-control form-control-sm" id="prosthesa" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="cacat_tubuh" class="form-label">Cacat Tubuh</label>
                                <input type="text" class="form-control form-control-sm" id="cacat_tubuh" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="adl_mandiri" class="form-label">ADL Mandiri</label>
                                <input type="text" class="form-control form-control-sm" id="adl_mandiri" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="riwayat_jatuh" class="form-label">Riwayat Jatuh</label>
                                <input type="text" class="form-control form-control-sm" id="riwayat_jatuh" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="status_psikologi" class="form-label">Status Psikologi</label>
                                <input type="text" class="form-control form-control-sm" id="status_psikologi" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="hambatan_edukasi" class="form-label">Hambatan Edukasi</label>
                                <input type="text" class="form-control form-control-sm" id="hambatan_edukasi" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="alergi" class="form-label">Alergi</label>
                                <input type="text" class="form-control form-control-sm" id="alergi" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="catatan" class="form-label">Catatan</label>
                                <input type="text" class="form-control form-control-sm" id="catatan" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="ruangan" class="form-label">Ruangan</label>
                                <input type="text" class="form-control form-control-sm" id="ruangan" readonly>
                            </div>
                        </div>
                    </div>
                </form>

                <h5 class="mt-4">Hasil Periksa UGD</h5>
                <form id="formHasilPeriksa">
                    <div class="container-fluid">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="tanggal_periksa" class="form-label">Tanggal</label>
                                <input type="text" class="form-control form-control-sm" id="tanggal_periksa" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="waktu_periksa" class="form-label">Waktu</label>
                                <input type="text" class="form-control form-control-sm" id="waktu_periksa" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="soap" class="form-label">SOAP</label>
                                <input type="text" class="form-control form-control-sm" id="soap" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="instruksi_tenagakerja" class="form-label">Instruksi Tenaga Kerja</label>
                                <input type="text" class="form-control form-control-sm" id="instruksi_tenagakerja" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="penanggung_jawab" class="form-label">Penanggung Jawab</label>
                                <input type="text" class="form-control form-control-sm" id="penanggung_jawab" readonly>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalPasienDetail = document.getElementById('modalPasienDetail');
        if (!modalPasienDetail) {
            console.error('modalPasienDetail element not found');
            return;
        }
        modalPasienDetail.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var noRekamMedis = button.getAttribute('data-no_rekam_medis');
            var nik = button.getAttribute('data-nik');
            var nama = button.getAttribute('data-nama');
            var tempatLahir = button.getAttribute('data-tempat_lahir');
            var tanggalLahir = button.getAttribute('data-tanggal_lahir');
            var jenisKelamin = button.getAttribute('data-jenis_kelamin');
            var golDarah = button.getAttribute('data-gol_darah');
            var agama = button.getAttribute('data-agama');
            var pekerjaan = button.getAttribute('data-pekerjaan');
            var statusPernikahan = button.getAttribute('data-status_pernikahan');
            var alamat = button.getAttribute('data-alamat');
            var rt = button.getAttribute('data-rt');
            var rw = button.getAttribute('data-rw');
            var kelurahan = button.getAttribute('data-kelurahan');
            var kecamatan = button.getAttribute('data-kecamatan');
            var kabupaten = button.getAttribute('data-kabupaten');
            var provinsi = button.getAttribute('data-provinsi');
            var jaminan = button.getAttribute('data-jaminan');
            var noKepesertaan = button.getAttribute('data-no_kepesertaan');
            var kepalaKeluarga = button.getAttribute('data-kepala_keluarga');
            var noHp = button.getAttribute('data-no_hp');

            modalPasienDetail.querySelector('#modalNoRekamMedis').value = noRekamMedis || '';
            modalPasienDetail.querySelector('#modalNikPasien').value = nik || '';
            modalPasienDetail.querySelector('#modalNamaPasien').value = nama || '';
            modalPasienDetail.querySelector('#modalTempatLahir').value = tempatLahir || '';
            modalPasienDetail.querySelector('#modalTanggalLahir').value = tanggalLahir || '';
            modalPasienDetail.querySelector('#modalJenisKelamin').value = jenisKelamin || '';
            modalPasienDetail.querySelector('#modalGolonganDarah').value = golDarah || '';
            modalPasienDetail.querySelector('#modalAgama').value = agama || '';
            modalPasienDetail.querySelector('#modalPekerjaan').value = pekerjaan || '';
            modalPasienDetail.querySelector('#modalStatusPernikahan').value = statusPernikahan || '';
            modalPasienDetail.querySelector('#modalAlamat').value = alamat || '';
            modalPasienDetail.querySelector('#modalRt').value = rt || '';
            modalPasienDetail.querySelector('#modalRw').value = rw || '';
            modalPasienDetail.querySelector('#modalKelurahan').value = kelurahan || '';
            modalPasienDetail.querySelector('#modalKecamatan').value = kecamatan || '';
            modalPasienDetail.querySelector('#modalKabupaten').value = kabupaten || '';
            modalPasienDetail.querySelector('#modalProvinsi').value = provinsi || '';
            modalPasienDetail.querySelector('#modalJaminan').value = jaminan || '';
            modalPasienDetail.querySelector('#modalNoKepesertaan').value = noKepesertaan || '';
            modalPasienDetail.querySelector('#modalKepalaKeluarga').value = kepalaKeluarga || '';
            modalPasienDetail.querySelector('#modalNoHp').value = noHp || '';
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalRiwayatBerobat = document.getElementById('modalRiwayatBerobat');
        if (!modalRiwayatBerobat) {
            console.error('modalRiwayatBerobat element not found');
            return;
        }
        modalRiwayatBerobat.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var noRekamMedis = button.getAttribute('data-no_rekam_medis');
            var namaPasien = button.getAttribute('data-nama');

            // Set the modal title with patient name
            document.getElementById('riwayatNamaPasien').textContent = namaPasien || '';

            // Clear previous form data
            var formHasilAnalisa = document.getElementById('formHasilAnalisa');
            var formHasilPeriksa = document.getElementById('formHasilPeriksa');
            formHasilAnalisa.reset();
            formHasilPeriksa.reset();

            // Fetch riwayat berobat data via AJAX
            fetch('/admin/datapasien/riwayat-berobat/' + noRekamMedis)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        var hasilAnalisa = data.data.hasil_analisa;
                        var hasilPeriksa = data.data.hasil_periksa;

                        // Populate Hasil Analisa Rawat Inap form with latest record
                        if (hasilAnalisa.length > 0) {
                            var latestAnalisa = hasilAnalisa[0];
                            formHasilAnalisa.querySelector('#tekanan_darah').value = latestAnalisa.tekanan_darah || '';
                            formHasilAnalisa.querySelector('#frekuensi_nadi').value = latestAnalisa.frekuensi_nadi || '';
                            formHasilAnalisa.querySelector('#suhu').value = latestAnalisa.suhu || '';
                            formHasilAnalisa.querySelector('#frekuensi_nafas').value = latestAnalisa.frekuensi_nafas || '';
                            formHasilAnalisa.querySelector('#skor_nyeri').value = latestAnalisa.skor_nyeri || '';
                            formHasilAnalisa.querySelector('#skor_jatuh').value = latestAnalisa.skor_jatuh || '';
                            formHasilAnalisa.querySelector('#berat_badan').value = latestAnalisa.berat_badan || '';
                            formHasilAnalisa.querySelector('#tinggi_badan').value = latestAnalisa.tinggi_badan || '';
                            formHasilAnalisa.querySelector('#lingkar_kepala').value = latestAnalisa.lingkar_kepala || '';
                            formHasilAnalisa.querySelector('#imt').value = latestAnalisa.imt || '';
                            formHasilAnalisa.querySelector('#alat_bantu').value = latestAnalisa.alat_bantu || '';
                            formHasilAnalisa.querySelector('#prosthesa').value = latestAnalisa.prosthesa || '';
                            formHasilAnalisa.querySelector('#cacat_tubuh').value = latestAnalisa.cacat_tubuh || '';
                            formHasilAnalisa.querySelector('#adl_mandiri').value = latestAnalisa.adl_mandiri || '';
                            formHasilAnalisa.querySelector('#riwayat_jatuh').value = latestAnalisa.riwayat_jatuh || '';
                            // Parse JSON fields if they are strings
                            var statusPsikologi = latestAnalisa.status_psikologi;
                            if (typeof statusPsikologi === 'string') {
                                try {
                                    statusPsikologi = JSON.parse(statusPsikologi);
                                } catch (e) {
                                    statusPsikologi = [];
                                }
                            }
                            formHasilAnalisa.querySelector('#status_psikologi').value = Array.isArray(statusPsikologi) ? statusPsikologi.join(', ') : '';

                            var hambatanEdukasi = latestAnalisa.hambatan_edukasi;
                            if (typeof hambatanEdukasi === 'string') {
                                try {
                                    hambatanEdukasi = JSON.parse(hambatanEdukasi);
                                } catch (e) {
                                    hambatanEdukasi = [];
                                }
                            }
                            formHasilAnalisa.querySelector('#hambatan_edukasi').value = Array.isArray(hambatanEdukasi) ? hambatanEdukasi.join(', ') : '';
                            formHasilAnalisa.querySelector('#alergi').value = latestAnalisa.alergi || '';
                            formHasilAnalisa.querySelector('#catatan').value = latestAnalisa.catatan || '';
                            formHasilAnalisa.querySelector('#ruangan').value = latestAnalisa.ruangan || '';
                        } else {
                            // Clear form fields if no data
                            formHasilAnalisa.querySelector('#tekanan_darah').value = '';
                            formHasilAnalisa.querySelector('#frekuensi_nadi').value = '';
                            formHasilAnalisa.querySelector('#suhu').value = '';
                            formHasilAnalisa.querySelector('#frekuensi_nafas').value = '';
                            formHasilAnalisa.querySelector('#skor_nyeri').value = '';
                            formHasilAnalisa.querySelector('#skor_jatuh').value = '';
                            formHasilAnalisa.querySelector('#berat_badan').value = '';
                            formHasilAnalisa.querySelector('#tinggi_badan').value = '';
                            formHasilAnalisa.querySelector('#lingkar_kepala').value = '';
                            formHasilAnalisa.querySelector('#imt').value = '';
                            formHasilAnalisa.querySelector('#alat_bantu').value = '';
                            formHasilAnalisa.querySelector('#prosthesa').value = '';
                            formHasilAnalisa.querySelector('#cacat_tubuh').value = '';
                            formHasilAnalisa.querySelector('#adl_mandiri').value = '';
                            formHasilAnalisa.querySelector('#riwayat_jatuh').value = '';
                            formHasilAnalisa.querySelector('#status_psikologi').value = '';
                            formHasilAnalisa.querySelector('#hambatan_edukasi').value = '';
                            formHasilAnalisa.querySelector('#alergi').value = '';
                            formHasilAnalisa.querySelector('#catatan').value = '';
                            formHasilAnalisa.querySelector('#ruangan').value = '';
                        }

                        // Populate Hasil Periksa UGD form with latest record
                        if (hasilPeriksa.length > 0) {
                            var latestPeriksa = hasilPeriksa[0];
                            formHasilPeriksa.querySelector('#tanggal_periksa').value = latestPeriksa.tanggal || '';
                            formHasilPeriksa.querySelector('#waktu_periksa').value = latestPeriksa.waktu || '';
                            formHasilPeriksa.querySelector('#soap').value = latestPeriksa.soap || '';
                            formHasilPeriksa.querySelector('#instruksi_tenagakerja').value = latestPeriksa.intruksi_tenagakerja || '';
                            formHasilPeriksa.querySelector('#penanggung_jawab').value = latestPeriksa.penanggung_jawab || '';
                        } else {
                            // Clear form fields if no data
                            formHasilPeriksa.querySelector('#tanggal_periksa').value = '';
                            formHasilPeriksa.querySelector('#waktu_periksa').value = '';
                            formHasilPeriksa.querySelector('#soap').value = '';
                            formHasilPeriksa.querySelector('#instruksi_tenagakerja').value = '';
                            formHasilPeriksa.querySelector('#penanggung_jawab').value = '';
                        }
                    } else {
                        console.error('Data riwayat berobat tidak ditemukan');
                        // Clear all form fields
                        formHasilAnalisa.reset();
                        formHasilPeriksa.reset();
                    }
                })
                .catch(error => {
                    console.error('Error fetching riwayat berobat data:', error);
                    // Clear all form fields
                    formHasilAnalisa.reset();
                    formHasilPeriksa.reset();
                });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalEditPasien = document.getElementById('modalEditPasien');
        if (!modalEditPasien) {
            console.error('modalEditPasien element not found');
            return;
        }

        modalEditPasien.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;

            var id = button.getAttribute('data-id');
            var noRekamMedis = button.getAttribute('data-no_rekam_medis');
            var nik = button.getAttribute('data-nik');
            var namaPasien = button.getAttribute('data-nama_pasien');
            var tempatLahir = button.getAttribute('data-tempat_lahir');
            var tanggalLahir = button.getAttribute('data-tanggal_lahir');
            var jenisKelamin = button.getAttribute('data-jenis_kelamin');
            var golDarah = button.getAttribute('data-gol_darah');
            var agama = button.getAttribute('data-agama');
            var pekerjaan = button.getAttribute('data-pekerjaan');
            var statusPernikahan = button.getAttribute('data-status_pernikahan');
            var alamatJalan = button.getAttribute('data-alamat_jalan');
            var rt = button.getAttribute('data-rt');
            var rw = button.getAttribute('data-rw');
            var kelurahan = button.getAttribute('data-kelurahan');
            var kecamatan = button.getAttribute('data-kecamatan');
            var kabupaten = button.getAttribute('data-kabupaten');
            var provinsi = button.getAttribute('data-provinsi');
            var jaminanKesehatan = button.getAttribute('data-jaminan_kesehatan');
            var nomorKepesertaan = button.getAttribute('data-nomor_kepesertaan');
            var kepalaKeluarga = button.getAttribute('data-kepala_keluarga');
            var noHp = button.getAttribute('data-no_hp');

            modalEditPasien.querySelector('#formEditPasien').action = "{{ url('/admin/datapasien') }}/" + id;
            modalEditPasien.querySelector('#editNoRekamMedis').value = noRekamMedis || '';
            modalEditPasien.querySelector('#editNik').value = nik || '';
            modalEditPasien.querySelector('#editNamaPasien').value = namaPasien || '';
            modalEditPasien.querySelector('#editTempatLahir').value = tempatLahir || '';
            modalEditPasien.querySelector('#editTanggalLahir').value = tanggalLahir || '';
            modalEditPasien.querySelector('#editJenisKelamin').value = jenisKelamin || '';
            modalEditPasien.querySelector('#editGolDarah').value = golDarah || '';
            modalEditPasien.querySelector('#editAgama').value = agama || '';
            modalEditPasien.querySelector('#editPekerjaan').value = pekerjaan || '';
            modalEditPasien.querySelector('#editStatusPernikahan').value = statusPernikahan || '';
            modalEditPasien.querySelector('#editAlamatJalan').value = alamatJalan || '';
            modalEditPasien.querySelector('#editRt').value = rt || '';
            modalEditPasien.querySelector('#editRw').value = rw || '';
            modalEditPasien.querySelector('#editKelurahan').value = kelurahan || '';
            modalEditPasien.querySelector('#editKecamatan').value = kecamatan || '';
            modalEditPasien.querySelector('#editKabupaten').value = kabupaten || '';
            modalEditPasien.querySelector('#editProvinsi').value = provinsi || '';
            modalEditPasien.querySelector('#editJaminanKesehatan').value = jaminanKesehatan || '';
            modalEditPasien.querySelector('#editNomorKepesertaan').value = nomorKepesertaan || '';
            modalEditPasien.querySelector('#editKepalaKeluarga').value = kepalaKeluarga || '';
            modalEditPasien.querySelector('#editNoHp').value = noHp || '';
        });
    });
</script>
@endsection
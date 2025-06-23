@extends('dashboardDokter')

@section('dokter')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>Data Pasien</strong></h1>
    <div class="row">
        <div class="col-12 col-lg-12 col-xxl-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex justify-content-between">
                    <!-- Input Pencarian -->
                    <form method="GET" action="{{ route('dokter.pasien') }}" class="d-flex align-items-center"
                        style="gap: 10px;">
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
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover my-0" id="dokterPasien">
                        <thead>
                            <tr>
                                <th style="white-space: nowrap;">No.</th>
                                <th style="white-space: nowrap;">No. RM</th>
                                <th style="white-space: nowrap;">Nama Pasien</th>
                                <th style="white-space: nowrap;">Tempat, Tanggal Lahir</th>
                                <th style="white-space: nowrap;">Jenis Kelamin</th>
                                <th style="white-space: nowrap;">JamKes</th>
                                <th style="white-space: nowrap;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pasiens as $index => $pasien)
                            <tr>
                                <td class="white-space: nowrap">{{ $pasiens->firstItem() + $index }}</td>
                                <td style="white-space: nowrap;">{{ $pasien->no_rekam_medis }}</td>
                                <td style="white-space: nowrap;">{{ $pasien->nama_pasien}}</td>
                                <td style="white-space: nowrap;">
                                    {{ $pasien->tempat_lahir }},
                                    {{ $pasien->tanggal_lahir ? $pasien->tanggal_lahir->format('d-m-Y') : 'Tanggal tidak tersedia' }}
                                </td>
                                <td style="white-space: nowrap;">{{ $pasien->jenis_kelamin }}</td>
                                <td style="white-space: nowrap;">{{ $pasien->jaminan_kesehatan }}</td>
                                <td style="white-space: nowrap;">
                                    <button type="button" class="btn btn-primary btn-sm rounded" data-bs-toggle="modal"
                                        data-bs-target="#modalPasienDetail"
                                        data-no_rekam_medis="{{ $pasien->no_rekam_medis }}"
                                        data-nik="{{ $pasien->nik }}" data-nama="{{ $pasien->nama_pasien }}"
                                        data-tempat_lahir="{{ $pasien->tempat_lahir }}"
                                        data-tanggal_lahir="{{ $pasien->tanggal_lahir ? $pasien->tanggal_lahir->format('d-m-Y') : 'Tanggal tidak tersedia' }}"
                                        data-jenis_kelamin="{{ $pasien->jenis_kelamin }}"
                                        data-gol_darah="{{ $pasien->gol_darah }}" data-agama="{{ $pasien->agama }}"
                                        data-pekerjaan="{{ $pasien->pekerjaan }}"
                                        data-status_pernikahan="{{ $pasien->status_pernikahan }}"
                                        data-alamat="{{ $pasien->alamat_jalan }}" data-rt="{{ $pasien->rt }}"
                                        data-rw="{{ $pasien->rw }}" data-kelurahan="{{ $pasien->kelurahan }}"
                                        data-kecamatan="{{ $pasien->kecamatan }}"
                                        data-kabupaten="{{ $pasien->kabupaten }}"
                                        data-provinsi="{{ $pasien->provinsi }}"
                                        data-jaminan="{{ $pasien->jaminan_kesehatan }}"
                                        data-no_kepesertaan="{{ $pasien->nomor_kepesertaan }}"
                                        data-kepala_keluarga="{{ $pasien->kepala_keluarga}}"
                                        data-no_hp="{{ $pasien->no_hp}}">
                                        Selengkapnya
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm rounded" data-bs-toggle="modal"
                                        data-bs-target="#modalRiwayatBerobat"
                                        data-no_rekam_medis="{{ $pasien->no_rekam_medis }}"
                                        data-nama="{{ $pasien->nama_pasien }}">
                                        Riwayat Berobat
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                            @if ($pasiens->isEmpty())
                            <tr>
                                <td colspan="8" class="text-center">Data pasien tidak ditemukan</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 mb-2">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="small text-muted mb-2 text-start ps-3 pagination-info-text" style="max-width: 50%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
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

            <!-- Modal Filter -->
            <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <form method="GET" action="{{ route('dokter.pasien') }}">
                            <div class="modal-header d-flex justify-content-between align-items-center">
                                <h3 class="modal-title" id="filterModalLabel"><strong>Filter Data Pasien</strong></h3>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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
                                <label for="modalKepalaKeluar" class="form-label">Kepala Keluarga</label>
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
        </div>
    </div>
</div>

<!-- Modal Riwayat Berobat -->
<div class="modal fade" id="modalRiwayatBerobat" tabindex="-1" aria-labelledby="modalRiwayatBerobatLabel"
    aria-hidden="true">
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
                                <td id="detailKesanStatusGizi"></td>
                            </tr>
                            <tr>
                                <th>Penanggung Jawab</th>
                                <td id="detailPenanggungJawab"></td>
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

@endsection

@section('scripts')
<script>
    var pasienDetailModal = document.getElementById('modalPasienDetail');
    pasienDetailModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        // Extract info from data-bs-* attributes
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

        // Update the modal's input fields
        pasienDetailModal.querySelector('#modalNoRekamMedis').value = noRekamMedis || '';
        pasienDetailModal.querySelector('#modalNikPasien').value = nik || '';
        pasienDetailModal.querySelector('#modalNamaPasien').value = nama || '';
        pasienDetailModal.querySelector('#modalTempatLahir').value = tempatLahir || '';
        pasienDetailModal.querySelector('#modalTanggalLahir').value = tanggalLahir || '';
        pasienDetailModal.querySelector('#modalJenisKelamin').value = jenisKelamin || '';
        pasienDetailModal.querySelector('#modalGolonganDarah').value = golDarah || '';
        pasienDetailModal.querySelector('#modalAgama').value = agama || '';
        pasienDetailModal.querySelector('#modalPekerjaan').value = pekerjaan || '';
        pasienDetailModal.querySelector('#modalStatusPernikahan').value = statusPernikahan || '';
        pasienDetailModal.querySelector('#modalAlamat').value = alamat || '';
        pasienDetailModal.querySelector('#modalRt').value = rt || '';
        pasienDetailModal.querySelector('#modalRw').value = rw || '';
        pasienDetailModal.querySelector('#modalKelurahan').value = kelurahan || '';
        pasienDetailModal.querySelector('#modalKecamatan').value = kecamatan || '';
        pasienDetailModal.querySelector('#modalKabupaten').value = kabupaten || '';
        pasienDetailModal.querySelector('#modalProvinsi').value = provinsi || '';
        pasienDetailModal.querySelector('#modalJaminan').value = jaminan || '';
        pasienDetailModal.querySelector('#modalNoKepesertaan').value = noKepesertaan || '';
        pasienDetailModal.querySelector('#modalKepalaKeluarga').value = kepalaKeluarga || '';
        pasienDetailModal.querySelector('#modalNoHp').value = noHp || '';
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const pasienTableBody = document.querySelector('#dokterPasien tbody');

        function renderTableRows(pasiens) {
            pasienTableBody.innerHTML = '';
            if (!pasiens.data || pasiens.data.length === 0) {
                pasienTableBody.innerHTML =
                    '<tr><td colspan="8" class="text-center">Data pasien tidak ditemukan</td></tr>';
                return;
            }
            pasiens.data.forEach((pasien, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                                <td class="nowrap">${index + 1 + (pasiens.current_page - 1) * pasiens.per_page}</td>
                                <td class="nowrap">${pasien.no_rekam_medis}</td>
                                <td class="nowrap">${pasien.nama_pasien}</td>
                                <td class="nowrap">${pasien.tempat_lahir}, ${pasien.tanggal_lahir ? new Date(pasien.tanggal_lahir).toLocaleDateString('id-ID') : 'Tanggal tidak tersedia'}</td>
                                <td class="nowrap">${pasien.jenis_kelamin}</td>
                                <td class="nowrap">${pasien.jaminan_kesehatan}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm rounded" data-bs-toggle="modal"
                                        data-bs-target="#modalPasienDetail"
                                        data-no_rekam_medis="${pasien.no_rekam_medis}" data-nik="${pasien.nik}"
                                        data-nama="${pasien.nama_pasien}"
                                        data-tempat_lahir="${pasien.tempat_lahir}"
                                        data-tanggal_lahir="${pasien.tanggal_lahir ? new Date(pasien.tanggal_lahir).toLocaleDateString('id-ID') : 'Tanggal tidak tersedia'}"
                                        data-jenis_kelamin="${pasien.jenis_kelamin}"
                                        data-gol_darah="${pasien.gol_darah}" data-agama="${pasien.agama}"
                                        data-pekerjaan="${pasien.pekerjaan}"
                                        data-status_pernikahan="${pasien.status_pernikahan}"
                                        data-alamat="${pasien.alamat_jalan}" data-rt="${pasien.rt}"
                                        data-rw="${pasien.rw}" data-kelurahan="${pasien.kelurahan}"
                                        data-kecamatan="${pasien.kecamatan}" data-kabupaten="${pasien.kabupaten}"
                                        data-provinsi="${pasien.provinsi}"
                                        data-jaminan="${pasien.jaminan_kesehatan}"
                                        data-no_kepesertaan="${pasien.nomor_kepesertaan}"
                                        data-kepala_keluarga="${pasien.kepala_keluarga}"
                                        data-no_hp="${pasien.no_hp}">
                                        Selengkapnya
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm rounded" data-bs-toggle="modal"
                                        data-bs-target="#modalRiwayatBerobat"
                                        data-no_rekam_medis="${pasien.no_rekam_medis}"
                                        data-nama="${pasien.nama_pasien}">
                                        Riwayat Berobat
                                    </button>
                                </td>
                            `;
                pasienTableBody.appendChild(row);
            });
        }

        let debounceTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => {
                const query = searchInput.value.trim();
                fetch(`{{ route('dokter.pasien') }}?search=${encodeURIComponent(query)}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        renderTableRows(data);
                    })
                    .catch(error => {
                        console.error('Error fetching search results:', error);
                    });
            }, 300);
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle Riwayat Berobat button click in pasien view
        document.querySelectorAll('button[data-bs-target="#modalRiwayatBerobat"]').forEach(button => {
            button.addEventListener('click', function() {
                const noRekamMedis = this.getAttribute('data-no_rekam_medis');
                const namaPasien = this.getAttribute('data-nama');
                const modal = new bootstrap.Modal(document.getElementById(
                    'modalRiwayatBerobat'));
                const riwayatList = document.getElementById('riwayatList');
                const hasilPeriksaDetail = document.getElementById('hasilPeriksaDetail');

                // Clear previous content
                riwayatList.innerHTML = '';
                hasilPeriksaDetail.style.display = 'none';

                // Fetch riwayat berobat dates by pasien no_rekam_medis or id if available
                fetch(`/dokter/riwayat-berobat/${noRekamMedis}`)
                    .then(response => response.json())
                    .then(dates => {
                        if (dates.length === 0) {
                            riwayatList.innerHTML = '<p>Tidak ada riwayat berobat.</p>';
                        } else {
                            dates.forEach((tanggal, index) => {
                                const dateObj = new Date(tanggal);
                                const options = {
                                    weekday: 'long',
                                    year: 'numeric',
                                    month: '2-digit',
                                    day: '2-digit'
                                };
                                const dateStr = dateObj.toLocaleDateString('id-ID',
                                    options);
                                const div = document.createElement('div');
                                div.classList.add('d-flex',
                                    'justify-content-between',
                                    'align-items-center');
                                if (index < dates.length - 1) {
                                    div.style.borderBottom = '1px solid #dee2e6';
                                    div.style.paddingBottom = '0.5rem';
                                    div.style.marginBottom = '0.5rem';
                                }
                                div.innerHTML = `
                                        <span>${dateStr}</span>
                                        <button class="btn btn-primary btn-sm btnLihat" data-tanggal="${tanggal}" data-norm="${noRekamMedis}">Lihat</button>
                                    `;
                                riwayatList.appendChild(div);
                            });

                            // Add event listeners to Lihat buttons
                            riwayatList.querySelectorAll('.btnLihat').forEach(btnLihat => {
                                btnLihat.addEventListener('click', function() {
                                    const tanggal = this.getAttribute(
                                        'data-tanggal');
                                    const noRekamMedis = this.getAttribute(
                                        'data-norm');

                                    fetch(
                                            `/dokter/hasil-periksa-detail/${noRekamMedis}/${tanggal}`
                                        )
                                        .then(response => {
                                            if (!response.ok) {
                                                throw new Error(
                                                    'Data hasil periksa tidak ditemukan'
                                                );
                                            }
                                            return response.json();
                                        })
                                        .then(data => {
                                            hasilPeriksaDetail.style
                                                .display = 'block';
                                            riwayatList.style.display =
                                                'none';
                                            const dateObj = new Date(
                                                data.tanggal_periksa
                                            );
                                            const options = {
                                                weekday: 'long',
                                                year: 'numeric',
                                                month: '2-digit',
                                                day: '2-digit'
                                            };
                                            document.getElementById(
                                                    'detailTanggal')
                                                .textContent = dateObj
                                                .toLocaleDateString(
                                                    'id-ID', options);
                                            document.getElementById(
                                                    'detailAnamnesis')
                                                .textContent = data
                                                .anamnesis || '-';
                                            document.getElementById(
                                                    'detailPemeriksaanFisik'
                                                ).textContent = data
                                                .pemeriksaan_fisik ||
                                                '-';
                                            document.getElementById(
                                                    'detailRencanaTerapi'
                                                ).textContent = data
                                                .rencana_dan_terapi ||
                                                '-';
                                            document.getElementById(
                                                    'detailDiagnosis')
                                                .textContent = data
                                                .diagnosis || '-';
                                            document.getElementById(
                                                    'detailEdukasi')
                                                .textContent = data
                                                .edukasi || '-';
                                            document.getElementById(
                                                    'detailKodeICD')
                                                .textContent = data
                                                .kode_icd || '-';
                                            document.getElementById(
                                                    'detailKesanStatusGizi'
                                                ).textContent = data
                                                .kesan_status_gizi ||
                                                '-';
                                            document.getElementById(
                                                    'detailPenanggungJawab'
                                                ).textContent = data
                                                .penanggung_jawab_nama ||
                                                '-';
                                        })
                                        .catch(error => {
                                            alert(error.message);
                                        });
                                });
                            });
                        }
                    })
                    .catch(error => {
                        riwayatList.innerHTML = `<p>Error: ${error.message}</p>`;
                    });

                modal.show();

                // Add event listener for the new Tutup button inside detail view
                document.getElementById('btnTutupDetail').addEventListener('click',
                    function() {
                        hasilPeriksaDetail.style.display = 'none';
                        riwayatList.style.display = 'block';
                    });
            });
        });
    });
</script>
@endsection
@extends('dashboardrawatinap')

@section('rawatinap')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>Data Pasien</strong></h1>
    <div class="row">
        <div class="col-12 col-lg-12 col-xxl-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <!-- Input Pencarian -->
                        <form method="GET" action="{{ route('rawatinap.pasien') }}" class="d-flex align-items-center" style="gap: 10px;">
                        <div class="input-group" style="width: 250px;">
                            <input type="text" name="search" class="form-control" id="searchInput" placeholder="Pencarian..." aria-label="Search" value="{{ request('search') }}" autocomplete="off">
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" id="filterButton" title="Filter Data Pasien" data-bs-toggle="modal" data-bs-target="#filterModal">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </form>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahPasien">
                        <i class="fas fa-plus"></i> Tambah Pasien
                    </button>
                </div>
                <table class="table table-hover my-0" id="dokterPasien">
                    <thead>
                        <tr>
                            <th style="white-space: nowrap;">No.</th>
                            <th style="white-space: nowrap;">No. RM</th>
                            <th style="white-space: nowrap;">Nama Pasien</th>
                            <th style="white-space: nowrap;">Tempat, Tanggal Lahir</th>
                            <th style="white-space: nowrap;">Jenis Kelamin</th>
                            <th style="white-space: nowrap;">Gol.Darah</th>
                            <th style="white-space: nowrap;">JamKes</th>
                            <th style="white-space: nowrap;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pasiens as $index => $pasien)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $pasien->no_rekam_medis }}</td>
                            <td>{{ $pasien->nama_pasien}}</td>
                            <td>{{ $pasien->tempat_lahir }}, {{ \Carbon\Carbon::parse($pasien->tanggal_lahir)->format('d-m-Y') ?? 'Tanggal tidak tersedia' }}</td>
                            <td>{{ $pasien->jenis_kelamin }}</td>
                            <td>{{ $pasien->gol_darah }}</td>
                            <td>{{ $pasien->jaminan_kesehatan }}</td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm rounded" data-bs-toggle="modal" data-bs-target="#modalPasienDetail"
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
                                <button type="button" class="btn btn-danger btn-sm rounded" data-bs-toggle="modal"
                                    data-bs-target="#modalRiwayatBerobat"
                                    data-no_rekam_medis="{{ $pasien->no_rekam_medis }}"
                                    data-nama="{{ $pasien->nama_pasien }}">
                                    Riwayat Berobat
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Modal Filter -->
            <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <form method="GET" action="{{ route('rawatinap.pasien') }}">
                            <div class="modal-header">
                                <h5 class="modal-title" id="filterModalLabel">Filter Data Pasien</h5>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-select">
                                            <option value="">Semua</option>
                                            <option value="Laki-laki" {{ request('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="Perempuan" {{ request('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gol_darah" class="form-label">Golongan Darah</label>
                                        <select name="gol_darah" id="gol_darah" class="form-select">
                                            <option value="">Semua</option>
                                            <option value="A" {{ request('gol_darah') == 'A' ? 'selected' : '' }}>A</option>
                                            <option value="B" {{ request('gol_darah') == 'B' ? 'selected' : '' }}>B</option>
                                            <option value="AB" {{ request('gol_darah') == 'AB' ? 'selected' : '' }}>AB</option>
                                            <option value="O" {{ request('gol_darah') == 'O' ? 'selected' : '' }}>O</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="jaminan_kesehatan" class="form-label">Jaminan Kesehatan</label>
                                        <select name="jaminan_kesehatan" id="jaminan_kesehatan" class="form-select">
                                            <option value="">Semua</option>
                                            <option value="Umum" {{ request('jaminan_kesehatan') == 'Umum' ? 'selected' : '' }}>Umum</option>
                                            <option value="BPJS Kesehatan" {{ request('jaminan_kesehatan') == 'BPJS Kesehatan' ? 'selected' : '' }}>BPJS Kesehatan</option>
                                            <option value="Perusahaan" {{ request('jaminan_kesehatan') == 'Perusahaan' ? 'selected' : '' }}>Perusahaan</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                                        <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control" value="{{ request('tempat_lahir') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="kecamatan" class="form-label">Kecamatan</label>
                                        <input type="text" name="kecamatan" id="kecamatan" class="form-control" value="{{ request('kecamatan') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="kelurahan" class="form-label">Kelurahan</label>
                                        <input type="text" name="kelurahan" id="kelurahan" class="form-control" value="{{ request('kelurahan') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="status_pernikahan" class="form-label">Status Pernikahan</label>
                                        <select name="status_pernikahan" id="status_pernikahan" class="form-select">
                                            <option value="">Semua</option>
                                            <option value="Belum Menikah" {{ request('status_pernikahan') == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah</option>
                                            <option value="Menikah" {{ request('status_pernikahan') == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                                            <option value="Cerai" {{ request('status_pernikahan') == 'Cerai' ? 'selected' : '' }}>Cerai</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" value="{{ request('tanggal_lahir') }}">
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
        </div>
    </div>
</div>

<!-- Modal Data Pasien -->
<div class="modal fade" id="modalPasienDetail" tabindex="-1" aria-labelledby="modalPasienDetailLabel" aria-hidden="true">
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
                                <input type="text" class="form-control form-control-sm" id="modalGolonganDarah" readonly>
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
                                <input type="text" class="form-control form-control-sm" id="modalStatusPernikahan" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalKepalaKeluarga" class="form-label">Kepala Keluarga</label>
                                <input type="text" class="form-control form-control-sm" id="modalKepalaKeluarga" readonly>
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
                <div id="emptyRiwayatMessage" style="display:none; text-align:center; font-weight:bold; margin-top: 20px;">
                    Tidak ada riwayat berobat.
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

<!-- Modal Tambah Pasien -->
<div class="modal fade" id="modalTambahPasien" tabindex="-1" aria-labelledby="modalTambahPasienLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 95vw;">
        <div class="modal-content" style="overflow-x: auto;">
            <div class="modal-header d-flex justify-content-between">
                <h3 class="modal-title" id="modalTambahPasienLabel"><strong>Tambah Pasien</strong></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow-y: auto; padding: 10px;">
<form id="formTambahPasien" method="POST" action="{{ route('rawatinap.pasien.store') }}" novalidate>
    @csrf
    <div class="row mb-3">
        <div class="col-6">
            <label for="noRekamMedisTambah" class="form-label">No. Rekam Medis</label>
            <input type="text" name="no_rekam_medis" class="form-control form-control-sm" id="noRekamMedisTambah" required
                readonly>
            <div class="invalid-feedback">No. Rekam Medis wajib diisi.</div>
        </div>
        <div class="col-6">
            <label for="nikTambah" class="form-label">NIK</label>
            <input type="text" name="nik" class="form-control form-control-sm" id="nikTambah" required
                pattern="\d{16}" maxlength="16" title="NIK harus terdiri dari 16 digit angka"
                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,16);">
            <div class="invalid-feedback">NIK wajib diisi dan harus 16 digit angka.</div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-6">
            <label for="namaTambah" class="form-label">Nama Pasien</label>
            <input type="text" name="nama_pasien" class="form-control form-control-sm" id="namaTambah" required>
            <div class="invalid-feedback">Nama Pasien wajib diisi.</div>
        </div>
        <div class="col-6">
            <label for="tempatLahirTambah" class="form-label">Tempat Lahir</label>
            <input type="text" name="tempat_lahir" class="form-control form-control-sm" id="tempatLahirTambah" required>
            <div class="invalid-feedback">Tempat Lahir wajib diisi.</div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-6">
            <label for="tanggalLahirTambah" class="form-label">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" class="form-control form-control-sm" id="tanggalLahirTambah" required>
            <div class="invalid-feedback">Tanggal Lahir wajib diisi.</div>
        </div>
        <div class="col-6">
            <label for="jenisKelaminTambah" class="form-label">Jenis Kelamin</label>
            <select name="jenis_kelamin" class="form-select form-select-sm" id="jenisKelaminTambah" required>
                <option value="">Pilih Jenis Kelamin</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>
            <div class="invalid-feedback">Jenis Kelamin wajib dipilih.</div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-6">
            <label for="golonganDarahTambah" class="form-label">Golongan Darah</label>
            <select name="gol_darah" class="form-select form-select-sm" id="golonganDarahTambah" required>
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
            <div class="invalid-feedback">Golongan Darah wajib dipilih.</div>
        </div>
        <div class="col-6">
            <label for="agamaTambah" class="form-label">Agama</label>
            <select name="agama" class="form-select form-select-sm" id="agamaTambah" required>
                <option value="">Pilih Agama</option>
                <option value="Islam">Islam</option>
                <option value="Kristen Protestan">Kristen Protestan</option>
                <option value="Katolik">Katolik</option>
                <option value="Hindu">Hindu</option>
                <option value="Buddha">Buddha</option>
                <option value="Konghucu">Konghucu</option>
                <option value="Lainnya">Lainnya</option>
            </select>
            <div class="invalid-feedback">Agama wajib dipilih.</div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-6">
            <label for="pekerjaanTambah" class="form-label">Pekerjaan</label>
            <input type="text" name="pekerjaan" class="form-control form-control-sm" id="pekerjaanTambah" required>
            <div class="invalid-feedback">Pekerjaan wajib diisi.</div>
        </div>
        <div class="col-6">
            <label for="statusPernikahanTambah" class="form-label">Status Pernikahan</label>
            <select name="status_pernikahan" class="form-select form-select-sm" id="statusPernikahanTambah" required>
                <option value="">Pilih Status Pernikahan</option>
                <option value="Belum Menikah">Belum Menikah</option>
                <option value="Menikah">Menikah</option>
                <option value="Cerai">Cerai</option>
            </select>
            <div class="invalid-feedback">Status Pernikahan wajib dipilih.</div>
        </div>
    </div>
    <div class="mb-3">
        <label for="alamatTambah" class="form-label">Alamat Jalan</label>
        <input type="text" name="alamat_jalan" class="form-control form-control-sm" id="alamatTambah" required>
        <div class="invalid-feedback">Alamat wajib diisi.</div>
    </div>
    <div class="row mb-3">
        <div class="col-4">
            <label for="rtTambah" class="form-label">RT</label>
            <input type="text" name="rt" class="form-control form-control-sm" id="rtTambah" required>
            <div class="invalid-feedback">RT wajib diisi.</div>
        </div>
        <div class="col-4">
            <label for="rwTambah" class="form-label">RW</label>
            <input type="text" name="rw" class="form-control form-control-sm" id="rwTambah" required>
            <div class="invalid-feedback">RW wajib diisi.</div>
        </div>
        <div class="col-4">
            <label for="kelurahanTambah" class="form-label">Kelurahan</label>
            <input type="text" name="kelurahan" class="form-control form-control-sm" id="kelurahanTambah" required>
            <div class="invalid-feedback">Kelurahan wajib diisi.</div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-4">
            <label for="kecamatanTambah" class="form-label">Kecamatan</label>
            <input type="text" name="kecamatan" class="form-control form-control-sm" id="kecamatanTambah" required>
            <div class="invalid-feedback">Kecamatan wajib diisi.</div>
        </div>
        <div class="col-4">
            <label for="kabupatenTambah" class="form-label">Kabupaten</label>
            <input type="text" name="kabupaten" class="form-control form-control-sm" id="kabupatenTambah" required>
            <div class="invalid-feedback">Kabupaten wajib diisi.</div>
        </div>
        <div class="col-4">
            <label for="provinsiTambah" class="form-label">Provinsi</label>
            <input type="text" name="provinsi" class="form-control form-control-sm" id="provinsiTambah" required>
            <div class="invalid-feedback">Provinsi wajib diisi.</div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-6">
            <label for="jaminanTambah" class="form-label">Jaminan Kesehatan</label>
            <select name="jaminan_kesehatan" class="form-select form-select-sm" id="jaminanTambah" required>
                <option value="">Pilih Jaminan Kesehatan</option>
                <option value="Umum">Umum</option>
                <option value="BPJS Kesehatan">BPJS Kesehatan</option>
                <option value="Perusahaan">Perusahaan</option>
            </select>
            <div class="invalid-feedback">Jaminan Kesehatan wajib dipilih.</div>
        </div>
        <div class="col-6">
            <label for="noKepesertaanTambah" class="form-label">Nomor Kepesertaan</label>
            <input type="text" name="nomor_kepesertaan" class="form-control form-control-sm" id="noKepesertaanTambah" required
                maxlength="16" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,16);">
            <div class="invalid-feedback">Nomor Kepesertaan wajib diisi.</div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-6">
            <label for="kepalaKeluargaTambah" class="form-label">Kepala Keluarga</label>
            <input type="text" name="kepala_keluarga" class="form-control form-control-sm" id="kepalaKeluargaTambah" required>
            <div class="invalid-feedback">Kepala Keluarga wajib diisi.</div>
        </div>
        <div class="col-6">
            <label for="noHpTambah" class="form-label">No. HP</label>
            <input type="text" name="no_hp" class="form-control form-control-sm" id="noHpTambah" required
                maxlength="14" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,14);">
            <div class="invalid-feedback">No. HP wajib diisi.</div>
        </div>
    </div>
</form>
            </div>
            <div class="modal-footer d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-success ms-2" id="btnSimpanTambah">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Pastikan nilai No. Rekam Medis tidak tertukar dengan NIK saat modal tambah pasien dibuka
    var modalTambahPasien = document.getElementById('modalTambahPasien');
    modalTambahPasien.addEventListener('show.bs.modal', function (event) {
        var form = document.getElementById('formTambahPasien');
        form.reset();
        // Ambil nomor rekam medis terbaru dari server via AJAX
fetch("{{ route('rawatinap.pasien') }}?get_new_no_rm=1", {
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    credentials: 'same-origin'
})
        .then(response => response.json())
        .then(data => {
            if (data && data.newNoRekamMedis) {
                document.getElementById('noRekamMedisTambah').value = data.newNoRekamMedis;
            } else {
                document.getElementById('noRekamMedisTambah').value = '{{ $newNoRekamMedis }}';
            }
        })
        .catch(() => {
            document.getElementById('noRekamMedisTambah').value = '{{ $newNoRekamMedis }}';
        });
    });

    document.getElementById('btnSimpanTambah').addEventListener('click', function () {
        var form = document.getElementById('formTambahPasien');
        var isValid = true;
        // Validasi manual untuk semua input required
        form.querySelectorAll('[required]').forEach(function(input) {
            if (!input.value || input.value.trim() === '') {
                input.classList.add('is-invalid');
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        });
        if (!isValid) {
            // Fokus ke field pertama yang error
            var firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) firstInvalid.focus();
            return;
        }
        var formData = new FormData(form);
        fetch("{{ route('rawatinap.pasien.store') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: formData
        })
        .then(response => {
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.indexOf('application/json') !== -1) {
                return response.json().then(data => ({ status: response.status, body: data }));
            } else {
                return response.text().then(text => {
                    return { status: response.status, body: { message: text } };
                });
            }
        })
        .then(({ status, body }) => {
            if (status === 200 || status === 201) {
                alert('Data pasien berhasil disimpan.');
                form.reset();
                if (body && body.newNoRekamMedis) {
                    document.getElementById('noRekamMedisTambah').value = body.newNoRekamMedis;
                }
                var modal = bootstrap.Modal.getInstance(document.getElementById('modalTambahPasien'));
                if (modal) modal.hide();
                location.reload();
            } else {
                // Tampilkan error validasi dari backend
                if (body.errors) {
                    Object.keys(body.errors).forEach(function(field) {
                        var input = form.querySelector('[name="'+field+'"]');
                        if (input) {
                            input.classList.add('is-invalid');
                            var feedback = input.parentElement.querySelector('.invalid-feedback');
                            if (feedback) feedback.textContent = body.errors[field][0];
                        }
                    });
                    var firstInvalid = form.querySelector('.is-invalid');
                    if (firstInvalid) firstInvalid.focus();
                } else if (body.message) {
                    alert('Gagal menyimpan data pasien: ' + body.message);
                } else {
                    alert('Gagal menyimpan data pasien.');
                }
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan saat menyimpan data pasien.');
            console.error('Fetch error:', error);
        });
    });

    // Event listener tombol Selengkapnya
    $(document).on('click', '[data-bs-target="#modalPasienDetail"]', function() {
        var noRekamMedis = $(this).data('no_rekam_medis');
        if (!noRekamMedis) return;
        // Kosongkan field modal dulu
        $('#modalPasienDetail input').val('');
        // Ambil data pasien via AJAX (route yang benar)
        fetch('/rawatinap/pasien/detail/' + encodeURIComponent(noRekamMedis))
            .then(response => response.json())
            .then(data => {
                if (data && !data.error) {
                    $('#modalNoRekamMedis').val(data.no_rekam_medis || '');
                    $('#modalNikPasien').val(data.nik || '');
                    $('#modalNamaPasien').val(data.nama_pasien || '');
                    $('#modalTempatLahir').val(data.tempat_lahir || '');
                    $('#modalTanggalLahir').val(data.tanggal_lahir || '');
                    $('#modalJenisKelamin').val(data.jenis_kelamin || '');
                    $('#modalGolonganDarah').val(data.gol_darah || '');
                    $('#modalAgama').val(data.agama || '');
                    $('#modalPekerjaan').val(data.pekerjaan || '');
                    $('#modalStatusPernikahan').val(data.status_pernikahan || '');
                    $('#modalKepalaKeluarga').val(data.kepala_keluarga || '');
                    $('#modalNoHp').val(data.no_hp || '');
                    $('#modalAlamat').val(data.alamat_jalan || '');
                    $('#modalRt').val(data.rt || '');
                    $('#modalRw').val(data.rw || '');
                    $('#modalKelurahan').val(data.kelurahan || '');
                    $('#modalKecamatan').val(data.kecamatan || '');
                    $('#modalKabupaten').val(data.kabupaten || '');
                    $('#modalProvinsi').val(data.provinsi || '');
                    $('#modalJaminan').val(data.jaminan_kesehatan || '');
                    $('#modalNoKepesertaan').val(data.nomor_kepesertaan || '');
                } else {
                    alert('Data pasien tidak ditemukan!');
                }
            })
            .catch(() => alert('Gagal mengambil data pasien.'));
    });

    $(document).ready(function() {
        var searchTimeout;
        var $searchInput = $('#searchInput');
        var $tableBody = $('#dokterPasien tbody');
        var $loadingIndicator = $('<div id="searchLoading" style="position:absolute; right: 270px; top: 10px; display:none;">Loading...</div>');
        $searchInput.after($loadingIndicator);

        $searchInput.on('input', function() {
            clearTimeout(searchTimeout);
            var query = $(this).val();

            searchTimeout = setTimeout(function() {
                $loadingIndicator.show();
$.ajax({
    url: '{{ route('rawatinap.pasien') }}',
    type: 'GET',
    data: { search: query, per_page: 1000 }, // tampilkan semua hasil
    dataType: 'json',
    success: function(response) {
        console.log('Search response:', response);
        var tbody = '';
        var data = response.data || response;
        if (data.length > 0) {
            $.each(data, function(index, pasien) {
                tbody += '<tr>' +
                    '<td>' + (index + 1) + '</td>' +
                    '<td>' + pasien.no_rekam_medis + '</td>' +
                    '<td>' + pasien.nama_pasien + '</td>' +
                    '<td>' + pasien.tempat_lahir + ', ' + (pasien.tanggal_lahir ? moment(pasien.tanggal_lahir).format('DD-MM-YYYY') : '-') + '</td>' +
                    '<td>' + pasien.jenis_kelamin + '</td>' +
                    '<td>' + pasien.gol_darah + '</td>' +
                    '<td>' + pasien.jaminan_kesehatan + '</td>' +
                    '<td>' +
                    '<button type="button" class="btn btn-primary btn-sm rounded" data-bs-toggle="modal" data-bs-target="#modalPasienDetail"' +
                    ' data-no_rekam_medis="' + pasien.no_rekam_medis + '"' +
                    ' data-nik="' + pasien.nik + '"' +
                    ' data-nama="' + pasien.nama_pasien + '"' +
                    ' data-tempat_lahir="' + pasien.tempat_lahir + '"' +
                    ' data-tanggal_lahir="' + (pasien.tanggal_lahir ? moment(pasien.tanggal_lahir).format('DD-MM-YYYY') : 'Tanggal tidak tersedia') + '"' +
                    ' data-jenis_kelamin="' + pasien.jenis_kelamin + '"' +
                    ' data-gol_darah="' + pasien.gol_darah + '"' +
                    ' data-agama="' + pasien.agama + '"' +
                    ' data-pekerjaan="' + pasien.pekerjaan + '"' +
                    ' data-status_pernikahan="' + pasien.status_pernikahan + '"' +
                    ' data-alamat="' + pasien.alamat_jalan + '"' +
                    ' data-rt="' + pasien.rt + '"' +
                    ' data-rw="' + pasien.rw + '"' +
                    ' data-kelurahan="' + pasien.kelurahan + '"' +
                    ' data-kecamatan="' + pasien.kecamatan + '"' +
                    ' data-kabupaten="' + pasien.kabupaten + '"' +
                    ' data-provinsi="' + pasien.provinsi + '"' +
                    ' data-jaminan="' + pasien.jaminan_kesehatan + '"' +
                    ' data-no_kepesertaan="' + pasien.nomor_kepesertaan + '"' +
                    ' data-kepala_keluarga="' + pasien.kepala_keluarga + '"' +
                    ' data-no_hp="' + pasien.no_hp + '">' +
                    'Selengkapnya' +
                    '</button> ' +
                    '<button type="button" class="btn btn-danger btn-sm rounded" data-bs-toggle="modal" data-bs-target="#modalRiwayatBerobat"' +
                    ' data-no_rekam_medis="' + pasien.no_rekam_medis + '"' +
                    ' data-nama="' + pasien.nama_pasien + '">' +
                    'Riwayat Berobat' +
                    '</button>' +
                    '</td>' +
                    '</tr>';
            });
            $('#dokterPasien tbody').html(tbody);
        } else {
            $('#dokterPasien tbody').html('<tr><td colspan="8" class="text-center">Data tidak ditemukan</td></tr>');
        }
        $loadingIndicator.hide();
    },
    error: function() {
        $('#dokterPasien tbody').html('<tr><td colspan="8" class="text-center">Gagal memuat data</td></tr>');
        $loadingIndicator.hide();
    }
});
            }, 400); // debounce 400ms
        });

        // Cegah submit form pencarian agar tidak reload halaman
        $('#searchInput').closest('form').on('submit', function(e) {
            e.preventDefault();
        });

        // New code: Load riwayat berobat data when modalRiwayatBerobat is shown
        var modalRiwayatBerobat = document.getElementById('modalRiwayatBerobat');
        modalRiwayatBerobat.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var noRekamMedis = button.getAttribute('data-no_rekam_medis');
            var modal = this;

            // Clear previous content
            modal.querySelector('#riwayatList').innerHTML = '';
            modal.querySelector('#emptyRiwayatMessage').style.display = 'none';
            modal.querySelector('#hasilPeriksaDetail').style.display = 'none';

            if (!noRekamMedis) {
                modal.querySelector('#emptyRiwayatMessage').style.display = 'block';
                modal.querySelector('#emptyRiwayatMessage').textContent = 'No. Rekam Medis tidak ditemukan.';
                return;
            }

            fetch('/rawatinap/pasien/riwayat-berobat/' + encodeURIComponent(noRekamMedis))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        var hasilAnalisa = data.data.hasil_analisa;
                        var hasilPeriksa = data.data.hasil_periksa;

                        if ((hasilAnalisa.length === 0) && (hasilPeriksa.length === 0)) {
                            modal.querySelector('#emptyRiwayatMessage').style.display = 'block';
                            modal.querySelector('#emptyRiwayatMessage').textContent = 'Tidak ada riwayat berobat.';
                            return;
                        }

                        var riwayatList = modal.querySelector('#riwayatList');
                        riwayatList.innerHTML = '';

                        // Create list of dates from hasilAnalisa and hasilPeriksa combined, sorted descending
                        var combined = [];

                        hasilAnalisa.forEach(function(item) {
                            combined.push({
                                type: 'Analisa Rawat Inap',
                                date: item.created_at,
                                data: item
                            });
                        });

                        hasilPeriksa.forEach(function(item) {
                            combined.push({
                                type: 'Hasil Periksa UGD',
                                date: item.tanggal + ' ' + item.waktu,
                                data: item
                            });
                        });

                        // Sort combined by date descending
                        combined.sort(function(a, b) {
                            return new Date(b.date) - new Date(a.date);
                        });

                        // Create clickable list items
                        combined.forEach(function(item, index) {
                            var div = document.createElement('div');
                            div.classList.add('riwayat-item');
                            div.style.cursor = 'pointer';
                            div.style.padding = '5px';
                            div.style.borderBottom = '1px solid #ddd';
                            div.textContent = item.type + ' - ' + new Date(item.date).toLocaleString();
                            div.addEventListener('click', function() {
                                // Show detail in hasilPeriksaDetail section
                                var detailSection = modal.querySelector('#hasilPeriksaDetail');
                                detailSection.style.display = 'block';

                                // Populate detail fields based on type
                                if (item.type === 'Analisa Rawat Inap') {
                                    var d = item.data;
                                    modal.querySelector('#detailTanggal').textContent = new Date(d.created_at).toLocaleString();
                                    modal.querySelector('#detailAnamnesis').textContent = d.anamnesis || '-';
                                    modal.querySelector('#detailPemeriksaanFisik').textContent = d.pemeriksaan_fisik || '-';
                                    modal.querySelector('#detailRencanaTerapi').textContent = d.rencana_terapi || '-';
                                    modal.querySelector('#detailDiagnosis').textContent = d.diagnosis || '-';
                                    modal.querySelector('#detailEdukasi').textContent = d.edukasi || '-';
                                    modal.querySelector('#detailKodeICD').textContent = d.kode_icd || '-';
                                    modal.querySelector('#detailKesanStatusGizi').textContent = d.kesan_status_gizi || '-';
                                    modal.querySelector('#detailPenanggungJawab').textContent = d.penanggung_jawab || '-';
                                } else if (item.type === 'Hasil Periksa UGD') {
                                    var d = item.data;
                                    modal.querySelector('#detailTanggal').textContent = new Date(d.tanggal + ' ' + d.waktu).toLocaleString();
                                    modal.querySelector('#detailAnamnesis').textContent = d.soap || '-';
                                    modal.querySelector('#detailPemeriksaanFisik').textContent = d.intruksi_tenaga_kerja || '-';
                                    modal.querySelector('#detailRencanaTerapi').textContent = '-';
                                    modal.querySelector('#detailDiagnosis').textContent = '-';
                                    modal.querySelector('#detailEdukasi').textContent = '-';
                                    modal.querySelector('#detailKodeICD').textContent = '-';
                                    modal.querySelector('#detailKesanStatusGizi').textContent = '-';
                                    modal.querySelector('#detailPenanggungJawab').textContent = d.penanggung_jawab || '-';
                                }

                                // Scroll to detail section
                                detailSection.scrollIntoView({ behavior: 'smooth' });
                            });
                            riwayatList.appendChild(div);
                        });

                        // Add event listener to close button in detail section
                        var btnTutupDetail = modal.querySelector('#btnTutupDetail');
                        if (btnTutupDetail) {
                            btnTutupDetail.addEventListener('click', function() {
                                modal.querySelector('#hasilPeriksaDetail').style.display = 'none';
                            });
                        }
                    } else {
                        modal.querySelector('#emptyRiwayatMessage').style.display = 'block';
                        modal.querySelector('#emptyRiwayatMessage').textContent = 'Gagal mengambil data riwayat berobat.';
                    }
                })
                .catch(() => {
                    modal.querySelector('#emptyRiwayatMessage').style.display = 'block';
                    modal.querySelector('#emptyRiwayatMessage').textContent = 'Gagal mengambil data riwayat berobat.';
                });
        });
    });
</script>
@endsection


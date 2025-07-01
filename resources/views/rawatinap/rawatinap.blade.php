@extends('dashboardrawatinap')

@section('rawatinap')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>Data Pasien Rawat Inap</strong></h1>
    <div class="row">
        <div class="col-12 col-lg-12 col-xxl-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div style="max-width: 300px;">
                        <input type="text" id="searchInput" class="form-control form-control-sm"
                            placeholder="Cari pasien...">
                    </div>
                    <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#modalTambahPasien">
                            Tambah Pasien
                        </button> -->
                </div>
                <div class="table-responsive">
                    <table class="table table-hover my-0" id="pasienTable">
                        <thead>
                            <tr>
                                <th class="nowrap">No.</th>
                                <th class="nowrap">Hari/Tanggal Masuk</th>
                                <th class="nowrap">Nama Pasien</th>
                                <th class="nowrap">Umur</th>
                                <th class="nowrap">Status</th>
                                <th class="nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($pasiens_ugd) > 0)
                            @foreach ($pasiens_ugd as $index => $pasien)
                            <tr>
                                <td class="nowrap">{{ $index + 1 }}</td>
                                <td class="nowrap">{{ \Carbon\Carbon::parse($pasien->tanggal_masuk)->translatedFormat('l, d-m-Y') }}
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
                                    <button class="btn btn-success btn-sm ms-1 rounded" data-bs-toggle="modal"
                                        data-bs-target="#modalRiwayatPeriksa" data-pasien-id="{{ $pasien->pasien_id }}"
                                        data-nama-pasien="{{ $pasien->nama_pasien }}">Riwayat Periksa</button>
                                    <button type="button" class="btn btn-warning btn-sm ms-1 rounded" data-bs-toggle="modal" data-bs-target="#modalPeriksa" data-pasien-id="{{ $pasien->pasien_id }}" data-nama-pasien="{{ $pasien->nama_pasien }}">Periksa</button>
                                    <!-- <button type="button" class="btn btn-success btn-sm ms-1 btn-hasil-periksa" data-bs-toggle="modal" data-bs-target="#modalHasilPeriksa" data-pasien-id="{{ $pasien->pasien_id }}">Hasil Periksa</button> -->
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data pasien unit gawat darurat</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
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
                </div>
                <div class="modal-footer d-flex justify-content-end mt-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Pasien -->
<div class="modal fade" id="modalTambahPasien" tabindex="-1" aria-labelledby="modalTambahPasienLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formTambahPasienUgd" method="POST" action="{{ url('/rawatinap/ugd/store') }}">
                @csrf
                <input type="hidden" id="pasien_id_cari" name="pasien_id" required>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahPasienLabel">Tambah Pasien UGD</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 row align-items-end">
                        <div class="col-md-8">
                            <label for="nomor_kepesertaan_cari" class="form-label">Nomor Kepesertaan</label>
                            <input type="text" class="form-control" id="nomor_kepesertaan_cari"
                                name="nomor_kepesertaan_cari" required maxlength="16" pattern="\d{1,16}"
                                inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,16);">
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-primary w-100" id="btnCariPasien">Cari</button>
                        </div>
                    </div>
                    <div id="hasilCariPasien" style="display:none;">
                        <div class="mb-2">
                            <label class="form-label">Nama Pasien</label>
                            <input type="text" class="form-control" id="nama_pasien_cari" name="nama_pasien" readonly>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">No. Rekam Medis</label>
                            <input type="text" class="form-control" id="no_rekam_medis_cari" name="no_rekam_medis"
                                readonly>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">NIK</label>
                            <input type="text" class="form-control" id="nik_pasien_cari" readonly>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="text" class="form-control" id="tgl_lahir_pasien_cari" readonly>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Jenis Kelamin</label>
                            <input type="text" class="form-control" id="jk_pasien_cari" readonly>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Alamat</label>
                            <input type="text" class="form-control" id="alamat_pasien_cari" readonly>
                        </div>
                    </div>
                    <div id="formFieldUgd" style="display:none;">
                        <div class="mb-3">
                            <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                            <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success" id="btnTambahPasienUgd"
                        style="display:none;">Tambah</button>
                </div>
            </form>
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
                        <textarea class="form-control" id="penanggung_jawab" name="penanggung_jawab" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" form="formPeriksa" class="btn btn-success">Simpan</button>
            </div>
        </div>
    </div>
</div>
<!-- Pastikan jQuery dimuat sebelum script custom
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" /> -->

<!-- Modal Riwayat Analisa (gaya detail pasien, read-only) -->
<div class="modal fade" id="modalRiwayatAnalisa" tabindex="-1" aria-labelledby="modalRiwayatAnalisaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
        <div class="modal-content" style="overflow-x: hidden;">
            <div class="modal-header d-flex justify-content-between">
                <h3 class="modal-title" id="modalRiwayatAnalisaLabel"><strong>Riwayat Analisa <span id="riwayat_nama_pasien_display"></span></strong></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3" style="max-height: 400px; overflow-y: auto;">
                <form id="formRiwayatAnalisa" autocomplete="off">
                    <div class="container-fluid">
                        <div id="riwayatAnalisaContent">
                            <div class="text-center text-muted">Memuat data analisa...</div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    $(function() {
        $(document).on('click', '.btn-hasil-periksa', function() {
            var pasienId = $(this).data('pasien-id');
            var $content = $('#hasilPeriksaContent');
            $content.html('<div class="text-center text-muted">Memuat data hasil periksa...</div>');
            if (!pasienId) {
                $content.html('<div class="text-danger">ID pasien tidak ditemukan.</div>');
                return;
            }
            $.get('/rawatinap/hasilperiksa/data/' + encodeURIComponent(pasienId), function(res) {
                if (res.success && res.data) {
                    var data = res.data;
                    var html = '';
                    data.forEach(function(item) {
                        html += '<div class="mb-3 border-bottom pb-2">';
                        html += '<div><strong>Tanggal:</strong> ' + item.tanggal + '</div>';
                        html += '<div><strong>Waktu:</strong> ' + item.waktu + '</div>';
                        html += '<div><strong>SOAP:</strong> ' + (item.soap || '-') + '</div>';
                        html += '<div><strong>Intruksi Tenaga Kerja:</strong> ' + (item.intruksi_tenagakerja || '-') + '</div>';
                        html += '<div><strong>Penanggung Jawab:</strong> ' + (item.penanggung_jawab || '-') + '</div>';
                        html += '</div>';
                    });
                    $content.html(html);
                } else {
                    $content.html('<div class="text-danger">Data hasil periksa tidak ditemukan.</div>');
                }
            }).fail(function() {
                $content.html('<div class="text-danger">Gagal mengambil data hasil periksa.</div>');
            });
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
</script>
@endsection
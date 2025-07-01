@extends('dashboardrawatinap')

@section('rawatinap')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>Data Pasien Unit Gawat Darurat</strong></h1>
    <div class="row">
        <div class="col-12 col-lg-12 col-xxl-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <form method="GET" action="#" class="d-flex flex-wrap align-items-center gap-2 m-0 p-0">
                        <div class="input-group" style="width: 250px;">
                            <input type="text" name="search" class="form-control" id="searchInput" placeholder="Pencarian..." aria-label="Search" autocomplete="off">
                        </div>
                    </form>
                    <button type="button" class="btn btn-success btn-lg" style="padding: 5px 10px; font-size: 0.9rem;" data-bs-toggle="modal"
                        data-bs-target="#modalTambahPasien">
                        <i class="fas fa-plus"></i> Tambah Pasien
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover my-0" id="pasienTable" style="font-size: 14px;">
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
                                <td class="nowrap">
                                    @php
                                    $date = \Carbon\Carbon::parse($pasien->tanggal_masuk);
                                    $days = [
                                    'Monday' => 'Senin',
                                    'Tuesday' => 'Selasa',
                                    'Wednesday' => 'Rabu',
                                    'Thursday' => 'Kamis',
                                    'Friday' => 'Jumat',
                                    'Saturday' => 'Sabtu',
                                    'Sunday' => 'Minggu',
                                    ];
                                    $dayName = $days[$date->format('l')] ?? $date->format('l');
                                    echo $dayName . ', ' . $date->format('d-m-Y');
                                    @endphp
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
                                    $status = $pasien->status ?: 'Perlu Analisa';
                                    @endphp
                                    @php
                                    $badgeClass = strtolower($status) === 'perlu analisa' ? 'bg-danger' : 'bg-warning';
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                </td>
                                <td class="nowrap">
                                    <button class="btn btn-primary btn-sm rounded" data-bs-toggle="modal"
                                        data-bs-target="#modalDetailPasien"
                                        data-no-rekam-medis="{{ $pasien->pasien ? $pasien->pasien->no_rekam_medis : '' }}">Selengkapnya</button>
                                    @if(strtolower($pasien->status) === 'ugd')
                                    <button class="btn btn-info btn-sm ms-1 rounded btn-hasil-analisa" data-bs-toggle="modal"
                                        data-bs-target="#modalHasilAnalisa" data-pasien="{{ json_encode($pasien) }}"
                                        data-pasien-id="{{ $pasien->pasien_id }}">Hasil Analisa</button>
                                    @else
                                    <button class="btn btn-warning btn-sm ms-1 rounded" data-bs-toggle="modal"
                                        data-bs-target="#modalAnalisa" data-pasien="{{ json_encode($pasien) }}"
                                        data-pasien-id="{{ $pasien->pasien_id }}">Analisa</button>
                                    @endif
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
</div>

<!-- Modal Tambah Pasien -->
<div class="modal fade" id="modalTambahPasien" tabindex="-1" aria-labelledby="modalTambahPasienLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formTambahPasienUgd" method="POST" action="{{ url('/rawatinap/ugd/store') }}">
                @csrf
                <input type="hidden" id="pasien_id_cari" name="pasien_id" required>
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h4 class="modal-title mb-0" id="modalTambahPasienLabel"><strong>Tambah Pasien UGD</strong></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 row align-items-end">
                        <div class="col-md-10">
                            <label for="nomor_kepesertaan_cari" class="form-label">Nomor Kepesertaan</label>
                            <input type="text" class="form-control" id="nomor_kepesertaan_cari"
                                name="nomor_kepesertaan_cari" required maxlength="16" pattern="\d{1,16}"
                                inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,16);">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-primary" id="btnCariPasien" style="width: 100px;">Cari</button>
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
                <div class="modal-footer d-flex justify-content-end">
                    <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button> -->
                    <button type="submit" class="btn btn-success mt-2" id="btnTambahPasienUgd"
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
                    <input type="hidden" id="ruangan" name="ruangan" value="">
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
                                document.getElementById('modalAnalisaStatusPsikologi').value = analisa.status_psikologi || '';
                                document.getElementById('modalAnalisaHambatanEdukasi').value = analisa.hambatan_edukasi || '';
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
    document.addEventListener('DOMContentLoaded', function() {
        var modalDetailPasien = document.getElementById('modalDetailPasien');
        if (modalDetailPasien) {
            modalDetailPasien.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var noRekamMedis = button.getAttribute('data-no-rekam-medis');

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

                if (!noRekamMedis) {
                    console.error('No Rekam Medis not found on button');
                    return;
                }

                fetch('/rawatinap/ugd/detail/' + encodeURIComponent(noRekamMedis))
                    .then(response => response.json())
                    .then(data => {
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
                    console.log('AJAX success response:', res);
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
                            '<button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalDetailPasien" data-pasien="' +
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
@endsection

<!-- Pastikan jQuery dimuat sebelum script custom -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
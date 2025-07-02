@extends('dashboardadmin')

@section('admin')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>Pasien Unit Gawat Darurat</strong></h1>
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
                    <table class="table table-hover my-0" id="pasienTable">
                        <thead>
                            <tr>
                                <th class="nowrap">No.</th>
                                <th class="nowrap">Hari/Tanggal Masuk</th>
                                <th class="nowrap">No. Rekam Medis</th>
                                <th class="nowrap">Nama Pasien</th>
                                <th class="nowrap">Umur</th>
                                <th class="nowrap">JamKes</th>
                                <th class="nowrap">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($pasiens_ugd) > 0)
                            @foreach ($pasiens_ugd as $index => $pasien)
                            <tr>
                                <td class="nowrap">{{ $index + 1 }}</td>
                                @php
                                \Carbon\Carbon::setLocale('id');
                                $tanggalMasuk = \Carbon\Carbon::parse($pasien->tanggal_masuk)->translatedFormat('l, d F Y');
                                @endphp
                                <td class="nowrap">{{ $tanggalMasuk }}</td>
                                <td class="nowrap">{{ $pasien->pasien ? $pasien->pasien->no_rekam_medis : '-' }}</td>
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
                                <td class="nowrap">{{ $pasien->pasien ? $pasien->pasien->jaminan_kesehatan : '-' }}</td>
                                <td class="nowrap">
                                    @php
                                    $status = $pasien->status ?: 'Perlu Analisa';
                                    $badgeClass = 'bg-secondary';
                                    if (strtolower($status) === 'ugd') {
                                    $badgeClass = 'bg-warning text-dark';
                                    } elseif (strtolower($status) === 'perlu analisa') {
                                    $badgeClass = 'bg-danger text-white';
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
                                <td colspan="7" class="text-center">Tidak ada data pasien unit gawat darurat</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
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
                                <input type="text" class="form-control form-control-sm" id="modalNoRekamMedis"
                                    readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalNikPasien" class="form-label">NIK</label>
                                <input type="text" class="form-control form-control-sm" id="modalNikPasien"
                                    readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalNamaPasien" class="form-label">Nama Pasien</label>
                                <input type="text" class="form-control form-control-sm" id="modalNamaPasien"
                                    readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="modalTempatLahir" class="form-label">Tempat Lahir</label>
                                <input type="text" class="form-control form-control-sm" id="modalTempatLahir"
                                    readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalTanggalLahir" class="form-label">Tanggal Lahir</label>
                                <input type="text" class="form-control form-control-sm" id="modalTanggalLahir"
                                    readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalJenisKelamin" class="form-label">Jenis Kelamin</label>
                                <input type="text" class="form-control form-control-sm" id="modalJenisKelamin"
                                    readonly>
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
                                <input type="text" class="form-control form-control-sm" id="modalPekerjaan"
                                    readonly>
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
                                <input type="text" class="form-control form-control-sm" id="modalKelurahan"
                                    readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="modalKecamatan" class="form-label">Kecamatan</label>
                                <input type="text" class="form-control form-control-sm" id="modalKecamatan"
                                    readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalKabupaten" class="form-label">Kabupaten</label>
                                <input type="text" class="form-control form-control-sm" id="modalKabupaten"
                                    readonly>
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
                            <input type="text" class="form-control form-control-sm" id="tekananDarah" name="tekanan_darah">
                            <div class="invalid-feedback">Tekanan Darah wajib diisi.</div>
                        </div>
                        <div class="col-6">
                            <label for="frekuensiNadi" class="form-label">Frekuensi Nadi (/menit)</label>
                            <input type="text" class="form-control form-control-sm" id="frekuensiNadi" name="frekuensi_nadi">
                            <div class="invalid-feedback">Frekuensi Nadi wajib diisi.</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="suhu" class="form-label">Suhu (°C)</label>
                            <input type="text" class="form-control form-control-sm" id="suhu" name="suhu">
                            <div class="invalid-feedback">Suhu wajib diisi.</div>
                        </div>
                        <div class="col-6">
                            <label for="frekuensiNafas" class="form-label">Frekuensi Nafas (/menit)</label>
                            <input type="text" class="form-control form-control-sm" id="frekuensiNafas"
                                name="frekuensi_nafas">
                            <div class="invalid-feedback">Frekuensi Nafas wajib diisi.</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="skorNyeri" class="form-label">Skor Nyeri</label>
                            <input type="text" class="form-control form-control-sm" id="skorNyeri"
                                name="skor_nyeri">
                            <div class="invalid-feedback">Skor Nyeri wajib diisi.</div>
                        </div>
                        <div class="col-6">
                            <label for="skorJatuh" class="form-label">Skor Jatuh</label>
                            <input type="text" class="form-control form-control-sm" id="skorJatuh"
                                name="skor_jatuh">
                            <div class="invalid-feedback">Skor Jatuh wajib diisi.</div>
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
                            <input type="text" class="form-control form-control-sm" id="beratBadan"
                                name="berat_badan">
                            <div class="invalid-feedback">Berat Badan wajib diisi.</div>
                        </div>
                        <div class="col-6">
                            <label for="tinggiBadan" class="form-label">Tinggi Badan</label>
                            <input type="text" class="form-control form-control-sm" id="tinggiBadan"
                                name="tinggi_badan">
                            <div class="invalid-feedback">Tinggi Badan wajib diisi.</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="lingkarKepala" class="form-label">Lingkar Kepala</label>
                            <input type="text" class="form-control form-control-sm" id="lingkarKepala"
                                name="lingkar_kepala">
                            <div class="invalid-feedback">Lingkar Kepala wajib diisi.</div>
                        </div>
                        <div class="col-6">
                            <label for="imt" class="form-label">IMT</label>
                            <input type="text" class="form-control form-control-sm" id="imt" name="imt">
                            <div class="invalid-feedback">IMT wajib diisi.</div>
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
                            <input type="text" class="form-control form-control-sm" id="alatBantu"
                                name="alat_bantu">
                            <div class="invalid-feedback">Alat Bantu wajib diisi.</div>
                        </div>
                        <div class="col-6">
                            <label for="prosthesa" class="form-label">Prosthesa</label>
                            <input type="text" class="form-control form-control-sm" id="prosthesa" name="prosthesa">
                            <div class="invalid-feedback">Prosthesa wajib diisi.</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="cacatTubuh" class="form-label">Cacat Tubuh</label>
                            <input type="text" class="form-control form-control-sm" id="cacatTubuh"
                                name="cacat_tubuh">
                            <div class="invalid-feedback">Cacat Tubuh wajib diisi.</div>
                        </div>
                        <div class="col-6">
                            <label for="adlMandiri" class="form-label">ADL Mandiri</label>
                            <input type="text" class="form-control form-control-sm" id="adlMandiri"
                                name="adl_mandiri">
                            <div class="invalid-feedback">ADL Mandiri wajib diisi.</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="riwayatJatuh" class="form-label">Riwayat Jatuh</label>
                            <input type="text" class="form-control form-control-sm" id="riwayatJatuh"
                                name="riwayat_jatuh">
                            <div class="invalid-feedback">Riwayat Jatuh wajib diisi.</div>
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
                            <div class="invalid-feedback">Status Psikologi wajib diisi.</div>
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
                            <div class="invalid-feedback">Hambatan Edukasi wajib diisi.</div>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="alergi" class="form-label">Alergi</label>
                            <textarea type="text" class="form-control form-control-sm" id="alergi"
                                name="alergi"></textarea>
                            <div class="invalid-feedback">Alergi wajib diisi.</div>
                        </div>
                        <div class="col-6">
                            <label for="catatan" class="form-label">Catatan</label>
                            <textarea type="text" class="form-control form-control-sm" id="catatan"
                                name="catatan"></textarea>
                            <div class="invalid-feedback">Catatan wajib diisi.</div>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="checkboxRawatInap"
                                    name="rawat_inap" value="1">
                                <label class="form-check-label" for="checkboxRawatInap">Rawat Inap</label>
                            </div>
                            <h5>Ruangan</h5>
                            <input type="text" class="form-control" id="selectPoli" name="ruangan"
                                placeholder="Masukkan Ruangan" disabled>
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
@endsection
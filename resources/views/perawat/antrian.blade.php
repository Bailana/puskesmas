@extends('dashboardperawat')

@section('perawat')
<!-- Include toastr CSS and JS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- CSS agar modal-footer fixed di kanan bawah modal analisa -->
<style>
    .modal-analisa-footer-fixed {
        position: absolute;
        right: 0;
        bottom: 0;
        width: 100%;
        background: #fff;
        /* border-top: 1px solid #dee2e6; */
        /* Hapus garis atas */
        z-index: 10;
        padding: 16px 24px 16px 24px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .modal-analisa-body-scroll {
        max-height: 400px;
        overflow-y: auto;
        padding-bottom: 40px !important;
        /* Kurangi ruang kosong bawah */
    }

    .modal-analisa-position-relative {
        position: relative;
    }
</style>

<div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>Antrian Pasien</strong></h1>
    <div class="row">
        <div class="col-12 col-lg-12 col-xxl-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex justify-content-between">
                    <!-- Input Pencarian -->
                    <div class="input-group" style="width: 250px;">
                        <input type="text" class="form-control" id="searchInput" placeholder="Pencarian..."
                            aria-label="Search">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover my-0" id="antrianTable" style="font-size: 14px;">
                        <thead>
                            <tr>
                                <th style="white-space: nowrap;">No.</th>
                                <th style="white-space: nowrap;">Nomor RM</th>
                                <th style="white-space: nowrap;">Nama Pasien</th>
                                <th style="white-space: nowrap;">Umur</th>
                                <th style="white-space: nowrap;">JamKes</th>
                                <th style="white-space: nowrap;">Status</th>
                                <th style="white-space: nowrap;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="antrianTbody">
                            @if(isset($antrians))
                            @forelse($antrians as $index => $antrian)
                            <tr>
                                <td style="white-space: nowrap;">{{ $antrians->firstItem() + $index }}.</td>
                                <td style="white-space: nowrap;">{{ $antrian->no_rekam_medis }}</td>
                                <td style="white-space: nowrap;">{{ $antrian->pasien ? $antrian->pasien->nama_pasien : 'Data Pasien Tidak Ditemukan' }}</td>
                                <td style="white-space: nowrap;">
                                    @if($antrian->pasien && $antrian->pasien->tanggal_lahir)
                                    {{ \Carbon\Carbon::parse($antrian->pasien->tanggal_lahir)->age }} tahun
                                    @else
                                    -
                                    @endif
                                </td>
                                <td style="white-space: nowrap;">{{ $antrian->pasien ? $antrian->pasien->jaminan_kesehatan : '-' }}</td>
                                <td style="white-space: nowrap;"><span class="badge bg-danger">{{ $antrian->status }}</span></td>
                                <td style="white-space: nowrap;">
                                    <button type="button" class="btn btn-primary btn-sm rounded btn-selengkapnya"
                                        data-bs-toggle="modal" data-bs-target="#modalPasienDetail"
                                        data-no-rekam-medis="{{ $antrian->no_rekam_medis }}"
                                        data-nik="{{ $antrian->pasien ? $antrian->pasien->nik : '' }}"
                                        data-nama="{{ $antrian->pasien ? $antrian->pasien->nama_pasien : '' }}"
                                        data-tempat-lahir="{{ $antrian->pasien ? $antrian->pasien->tempat_lahir : '' }}"
                                        data-tanggal-lahir="{{ $antrian->pasien ? $antrian->pasien->tanggal_lahir : '' }}"
                                        data-jenis-kelamin="{{ $antrian->pasien ? $antrian->pasien->jenis_kelamin : '' }}"
                                        data-golongan-darah="{{ $antrian->pasien ? $antrian->pasien->gol_darah : '' }}"
                                        data-agama="{{ $antrian->pasien ? $antrian->pasien->agama : '' }}"
                                        data-pekerjaan="{{ $antrian->pasien ? $antrian->pasien->pekerjaan : '' }}"
                                        data-status-pernikahan="{{ $antrian->pasien ? $antrian->pasien->status_pernikahan : '' }}"
                                        data-kepala-keluarga="{{ $antrian->pasien ? $antrian->pasien->kepala_keluarga : '' }}"
                                        data-no-hp="{{ $antrian->pasien ? $antrian->pasien->no_hp : '' }}"
                                        data-alamat="{{ $antrian->pasien ? $antrian->pasien->alamat_jalan : '' }}"
                                        data-rt="{{ $antrian->pasien ? $antrian->pasien->rt : '' }}"
                                        data-rw="{{ $antrian->pasien ? $antrian->pasien->rw : '' }}"
                                        data-kelurahan="{{ $antrian->pasien ? $antrian->pasien->kelurahan : '' }}"
                                        data-kecamatan="{{ $antrian->pasien ? $antrian->pasien->kecamatan : '' }}"
                                        data-kabupaten="{{ $antrian->pasien ? $antrian->pasien->kabupaten : '' }}"
                                        data-provinsi="{{ $antrian->pasien ? $antrian->pasien->provinsi : '' }}"
                                        data-jaminan="{{ $antrian->pasien ? $antrian->pasien->jaminan_kesehatan : '' }}"
                                        data-no-kepesertaan="{{ $antrian->pasien ? $antrian->pasien->nomor_kepesertaan : '' }}"
                                        @if(!$antrian->pasien) disabled @endif>
                                        Selengkapnya
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm rounded btn-analisa"
                                        data-rekam-medis="{{ $antrian->no_rekam_medis }}"
                                        data-pasien-id="{{ $antrian->pasien ? $antrian->pasien->id : '' }}"
                                        data-nama="{{ $antrian->pasien ? $antrian->pasien->nama_pasien : '' }}"
                                        data-tanggal-lahir="{{ $antrian->pasien ? $antrian->pasien->tanggal_lahir : '' }}"
                                        data-bs-toggle="modal" data-bs-target="#modalAnalisa"
                                        @if(!$antrian->pasien) disabled @endif>Analisa</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Antrian pasien tidak ditemukan</td>
                            </tr>
                            @endforelse
                            @endif
                            <!-- Data antrian akan diisi via JS -->
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 mb-2">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="small text-muted mb-2 text-start ps-3 pagination-info-text-antrian" style="max-width: 50%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            Showing {{ $antrians->firstItem() }} to {{ $antrians->lastItem() }} of {{ $antrians->total() }} result
                        </div>
                        <nav class="d-flex justify-content-center">
                            <ul class="pagination d-flex flex-row flex-wrap gap-2" style="list-style-type: none; padding-left: 0; margin-bottom: 0;">
                                {{-- Previous Page Link --}}
                                @if ($antrians->onFirstPage())
                                    <li class="page-item disabled" aria-disabled="true" aria-label="Previous">
                                        <span class="page-link" aria-hidden="true">&laquo;</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $antrians->previousPageUrl() }}" rel="prev" aria-label="Previous">&laquo;</a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @php
                                    $totalPages = $antrians->lastPage();
                                    $currentPage = $antrians->currentPage();
                                    $maxButtons = 3;
                                    if ($totalPages <= $maxButtons) {
                                        $start = 1;
                                        $end = $totalPages;
                                    } else {
                                        if ($currentPage == 1) {
                                            $start = 1;
                                            $end = 3;
                                        } elseif ($currentPage == $totalPages) {
                                            $start = $totalPages - 2;
                                            $end = $totalPages;
                                        } else {
                                            $start = $currentPage - 1;
                                            $end = $currentPage + 1;
                                        }
                                        if ($start < 1) $start = 1;
                                        if ($end > $totalPages) $end = $totalPages;
                                    }
                                @endphp
                                @for ($i = $start; $i <= $end; $i++)
                                    @if ($i == $currentPage)
                                        <li class="page-item active" aria-current="page"><span class="page-link">{{ $i }}</span></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="{{ $antrians->url($i) }}">{{ $i }}</a></li>
                                    @endif
                                @endfor

                                {{-- Next Page Link --}}
                                @if ($antrians->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $antrians->nextPageUrl() }}" rel="next" aria-label="Next">&raquo;</a>
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

<div class="modal fade" id="modalAnalisa" tabindex="-1" aria-labelledby="modalAnalisaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
        <div class="modal-content modal-analisa-position-relative" style="overflow-x: hidden;">
            <div class="modal-header d-flex justify-content-between">
                <h3 class="modal-title" id="modalAnalisaLabel">
                    <strong>Analisa <span class="nama-pasien" id="nama_pasien_display"></span></strong>
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-analisa-body-scroll" style="max-height: 400px; overflow-y: auto; padding: 16px 24px 80px 24px;">
                @csrf
                <form id="formAnalisa" method="POST" action="{{ route('perawat.hasilanalisa.store') }}">
                    @csrf
                    <input type="hidden" id="pasien_id" name="pasien_id">
                    <input type="hidden" id="penanggung_jawab" name="penanggung_jawab" value="{{ auth()->user()->id }}">

                    <!-- Tanda Vital -->
                    <div class="mb-3">
                        <h5 class="mb-3">Tanda Vital</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="tekananDarah" class="form-label">Tekanan Darah (mmHg)</label>
                                <input type="text" class="form-control form-control-sm" id="tekananDarah" name="tekanan_darah">
                            </div>
                            <div class="col-md-6">
                                <label for="frekuensiNadi" class="form-label">Frekuensi Nadi (/menit)</label>
                                <input type="text" class="form-control form-control-sm" id="frekuensiNadi" name="frekuensi_nadi">
                            </div>
                            <div class="col-md-6">
                                <label for="suhu" class="form-label">Suhu (°C)</label>
                                <input type="text" class="form-control form-control-sm" id="suhu" name="suhu">
                            </div>
                            <div class="col-md-6">
                                <label for="frekuensiNafas" class="form-label">Frekuensi Nafas (/menit)</label>
                                <input type="text" class="form-control form-control-sm" id="frekuensiNafas" name="frekuensi_nafas">
                            </div>
                            <div class="col-md-6">
                                <label for="skorNyeri" class="form-label">Skor Nyeri</label>
                                <input type="text" class="form-control form-control-sm" id="skorNyeri" name="skor_nyeri">
                            </div>
                            <div class="col-md-6">
                                <label for="skorJatuh" class="form-label">Skor Jatuh</label>
                                <input type="text" class="form-control form-control-sm" id="skorJatuh" name="skor_jatuh">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!-- Antropometri -->
                    <div class="mb-3">
                        <h5 class="mb-3">Antropometri</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="beratBadan" class="form-label">Berat Badan</label>
                                <input type="text" class="form-control form-control-sm" id="beratBadan" name="berat_badan">
                            </div>
                            <div class="col-md-6">
                                <label for="tinggiBadan" class="form-label">Tinggi Badan</label>
                                <input type="text" class="form-control form-control-sm" id="tinggiBadan" name="tinggi_badan">
                            </div>
                            <div class="col-md-6">
                                <label for="lingkarKepala" class="form-label">Lingkar Kepala</label>
                                <input type="text" class="form-control form-control-sm" id="lingkarKepala" name="lingkar_kepala">
                            </div>
                            <div class="col-md-6">
                                <label for="imt" class="form-label">IMT</label>
                                <input type="text" class="form-control form-control-sm" id="imt" name="imt">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!-- Fungsional -->
                    <div class="mb-3">
                        <h5 class="mb-3">Fungsional</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="alatBantu" class="form-label">Alat Bantu</label>
                                <input type="text" class="form-control form-control-sm" id="alatBantu" name="alat_bantu">
                            </div>
                            <div class="col-md-6">
                                <label for="prosthesa" class="form-label">Prosthesa</label>
                                <input type="text" class="form-control form-control-sm" id="prosthesa" name="prosthesa">
                            </div>
                            <div class="col-md-6">
                                <label for="cacatTubuh" class="form-label">Cacat Tubuh</label>
                                <input type="text" class="form-control form-control-sm" id="cacatTubuh" name="cacat_tubuh">
                            </div>
                            <div class="col-md-6">
                                <label for="adlMandiri" class="form-label">ADL Mandiri</label>
                                <input type="text" class="form-control form-control-sm" id="adlMandiri" name="adl_mandiri">
                            </div>
                            <div class="col-md-6">
                                <label for="riwayatJatuh" class="form-label">Riwayat Jatuh</label>
                                <input type="text" class="form-control form-control-sm" id="riwayatJatuh" name="riwayat_jatuh">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!-- Status Psikologi & Hambatan Edukasi -->
                    <div class="mb-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <h5>Status Psikologi</h5>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkboxDepresi" name="status_psikologi[]" value="Depresi">
                                    <label class="form-check-label" for="checkboxDepresi">Depresi</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkboxTakut" name="status_psikologi[]" value="Takut">
                                    <label class="form-check-label" for="checkboxTakut">Takut</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkboxAgresif" name="status_psikologi[]" value="Agresif">
                                    <label class="form-check-label" for="checkboxAgresif">Agresif</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkboxMelukaiDiri" name="status_psikologi[]" value="Melukai diri sendiri/Orang lain">
                                    <label class="form-check-label" for="checkboxMelukaiDiri">Melukai diri sendiri/Orang lain</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5>Hambatan Edukasi</h5>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkboxBahasa" name="hambatan_edukasi[]" value="Bahasa">
                                    <label class="form-check-label" for="checkboxBahasa">Bahasa</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkboxCacatFisik" name="hambatan_edukasi[]" value="Cacat Fisik">
                                    <label class="form-check-label" for="checkboxCacatFisik">Cacat Fisik</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkboxCacatKognitif" name="hambatan_edukasi[]" value="Cacat Kognitif">
                                    <label class="form-check-label" for="checkboxCacatKognitif">Cacat Kognitif</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!-- Alergi & Catatan -->
                    <div class="mb-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="alergi" class="form-label">Alergi</label>
                                <textarea class="form-control form-control-sm" id="alergi" name="alergi"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="catatan" class="form-label">Catatan</label>
                                <textarea class="form-control form-control-sm" id="catatan" name="catatan"></textarea>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!-- Poli Tujuan -->
                    <div class="mb-3">
                        <h5 class="mb-3">Pilih Poli Tujuan</h5>
                        <select class="form-select" id="selectPoli" name="poli_tujuan" required>
                            <option value="" disabled selected>Pilih Poli</option>
                            <option value="1">Poli Umum</option>
                            <option value="2">Poli Gigi</option>
                            <option value="3">Poli KIA</option>
                        </select>
                    </div>
                    <div class="modal-footer modal-analisa-footer-fixed">
                        <button type="submit" class="btn btn-success ms-2" id="btnSimpanAnalisa">Simpan</button>
                    </div>
                </form>
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
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var modalAnalisa = document.getElementById('modalAnalisa');
    modalAnalisa.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var pasienId = button.getAttribute('data-pasien-id');
        var namaPasien = button.getAttribute('data-nama');
        var tanggalLahir = button.getAttribute('data-tanggal-lahir');
        modalAnalisa.querySelector('#pasien_id').value = pasienId;
        modalAnalisa.querySelector('#nama_pasien_display').textContent = namaPasien;
    });

    // Modal Detail Pasien: isi data ke setiap field
    var modalPasienDetail = document.getElementById('modalPasienDetail');
    modalPasienDetail.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var namaPasien = button.getAttribute('data-nama') || '';
        if (!namaPasien) {
            // Jika data pasien kosong/null, tampilkan pesan di modal
            document.getElementById('modalNoRekamMedis').value = '';
            document.getElementById('modalNikPasien').value = '';
            document.getElementById('modalNamaPasien').value = 'Data Pasien Tidak Ditemukan';
            // Kosongkan field lain
            document.getElementById('modalTempatLahir').value = '';
            document.getElementById('modalTanggalLahir').value = '';
            document.getElementById('modalJenisKelamin').value = '';
            document.getElementById('modalGolonganDarah').value = '';
            document.getElementById('modalAgama').value = '';
            document.getElementById('modalPekerjaan').value = '';
            document.getElementById('modalStatusPernikahan').value = '';
            document.getElementById('modalKepalaKeluarga').value = '';
            document.getElementById('modalNoHp').value = '';
            document.getElementById('modalAlamat').value = '';
            document.getElementById('modalRt').value = '';
            document.getElementById('modalRw').value = '';
            document.getElementById('modalKelurahan').value = '';
            document.getElementById('modalKecamatan').value = '';
            document.getElementById('modalKabupaten').value = '';
            document.getElementById('modalProvinsi').value = '';
            document.getElementById('modalJaminan').value = '';
            document.getElementById('modalNoKepesertaan').value = '';
            return;
        }
        document.getElementById('modalNoRekamMedis').value = button.getAttribute('data-no-rekam-medis') || '';
        document.getElementById('modalNikPasien').value = button.getAttribute('data-nik') || '';
        document.getElementById('modalNamaPasien').value = namaPasien;
        document.getElementById('modalTempatLahir').value = button.getAttribute('data-tempat-lahir') || '';
        document.getElementById('modalTanggalLahir').value = button.getAttribute('data-tanggal-lahir') || '';
        document.getElementById('modalJenisKelamin').value = button.getAttribute('data-jenis-kelamin') || '';
        document.getElementById('modalGolonganDarah').value = button.getAttribute('data-golongan-darah') || '';
        document.getElementById('modalAgama').value = button.getAttribute('data-agama') || '';
        document.getElementById('modalPekerjaan').value = button.getAttribute('data-pekerjaan') || '';
        document.getElementById('modalStatusPernikahan').value = button.getAttribute('data-status-pernikahan') || '';
        document.getElementById('modalKepalaKeluarga').value = button.getAttribute('data-kepala-keluarga') || '';
        document.getElementById('modalNoHp').value = button.getAttribute('data-no-hp') || '';
        document.getElementById('modalAlamat').value = button.getAttribute('data-alamat') || '';
        document.getElementById('modalRt').value = button.getAttribute('data-rt') || '';
        document.getElementById('modalRw').value = button.getAttribute('data-rw') || '';
        document.getElementById('modalKelurahan').value = button.getAttribute('data-kelurahan') || '';
        document.getElementById('modalKecamatan').value = button.getAttribute('data-kecamatan') || '';
        document.getElementById('modalKabupaten').value = button.getAttribute('data-kabupaten') || '';
        document.getElementById('modalProvinsi').value = button.getAttribute('data-provinsi') || '';
        document.getElementById('modalJaminan').value = button.getAttribute('data-jaminan') || '';
        document.getElementById('modalNoKepesertaan').value = button.getAttribute('data-no-kepesertaan') || '';
    });

    // Tambahkan log untuk debug
    var form = document.getElementById('formAnalisa');
    if (!form) {
        console.error('formAnalisa tidak ditemukan!');
    }
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Submit formAnalisa dijalankan');
        var url = form.action;
        var formData = new FormData(form);
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errData => { throw errData; });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                toastr.success(data.message || 'Analisa berhasil disimpan!');
                var modal = bootstrap.Modal.getInstance(modalAnalisa);
                modal.hide();
                form.reset();
                location.reload();
            } else {
                toastr.error('Gagal menyimpan data analisa.');
            }
        })
        .catch(errorData => {
            if (errorData && errorData.errors) {
                Object.values(errorData.errors).forEach(function(msgArr) {
                    toastr.error(msgArr[0]);
                });
            } else {
                toastr.error('Terjadi kesalahan saat menyimpan data.');
            }
        });
    });

    // Debug tombol simpan
    var btnSimpan = document.getElementById('btnSimpanAnalisa');
    if (btnSimpan) {
        btnSimpan.addEventListener('click', function() {
            console.log('Tombol Simpan diklik');
        });
    }

    var btnTutup = document.getElementById('btnTutup');
    if (btnTutup) {
        btnTutup.addEventListener('click', function() {
            var modal = bootstrap.Modal.getInstance(modalAnalisa);
            modal.hide();
            form.reset();
        });
    }
    modalAnalisa.addEventListener('hidden.bs.modal', function() {
        form.reset();
    });
});
</script>
@endsection
@extends('dashboardDokter')

@section('dokter')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>Antrian Pasien</strong></h1>
    <div class="row">
        <div class="col-12 col-lg-12 col-xxl-12">
            <div class="card flex-fill">
                <div class="card-header d-flex justify-content-between">
                    <!-- Input Pencarian -->
                    <div class="input-group" style="width: 250px;">
                        <input type="text" class="form-control" id="searchInput" placeholder="Pencarian..."
                            aria-label="Search" autocomplete="off">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover my-0" id="antrianTable">
                        <thead>
                            <tr>
                                <th style="font-weight: 600; font-size: 0.875rem; white-space: nowrap;">No.</th>
                                <th class="col-nomor-rm" style="font-weight: 600; font-size: 0.875rem; white-space: nowrap;">Nomor RM</th>
                                <th class="col-nama-pasien" style="font-weight: 600; font-size: 0.875rem; white-space: nowrap;">Nama Pasien
                                </th>
                                <th style="font-weight: 600; font-size: 0.875rem; white-space: nowrap;">Umur</th>
                                <th class="col-jamkes" style="font-weight: 600; font-size: 0.875rem; white-space: nowrap;">JamKes</th>
                                <!-- Removed Poli Tujuan column as per user request -->
                                <!-- <th style="font-weight: 600; font-size: 0.875rem;">Tgl. Berobat</th> -->
                                <th style="font-weight: 600; font-size: 0.875rem; white-space: nowrap;">Status</th>
                                <th style="font-weight: 600; font-size: 0.875rem; white-space: nowrap;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 0.875rem;">
                            @if ($antrians->count() == 0)
                            <tr>
                                <td colspan="7" class="text-center">Belum ada antrian pasien</td>
                            </tr>
                            @else
                            @foreach ($antrians as $index => $antrian)
                            <tr>
                                <td style="white-space: nowrap;">{{ $antrians->firstItem() + $index }}</td>
                                <td style="white-space: nowrap;">{{ $antrian->no_rekam_medis }}</td>
                                <td style="white-space: nowrap;">{{ $antrian->pasien->nama_pasien }}</td>
                                <td style="white-space: nowrap;">
                                    {{ \Carbon\Carbon::parse($antrian->pasien->tanggal_lahir)->age }} Tahun
                                </td>
                                <td style="white-space: nowrap;">{{ $antrian->pasien->jaminan_kesehatan }}</td>
                                <!-- <td style="white-space: nowrap;">{{ \Carbon\Carbon::parse($antrian->tanggal_berobat)->format('d-m-Y') }}</td> -->
                                <td style="white-space: nowrap;"><span
                                        class="badge bg-warning">{{ $antrian->status }}</span></td>
                                <td style="white-space: nowrap;">
                                    <button type="button" class="btn btn-success btn-sm rounded" data-bs-toggle="modal"
                                        data-bs-target="#modalAnalisa">Hasil Analisa</button>
                                    <button type="button" class="btn btn-primary btn-sm rounded btnPeriksa"
                                        data-bs-toggle="modal" data-bs-target="#modalPeriksaPasien"
                                        data-pasien-id="{{ $antrian->pasien->id }}">Periksa</button>
                                    <button type="button" class="btn btn-danger btn-sm rounded btnRiwayat"
                                        data-no-rekam-medis="{{ $antrian->pasien->no_rekam_medis }}">Riwayat
                                        Berobat</button>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 mb-2">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="small text-muted mb-2 text-start ps-3 pagination-info-text" style="max-width: 50%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            Showing {{ $antrians->firstItem() }} to {{ $antrians->lastItem() }} of
                            {{ $antrians->total() }} results
                        </div>
                        <nav class="d-flex justify-content-center">
                            <ul class="pagination d-flex flex-row flex-wrap gap-2"
                                style="list-style-type: none; padding-left: 0; margin-bottom: 0;">
                                {{-- Previous Page Link --}}
                                @if ($antrians->onFirstPage())
                                <li class="page-item disabled" aria-disabled="true" aria-label="Previous">
                                    <span class="page-link" aria-hidden="true">&laquo;</span>
                                </li>
                                @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $antrians->previousPageUrl() }}" rel="prev"
                                        aria-label="Previous">&laquo;</a>
                                </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @php
                                $totalPages = $antrians->lastPage();
                                $currentPage = $antrians->currentPage();
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
                                    <li class="page-item"><a class="page-link" href="{{ $antrians->url($page) }}">{{ $page }}</a></li>
                                    @endif
                                    @endfor

                                    {{-- Next Page Link --}}
                                    @if ($antrians->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $antrians->nextPageUrl() }}" rel="next"
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

    <!-- Modal Hasil Analisa -->
    <div class="modal fade" id="modalAnalisa" tabindex="-1" aria-labelledby="modalAnalisaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
            <div class="modal-content" style="overflow-x: hidden;">
                <div class="modal-header d-flex justify-content-between">
                    <h3 class="modal-title" id="modalAnalisaLabel"><strong>Hasil Analisa Pasien</strong></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 400px; overflow-y: auto; padding: 10px;">
                    <form id="formAnalisa">
                        <!-- Tanda Vital -->
                        <div class="row mb-3">
                            <h5><strong>Tanda Vital</strong></h5>
                            <div class="col-6">
                                <label for="tekananDarah" class="form-label">Tekanan Darah (mmHg)</label>
                                <input type="text" class="form-control form-control-sm" id="tekananDarah" disabled>
                            </div>
                            <div class="col-6">
                                <label for="frekuensiNadi" class="form-label">Frekuensi Nadi (/menit)</label>
                                <input type="text" class="form-control form-control-sm" id="frekuensiNadi" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="suhu" class="form-label">Suhu (°C)</label>
                                <input type="text" class="form-control form-control-sm" id="suhu" disabled>
                            </div>
                            <div class="col-6">
                                <label for="frekuensiNafas" class="form-label">Frekuensi Nafas (/menit)</label>
                                <input type="text" class="form-control form-control-sm" id="frekuensiNafas" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="skorNyeri" class="form-label">Skor Nyeri</label>
                                <input type="text" class="form-control form-control-sm" id="skorNyeri" disabled>
                            </div>
                            <div class="col-6">
                                <label for="skorJatuh" class="form-label">Skor Jatuh</label>
                                <input type="text" class="form-control form-control-sm" id="skorJatuh" disabled>
                            </div>
                        </div>
                        <hr>
                        <!-- Antropometri -->
                        <div class="row mb-3">
                            <h5><strong>Antropometri</strong></h5>
                            <div class="col-6">
                                <label for="beratBadan" class="form-label">Berat Badan</label>
                                <input type="text" class="form-control form-control-sm" id="beratBadan" disabled>
                            </div>
                            <div class="col-6">
                                <label for="tinggiBadan" class="form-label">Tinggi Badan</label>
                                <input type="text" class="form-control form-control-sm" id="tinggiBadan" disabled>
                            </div>
                        </div>
                        <hr>
                        <!-- Fungsional -->
                        <div class="row mb-3">
                            <h5><strong>Fungsional</strong></h5>
                            <div class="col-6">
                                <label for="alatBantu" class="form-label">Alat Bantu</label>
                                <input type="text" class="form-control form-control-sm" id="alatBantu" disabled>
                            </div>
                            <div class="col-6">
                                <label for="prosthesa" class="form-label">Prosthesa</label>
                                <input type="text" class="form-control form-control-sm" id="prosthesa" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="cacatTubuh" class="form-label">Cacat Tubuh</label>
                                <input type="text" class="form-control form-control-sm" id="cacatTubuh" disabled>
                            </div>
                            <div class="col-6">
                                <label for="adlMandiri" class="form-label">ADL Mandiri</label>
                                <input type="text" class="form-control form-control-sm" id="adlMandiri" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="riwayatJatuh" class="form-label">Riwayat Jatuh</label>
                                <input type="text" class="form-control form-control-sm" id="riwayatJatuh" disabled>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-6">
                                <h5>Status Psikologi</h5>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkboxDepresi" disabled>
                                    <label class="form-check-label" for="checkboxDepresi">Depresi</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkboxTakut" disabled>
                                    <label class="form-check-label" for="checkboxTakut">Takut</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkboxAgresif" disabled>
                                    <label class="form-check-label" for="checkboxAgresif">Agresif</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkboxMelukaiDiri" disabled>
                                    <label class="form-check-label" for="checkboxMelukaiDiri">Melukai diri sendiri/Orang
                                        lain</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <h5>Hambatan Edukasi</h5>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkboxBahasa" disabled>
                                    <label class="form-check-label" for="checkboxBahasa">Bahasa</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkboxCacatFisik" disabled>
                                    <label class="form-check-label" for="checkboxCacatFisik">Cacat Fisik</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkboxCacatKognitif" disabled>
                                    <label class="form-check-label" for="checkboxCacatKognitif">Cacat Kognitif</label>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="alergi" class="form-label">Alergi</label>
                                <textarea type="text" class="form-control form-control-sm" id="alergi"
                                    disabled></textarea>
                            </div>
                            <div class="col-6">
                                <label for="catatan" class="form-label">Catatan</label>
                                <textarea type="text" class="form-control form-control-sm" id="catatan"
                                    disabled></textarea>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="penanggungJawab" class="form-label">Penanggung Jawab</label>
                                <input type="text" class="form-control form-control-sm" id="penanggungJawab"
                                    name="penanggungJawab" disabled>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Periksa Pasien -->
    <div class="modal fade" id="modalPeriksaPasien" tabindex="-1" aria-labelledby="modalPeriksaPasienLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
            <div class="modal-content" style="overflow-x: hidden;">
                <div class="modal-header d-flex justify-content-between">
                    <h3 class="modal-title" id="modalPeriksaPasienLabel"><strong>Periksa Pasien</strong></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 400px; overflow-y: auto; padding: 10px;">
                    <form id="formPeriksaPasien">
                        <!-- Anamnesis -->
                        <div class="mb-3">
                            <label for="anamnesis" class="form-label">Anamnesis</label>
                            <textarea class="form-control" id="anamnesis" rows="3" required></textarea>
                        </div>

                        <!-- Pemeriksaan Fisik -->
                        <div class="mb-3">
                            <label for="pemeriksaanFisik" class="form-label">Pemeriksaan Fisik</label>
                            <textarea class="form-control" id="pemeriksaanFisik" rows="3" required></textarea>
                        </div>

                        <!-- Rencana dan Terapi -->
                        <div class="mb-3">
                            <label for="rencanaTerapi" class="form-label">Rencana dan Terapi</label>
                            <textarea class="form-control" id="rencanaTerapi" rows="3" required></textarea>
                        </div>

                        <!-- Diagnosis -->
                        <div class="mb-3">
                            <label for="diagnosis" class="form-label">Diagnosis</label>
                            <textarea class="form-control" id="diagnosis" rows="3" required></textarea>
                        </div>

                        <!-- Edukasi -->
                        <div class="mb-3">
                            <label for="edukasi" class="form-label">Edukasi</label>
                            <textarea class="form-control" id="edukasi" rows="3" required></textarea>
                        </div>

                        <!-- Code ICD -->
                        <div class="mb-3">
                            <label for="kodeICD" class="form-label">Kode ICD</label>
                            <input type="text" class="form-control form-control-sm" id="kodeICD" required>
                        </div>

                        <!-- Kesan Status Gizi -->
                        <div class="mb-3">
                            <label for="kesanStatusGizi" class="form-label">Kesan Status Gizi</label>
                            <select class="form-control form-control-sm" id="kesanStatusGizi" required>
                                <option value="" disabled selected>Pilih Status Gizi</option>
                                <option value="Gizi Kurang/Buruk">Gizi Kurang/Buruk</option>
                                <option value="Gizi Cukup">Gizi Cukup</option>
                                <option value="Gizi Lebih">Gizi Lebih</option>
                            </select>
                        </div>
                        <div id="resepObatContainer" class="d-flex flex-column mb-2 gap-2">
                            <div class="resep-obat-item d-flex gap-2">
                                <div class="flex-grow-1 d-flex flex-column equal-width">
                                    <label for="resepObat" class="form-label text-start">Resep Obat</label>
                                    <select class="form-select" name="resep_obat[]" required>
                                        <option value="" disabled selected>Pilih Obat</option>
                                        @foreach ($obats as $obat)
                                        <option value="{{ $obat->id }}">{{ $obat->nama_obat }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex-grow-1 d-flex flex-column input-container equal-width">
                                    <label for="bentukObat" class="form-label">Bentuk Obat</label>
                                    <input type="text" class="form-control" name="bentuk_obat[]" value="" required readonly>
                                </div>
                                <div class="flex-grow-1 d-flex flex-column input-container equal-width">
                                    <label for="jumlahObat" class="form-label">Jumlah Obat <small class="text-muted" style="font-weight: normal;" name="stok_obat_display">Stok: -</small></label>
                                    <input type="number" class="form-control" name="jumlah_obat[]" min="1" value="" required>
                                </div>
                                <div class="btn-remove-container d-flex align-items-end" style="margin-bottom: 2px;">
                                    <button type="button" class="btn btn-danger btn-sm btnRemoveResep" title="Hapus Resep Obat" style="height: 28px; width: 28px; padding: 0; font-size: 1.2rem; line-height: 1;">&times;</button>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="catatanObat" class="form-label">Catatan Obat</label>
                            <textarea class="form-control" id="catatanObat" rows="3"></textarea>
                        </div>
                        <button type="button" class="btn btn-primary d-flex align-items-center gap-2 mt-2" id="btnTambahResep" title="Tambah Resep Obat" style="padding: 0 8px; min-width: auto; height: 28px; font-size: 0.875rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                            </svg>
                            Tambah Resep Obat Baru
                        </button>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-end mt-3">
                    <button type="button" class="btn btn-success ms-2" id="btnSimpanPeriksa">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<style>
    .error-message {
        color: red;
        font-size: 0.875em;
        margin-top: 0.25rem;
        min-height: 1.2em;
        position: relative;
        bottom: auto;
        left: auto;
        display: block;
    }

    .input-error {
        border: 1px solid red !important;
        box-shadow: 0 0 5px red !important;
    }

    .input-container {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        /* min-height: 5.5rem; */
        /* enough to hold input and error message */
    }

    .resep-obat-item.d-flex.align-items-start.gap-2 {
        align-items: stretch;
    }

    .resep-obat-item.d-flex.align-items-start.gap-2>div {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
    }

    .resep-obat-item.d-flex.align-items-start.gap-2>div>.error-message {
        margin-top: 0.25rem;
    }

    .resep-obat-item.d-flex.align-items-start.gap-2>div.d-flex.align-items-center {
        align-items: flex-end;
        transition: margin-top 0.3s ease;
    }

    .resep-obat-item.d-flex.align-items-start.gap-2>div.d-flex.align-items-center.shift-down {
        margin-top: 1.5rem;
        /* Adjust to match error message height */
    }

    .resep-obat-item.d-flex.align-items-start.gap-2>button.btnRemoveResep {
        margin-top: 0;
        /* align-self: center; */
        /* display: flex;
        justify-content: center;
        height: auto; */
    }

    .resep-obat-item.d-flex.align-items-start.gap-2 {
        align-items: flex-start;
        gap: 2px;
    }

    .input-container {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        min-height: 2.0rem;
        /* enough to hold input and error message */
        flex-grow: 1;
    }

    .btn-remove-container {
        display: flex;
        align-items: end !important;
        margin-bottom: 0 !important;
        height: 100%;
    }

    .resep-obat-item { position: relative; }
.hasil-cari-obat {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 2050; /* lebih tinggi dari modal bootstrap */
    max-height: 220px;
    overflow-y: auto;
    border: 1px solid #ddd;
    background: #fff;
}
.item-obat.active, .item-obat:hover {
    background: #f0f0f0;
}
.resep-obat-item {
    align-items: flex-start;
}
.resep-obat-item .equal-width {
    min-width: 0;
    flex: 1 1 0;
}
.resep-obat-item .form-select,
.resep-obat-item .form-control {
    width: 100% !important;
    min-width: 0;
    box-sizing: border-box;
}
.resep-obat-item .input-container {
    min-width: 0;
}
.resep-obat-item .is-invalid,
.resep-obat-item .form-control.is-invalid,
.resep-obat-item .form-select.is-invalid {
    width: 100% !important;
    min-width: 0;
    box-sizing: border-box;
}
</style>
@section('scripts')
<!-- Select2 CSS & JS CDN -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const antrianTableBody = document.querySelector('#antrianTable tbody');

        function renderTableRows(antrians) {
            antrianTableBody.innerHTML = '';
            if (antrians.data.length === 0) {
                antrianTableBody.innerHTML =
                    '<tr><td colspan="7" class="text-center">Data antrian tidak ditemukan</td></tr>';
                return;
            }

            function calculateAge(birthDateString) {
                if (!birthDateString) return 'Tanggal tidak tersedia';
                const birthDate = new Date(birthDateString);
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const m = today.getMonth() - birthDate.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                return age + ' Tahun';
            }
            antrians.data.forEach((antrian, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td style="white-space: nowrap;">${antrians.from + index}</td>
                    <td style="white-space: nowrap;">${antrian.no_rekam_medis}</td>
                    <td style="white-space: nowrap;">${antrian.pasien.nama_pasien}</td>
                    <td style="white-space: nowrap;">${calculateAge(antrian.pasien.tanggal_lahir)}</td>
                    <td style="white-space: nowrap;">${antrian.pasien.jaminan_kesehatan}</td>
                    <td style="white-space: nowrap;"><span class="badge bg-warning">${antrian.status}</span></td>
                    <td style="white-space: nowrap;">
                        <button type="button" class="btn btn-success btn-sm rounded" data-bs-toggle="modal"
                            data-bs-target="#modalAnalisa">Hasil Analisa</button>
                        <button type="button" class="btn btn-primary btn-sm rounded btnPeriksa"
                            data-bs-toggle="modal" data-bs-target="#modalPeriksaPasien"
                            data-pasien-id="${antrian.pasien.id}">Periksa</button>
                        <button type="button" class="btn btn-danger btn-sm rounded btnRiwayat"
                            data-no-rekam-medis="${antrian.pasien.no_rekam_medis}">Riwayat
                            Berobat</button>
                    </td>
                `;
                antrianTableBody.appendChild(row);
            });
        }

        let debounceTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => {
                const query = searchInput.value.trim();
                fetch(`{{ route('dokter.antrian') }}?search=${encodeURIComponent(query)}`, {
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

        // Existing scripts below...

        // Handle Riwayat Berobat button click
        document.querySelectorAll('.btnRiwayat').forEach(button => {
            button.addEventListener('click', function() {
                const noRekamMedis = this.getAttribute('data-no-rekam-medis');
                const modal = new bootstrap.Modal(document.getElementById(
                    'modalRiwayatBerobat'));
                const riwayatList = document.getElementById('riwayatList');
                const hasilPeriksaDetail = document.getElementById('hasilPeriksaDetail');

                // Clear previous content
                riwayatList.innerHTML = '';
                hasilPeriksaDetail.style.display = 'none';

                // Fetch riwayat berobat dates
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
                                const dateStr = dateObj.toLocaleDateString(
                                    'id-ID',
                                    options);
                                const div = document.createElement('div');
                                div.classList.add('d-flex',
                                    'justify-content-between',
                                    'align-items-center');
                                if (index < dates.length - 1) {
                                    div.style.borderBottom =
                                        '1px solid #dee2e6';
                                    div.style.paddingBottom = '0.5rem';
                                    div.style.marginBottom = '0.5rem';
                                }
                                div.innerHTML = `
                                    <span>${dateStr}</span>
                                    <button class="btn btn-primary btn-sm btnLihat" data-tanggal="${tanggal}" data-no-rekam-medis="${noRekamMedis}">Lihat</button>
                                `;
                                riwayatList.appendChild(div);
                            });

                            // Add event listeners to Lihat buttons
                            riwayatList.querySelectorAll('.btnLihat').forEach(
                                btnLihat => {
                                    btnLihat.addEventListener('click', function() {
                                        const tanggal = this.getAttribute(
                                            'data-tanggal');
                                        const noRekamMedis = this
                                            .getAttribute(
                                                'data-no-rekam-medis');

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
                                                riwayatList.style
                                                    .display =
                                                    'none';
                                                const dateObj =
                                                    new Date(
                                                        data
                                                        .tanggal_periksa
                                                    );
                                                const options = {
                                                    weekday: 'long',
                                                    year: 'numeric',
                                                    month: '2-digit',
                                                    day: '2-digit'
                                                };
                                                document.getElementById(
                                                        'detailTanggal')
                                                    .textContent =
                                                    dateObj
                                                    .toLocaleDateString(
                                                        'id-ID', options
                                                    );
                                                document.getElementById(
                                                        'detailAnamnesis'
                                                    ).textContent =
                                                    data.anamnesis ||
                                                    '-';
                                                document.getElementById(
                                                        'detailPemeriksaanFisik'
                                                    ).textContent =
                                                    data
                                                    .pemeriksaan_fisik ||
                                                    '-';
                                                document.getElementById(
                                                        'detailRencanaTerapi'
                                                    ).textContent =
                                                    data
                                                    .rencana_dan_terapi ||
                                                    '-';
                                                document.getElementById(
                                                        'detailDiagnosis'
                                                    ).textContent =
                                                    data.diagnosis ||
                                                    '-';
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

        // Handle Periksa Pasien modal form submission
        const btnSimpanPeriksa = document.getElementById('btnSimpanPeriksa');
        const modalPeriksaPasien = new bootstrap.Modal(document.getElementById('modalPeriksaPasien'));

        function clearValidationErrors(form) {
            const errorMessages = form.querySelectorAll('.error-message');
            errorMessages.forEach(msg => msg.remove());
            // Remove error highlight class from inputs
            const errorInputs = form.querySelectorAll('.input-error');
            errorInputs.forEach(input => input.classList.remove('input-error'));

            // Remove margin-bottom and shift-down class from all remove button containers
            const removeBtnContainers = form.querySelectorAll('.btnRemoveResep');
            removeBtnContainers.forEach(btn => {
                const container = btn.parentElement;
                if (container) {
                    container.classList.remove('shift-down');
                    container.style.marginBottom = '';
                }
            });
        }

        function showValidationError(inputElement, message) {
            // Remove existing error message if any
            const existingError = inputElement.parentNode.querySelector('.error-message');
            if (existingError) {
                existingError.remove();
            }
            // Add error highlight class to input
            inputElement.classList.add('input-error');
            const error = document.createElement('div');
            error.className = 'error-message';
            error.textContent = message;
            inputElement.parentNode.appendChild(error);

            // If input is jumlah_obat[] or resep_obat[], add shift-down class and margin-bottom to remove button container
            if (inputElement.name === 'jumlah_obat[]' || inputElement.name === 'resep_obat[]') {
                const resepItem = inputElement.closest('.resep-obat-item');
                if (resepItem) {
                    const removeBtnContainer = resepItem.querySelector('.btnRemoveResep').parentElement;
                    if (removeBtnContainer) {
                        removeBtnContainer.classList.add('shift-down');
                        removeBtnContainer.style.marginBottom = '25px';
                    }
                }
            }
        }

        if (btnSimpanPeriksa) {
            btnSimpanPeriksa.addEventListener('click', function() {
                const formPeriksaPasien = document.getElementById('formPeriksaPasien');
                clearValidationErrors(formPeriksaPasien);

                let isValid = true;
                const requiredFields = formPeriksaPasien.querySelectorAll('[required]');

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        showValidationError(field, 'Field ini wajib diisi.');
                        isValid = false;
                    }
                });

                if (!isValid) {
                    return; // Prevent submission if validation fails
                }

                const formData = {
                    pasien_id: window.selectedPasienId || null,
                    tanggal_periksa: new Date().toISOString().split('T')[0],
                    anamnesis: document.getElementById('anamnesis').value,
                    pemeriksaan_fisik: document.getElementById('pemeriksaanFisik').value,
                    rencana_dan_terapi: document.getElementById('rencanaTerapi').value,
                    diagnosis: document.getElementById('diagnosis').value,
                    edukasi: document.getElementById('edukasi').value,
                    kode_icd: document.getElementById('kodeICD').value,
                    kesan_status_gizi: document.getElementById('kesanStatusGizi').value,
                    obats: Array.from(document.querySelectorAll('.resep-obat-item')).map(item => ({
                        id: item.querySelector('select').value,
                        jumlah: item.querySelector('input[name="jumlah_obat[]"]').value,
                        bentuk: item.querySelector('input[name="bentuk_obat[]"]').value,
                        catatan_obat: document.getElementById('catatanObat').value.trim(),
                    })),
                };

                fetch("{{ route('dokter.hasilperiksa.store') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(formData),
                    })
                    .then(async response => {
                        if (!response.ok) {
                            const errorData = await response.json();
                            let errorMessage =
                                'Terjadi kesalahan saat menyimpan data hasil periksa.';
                            if (errorData.errors) {
                                errorMessage = Object.values(errorData.errors).flat().join(
                                    '\n');
                            } else if (errorData.message) {
                                errorMessage = errorData.message;
                            }
                            throw new Error(errorMessage);
                        }
                        return response.json();
                    })
                    .then(data => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Diagnosis pasien berhasil disimpan',
                        }).then(() => {
                            location.reload();
                        });
                        modalPeriksaPasien.hide();
                        formPeriksaPasien.reset();
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: error.message,
                        });
                    });
            });
        }

        document.querySelectorAll('.btnPeriksa').forEach(button => {
            button.addEventListener('click', function() {
                window.selectedPasienId = this.getAttribute('data-pasien-id');
            });
        });

        // Reset form and clear validation errors when modal close button is clicked
        const modalPeriksaPasienElement = document.getElementById('modalPeriksaPasien');
        const formPeriksaPasien = document.getElementById('formPeriksaPasien');
        if (modalPeriksaPasienElement && formPeriksaPasien) {
            modalPeriksaPasienElement.querySelectorAll('button.btn-close').forEach(button => {
                button.addEventListener('click', () => {
                    formPeriksaPasien.reset();
                    clearValidationErrors(formPeriksaPasien);
                    // Remove dynamically added resep obat items, keep only the first one
                    const resepObatContainer = document.getElementById('resepObatContainer');
                    if (resepObatContainer) {
                        const items = resepObatContainer.querySelectorAll('.resep-obat-item');
                        items.forEach((item, index) => {
                            if (index > 0) {
                                item.remove();
                            } else {
                                // Reset the first item fields
                                const select = item.querySelector('select');
                                const input = item.querySelector('input[type="number"]');
                                if (select) select.selectedIndex = 0;
                                if (input) input.value = '';
                            }
                        });
                    }
                });
            });
        }

        // Dynamic add/remove resep obat fields
        document.addEventListener('DOMContentLoaded', function() {
            const resepObatContainer = document.getElementById('resepObatContainer');
            const btnTambahResep = document.getElementById('btnTambahResep');

            btnTambahResep.addEventListener('click', function() {
                const firstItem = resepObatContainer.querySelector('.resep-obat-item');
                if (!firstItem) return;

                const newItem = firstItem.cloneNode(true);
                // Clear values in cloned fields
                const select = newItem.querySelector('select');
                const input = newItem.querySelector('input[type="number"]');
                if (select) {
                    select.selectedIndex = 0;
                }
                if (input) {
                    input.value = 1;
                }
                resepObatContainer.appendChild(newItem);
            });

            // Use event delegation for remove buttons
            resepObatContainer.addEventListener('click', function(event) {
                if (event.target.classList.contains('btnRemoveResep')) {
                    if (resepObatContainer.children.length > 1) {
                        event.target.closest('.resep-obat-item').remove();
                    }
                }
            });

            // Update bentuk obat otomatis
            const obatsData = @json($obats);
            function updateBentukObat(selectElement) {
                const selectedObatId = selectElement.value;
                const bentukInput = selectElement.closest('.resep-obat-item').querySelector('input[name="bentuk_obat[]"]');
                const jumlahInput = selectElement.closest('.resep-obat-item').querySelector('input[name="jumlah_obat[]"]');
                const stokDisplay = selectElement.closest('.resep-obat-item').querySelector('small[name="stok_obat_display"]');
                if (!selectedObatId) {
                    bentukInput.value = '';
                    if (jumlahInput) {
                        jumlahInput.removeAttribute('max');
                        jumlahInput.value = '';
                    }
                    if (stokDisplay) {
                        stokDisplay.textContent = 'Stok: -';
                    }
                    return;
                }
                const obat = obatsData.find(o => o.id == selectedObatId);
                if (obat) {
                    bentukInput.value = obat.bentuk_obat || '';
                    if (jumlahInput) {
                        jumlahInput.setAttribute('max', obat.stok);
                        if (parseInt(jumlahInput.value) > obat.stok) {
                            jumlahInput.value = obat.stok;
                        }
                    }
                    if (stokDisplay) {
                        stokDisplay.textContent = 'Stok: ' + (obat.stok ?? '-');
                    }
                } else {
                    bentukInput.value = '';
                    if (jumlahInput) {
                        jumlahInput.removeAttribute('max');
                        jumlahInput.value = '';
                    }
                    if (stokDisplay) {
                        stokDisplay.textContent = 'Stok: -';
                    }
                }
            }
            resepObatContainer.addEventListener('change', function(event) {
                if (event.target.matches('select[name="resep_obat[]"]')) {
                    updateBentukObat(event.target);
                }
            });
            // Update bentuk obat untuk existing items
            resepObatContainer.querySelectorAll('select[name="resep_obat[]"]').forEach(select => {
                updateBentukObat(select);
            });
            // Update bentuk obat untuk resep baru
            btnTambahResep.addEventListener('click', function() {
                setTimeout(() => {
                    const newSelect = resepObatContainer.querySelector('.resep-obat-item:last-child select[name="resep_obat[]"]');
                    if (newSelect) {
                        updateBentukObat(newSelect);
                    }
                }, 0);
            });
        });

        // Convert PHP $obats to JS object
        const obatsData = @json($obats);

        const resepObatContainer = document.getElementById('resepObatContainer');

        function updateBentukObat(selectElement) {
            const selectedObatId = selectElement.value;
            const bentukInput = selectElement.closest('.resep-obat-item').querySelector('input[name="bentuk_obat[]"]');
            const jumlahInput = selectElement.closest('.resep-obat-item').querySelector('input[name="jumlah_obat[]"]');
            const stokDisplay = selectElement.closest('.resep-obat-item').querySelector('small[name="stok_obat_display"]');
            if (!selectedObatId) {
                bentukInput.value = '';
                if (jumlahInput) {
                    jumlahInput.removeAttribute('max');
                    jumlahInput.value = '';
                }
                if (stokDisplay) {
                    stokDisplay.textContent = 'Stok: -';
                }
                return;
            }
            const obat = obatsData.find(o => o.id === parseInt(selectedObatId));
            if (obat) {
                bentukInput.value = obat.bentuk_obat || '';
                if (jumlahInput) {
                    jumlahInput.setAttribute('max', obat.stok);
                    if (parseInt(jumlahInput.value) > obat.stok) {
                        jumlahInput.value = obat.stok;
                    }
                    // Add event listener to clamp input value to max
                    jumlahInput.addEventListener('input', function() {
                        const max = parseInt(this.getAttribute('max'));
                        if (this.value !== '' && parseInt(this.value) > max) {
                            this.value = max;
                        } else if (this.value !== '' && parseInt(this.value) < 1) {
                            this.value = 1;
                        }
                    });
                }
                if (stokDisplay) {
                    stokDisplay.textContent = 'Stok: ' + (obat.stok ?? '-');
                }
            } else {
                bentukInput.value = '';
                if (jumlahInput) {
                    jumlahInput.removeAttribute('max');
                    jumlahInput.value = '';
                }
                if (stokDisplay) {
                    stokDisplay.textContent = 'Stok: -';
                }
            }
        }

        // Event delegation for obat select change
        resepObatContainer.addEventListener('change', function(event) {
            if (event.target.matches('select[name="resep_obat[]"]')) {
                updateBentukObat(event.target);
            }
        });

        // Update bentuk obat for existing items on page load
        resepObatContainer.querySelectorAll('select[name="resep_obat[]"]').forEach(select => {
            updateBentukObat(select);
        });

        // Also update bentuk obat when new resep obat item is added
        const btnTambahResep = document.getElementById('btnTambahResep');
        btnTambahResep.addEventListener('click', function() {
            setTimeout(() => {
                const newSelect = resepObatContainer.querySelector('.resep-obat-item:last-child select[name="resep_obat[]"]');
                if (newSelect) {
                    updateBentukObat(newSelect);
                }
            }, 0);
        });

        $(document).ready(function() {
            function initSelect2ResepObat() {
                $('.select2-resep-obat').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Cari dan pilih obat',
                    allowClear: true,
                    ajax: {
                        url: '/dokter/search-obat',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return { q: params.term };
                        },
                        processResults: function(data) {
                            return { results: data.results };
                        },
                        cache: true
                    }
                }).on('select2:select', function(e) {
                    var data = e.params.data;
                    var $item = $(this).closest('.resep-obat-item');
                    $item.find('input[name="bentuk_obat[]"]').val(data.bentuk_obat || '');
                    $item.find('input[name="jumlah_obat[]"]').attr('max', data.stok || '');
                    $item.find('[name="stok_obat_display"]').text('Stok: ' + (data.stok ?? '-'));
                }).on('select2:clear', function(e) {
                    var $item = $(this).closest('.resep-obat-item');
                    $item.find('input[name="bentuk_obat[]"]').val('');
                    $item.find('input[name="jumlah_obat[]"]').removeAttr('max').val('');
                    $item.find('[name="stok_obat_display"]').text('Stok: -');
                });
            }
            // Inisialisasi untuk resep obat yang sudah ada
            initSelect2ResepObat();
            // Inisialisasi ulang jika tambah resep obat baru
            $('#btnTambahResep').on('click', function() {
                setTimeout(function() {
                    initSelect2ResepObat();
                }, 100);
            });
        });

        // Navigasi keyboard pada hasil pencarian
        $(document).on('keydown', '.input-cari-obat', function(e) {
            const $input = $(this);
            const $hasil = $input.siblings('.hasil-cari-obat');
            const $items = $hasil.find('.item-obat');
            let idx = $items.index($hasil.find('.item-obat.active'));
            if (!$hasil.is(':visible') || !$items.length) return;
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                idx = (idx + 1) % $items.length;
                $items.removeClass('active');
                $items.eq(idx).addClass('active').focus();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                idx = (idx - 1 + $items.length) % $items.length;
                $items.removeClass('active');
                $items.eq(idx).addClass('active').focus();
            } else if (e.key === 'Enter') {
                if (idx >= 0) {
                    e.preventDefault();
                    $items.eq(idx).trigger('click');
                }
            }
        });
        // Hover pada item
        $(document).on('mouseenter', '.hasil-cari-obat .item-obat', function() {
            $(this).addClass('active').siblings().removeClass('active');
        });

        // Tutup dropdown jika klik di luar input dan dropdown
    $(document).on('mousedown', function(e) {
        $('.input-cari-obat').each(function() {
            const $input = $(this);
            const $hasil = $input.siblings('.hasil-cari-obat');
            if ($hasil.is(':visible')) {
                if (!$(e.target).closest($input).length && !$(e.target).closest($hasil).length) {
                    $hasil.hide();
                }
            }
        });
    });
    });

    // Toggle dropdown saat field resep obat di-focus
    $(document).on('focus', '.input-cari-obat', function() {
        const input = this;
        const $hasil = $(input).siblings('.hasil-cari-obat');
        if ($hasil.is(':visible')) {
            $hasil.hide();
            return;
        }
        const query = input.value;
        $.ajax({
            url: '/dokter/search-obat',
            data: {q: query},
            success: function(res) {
                if (res.results && res.results.length > 0) {
                    let html = '';
                    res.results.forEach(function(obat) {
                        html += `<div class='item-obat px-2 py-1' style='cursor:pointer;' data-id='${obat.id}' data-nama='${obat.text}' data-bentuk='${obat.bentuk_obat}' data-stok='${obat.stok}'>${obat.text} <span class='text-muted small'>(${obat.bentuk_obat}, stok: ${obat.stok})</span></div>`;
                    });
                    $hasil.html(html).show();
                } else {
                    $hasil.html('<div class="px-2 py-1 text-muted">Obat tidak ditemukan</div>').show();
                }
            }
        });
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const resepObatContainer = document.getElementById('resepObatContainer');
    const btnTambahResep = document.getElementById('btnTambahResep');

    btnTambahResep.addEventListener('click', function() {
        const firstItem = resepObatContainer.querySelector('.resep-obat-item');
        if (!firstItem) return;
        const newItem = firstItem.cloneNode(true);
        // Clear values in cloned fields
        const select = newItem.querySelector('select');
        const input = newItem.querySelector('input[type="number"]');
        if (select) {
            select.selectedIndex = 0;
        }
        if (input) {
            input.value = 1;
        }
        resepObatContainer.appendChild(newItem);
    });

    // Use event delegation for remove buttons
    resepObatContainer.addEventListener('click', function(event) {
        if (event.target.classList.contains('btnRemoveResep')) {
            if (resepObatContainer.children.length > 1) {
                event.target.closest('.resep-obat-item').remove();
            }
        }
    });

    // Update bentuk obat otomatis
    const obatsData = @json($obats);
    function updateBentukObat(selectElement) {
        const selectedObatId = selectElement.value;
        const bentukInput = selectElement.closest('.resep-obat-item').querySelector('input[name="bentuk_obat[]"]');
        const jumlahInput = selectElement.closest('.resep-obat-item').querySelector('input[name="jumlah_obat[]"]');
        const stokDisplay = selectElement.closest('.resep-obat-item').querySelector('small[name="stok_obat_display"]');
        if (!selectedObatId) {
            bentukInput.value = '';
            if (jumlahInput) {
                jumlahInput.removeAttribute('max');
                jumlahInput.value = '';
            }
            if (stokDisplay) {
                stokDisplay.textContent = 'Stok: -';
            }
            return;
        }
        const obat = obatsData.find(o => o.id == selectedObatId);
        if (obat) {
            bentukInput.value = obat.bentuk_obat || '';
            if (jumlahInput) {
                jumlahInput.setAttribute('max', obat.stok);
                if (parseInt(jumlahInput.value) > obat.stok) {
                    jumlahInput.value = obat.stok;
                }
            }
            if (stokDisplay) {
                stokDisplay.textContent = 'Stok: ' + (obat.stok ?? '-');
            }
        } else {
            bentukInput.value = '';
            if (jumlahInput) {
                jumlahInput.removeAttribute('max');
                jumlahInput.value = '';
            }
            if (stokDisplay) {
                stokDisplay.textContent = 'Stok: -';
            }
        }
    }
    resepObatContainer.addEventListener('change', function(event) {
        if (event.target.matches('select[name="resep_obat[]"]')) {
            updateBentukObat(event.target);
        }
    });
    // Update bentuk obat untuk existing items
    resepObatContainer.querySelectorAll('select[name="resep_obat[]"]').forEach(select => {
        updateBentukObat(select);
    });
    // Update bentuk obat untuk resep baru
    btnTambahResep.addEventListener('click', function() {
        setTimeout(() => {
            const newSelect = resepObatContainer.querySelector('.resep-obat-item:last-child select[name="resep_obat[]"]');
            if (newSelect) {
                updateBentukObat(newSelect);
            }
        }, 0);
    });
});
</script>
@endsection
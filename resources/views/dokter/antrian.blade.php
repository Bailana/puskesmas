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
                                <td style="white-space: nowrap;">{{ $antrians->firstItem() + $index }}.</td>
                                <td style="white-space: nowrap;">{{ $antrian->no_rekam_medis }}</td>
                                <td style="white-space: nowrap;">{{ $antrian->pasien->nama_pasien }}</td>
                                <td style="white-space: nowrap;">
                                    {{ \Carbon\Carbon::parse($antrian->pasien->tanggal_lahir)->age }} Tahun
                                </td>
                                <td style="white-space: nowrap;">{{ $antrian->pasien->jaminan_kesehatan }}</td>
                                <!-- <td style="white-space: nowrap;">{{ \Carbon\Carbon::parse($antrian->tanggal_berobat)->format('d-m-Y') }}</td> -->
                                <td style="white-space: nowrap;"><span
                                        class="badge bg-danger">{{ $antrian->status }}</span></td>
                                <td style="white-space: nowrap;">
                                    <button type="button" class="btn btn-success btn-sm rounded btn-hasilanalisa"
                                        data-rekam-medis="{{ $antrian->no_rekam_medis }}"
                                        data-bs-toggle="modal" data-bs-target="#modalAnalisa">
                                        Hasil Analisa</button>
                                    <button type="button" class="btn btn-primary btn-sm rounded btnPeriksa"
                                        data-bs-toggle="modal" data-bs-target="#modalPeriksaPasien"
                                        data-pasien-id="{{ $antrian->pasien->id }}">Periksa</button>
                                    <button type="button" class="btn btn-danger btn-sm rounded btn-riwayat"
                                        data-rekam-medis="{{ $antrian->no_rekam_medis }}"
                                        data-nama="{{ $antrian->nama_pasien }}"
                                        data-bs-toggle="modal" data-bs-target="#modalRiwayatBerobat">
                                        Riwayat Berobat
                                    </button>
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
                                    <label class="form-label">Poli Tujuan</label>
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
        align-items: flex-center;
        /* align button to bottom of input + error message */
    }

    /* Fix equal-width class to prevent shrinking of first field */
    .equal-width {
        flex: 1 1 0;
        min-width: 0;
        max-width: 33.3333%;
    }

    /* Ensure select and input inside equal-width take full width */
    .equal-width select.form-select,
    .equal-width input.form-control {
        width: 100%;
        box-sizing: border-box;
    }

    #modalRiwayatBerobat th,
    #modalRiwayatBerobat td {
        font-size: 0.875rem !important;
        font-weight: 400 !important;
    }

    #modalRiwayatBerobat th {
        font-weight: 600 !important;
    }
</style>
@section('scripts')
<!-- Select2 CSS & JS CDN -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    const searchInput = document.getElementById('searchInput');
    const antrianTableBody = document.querySelector('#antrianTable tbody');

    function doSearchAntrian(pageUrl) {
        var search = document.getElementById('searchInput').value;
        var url = pageUrl || window.location.pathname + window.location.search;
        var params = new URLSearchParams(window.location.search);

        if (search) {
            params.set('search', search);
        } else {
            params.delete('search');
        }

        // Update the 'search' param in the URL's query string
        var baseUrl = url.split('?')[0];
        var existingParams = new URLSearchParams(url.split('?')[1] || '');

        // Merge existing params with updated search param
        if (search) {
            existingParams.set('search', search);
        } else {
            existingParams.delete('search');
        }

        var newQueryString = existingParams.toString();
        var newUrl = baseUrl + (newQueryString ? '?' + newQueryString : '');

        history.pushState(null, '', newUrl);

        fetch(newUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(response) {
                return response.text();
            })
            .then(function(html) {
                var parser = new DOMParser();
                var doc = parser.parseFromString(html, 'text/html');
                var newTbody = doc.querySelector('tbody');
                var newInfo = doc.querySelector('.pagination-info-text');
                var newPagination = doc.querySelector('ul.pagination');
                if (newTbody && document.querySelector('#antrianTable tbody')) {
                    document.querySelector('#antrianTable tbody').innerHTML = newTbody.innerHTML;
                }
                if (newInfo && document.querySelector('.pagination-info-text')) {
                    document.querySelector('.pagination-info-text').innerHTML = newInfo.innerHTML;
                }
                if (newPagination && document.querySelector('ul.pagination')) {
                    document.querySelector('ul.pagination').innerHTML = newPagination.innerHTML;
                }
                attachButtonEventListeners();
            });
    }

    window.refreshAntrianTableGlobal = function() {
        doSearchAntrian(window.location.pathname + window.location.search);
    }

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
                    <button type="button" class="btn btn-success btn-sm rounded btn-hasilanalisa" data-bs-toggle="modal"
                        data-bs-target="#modalAnalisa" data-rekam-medis="${antrian.no_rekam_medis}">Hasil Analisa</button>
                    <button type="button" class="btn btn-primary btn-sm rounded btnPeriksa"
                        data-bs-toggle="modal" data-bs-target="#modalPeriksaPasien"
                        data-pasien-id="${antrian.pasien.id}">Periksa</button>
                    <button type="button" class="btn btn-danger btn-sm rounded btn-riwayat"
                        data-rekam-medis="${antrian.no_rekam_medis}">Riwayat
                        Berobat</button>
                </td>
            `;
            antrianTableBody.appendChild(row);
        });
    }

    function attachButtonEventListeners() {
        // Attach event listeners to dynamically created buttons after table refresh
        document.querySelectorAll('.btnPeriksa').forEach(button => {
            button.addEventListener('click', function() {
                window.selectedPasienId = this.getAttribute('data-pasien-id');
                modalPeriksaPasien.show();
            });
        });

        document.querySelectorAll('.btn-hasilanalisa').forEach(button => {
            button.addEventListener('click', function() {
                // The modal is triggered by data-bs-toggle and data-bs-target attributes
                // Additional logic can be added here if needed
            });
        });

        document.querySelectorAll('.btn-riwayat').forEach(button => {
            button.addEventListener('click', function() {
                const noRekamMedis = this.getAttribute('data-rekam-medis');
                const modal = new bootstrap.Modal(document.getElementById('modalRiwayatBerobat'));
                const riwayatList = document.getElementById('riwayatList');
                const hasilPeriksaDetail = document.getElementById('hasilPeriksaDetail');

                // Clear previous content
                riwayatList.innerHTML = '';
                hasilPeriksaDetail.style.display = 'none';
                riwayatList.style.display = 'block';

                // Fetch riwayat berobat dates by pasien no_rekam_medis
                fetch(`/dokter/riwayat-berobat/${noRekamMedis}/dates`)
                    .then(response => response.json())
                    .then(result => {
                        if (!result.success) {
                            riwayatList.innerHTML = `<p>Error: ${result.message}</p>`;
                            return;
                        }
                        const dates = result.data;
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
                                const dateStr = dateObj.toLocaleDateString('id-ID', options);
                                const div = document.createElement('div');
                                div.classList.add('d-flex', 'justify-content-between', 'align-items-center');
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
                        }
                    })
                    .catch(error => {
                        riwayatList.innerHTML = `<p>Error: ${error.message}</p>`;
                    });

                modal.show();
            });
        });
    }

    let debounceTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            doSearchAntrian();
        }, 300);
    });

    function refreshAntrianTable() {
        fetch(`{{ route('dokter.antrian') }}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                renderTableRows(data);
            })
            .catch(error => {
                console.error('Error refreshing antrian table:', error);
            });
    }

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
            data: {
                q: query
            },
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
                fetch(`/dokter/riwayat-berobat/${noRekamMedis}/dates`)
                    .then(response => response.json())
                    .then(result => {
                        if (!result.success) {
                            riwayatList.innerHTML = `<p>Error: ${result.message}</p>`;
                            return;
                        }
                        const dates = result.data;
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
                                const dateStr = dateObj.toLocaleDateString('id-ID', options);
                                const div = document.createElement('div');
                                div.classList.add('d-flex', 'justify-content-between', 'align-items-center');
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
                        }
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
                fetch(`/dokter/riwayat-berobat/${noRekamMedis}/${tanggal}`)
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

        // Event listener tombol Tutup di detail view
        document.getElementById('btnTutupDetail').addEventListener('click', function() {
            document.getElementById('hasilPeriksaDetail').style.display = 'none';
            document.getElementById('riwayatList').style.display = 'block';
        });
    });
</script>
<script>
    var modalAnalisa = document.getElementById('modalAnalisa');
    if (modalAnalisa) {
        modalAnalisa.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var noRekamMedis = button.getAttribute('data-rekam-medis');
            var noRekamMedisInput = modalAnalisa.querySelector('#no_rekam_medis');
            if (noRekamMedisInput) noRekamMedisInput.value = noRekamMedis;
            // Set patient name display in modal header
            var patientName = button.closest('tr').querySelector('td:nth-child(3)').textContent.trim();
            var namaPasienDisplay = modalAnalisa.querySelector('#nama_pasien_display');
            if (namaPasienDisplay) namaPasienDisplay.textContent = patientName;

            // Clear modal fields before fetching new data
            var ids = [
                'modalAnalisaTekananDarah', 'modalAnalisaFrekuensiNadi', 'modalAnalisaSuhu', 'modalAnalisaFrekuensiNafas',
                'modalAnalisaSkorNyeri', 'modalAnalisaSkorJatuh', 'modalAnalisaBeratBadan', 'modalAnalisaTinggiBadan',
                'modalAnalisaLingkarKepala', 'modalAnalisaIMT', 'modalAnalisaAlatBantu', 'modalAnalisaProsthesa',
                'modalAnalisaCacatTubuh', 'modalAnalisaADLMandiri', 'modalAnalisaRiwayatJatuh', 'modalAnalisaStatusPsikologi',
                'modalAnalisaHambatanEdukasi', 'modalAnalisaAlergi', 'modalAnalisaCatatan', 'modalAnalisaPoliTujuan',
                'modalAnalisaPenanggungJawab'
            ];
            ids.forEach(function(id) {
                var el = document.getElementById(id);
                if (el) el.value = '';
            });

            // Remove any existing no-data message
            var noDataMessage = modalAnalisa.querySelector('#noDataMessage');
            if (noDataMessage) {
                noDataMessage.remove();
            }

            // Fetch hasil analisa data and populate modal fields
            fetch('/dokter/hasil-analisa/' + encodeURIComponent(noRekamMedis), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(function(data) {
                    console.log('AJAX success response:', data);
                    if (data && data.hasil) {
                        var h = data.hasil;
                        document.getElementById('modalAnalisaTekananDarah').value = h.tekanan_darah || '';
                        document.getElementById('modalAnalisaFrekuensiNadi').value = h.frekuensi_nadi || '';
                        document.getElementById('modalAnalisaSuhu').value = h.suhu || '';
                        document.getElementById('modalAnalisaFrekuensiNafas').value = h.frekuensi_nafas || '';
                        document.getElementById('modalAnalisaSkorNyeri').value = h.skor_nyeri || '';
                        document.getElementById('modalAnalisaSkorJatuh').value = h.skor_jatuh || '';
                        document.getElementById('modalAnalisaBeratBadan').value = h.berat_badan || '';
                        document.getElementById('modalAnalisaTinggiBadan').value = h.tinggi_badan || '';
                        document.getElementById('modalAnalisaLingkarKepala').value = h.lingkar_kepala || '';
                        document.getElementById('modalAnalisaIMT').value = h.imt || '';
                        document.getElementById('modalAnalisaAlatBantu').value = h.alat_bantu || '';
                        document.getElementById('modalAnalisaProsthesa').value = h.prosthesa || '';
                        document.getElementById('modalAnalisaCacatTubuh').value = h.cacat_tubuh || '';
                        document.getElementById('modalAnalisaADLMandiri').value = h.adl_mandiri || '';
                        document.getElementById('modalAnalisaRiwayatJatuh').value = h.riwayat_jatuh || '';
                        document.getElementById('modalAnalisaStatusPsikologi').value = h.status_psikologi || '';
                        document.getElementById('modalAnalisaHambatanEdukasi').value = h.hambatan_edukasi || '';
                        document.getElementById('modalAnalisaAlergi').value = h.alergi || '';
                        document.getElementById('modalAnalisaCatatan').value = h.catatan || '';
                        document.getElementById('modalAnalisaPoliTujuan').value = h.nama_poli || '';
                        document.getElementById('modalAnalisaPenanggungJawab').value = h.nama_penanggung_jawab || '';
                        // Ensure modal body form is visible
                        var modalBody = modalAnalisa.querySelector('.modal-body form');
                        if (modalBody) {
                            modalBody.style.display = '';
                        }
                    } else {
                        // Show no data message in modal body
                        var modalBody = modalAnalisa.querySelector('.modal-body form');
                        if (modalBody) {
                            // Prevent duplicate message insertion
                            if (!modalAnalisa.querySelector('#noDataMessage')) {
                                var messageDiv = document.createElement('div');
                                messageDiv.id = 'noDataMessage';
                                messageDiv.style.padding = '5px';
                                messageDiv.style.textAlign = 'center';
                                // messageDiv.style.fontWeight = 'bold';
                                messageDiv.textContent = 'Tidak ada hasil analisa pasien.';
                                modalBody.parentNode.insertBefore(messageDiv, modalBody.nextSibling);
                                modalBody.style.display = 'none';
                            }
                        }
                    }
                })
                .catch(function(error) {
                    console.error('AJAX error:', error);
                    // Error, field tetap kosong
                });
        });
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnSimpanPeriksa = document.getElementById('btnSimpanPeriksa');
        const formPeriksaPasien = document.getElementById('formPeriksaPasien');
        const modalPeriksaPasien = new bootstrap.Modal(document.getElementById('modalPeriksaPasien'));

        function clearErrors() {
            const errorMessages = formPeriksaPasien.querySelectorAll('.error-message');
            errorMessages.forEach(msg => msg.remove());
            // Remove error highlight class from inputs
            const errorInputs = formPeriksaPasien.querySelectorAll('.input-error');
            errorInputs.forEach(input => input.classList.remove('input-error'));

            // Remove margin-bottom and shift-down class from all remove button containers
            const removeBtnContainers = formPeriksaPasien.querySelectorAll('.btnRemoveResep');
            removeBtnContainers.forEach(btn => {
                const container = btn.parentElement;
                if (container) {
                    container.classList.remove('shift-down');
                    container.style.marginBottom = '2px';
                }
            });
        }

        function showError(inputElement, message) {
            // Remove existing error message if any
            const existingError = inputElement.parentElement.querySelector('.error-message');
            if (existingError) {
                existingError.remove();
            }
            // Add error highlight class to input
            inputElement.classList.add('input-error');
            const error = document.createElement('div');
            error.classList.add('error-message');
            error.textContent = message;
            inputElement.parentElement.appendChild(error);

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

        function validateForm() {
            clearErrors();
            let isValid = true;

            const fields = [{
                    id: 'anamnesis',
                    name: 'Anamnesis'
                },
                {
                    id: 'pemeriksaanFisik',
                    name: 'Pemeriksaan Fisik'
                },
                {
                    id: 'rencanaTerapi',
                    name: 'Rencana dan Terapi'
                },
                {
                    id: 'diagnosis',
                    name: 'Diagnosis'
                },
                {
                    id: 'edukasi',
                    name: 'Edukasi'
                },
                {
                    id: 'kodeICD',
                    name: 'Kode ICD'
                },
                {
                    id: 'kesanStatusGizi',
                    name: 'Kesan Status Gizi'
                }
            ];

            fields.forEach(field => {
                const input = document.getElementById(field.id);
                if (!input.value || input.value.trim() === '') {
                    showError(input, 'Field ini wajib diisi.');
                    isValid = false;
                }
            });

            // Validate resep obat fields
            const resepObatItems = document.querySelectorAll('.resep-obat-item');
            resepObatItems.forEach(item => {
                const selectObat = item.querySelector('select[name="resep_obat[]"]');
                const inputBentuk = item.querySelector('input[name="bentuk_obat[]"]');
                const inputJumlah = item.querySelector('input[name="jumlah_obat[]"]');

                if (!selectObat.value) {
                    showError(selectObat, 'Field ini wajib diisi.');
                    isValid = false;
                }
                if (!inputBentuk.value) {
                    showError(inputBentuk, 'Field ini wajib diisi.');
                    isValid = false;
                }
                if (!inputJumlah.value || inputJumlah.value.trim() === '') {
                    showError(inputJumlah, 'Field ini wajib diisi.');
                    isValid = false;
                }
            });

            return isValid;
        }


        if (btnSimpanPeriksa) {
            btnSimpanPeriksa.addEventListener('click', function() {
                if (!validateForm()) {
                    return;
                }

                const formData = {
                    pasien_id: getSelectedPasienId(),
                    tanggal_periksa: new Date().toISOString().split('T')[0],
                    anamnesis: document.getElementById('anamnesis').value,
                    pemeriksaan_fisik: document.getElementById('pemeriksaanFisik').value,
                    rencana_dan_terapi: document.getElementById('rencanaTerapi').value,
                    diagnosis: document.getElementById('diagnosis').value,
                    edukasi: document.getElementById('edukasi').value,
                    kode_icd: document.getElementById('kodeICD').value,
                    kesan_status_gizi: document.getElementById('kesanStatusGizi').value,
                    obats: Array.from(document.querySelectorAll('.resep-obat-item')).map(item => {
                        const id = item.querySelector('select').value;
                        const jumlah = item.querySelector('input[name="jumlah_obat[]"]').value;
                        if (!id || !jumlah) {
                            return null; // skip empty or invalid items
                        }
                        return {
                            id: id,
                            jumlah: jumlah,
                            bentuk: item.querySelector('input[name="bentuk_obat[]"]').value,
                            catatan_obat: document.getElementById('catatanObat').value.trim(),
                        };
                    }).filter(item => item !== null),
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
                            let errorMessage = 'Terjadi kesalahan saat menyimpan data hasil periksa.';
                            if (errorData.errors) {
                                errorMessage = Object.values(errorData.errors).flat().join('\n');
                            } else if (errorData.message) {
                                errorMessage = errorData.message;
                            }
                            throw new Error(errorMessage);
                        }
                        return response.json();
                    })
                    .then(data => {
                        toastr.success('Diagnosis pasien berhasil disimpan');
                        formPeriksaPasien.reset();
                        modalPeriksaPasien.hide();
                        window.refreshAntrianTableGlobal(); // Use global function explicitly
                    })
                    .catch(error => {
                        console.error('Error saat menyimpan data hasil periksa:', error.message);
                    });
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const modalPeriksaPasien = new bootstrap.Modal(document.getElementById('modalPeriksaPasien'));
            modalPeriksaPasien._element.addEventListener('hidden.bs.modal', function() {
                window.refreshAntrianTableGlobal(); // Use global function explicitly
            });
        });

        function getSelectedPasienId() {
            return window.selectedPasienId || null;
        }

        document.querySelectorAll('.btnPeriksa').forEach(button => {
            button.addEventListener('click', function() {
                window.selectedPasienId = this.getAttribute('data-pasien-id');
                modalPeriksaPasien.show();
            });
        });

        // Reset form and clear validation errors when modal close button is clicked
        const modalPeriksaPasienElement = document.getElementById('modalPeriksaPasien');
        if (modalPeriksaPasienElement && formPeriksaPasien) {
            modalPeriksaPasienElement.querySelectorAll('button.btn-close').forEach(button => {
                button.addEventListener('click', () => {
                    formPeriksaPasien.reset();
                    clearErrors();
                });
            });
        }
    });

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
            // updateRemoveButtonAlignment(newItem);
        });

        // Use event delegation for remove buttons and input validation
        resepObatContainer.addEventListener('click', function(event) {
            if (event.target.classList.contains('btnRemoveResep')) {
                if (resepObatContainer.children.length > 1) {
                    event.target.closest('.resep-obat-item').remove();
                }
            }
        });

        resepObatContainer.addEventListener('input', function(event) {
            if (event.target.tagName === 'SELECT' || (event.target.tagName === 'INPUT' && event.target.type === 'number')) {
                const item = event.target.closest('.resep-obat-item');
                if (item) {
                    // updateRemoveButtonAlignment(item);
                }
            }
        });

        // Initial update for existing items
        resepObatContainer.querySelectorAll('.resep-obat-item').forEach(item => {
            // updateRemoveButtonAlignment(item);
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
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
            // Wait a tick for the new item to be added
            setTimeout(() => {
                const newSelect = resepObatContainer.querySelector('.resep-obat-item:last-child select[name="resep_obat[]"]');
                if (newSelect) {
                    updateBentukObat(newSelect);
                }
            }, 0);
        });

        // Add input event listener to jumlah_obat inputs to enforce max value
        resepObatContainer.addEventListener('input', function(event) {
            if (event.target.matches('input[name="jumlah_obat[]"]')) {
                const input = event.target;
                const max = parseInt(input.getAttribute('max'));
                const value = parseInt(input.value);
                if (!isNaN(max) && !isNaN(value) && value > max) {
                    input.value = max;
                } else if (value < 1 || isNaN(value)) {
                    input.value = 1;
                }
            }
        });
    });
</script>
@endsection
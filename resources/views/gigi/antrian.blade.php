@extends('dashboardGigi')

@section('gigi')
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
                    <table class="table table-hover my-0" id="antrianTable">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th class="col-nomor-rm">Nomor RM</th>
                                <th class="col-nama-pasien">Nama Pasien</th>
                                <th>Umur</th>
                                <th class="col-jamkes">JamKes</th>
                                <!-- Removed Poli Tujuan column as per user request -->
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($antrians->count() > 0)
                            @foreach ($antrians as $index => $antrian)
                            <tr>
                                <td>{{ $index + 1 }}.</td>
                                <td>{{ $antrian->no_rekam_medis }}</td>
                                <td>{{ $antrian->pasien->nama_pasien }}</td>
                                <td>{{ \Carbon\Carbon::parse($antrian->pasien->tanggal_lahir)->age }} tahun</td>
                                <td>{{ $antrian->pasien->jaminan_kesehatan }}</td>
                                <!-- Removed Poli Tujuan data cell as per user request -->
                                <td><span class="badge bg-warning">{{ $antrian->status }}</span></td>
                                <td>
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
                            @else
                            <tr>
                                <td colspan="8" class="text-center">Antrian pasien tidak tersedia</td>
                            </tr>
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
                        <input type="hidden" id="pasienId" name="pasien_id" value="">
                        <input type="hidden" id="tanggalPeriksa" name="tanggal_periksa" value="">

                        <!-- Pemeriksaan Subjektif -->
                        <div class="mb-3">
                            <label for="pemeriksaanSubjektif" class="form-label">Pemeriksaan Subjektif<span
                                    style="color: red;">*</span></label>
                            <textarea class="form-control" id="pemeriksaanSubjektif" name="pemeriksaan_subjektif"
                                rows="3" required></textarea>
                        </div>

                        <!-- Pemeriksaan Objektif & Penunjang -->
                        <div class="mb-3">
                            <label for="pemeriksaanObjektif" class="form-label">Pemeriksaan Objektif & Penunjang</label>
                            <textarea class="form-control" id="pemeriksaanObjektif" name="pemeriksaan_objektif" rows="3"
                                required></textarea>
                        </div>

                        <!-- Diagnosa -->
                        <div class="mb-3">
                            <label for="diagnosa" class="form-label">Diagnosa</label>
                            <textarea class="form-control" id="diagnosa" name="diagnosa" rows="3" required></textarea>
                        </div>

                        <!-- Terapi & Anjuran -->
                        <div class="mb-3">
                            <label for="terapiAnjuran" class="form-label">Terapi & Anjuran</label>
                            <textarea class="form-control" id="terapiAnjuran" name="terapi_anjuran" rows="3"
                                required></textarea>
                        </div>

                        <!-- Catatan Paramedis -->
                        <div class="mb-3">
                            <label for="catatanParamedis" class="form-label">Catatan Paramedis</label>
                            <textarea class="form-control" id="catatanParamedis" name="catatan" rows="3"
                                required></textarea>
                        </div>

                        <!-- Odontogram Section -->
                        <div class="mb-3">
                            <label class="form-label">Odontogram</label>
                            <div id="odontogram" class="d-flex flex-wrap" style="max-width: 600px;">
                                <!-- Teeth will be generated by JS -->
                            </div>
                            <input type="hidden" id="odontogramData" name="odontogram" value="{}">
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-end mt-3">
                    <button type="button" class="btn btn-success ms-2" id="btnSimpanPeriksa">Simpan</button>
                </div>
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
</div>

@endsection

@section('scripts')
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
            fetch('/gigi/hasil-analisa/' + encodeURIComponent(noRekamMedis), {
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
                fetch(`/gigi/riwayat-berobat/${noRekamMedis}/dates?type=rawatinap`, {
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
                fetch(`/gigi/riwayat-berobat/${noRekamMedis}/${tanggal}?source=${ev.target.getAttribute('data-source')}`, {
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
                fetch(`/gigi/riwayat-berobat/${noRekamMedis}/${tanggal}`)
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var periksaButtons = document.querySelectorAll('.btnPeriksa');
        var modalElement = document.getElementById('modalPeriksaPasien');
        var modal = new bootstrap.Modal(modalElement);

        periksaButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const pasienId = button.getAttribute('data-pasien-id');
                document.getElementById('pasienId').value = pasienId;
                document.getElementById('tanggalPeriksa').value = new Date().toISOString()
                    .slice(0, 10);

                document.getElementById('formPeriksaPasien').reset();

                const odontogram = document.getElementById('odontogram');
                const teeth = odontogram.querySelectorAll('.tooth');
                teeth.forEach(tooth => {
                    tooth.dataset.stateIndex = 0;
                    tooth.style.backgroundColor = '#a8d5a2'; // warna sehat
                });
                document.getElementById('odontogramData').value = JSON.stringify({});

                modal.show();
            });
        });

        const btnSimpanPeriksa = document.getElementById('btnSimpanPeriksa');
        btnSimpanPeriksa.addEventListener('click', function() {
            btnSimpanPeriksa.disabled = true; // nonaktifkan tombol untuk mencegah klik ganda
            const form = document.getElementById('formPeriksaPasien');
            const formData = new FormData(form);

            fetch("{{ route('gigi.hasilperiksagigi.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    toastr.success(data.message || 'Data berhasil disimpan', '', {
                        timeOut: 3000,
                        extendedTimeOut: 3000
                    });
                    modal.hide();
                    btnSimpanPeriksa.disabled = false; // aktifkan kembali tombol

                    // Perbarui tabel antrian pasien secara AJAX tanpa reload halaman
                    fetch(window.location.href, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.text())
                        .then(html => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            const newTableBody = doc.querySelector('table.table tbody');
                            const currentTableBody = document.querySelector(
                                'table.table tbody');
                            if (newTableBody && currentTableBody) {
                                currentTableBody.innerHTML = newTableBody.innerHTML;
                            }
                        });
                })
                .catch(error => {
                    toastr.error('Terjadi kesalahan saat menyimpan data');
                    console.error(error);
                    btnSimpanPeriksa.disabled = false; // aktifkan kembali tombol
                });
        });
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const odontogram = document.getElementById('odontogram');
        const odontogramDataInput = document.getElementById('odontogramData');

        // Define tooth states and colors
        const toothStates = ['healthy', 'decayed', 'filled'];
        const toothColors = {
            healthy: '#a8d5a2',
            decayed: '#e57373',
            filled: '#ffb74d'
        };

        // Initialize odontogram data
        let odontogramData = {};

        // Generate 32 teeth (1 to 32)
        for (let i = 1; i <= 32; i++) {
            const tooth = document.createElement('div');
            tooth.classList.add('tooth');
            tooth.style.width = '40px';
            tooth.style.height = '40px';
            tooth.style.margin = '4px';
            tooth.style.border = '1px solid #ccc';
            tooth.style.borderRadius = '4px';
            tooth.style.display = 'flex';
            tooth.style.alignItems = 'center';
            tooth.style.justifyContent = 'center';
            tooth.style.cursor = 'pointer';
            tooth.style.userSelect = 'none';
            tooth.style.backgroundColor = toothColors['healthy'];
            tooth.textContent = i;
            tooth.dataset.toothNumber = i;
            tooth.dataset.stateIndex = 0; // healthy

            tooth.addEventListener('click', () => {
                // Cycle through states
                let currentIndex = parseInt(tooth.dataset.stateIndex);
                let nextIndex = (currentIndex + 1) % toothStates.length;
                tooth.dataset.stateIndex = nextIndex;
                let state = toothStates[nextIndex];
                tooth.style.backgroundColor = toothColors[state];
                odontogramData[tooth.dataset.toothNumber] = state;
                odontogramDataInput.value = JSON.stringify(odontogramData);
            });

            odontogram.appendChild(tooth);
            odontogramData[i] = 'healthy';
        }

        odontogramDataInput.value = JSON.stringify(odontogramData);
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var periksaButtons = document.querySelectorAll('.btn-periksa');
        var modalElement = document.getElementById('modalPeriksaPasien');
        var modal = new bootstrap.Modal(modalElement);

        periksaButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const pasienId = button.getAttribute('data-pasien-id');
                document.getElementById('pasienId').value = pasienId;
                document.getElementById('tanggalPeriksa').value = new Date().toISOString()
                    .slice(0, 10);

                document.getElementById('formPeriksaPasien').reset();

                const odontogram = document.getElementById('odontogram');
                const teeth = odontogram.querySelectorAll('.tooth');
                teeth.forEach(tooth => {
                    tooth.dataset.stateIndex = 0;
                    tooth.style.backgroundColor = '#a8d5a2'; // warna sehat
                });
                document.getElementById('odontogramData').value = JSON.stringify({});

                modal.show();
            });
        });


    });
</script>
@endsection
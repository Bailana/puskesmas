@extends('dashboardbidan')

@section('bidan')
<!-- Include toastr CSS and JS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

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
                        <tbody>
                            @if(isset($antrians))
                            @forelse($antrians as $index => $antrian)
                            <tr>
                                <td style="white-space: nowrap;">{{ $antrians->firstItem() + $index }}</td>
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
                                    <button type="button" class="btn btn-success btn-sm rounded btn-hasilanalisa"
                                        data-rekam-medis="{{ $antrian->no_rekam_medis }}"
                                        data-bs-toggle="modal" data-bs-target="#modalAnalisa"
                                        @if(!$antrian->pasien) disabled @endif>Hasil Analisa</button>
                                    <button type="button" class="btn btn-primary btn-sm rounded btn-periksa"
                                        data-rekam-medis="{{ $antrian->no_rekam_medis }}"
                                        @if(!$antrian->pasien) disabled @endif>Periksa</button>
                                    <button type="button" class="btn btn-danger btn-sm rounded btn-riwayat"
                                        data-rekam-medis="{{ $antrian->no_rekam_medis }}"
                                        data-nama="{{ $antrian->pasien ? $antrian->pasien->nama_pasien : '' }}"
                                        data-bs-toggle="modal" data-bs-target="#modalRiwayatBerobat"
                                        @if(!$antrian->pasien) disabled @endif>Riwayat Berobat</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Antrian pasien tidak ditemukan</td>
                            </tr>
                            @endforelse
                            @endif
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
                                    if ($start < 1) $start=1;
                                    if ($end> $totalPages) $end = $totalPages;
                                    }
                                    @endphp
                                    @for ($i = $start; $i <= $end; $i++)
                                        @if ($i==$currentPage)
                                        <li class="page-item active" aria-current="page"><span class="page-link">{{ $i }}</span></li>
                                        @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $i == 1 ? $antrians->url(1) : $antrians->url($i) }}{{ $i == 1 && !str_contains($antrians->url(1), 'page=1') ? (str_contains($antrians->url(1), '?') ? '&page=1' : '?page=1') : '' }}">{{ $i }}</a>
                                        </li>
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

<!-- Modal Hasil Analisa (readonly, style identik perawat) -->
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

<!-- Modal Periksa Anak -->
<div class="modal fade" id="modalPeriksaAnak" tabindex="-1" aria-labelledby="modalPeriksaAnakLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
        <div class="modal-content" style="overflow-x: hidden;">
            <div class="modal-header d-flex justify-content-between">
                <h3 class="modal-title" id="modalPeriksaAnakLabel"><strong>Periksa Anak</strong></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow-y: auto; padding: 10px;">
                <form id="formPeriksaAnak">
                    <div class="mb-3">
                        <label for="beratBadanAnak" class="form-label">Berat Badan</label>
                        <input type="text" class="form-control form-control-sm" id="beratBadanAnak" name="berat_badan" required>
                    </div>
                    <div class="mb-3">
                        <label for="makananAnak" class="form-label">Makanan Anak</label>
                        <textarea class="form-control form-control-sm" id="makananAnak" name="makanan_anak" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="gejalaAnak" class="form-label">Gejala</label>
                        <textarea class="form-control form-control-sm" id="gejalaAnak" name="gejala" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="nasehatAnak" class="form-label">Nasehat</label>
                        <textarea class="form-control form-control-sm" id="nasehatAnak" name="nasehat" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="pegobatanAnak" class="form-label">Pengobatan</label>
                        <textarea class="form-control form-control-sm" id="pegobatanAnak" name="pegobatan" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-primary" id="btnSimpanPeriksaAnak">Simpan</button>
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
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Bind tombol Periksa untuk membuka modal dan menyimpan no rekam medis
        document.querySelectorAll('.btn-periksa').forEach(function(button) {
            button.addEventListener('click', function() {
                var noRekamMedis = this.getAttribute('data-rekam-medis');
                window.selectedNoRekamMedisAnak = noRekamMedis;
                var modal = new bootstrap.Modal(document.getElementById('modalPeriksaAnak'));
                modal.show();
            });
        });

        // Tangani klik tombol Simpan pada modal
        document.getElementById('btnSimpanPeriksaAnak').addEventListener('click', function() {
            var form = document.getElementById('formPeriksaAnak');
            var formData = {
                no_rekam_medis: window.selectedNoRekamMedisAnak,
                berat_badan: form.berat_badan.value,
                makanan_anak: form.makanan_anak.value,
                gejala: form.gejala.value,
                nasehat: form.nasehat.value,
                pegobatan: form.pegobatan.value,
            };

            fetch('/bidan/hasilperiksa-anak/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(formData),
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw err;
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    toastr.success('Data hasil periksa anak berhasil disimpan.');
                    form.reset();
                    var modal = bootstrap.Modal.getInstance(document.getElementById('modalPeriksaAnak'));
                    modal.hide();

                    // Refresh tabel antrian secara otomatis setelah modal tertutup
                    modal._element.addEventListener('hidden.bs.modal', function() {
                        // Panggil fungsi doSearchAntrian untuk refresh tabel
                        doSearchAntrian();
                    }, {
                        once: true
                    });
                })
                .catch(error => {
                    toastr.error('Gagal menyimpan data: ' + (error.message || JSON.stringify(error)));
                });
        });

        // Handle Riwayat Berobat button click in antrian view
        document.querySelectorAll('.btn-riwayat').forEach(button => {
            button.addEventListener('click', function() {
                const noRekamMedis = this.getAttribute('data-rekam-medis');
                const namaPasien = this.getAttribute('data-nama');
                const modal = new bootstrap.Modal(document.getElementById('modalRiwayatBerobat'));
                const riwayatList = document.getElementById('riwayatList');
                const hasilPeriksaDetail = document.getElementById('hasilPeriksaDetail');

                // Clear previous content
                riwayatList.innerHTML = '';
                hasilPeriksaDetail.style.display = 'none';
                riwayatList.style.display = 'block';

                // Fetch riwayat berobat dates by pasien no_rekam_medis
                fetch(`/bidan/riwayat-berobat/${noRekamMedis}/dates`)
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

                // Add event listener for the new Tutup button inside detail view
                document.getElementById('btnTutupDetail').addEventListener('click', function() {
                    hasilPeriksaDetail.style.display = 'none';
                    riwayatList.style.display = 'block';
                });
            });
        });

        // Event delegation for btnLihat inside #riwayatList
        document.getElementById('riwayatList').addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('btnLihat')) {
                const tanggal = e.target.getAttribute('data-tanggal');
                const noRekamMedis = e.target.getAttribute('data-norm');
                const hasilPeriksaDetail = document.getElementById('hasilPeriksaDetail');
                const riwayatList = document.getElementById('riwayatList');
                fetch(`/bidan/riwayat-berobat/${noRekamMedis}/${tanggal}`)
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
                            // Tambahkan penanggung jawab hasil periksa jika ada
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

        // =================== MODAL HASIL ANALISA ===================
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

                // Fetch hasil analisa data and populate modal fields
                fetch('/bidan/hasil-analisa/' + encodeURIComponent(noRekamMedis), {
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
                        } else {
                            console.log('No hasil data found in response');
                        }
                    })
                    .catch(function(error) {
                        console.error('AJAX error:', error);
                        // Error, field tetap kosong
                    });
            });
        }

        // =================== PAGINATION & SEARCH ===================
        function doSearchAntrian(pageUrl) {
            var search = document.getElementById('searchInput').value;
            var url = pageUrl || window.location.pathname;
            var params = new URLSearchParams(window.location.search);
            if (search) {
                params.set('search', search);
            } else {
                params.delete('search');
            }
            // Perbaiki agar hanya ada satu tanda tanya (?) pada URL
            if (params.toString()) {
                if (url.includes('?')) {
                    url += '&' + params.toString();
                } else {
                    url += '?' + params.toString();
                }
            }
            history.pushState(null, '', url);
            fetch(url, {
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
                    var newInfo = doc.querySelector('.pagination-info-text-antrian');
                    var newPagination = doc.querySelector('ul.pagination');
                    if (newTbody && document.querySelector('#antrianTable tbody')) {
                        document.querySelector('#antrianTable tbody').innerHTML = newTbody.innerHTML;
                    }
                    if (newInfo && document.querySelector('.pagination-info-text-antrian')) {
                        document.querySelector('.pagination-info-text-antrian').innerHTML = newInfo.innerHTML;
                    }
                    if (newPagination && document.querySelector('ul.pagination')) {
                        document.querySelector('ul.pagination').innerHTML = newPagination.innerHTML;
                    }
                    bindAntrianPagination();
                    bindHasilAnalisaButtons();
                });
        }

        function bindAntrianPagination() {
            document.querySelectorAll('.pagination a.page-link').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    var url = this.getAttribute('href');
                    var urlObj = new URL(url, window.location.origin);
                    var params = new URLSearchParams(urlObj.search);
                    // Ambil search dari input jika ada, override jika sudah ada
                    var search = document.getElementById('searchInput').value;
                    if (search) {
                        params.set('search', search);
                    } else {
                        params.delete('search');
                    }
                    // Ambil semua param dari current URL, kecuali page
                    var currentParams = new URLSearchParams(window.location.search);
                    currentParams.forEach(function(value, key) {
                        if (key !== 'page' && !params.has(key)) {
                            params.set(key, value);
                        }
                    });
                    // Susun ulang url
                    var finalUrl = urlObj.pathname + (params.toString() ? '?' + params.toString() : '');
                    doSearchAntrian(finalUrl);
                });
            });
            bindHasilAnalisaButtons();


            document.addEventListener('DOMContentLoaded', function() {
                bindAntrianPagination();
                bindHasilAnalisaButtons();
                // Pencarian AJAX pada input search
                var searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    var searchTimeout;
                    searchInput.addEventListener('input', function() {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(function() {
                            doSearchAntrian();
                        }, 500); // debounce 500ms
                    });
                    searchInput.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            doSearchAntrian();
                        }
                    });
                }
            });
        }
    });
</script>
@endsection
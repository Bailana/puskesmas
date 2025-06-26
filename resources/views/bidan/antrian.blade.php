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
                                <td id="detailPenanggungJawab"></td>
                            </tr>
                        </tbody>
                    </table>

                    <h5 id="headingHasilPeriksaAnak">Hasil Periksa Anak</h5>
                    <table class="table table-bordered" id="hasilPeriksaAnakTable">
                        <tbody>
                            <!-- Data hasil periksa anak akan dimasukkan di sini -->
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

                            // Add event listeners to Lihat buttons
                            riwayatList.querySelectorAll('.btnLihat').forEach(btnLihat => {
                                btnLihat.addEventListener('click', function() {
                                    const tanggal = this.getAttribute('data-tanggal');
                                    const noRekamMedis = this.getAttribute('data-norm');

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
                                            const dateObj = new Date(data.tanggal_periksa);
                                            const options = {
                                                weekday: 'long',
                                                year: 'numeric',
                                                month: '2-digit',
                                                day: '2-digit'
                                            };
                                            document.getElementById('detailTanggal').textContent = dateObj.toLocaleDateString('id-ID', options);
                                            document.getElementById('detailAnamnesis').textContent = data.anamnesis || '-';
                                            document.getElementById('detailPemeriksaanFisik').textContent = data.pemeriksaan_fisik || '-';
                                            document.getElementById('detailRencanaTerapi').textContent = data.rencana_dan_terapi || '-';
                                            document.getElementById('detailDiagnosis').textContent = data.diagnosis || '-';
                                            document.getElementById('detailEdukasi').textContent = data.edukasi || '-';
                                            document.getElementById('detailKodeICD').textContent = data.kode_icd || '-';

                                            // Kondisikan tampilkan atau sembunyikan heading dan tabel hasil analisa
                                            if (data.tekanan_darah || data.frekuensi_nadi || data.suhu || data.frekuensi_nafas || data.skor_nyeri || data.skor_jatuh || data.berat_badan || data.tinggi_badan || data.lingkar_kepala || data.imt || data.alat_bantu || data.prosthesa || data.cacat_tubuh || data.adl_mandiri || data.riwayat_jatuh || data.status_psikologi || data.hambatan_edukasi || data.alergi || data.catatan || data.poli_tujuan || data.penanggung_jawab_nama) {
                                                document.getElementById('headingHasilAnalisa').style.display = 'block';
                                                document.getElementById('tableHasilAnalisa').style.display = 'table';

                                            // Decode JSON fields for status_psikologi and hambatan_edukasi
                                            function decodeJsonField(field) {
                                                try {
                                                    const parsed = JSON.parse(field);
                                                    if (Array.isArray(parsed)) {
                                                        return parsed.join(', ');
                                                    }
                                                    return field;
                                                } catch {
                                                    return field;
                                                }
                                            }

                                            // Clear all hasil analisa input and textarea fields before setting new values
                                            const hasilAnalisaFields = [
                                                'detailTekananDarah', 'detailFrekuensiNadi', 'detailSuhu', 'detailFrekuensiNafas',
                                                'detailSkorNyeri', 'detailSkorJatuh', 'detailBeratBadan', 'detailTinggiBadan',
                                                'detailLingkarKepala', 'detailIMT', 'detailAlatBantu', 'detailProsthesa',
                                                'detailCacatTubuh', 'detailADLMandiri', 'detailRiwayatJatuh', 'detailStatusPsikologi',
                                                'detailHambatanEdukasi', 'detailAlergi', 'detailCatatan', 'detailPoliTujuan'
                                            ];
                                            hasilAnalisaFields.forEach(id => {
                                                const el = document.getElementById(id);
                                                if (el) {
                                                    if (el.tagName.toLowerCase() === 'input' || el.tagName.toLowerCase() === 'textarea') {
                                                        el.value = '';
                                                    } else {
                                                        el.textContent = '';
                                                    }
                                                }
                                            });

                                            // Set values for input and textarea fields
                                            document.getElementById('detailTekananDarah').textContent = data.tekanan_darah || '';
                                            document.getElementById('detailFrekuensiNadi').textContent = data.frekuensi_nadi || '';
                                            document.getElementById('detailSuhu').textContent = data.suhu || '';
                                            document.getElementById('detailFrekuensiNafas').textContent = data.frekuensi_nafas || '';
                                            document.getElementById('detailSkorNyeri').textContent = data.skor_nyeri || '';
                                            document.getElementById('detailSkorJatuh').textContent = data.skor_jatuh || '';
                                            document.getElementById('detailBeratBadan').textContent = data.berat_badan || '';
                                            document.getElementById('detailTinggiBadan').textContent = data.tinggi_badan || '';
                                            document.getElementById('detailLingkarKepala').textContent = data.lingkar_kepala || '';
                                            document.getElementById('detailIMT').textContent = data.imt || '';
                                            document.getElementById('detailAlatBantu').textContent = data.alat_bantu || '';
                                            document.getElementById('detailProsthesa').textContent = data.prosthesa || '';
                                            document.getElementById('detailCacatTubuh').textContent = data.cacat_tubuh || '';
                                            document.getElementById('detailADLMandiri').textContent = data.adl_mandiri || '';
                                            document.getElementById('detailRiwayatJatuh').textContent = data.riwayat_jatuh || '';
                                            document.getElementById('detailStatusPsikologi').textContent = decodeJsonField(data.status_psikologi) || '';
                                            document.getElementById('detailHambatanEdukasi').textContent = decodeJsonField(data.hambatan_edukasi) || '';
                                            document.getElementById('detailAlergi').textContent = data.alergi || '';
                                            document.getElementById('detailCatatan').textContent = data.catatan || '';
                                            document.getElementById('detailPoliTujuan').textContent = data.poli_tujuan || '';
                                            document.getElementById('detailPenanggungJawab').textContent = data.penanggung_jawab_nama || '';
                                            } else {
                                                document.getElementById('headingHasilAnalisa').style.display = 'none';
                                                document.getElementById('tableHasilAnalisa').style.display = 'none';
                                            }

                                            // Kondisikan tampilkan atau sembunyikan heading dan tabel hasil periksa
                                            if (data.anamnesis || data.pemeriksaan_fisik || data.rencana_dan_terapi || data.diagnosis || data.edukasi || data.kode_icd) {
                                                document.getElementById('headingHasilPeriksa').style.display = 'block';
                                                document.getElementById('tableHasilPeriksa').style.display = 'table';

                                                // Sembunyikan hasil periksa anak jika hasil periksa ada
                                                document.getElementById('headingHasilPeriksaAnak').style.display = 'none';
                                                document.getElementById('hasilPeriksaAnakTable').style.display = 'none';
                                            } else {
                                                // Sembunyikan tabel hasil periksa jika tidak ada data
                                                document.getElementById('headingHasilPeriksa').style.display = 'none';
                                                document.getElementById('tableHasilPeriksa').style.display = 'none';

                                                // Tampilkan hasil periksa anak jika hasil periksa tidak ada
                                                if (data.berat_badan_anak || data.makanan_anak || data.gejala_anak || data.nasehat_anak || data.pegobatan_anak) {
                                                    let anakData = `
                                                        <tr><th>Berat Badan Anak</th><td>${data.berat_badan_anak || '-'}</td></tr>
                                                        <tr><th>Makanan Anak</th><td>${data.makanan_anak || '-'}</td></tr>
                                                        <tr><th>Gejala Anak</th><td>${data.gejala_anak || '-'}</td></tr>
                                                        <tr><th>Nasehat Anak</th><td>${data.nasehat_anak || '-'}</td></tr>
                                                        <tr><th>Pengobatan Anak</th><td>${data.pegobatan_anak || '-'}</td></tr>
                                                    `;
                                                    // Tambahkan baris ini ke tabel hasil periksa anak
                                                    const tbody = document.querySelector('#hasilPeriksaAnakTable tbody');
                                                    tbody.innerHTML = ''; // Clear previous data
                                                    tbody.insertAdjacentHTML('beforeend', anakData);
                                                    document.getElementById('headingHasilPeriksaAnak').style.display = 'block';
                                                    document.getElementById('hasilPeriksaAnakTable').style.display = 'table';
                                                } else {
                                                    // Clear table if no data
                                                    const tbody = document.querySelector('#hasilPeriksaAnakTable tbody');
                                                    tbody.innerHTML = '';
                                                    document.getElementById('headingHasilPeriksaAnak').style.display = 'none';
                                                    document.getElementById('hasilPeriksaAnakTable').style.display = 'none';
                                                }
                                            }
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
                document.getElementById('btnTutupDetail').addEventListener('click', function() {
                    hasilPeriksaDetail.style.display = 'none';
                    riwayatList.style.display = 'block';
                });
            });
        });
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
            fetch('/bidan/hasilanalisa/' + encodeURIComponent(noRekamMedis), {
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
</script>
@endsection
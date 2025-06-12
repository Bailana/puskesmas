@extends('dashboardApotek')

@section('apoteker')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>Antrian Pasien</strong></h1>
    <div class="row">
        <div class="col-12 col-lg-12 col-xxl-12 d-flex">
                <div class="card flex-fill">
                    <div class="card-header d-flex justify-content-between">
                        <!-- Input Pencarian -->
                        <form method="GET" action="{{ route('apoteker.antrian') }}" class="d-flex align-items-center" style="gap: 10px;">
                            <div class="input-group" style="width: 250px;">
                                <input type="text" name="search" class="form-control" id="searchInput" placeholder="Pencarian..."
                                    aria-label="Search" value="{{ request('search') }}" autocomplete="off">
                            </div>
                        </form>
                    </div>

                <div class="table-responsive">
                    <table class="table table-hover my-0" id="antrianTable">
                        <thead>
                            <tr>
                                <th style="font-weight: 600; font-size: 0.875rem;">No.</th>
                                <th class="col-nomor-rm" style="font-weight: 600; font-size: 0.875rem;">Nomor RM</th>
                                <th class="col-nama-pasien" style="font-weight: 600; font-size: 0.875rem;">Nama Pasien
                                </th>
                                <th style="font-weight: 600; font-size: 0.875rem;">Umur</th>
                                <th class="col-jamkes" style="font-weight: 600; font-size: 0.875rem;">JamKes</th>
                                <th>Poli Tujuan</th>
                                <!-- Removed Poli Tujuan column as per user request -->
                                <th style="font-weight: 600; font-size: 0.875rem;">Status</th>
                                <th style="font-weight: 600; font-size: 0.875rem;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 0.875rem;">
                            @foreach ($antrians as $index => $antrian)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $antrian->no_rekam_medis }}</td>
                                <td>{{ $antrian->pasien->nama_pasien }}</td>
                                <td>{{ \Carbon\Carbon::parse($antrian->pasien->tanggal_lahir)->age }} tahun</td>
                                <td>{{ $antrian->pasien->jaminan_kesehatan }}</td>
                                <td>{{ $antrian->poli ? $antrian->poli->nama_poli : 'Tidak ada' }}</td>
                                <td><span class="badge bg-danger">{{ $antrian->status }}</span></td>
                                <td style="white-space: nowrap;">
                                    <button type="button" class="btn btn-success btn-sm rounded btnHasilPeriksa" data-bs-toggle="modal"
                                        data-bs-target="#modalPeriksaPasien" data-pasien-id="{{ $antrian->pasien->id }}">Hasil Periksa</button>
                                    <button type="button" class="btn btn-primary btn-sm rounded btnRacikObat"
                                        data-pasien-id="{{ $antrian->pasien->id }}">Racik Obat</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center w-50">
                            <div class="small text-muted mb-2 text-start ps-3">
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
                                    @foreach ($antrians->getUrlRange(1, $antrians->lastPage()) as $page => $url)
                                    @if ($page == $antrians->currentPage())
                                    <li class="page-item active" aria-current="page"><span
                                            class="page-link">{{ $page }}</span></li>
                                    @else
                                    <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                    @endforeach

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
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="lingkarKepala" class="form-label">Lingkar Kepala</label>
                            <input type="text" class="form-control form-control-sm" id="lingkarKepala" disabled>
                        </div>
                        <div class="col-6">
                            <label for="imt" class="form-label">IMT</label>
                            <input type="text" class="form-control form-control-sm" id="imt" disabled>
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
                                <input class="form-check-input" type="checkbox" id="checkboxDepresi">
                                <label class="form-check-label" for="checkboxDepresi">Depresi</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxTakut">
                                <label class="form-check-label" for="checkboxTakut">Takut</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxAgresif">
                                <label class="form-check-label" for="checkboxAgresif">Agresif</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxMelukaiDiri">
                                <label class="form-check-label" for="checkboxMelukaiDiri">Melukai diri sendiri/Orang
                                    lain</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <h5>Hambatan Edukasi</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxBahasa">
                                <label class="form-check-label" for="checkboxBahasa">Bahasa</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxCacatFisik">
                                <label class="form-check-label" for="checkboxCacatFisik">Cacat Fisik</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxCacatKognitif">
                                <label class="form-check-label" for="checkboxCacatKognitif">Cacat Kognitif</label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="alergi" class="form-label">Alergi</label>
                            <textarea type="text" class="form-control form-control-sm" id="alergi"></textarea>
                        </div>
                        <div class="col-6">
                            <label for="catatan" class="form-label">Catatan</label>
                            <textarea type="text" class="form-control form-control-sm" id="catatan"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hasil Periksa -->
<div class="modal fade" id="modalHasilPeriksa" tabindex="-1" aria-labelledby="modalHasilPeriksaLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
        <div class="modal-content" style="overflow-x: hidden;">
            <div class="modal-header d-flex justify-content-between">
                <h3 class="modal-title" id="modalHasilPeriksaLabel"><strong>Hasil Periksa</strong></h3>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow-y: auto; padding: 10px;">
                <form id="formHasilPeriksa">
                    <!-- Anamnesis -->
                    <div class="mb-3">
                        <label for="anamnesis" class="form-label">Anamnesis</label>
                        <textarea class="form-control" id="anamnesis" rows="3" readonly></textarea>
                    </div>

                    <!-- Pemeriksaan Fisik -->
                    <div class="mb-3">
                        <label for="pemeriksaanFisik" class="form-label">Pemeriksaan Fisik</label>
                        <textarea class="form-control" id="pemeriksaanFisik" rows="3" readonly></textarea>
                    </div>

                    <!-- Rencana dan Terapi -->
                    <div class="mb-3">
                        <label for="rencanaTerapi" class="form-label">Rencana dan Terapi</label>
                        <textarea class="form-control" id="rencanaTerapi" rows="3" readonly></textarea>
                    </div>

                    <!-- Diagnosis -->
                    <div class="mb-3">
                        <label for="diagnosis" class="form-label">Diagnosis</label>
                        <textarea class="form-control" id="diagnosis" rows="3" readonly></textarea>
                    </div>

                    <!-- Edukasi -->
                    <div class="mb-3">
                        <label for="edukasi" class="form-label">Edukasi</label>
                        <textarea class="form-control" id="edukasi" rows="3" readonly></textarea>
                    </div>

                    <!-- Code ICD -->
                    <div class="mb-3">
                        <label for="kodeICD" class="form-label">Kode ICD</label>
                        <input type="text" class="form-control form-control-sm" id="kodeICD" readonly>
                    </div>

                    <!-- Kesan Status Gizi -->
                    <div class="mb-3">
                        <label for="kesanStatusGizi" class="form-label">Kesan Status Gizi</label>
                        <input type="text" class="form-control form-control-sm" id="kesanStatusGizi" readonly>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-danger" id="btnTutupModal" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalHasilPeriksa = new bootstrap.Modal(document.getElementById('modalHasilPeriksa'));

        document.querySelectorAll('.btnHasilPeriksa').forEach(button => {
            button.addEventListener('click', function () {
                const pasienId = this.getAttribute('data-pasien-id');

                fetch(`/apoteker/hasil-periksa/${pasienId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Data hasil periksa tidak ditemukan');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Populate modal fields with data, all fields are read-only
                        document.getElementById('anamnesis').value = data.anamnesis || '';
                        document.getElementById('pemeriksaanFisik').value = data.pemeriksaan_fisik || '';
                        document.getElementById('rencanaTerapi').value = data.rencana_dan_terapi || '';
                        document.getElementById('diagnosis').value = data.diagnosis || '';
                        document.getElementById('edukasi').value = data.edukasi || '';
                        document.getElementById('kodeICD').value = data.kode_icd || '';
                        document.getElementById('kesanStatusGizi').value = data.kesan_status_gizi || '';
                        modalHasilPeriksa.show();
                    })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Data Tidak Ditemukan',
                                text: error.message,
                            });
                        });
            });
        });

        // AJAX search for antrian
        const searchInput = document.getElementById('searchInput');
        const antrianTableBody = document.querySelector('#antrianTable tbody');

        function renderTableRows(antrians) {
            antrianTableBody.innerHTML = '';
            if (antrians.length === 0) {
                antrianTableBody.innerHTML =
                    '<tr><td colspan="8" class="text-center">Data antrian tidak ditemukan</td></tr>';
                return;
            }
            antrians.forEach((antrian, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${antrian.no_rekam_medis}</td>
                    <td>${antrian.pasien.nama_pasien}</td>
                    <td>${antrian.pasien.tanggal_lahir ? new Date(antrian.pasien.tanggal_lahir).toLocaleDateString('id-ID') : 'Tanggal tidak tersedia'}</td>
                    <td>${antrian.pasien.jaminan_kesehatan}</td>
                    <td>${antrian.poli ? antrian.poli.nama_poli : 'Tidak ada'}</td>
                    <td><span class="badge bg-danger">${antrian.status}</span></td>
                    <td style="white-space: nowrap;">
                        <button type="button" class="btn btn-success btn-sm rounded btnHasilPeriksa" data-bs-toggle="modal"
                            data-bs-target="#modalPeriksaPasien" data-pasien-id="${antrian.pasien.id}">Hasil Periksa</button>
                        <button type="button" class="btn btn-primary btn-sm rounded btnRacikObat"
                            data-pasien-id="${antrian.pasien.id}">Racik Obat</button>
                    </td>
                `;
                antrianTableBody.appendChild(row);
            });
        }

        let debounceTimeout;
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => {
                const query = searchInput.value.trim();
                fetch(`{{ route('apoteker.antrian') }}?search=${encodeURIComponent(query)}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        renderTableRows(data.data);
                    })
                    .catch(error => {
                        console.error('Error fetching search results:', error);
                    });
            }, 300);
        });
    });
</script>
@endsection

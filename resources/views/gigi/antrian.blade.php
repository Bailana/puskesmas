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
                            @foreach ($antrians as $index => $antrian)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $antrian->no_rekam_medis }}</td>
                                <td>{{ $antrian->pasien->nama_pasien }}</td>
                                <td>{{ \Carbon\Carbon::parse($antrian->pasien->tanggal_lahir)->age }} tahun</td>
                                <td>{{ $antrian->pasien->jaminan_kesehatan }}</td>
                                <!-- Removed Poli Tujuan data cell as per user request -->
                                <td><span class="badge bg-warning">{{ $antrian->status }}</span></td>
                                <td>
                                    <button type="button" class="btn btn-success btn-sm rounded" data-bs-toggle="modal"
                                        data-bs-target="#modalAnalisa">Hasil Analisa</button>
                                    <button type="button" class="btn btn-primary btn-sm rounded btnPeriksa"
                                        data-bs-toggle="modal" data-bs-target="#modalPeriksaPasien"
                                        data-pasien-id="{{ $antrian->pasien->id }}">Periksa</button>
                                    <button type="button" class="btn btn-danger btn-sm rounded">Riwayat Berobat</button>

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

    <!-- Modal Periksa Pasien -->
    <div class="modal fade" id="modalPeriksaPasien" tabindex="-1" aria-labelledby="modalPeriksaPasienLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
            <div class="modal-content" style="overflow-x: hidden;">
                <div class="modal-header d-flex justify-content-between">
                    <h3 class="modal-title" id="modalPeriksaPasienLabel"><strong>Periksa Pasien</strong></h3>
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
                    <button type="button" class="btn btn-danger" id="btnTutupModal"
                        data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-success ms-2" id="btnSimpanPeriksa">Simpan</button>
                </div>
            </div>
        </div>
    </div>

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

            </div>
        </div>
    </div>

    @endsection

    @section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var periksaButtons = document.querySelectorAll('.btnPeriksa');
            var modalElement = document.getElementById('modalPeriksaPasien');
            var modal = new bootstrap.Modal(modalElement);

            periksaButtons.forEach(function (button) {
                button.addEventListener('click', function () {
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
            btnSimpanPeriksa.addEventListener('click', function () {
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
        document.addEventListener('DOMContentLoaded', function () {
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
        document.addEventListener('DOMContentLoaded', function () {
            var periksaButtons = document.querySelectorAll('.btn-periksa');
            var modalElement = document.getElementById('modalPeriksaPasien');
            var modal = new bootstrap.Modal(modalElement);

            periksaButtons.forEach(function (button) {
                button.addEventListener('click', function () {
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

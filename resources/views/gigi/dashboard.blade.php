@extends('dashboardGigi')

@section('gigi')
<div class="container-fluid p-0">

    <h1 class="h3 mb-3"><strong>Dashboard Dokter Gigi</strong></h1>

    <div class="row">
        <div class="col-xl-12 col-xxl-12 d-flex">
            <div class="w-100">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col mt-0">
                                        <h5 class="card-title">Poli Umum</h5>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat text-primary">
                                            <i class="align-middle" data-feather="truck"></i>
                                        </div>
                                    </div>
                                </div>
                                <h1 class="mt-1 mb-3">25</h1>
                                <div class="mb-0">
                                    <span class="text-muted">Pasien Telah Diperiksa</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col mt-0">
                                        <h5 class="card-title">Poli Gigi</h5>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat text-primary">
                                            <i class="align-middle" data-feather="dollar-sign"></i>
                                        </div>
                                    </div>
                                </div>
                                <h1 class="mt-1 mb-3">5</h1>
                                <div class="mb-0">
                                    <span class="text-muted">Pasien Telah Diperiksa</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col mt-0">
                                        <h5 class="card-title">Rawat Inap</h5>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat text-primary">
                                            <i class="align-middle" data-feather="dollar-sign"></i>
                                        </div>
                                    </div>
                                </div>
                                <h1 class="mt-1 mb-3">4</h1>
                                <div class="mb-0">
                                    <span class="text-muted">Pasien</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col mt-0">
                                        <h5 class="card-title">Rawat Jalan</h5>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat text-primary">
                                            <i class="align-middle" data-feather="dollar-sign"></i>
                                        </div>
                                    </div>
                                </div>
                                <h1 class="mt-1 mb-3">10</h1>
                                <div class="mb-0">
                                    <span class="text-muted">Pasien</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <h1 class="h3 mb-3"><strong>Antrian Pasien</strong></h1>
        <div class="col-12 col-lg-12 col-xxl-12 d-flex">
            <div class="card flex-fill">
                <table class="table table-hover my-0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nomor RM</th>
                            <th>Nama Pasien</th>
                            <th>Umur</th>
                            <th>JamKes</th>
                            
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($antrians as $index => $antrian)
                        @if ($antrian->status == 'Selesai')
                        @continue
                        @endif
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $antrian->no_rekam_medis }}</td>
                            <td>{{ $antrian->pasien->nama_pasien }}</td>
                            <td>{{ \Carbon\Carbon::parse($antrian->pasien->tanggal_lahir)->age }} tahun</td>
                            <td>{{ $antrian->pasien->jaminan_kesehatan }}</td>
                            <!-- Removed Poli Tujuan data cell as per user request -->
                            
                            <td>
                                @if ($antrian->status == 'Perlu Analisa')
                                <span class="badge bg-danger">{{ $antrian->status }}</span>
                                @elseif ($antrian->status == 'Sudah Analisa')
                                <span class="badge bg-primary">{{ $antrian->status }}</span>
                                @elseif ($antrian->status == 'Pemeriksaan')
                                <span class="badge bg-warning">{{ $antrian->status }}</span>
                                @else
                                <span class="badge bg-secondary">{{ $antrian->status }}</span>
                                <!-- Default color if not matched -->
                                @endif
                            </td>
                            <td><button type="button" class="btn btn-primary btn-sm rounded btn-periksa"
                                    data-pasien-id="{{ $antrian->pasien->id }}">Periksa</button></td>
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
                        <textarea class="form-control" id="pemeriksaanSubjektif" name="pemeriksaan_subjektif" rows="3"
                            required></textarea>
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
                <button type="button" class="btn btn-danger" id="btnTutupModal" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success ms-2" id="btnSimpanPeriksa">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var periksaButtons = document.querySelectorAll('.btn-periksa');
        var modalElement = document.getElementById('modalPeriksaPasien');
        var modal = new bootstrap.Modal(modalElement);

        periksaButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                modal.show();
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
@endsection

@extends('dashboardDokter')

@section('dokter')
<div class="container-fluid p-0">

    <h1 class="h3 mb-3"><strong>Dashboard Dokter</strong></h1>

    <div class="row">
        <div class="col-xl-12 col-xxl-12 d-flex">
            <div class="w-100">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col mt-0">
                                        <h5 class="card-title">Total Antrian</h5>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat text-primary">
                                            <i class="align-middle" data-feather="truck"></i>
                                        </div>
                                    </div>
                                </div>
                                <h1 class="mt-1 mb-3">20</h1>
                                <div class="mb-0">
                                    <span class="text-muted">Antrian</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col mt-0">
                                        <h5 class="card-title">Antrian Selesai</h5>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat text-primary">
                                            <i class="align-middle" data-feather="dollar-sign"></i>
                                        </div>
                                    </div>
                                </div>
                                <h1 class="mt-1 mb-3">5</h1>
                                <div class="mb-0">
                                    <span class="text-muted">Antrian</span>
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

    <div class="container-fluid p-0">
        <h1 class="h3 mb-3"><strong>Antrian Pasien</strong></h1>
        <div class="row">
            <div class="col-12 col-lg-12 col-xxl-12 d-flex">
                <div class="card flex-fill">
                    <div class="table-responsive">
                        <table class="table table-hover my-0">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nomor RM</th>
                                    <th>Nama Pasien</th>
                                    <th>Umur</th>
                                    <th>JamKes</th>
                                    <!-- Removed Poli Tujuan column as per user request -->
                                    <!-- <th>Tgl. Berobat</th> -->
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($antrians->count() == 0)
                                    <tr>
                                        <td colspan="7" class="text-center">Belum ada antrian pasien</td>
                                    </tr>
                                @else
                                    @foreach ($antrians as $index => $antrian)
                                    @if ($antrian->status == 'Selesai')
                                    @continue
                                    @endif
                                    <tr>
                                        <td style="white-space: nowrap;">{{ $antrians->firstItem() + $index }}</td>
                                        <td style="white-space: nowrap;">{{ $antrian->no_rekam_medis }}</td>
                                        <td style="white-space: nowrap;">{{ $antrian->pasien->nama_pasien }}</td>
                                        <td style="white-space: nowrap;">
                                            {{ \Carbon\Carbon::parse($antrian->pasien->tanggal_lahir)->age }} tahun</td>
                                        <td style="white-space: nowrap;">{{ $antrian->pasien->jaminan_kesehatan }}</td>
                                        <!-- Removed Poli Tujuan data cell as per user request -->
                                        <!-- <td style="white-space: nowrap;">
                                    {{ \Carbon\Carbon::parse($antrian->tanggal_berobat)->format('d-m-Y') }}</td> -->
                                        <td style="white-space: nowrap;">
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
                                        <td style="white-space: nowrap;"><button type="button"
                                                class="btn btn-primary btn-sm rounded btn-periksa"
                                                data-pasien-id="{{ $antrian->pasien->id }}">Periksa</button></td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center w-50">
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
                                        }
                                    @endphp

                                    @for ($page = $start; $page <= $end; $page++)
                                        @if ($page == $currentPage)
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
                        <option value="Gizi kurang/buruk">Gizi Kurang/Buruk</option>
                        <option value="Gizi cukup">Gizi Cukup</option>
                        <option value="Gizi lebih">Gizi Lebih</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="modal-footer d-flex justify-content-end mt-3">
            <button type="button" class="btn btn-success ms-2" id="btnSimpanPeriksa">Simpan</button>
        </div>
    </div>
    </div>
</div>

<style>
    .error-message {
        color: red;
        font-size: 0.875em;
        margin-top: 0.25rem;
    }
    .input-error {
        border: 1px solid red !important;
        box-shadow: 0 0 5px red !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        console.log('DOM fully loaded and parsed');
        const btnSimpanPeriksa = document.getElementById('btnSimpanPeriksa');
        const formPeriksaPasien = document.getElementById('formPeriksaPasien');
        const modalPeriksaPasien = new bootstrap.Modal(document.getElementById('modalPeriksaPasien'));

        function clearErrors() {
            const errorMessages = formPeriksaPasien.querySelectorAll('.error-message');
            errorMessages.forEach(msg => msg.remove());
            // Remove error highlight class from inputs
            const errorInputs = formPeriksaPasien.querySelectorAll('.input-error');
            errorInputs.forEach(input => input.classList.remove('input-error'));
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
        }

        function validateForm() {
            clearErrors();
            let isValid = true;

            const fields = [
                { id: 'anamnesis', name: 'Anamnesis' },
                { id: 'pemeriksaanFisik', name: 'Pemeriksaan Fisik' },
                { id: 'rencanaTerapi', name: 'Rencana dan Terapi' },
                { id: 'diagnosis', name: 'Diagnosis' },
                { id: 'edukasi', name: 'Edukasi' },
                { id: 'kodeICD', name: 'Kode ICD' },
                { id: 'kesanStatusGizi', name: 'Kesan Status Gizi' }
            ];

            fields.forEach(field => {
                const input = document.getElementById(field.id);
                if (!input.value || input.value.trim() === '') {
                    showError(input, 'Field ini wajib diisi.');
                    isValid = false;
                }
            });

            return isValid;
        }

        if (btnSimpanPeriksa) {
            console.log('btnSimpanPeriksa found, attaching event listener');
            btnSimpanPeriksa.addEventListener('click', function () {
                console.log('btnSimpanPeriksa clicked');

                if (!validateForm()) {
                    console.log('Validation failed');
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
                        console.log('Success:', data);
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
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: error.message,
                        });
                    });
            });
        } else {
            console.error('btnSimpanPeriksa not found');
        }

        function getSelectedPasienId() {
            return window.selectedPasienId || null;
        }

        document.querySelectorAll('.btn-periksa').forEach(button => {
            button.addEventListener('click', function () {
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

</script>
@endsection

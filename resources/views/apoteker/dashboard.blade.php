@extends('dashboardApotek')

@section('apoteker')
<div class="container-fluid p-0">

    @if(session('status'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            toastr.options = {
                "positionClass": "toast-top-right",
                "timeOut": "3000",
                "closeButton": true,
                "progressBar": true
            };
            toastr.success("{{ session('status') }}");
        });
    </script>
    @endif

    <h1 class="h3 mb-3"><strong>Dashboard Apoteker</strong></h1>

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
                                <h1 class="mt-1 mb-3">20</h1>
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

    <div class="container-fluid p-0">
        <h1 class="h3 mb-3"><strong>Antrian Pasien</strong></h1>
        <div class="row">
            <div class="col-12 col-lg-12 col-xxl-12 d-flex">
                <div class="card flex-fill">
                    <div class="table-responsive">
                        <table class="table table-hover my-0">
                            <thead>
                                <tr>
                                    <th style="white-space: nowrap;">No.</th>
                                    <th style="white-space: nowrap;">Nomor RM</th>
                                    <th style="white-space: nowrap;">Nama Pasien</th>
                                    <th style="white-space: nowrap;">Umur</th>
                                    <th style="white-space: nowrap;">JamKes</th>
                                    <th style="white-space: nowrap;">Poli Tujuan</th>
                                    <th style="white-space: nowrap;">Status</th>
                                    <th style="white-space: nowrap;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($antrians as $index => $antrian)
                                @if ($antrian->status == 'Selesai')
                                @continue
                                @endif
                                <tr>
                                    <td style="white-space: nowrap;">{{ $index + 1 }}</td>
                                    <td style="white-space: nowrap;">{{ $antrian->no_rekam_medis }}</td>
                                    <td style="white-space: nowrap;">{{ $antrian->pasien->nama_pasien }}</td>
                                    <td style="white-space: nowrap;">{{ \Carbon\Carbon::parse($antrian->pasien->tanggal_lahir)->age }} tahun</td>
                                    <td style="white-space: nowrap;">{{ $antrian->pasien->jaminan_kesehatan }}</td>
                                    <td style="white-space: nowrap;">{{ $antrian->poli ? $antrian->poli->nama_poli : 'Tidak ada' }}</td>
                                    <td style="white-space: nowrap;">
                                        <span class="badge bg-danger">{{ $antrian->status }}</span>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <button type="button" class="btn btn-success btn-sm rounded btnHasilPeriksa" data-bs-toggle="modal"
                                            data-bs-target="#modalHasilPeriksa" data-pasien-id="{{ $antrian->pasien->id }}">Hasil Periksa</button>
                                        <button type="button" class="btn btn-primary btn-sm rounded btnRacikObat"
                                            data-bs-toggle="modal" data-bs-target="#modalRacikObat"
                                            data-pasien-id="{{ $antrian->pasien->id }}">Racik Obat</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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

    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="card flex-fill w-100">
                <div class="card-header">
                    <h5 class="card-title">Line Chart</h5>
                    <!-- <h6 class="card-subtitle text-muted">A line chart is a way of plotting data points on a line.</h6> -->
                </div>
                <div class="card-body">
                    <div class="chart chart-sm">
                        <canvas id="chartjs-line"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card ">
                <div class="card-header">
                    <h5 class="card-title">Pasien Poli</h5>
                    <!-- <h6 class="card-subtitle text-muted">Doughnut charts are excellent at showing the relational proportions
                    between data.</h6> -->
                </div>
                <div class="card-body">
                    <div class="chart chart-sm">
                        <canvas id="chartjs-doughnut"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hasil Periksa -->
<div class="modal fade" id="modalHasilPeriksa" tabindex="-1" aria-labelledby="modalHasilPeriksaLabel" aria-hidden="true">
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

<div class="modal fade" id="modalRacikObat" tabindex="-1" aria-labelledby="modalRacikObatLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
        <div class="modal-content" style="overflow-x: hidden;">
            <div class="modal-header d-flex justify-content-between">
                <h3 class="modal-title" id="modalRacikObatLabel"><strong>Resep Obat Pasien</strong></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow-y: auto; padding: 10px;">
                <div id="resepObatContent">
                    <table class="table table-bordered" id="resepObatTable">
                        <thead>
                            <tr>
                                <th>Nama Obat</th>
                                <th>Bentuk Obat</th>
                                <th>Jumlah</th>
                                <th>Harga Satuan</th>
                                <th>Total Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Baris obat akan diisi di sini -->
                        </tbody>
                    </table>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="totalBiaya" class="form-label">Total Biaya Keseluruhan</label>
                            <input type="text" class="form-control form-control-sm" id="totalBiaya" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="statusPembayaran" class="form-label">Status Pembayaran</label>
                            <input type="text" class="form-control form-control-sm" id="statusPembayaran" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-primary" id="btnSiapkanObat">Siapkan Obat</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalHasilPeriksa = new bootstrap.Modal(document.getElementById('modalHasilPeriksa'));

        document.querySelectorAll('.btnHasilPeriksa').forEach(button => {
            button.addEventListener('click', function() {
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

        const modalRacikObat = new bootstrap.Modal(document.getElementById('modalRacikObat'));

        document.querySelectorAll('.btnRacikObat').forEach(button => {
            button.addEventListener('click', function() {
                const pasienId = this.getAttribute('data-pasien-id');

                // Clear previous modal content
                const tbody = document.querySelector('#resepObatTable tbody');
                tbody.innerHTML = '';
                document.getElementById('totalBiaya').value = '';
                document.getElementById('statusPembayaran').value = '';

                fetch(`/apoteker/tagihan/${pasienId}`, {
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errData => {
                                throw new Error(errData.message || 'Data tagihan tidak ditemukan');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (Array.isArray(data.resep_obat)) {
                            let totalBiayaKeseluruhan = 0;
                            data.resep_obat.forEach(item => {
                                const tr = document.createElement('tr');
                                const jumlah = parseFloat(item.jumlah) || 0;
                                const hargaSatuan = parseFloat(item.harga_satuan) || 0;
                                const totalHarga = jumlah * hargaSatuan;
                                tr.innerHTML = `
                                    <td>${item.nama_obat}</td>
                                    <td>${item.bentuk_obat || ''}</td>
                                    <td>${jumlah}</td>
                                    <td>${hargaSatuan.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })}</td>
                                    <td>${totalHarga.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })}</td>
                                `;
                                tbody.appendChild(tr);
                                totalBiayaKeseluruhan += totalHarga;
                            });
                            document.getElementById('totalBiaya').value = totalBiayaKeseluruhan.toLocaleString('id-ID', {
                                style: 'currency',
                                currency: 'IDR'
                            });
                        }
                        document.getElementById('statusPembayaran').value = data.status_pembayaran || '';
                        modalRacikObat.show();
                    })
                    .catch(error => {
                        alert(error.message || 'Gagal mengambil data tagihan.');
                    });
            });
        });

        // Store pasienId for use in Siapkan Obat button
        let currentPasienId = null;

        document.querySelectorAll('.btnRacikObat').forEach(button => {
            button.addEventListener('click', function() {
                currentPasienId = this.getAttribute('data-pasien-id');
            });
        });

        document.getElementById('btnSiapkanObat').addEventListener('click', function() {
            if (!currentPasienId) {
                alert('Pasien tidak ditemukan.');
                return;
            }

            fetch(`/apoteker/antrian/update-status/${currentPasienId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({})
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errData => {
                        throw new Error(errData.message || 'Gagal memperbarui status antrian.');
                    });
                }
                return response.json();
            })
            .then(data => {
                alert(data.message || 'Status antrian berhasil diperbarui.');
                // Close modal
                const modalRacikObat = bootstrap.Modal.getInstance(document.getElementById('modalRacikObat'));
                modalRacikObat.hide();
                // Optionally reload page or update UI
                location.reload();
            })
            .catch(error => {
                alert(error.message);
            });
        });
    });
</script>
@endsection

@extends('dashboardKasir')

@section('kasir')
<div class="container-fluid p-0">

    <h1 class="h3 mb-3"><strong>Dashboard Kasir</strong></h1>

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
                                    <td style="white-space: nowrap;">{{ $antrians->firstItem() + $index }}</td>
                                    <td style="white-space: nowrap;">{{ $antrian->no_rekam_medis }}</td>
                                    <td style="white-space: nowrap;">{{ $antrian->pasien->nama_pasien }}</td>
                                    <td style="white-space: nowrap;">{{ \Carbon\Carbon::parse($antrian->pasien->tanggal_lahir)->age }} tahun</td>
                                    <td style="white-space: nowrap;">{{ $antrian->pasien->jaminan_kesehatan }}</td>
                                    <td style="white-space: nowrap;">{{ $antrian->poli ? $antrian->poli->nama_poli : 'Tidak ada' }}</td>
                                    <td style="white-space: nowrap;">
                                        <span class="badge bg-danger">{{ $antrian->status }}</span>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <button type="button" class="btn btn-success btn-sm rounded btnHasilPeriksa"
                                            data-pasien-id="{{ $antrian->pasien->id }}">Hasil Periksa</button>
                                        <button type="button" class="btn btn-warning btn-sm rounded btnPeriksa" data-bs-toggle="modal" data-bs-target="#modalTagihan" data-pasien-id="{{ $antrian->pasien->id }}">Tagihan</button>
                                    </td>
                                </tr>
                                @endforeach
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
    </div>

</div>

<!-- Modal Hasil Periksa -->
<div class="modal fade" id="modalHasilPeriksa" tabindex="-1" aria-labelledby="modalHasilPeriksaLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
        <div class="modal-content" style="overflow-x: hidden;">
            <div class="modal-header d-flex justify-content-between">
                <h3 class="modal-title" id="modalHasilPeriksaLabel"><strong>Hasil Periksa</strong></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

                    <!-- Penanggung Jawab (Dokter) -->
                    <div class="mb-3">
                        <label for="penanggungJawab" class="form-label">Penanggung Jawab</label>
                        <input type="text" class="form-control form-control-sm" id="penanggungJawab" readonly>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tagihan -->
<div class="modal fade" id="modalTagihan" tabindex="-1" aria-labelledby="modalTagihanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
        <div class="modal-content" style="overflow-x: hidden;">
            <div class="modal-header d-flex justify-content-between">
                <h3 class="modal-title" id="modalTagihanLabel"><strong>Tagihan Pasien</strong></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow-y: auto; padding: 10px;">
                <div id="tagihanContent">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="tagihanNamaPasien" class="form-label">Nama Pasien</label>
                            <input type="text" class="form-control form-control-sm" id="tagihanNamaPasien" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="tagihanNomorRM" class="form-label">Nomor RM</label>
                            <input type="text" class="form-control form-control-sm" id="tagihanNomorRM" readonly>
                            <input type="hidden" id="tagihanPasienId" />
                        </div>
                        <div class="col-md-4">
                            <label for="tagihanPoliTujuan" class="form-label">Poli Tujuan</label>
                            <input type="text" class="form-control form-control-sm" id="tagihanPoliTujuan" readonly>
                        </div>
                    </div>
                    <hr>
                    <h5>Daftar Resep Obat</h5>
                    <table class="table table-bordered" id="tagihanObatTable">
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
                            <!-- Obat rows will be populated here -->
                        </tbody>
                    </table>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tagihanTotalBiaya" class="form-label">Total Biaya Keseluruhan</label>
                            <input type="text" class="form-control form-control-sm" id="tagihanTotalBiaya" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="tagihanStatus" class="form-label">Status Pembayaran</label>
                            <input type="text" class="form-control form-control-sm" id="tagihanStatus" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-primary" id="btnBayar">Bayar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalHasilPeriksa = new bootstrap.Modal(document.getElementById('modalHasilPeriksa'));
        const modalTagihan = new bootstrap.Modal(document.getElementById('modalTagihan'));

        // Fix to allow background scrolling when modal is open without affecting navbar/footer
        const originalBodyOverflow = document.body.style.overflow;
        const originalBodyPaddingRight = document.body.style.paddingRight;
        document.getElementById('modalHasilPeriksa').addEventListener('shown.bs.modal', function() {
            // Keep scrollbar visible by restoring overflow and paddingRight
            document.body.style.overflow = originalBodyOverflow || 'auto';
            document.body.style.paddingRight = originalBodyPaddingRight || '0px';
        });
        document.getElementById('modalHasilPeriksa').addEventListener('hidden.bs.modal', function() {
            // Restore original overflow and paddingRight styles
            document.body.style.overflow = originalBodyOverflow;
            document.body.style.paddingRight = originalBodyPaddingRight;
        });

        document.querySelectorAll('.btnHasilPeriksa').forEach(button => {
            button.addEventListener('click', function() {
                const pasienId = this.getAttribute('data-pasien-id');

                // Clear previous values
                document.getElementById('anamnesis').value = '';
                document.getElementById('pemeriksaanFisik').value = '';
                document.getElementById('rencanaTerapi').value = '';
                document.getElementById('diagnosis').value = '';
                document.getElementById('edukasi').value = '';
                document.getElementById('kodeICD').value = '';
                document.getElementById('kesanStatusGizi').value = '';

                fetch(`/kasir/hasil-periksa/${pasienId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Data hasil periksa tidak ditemukan');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Populate modal fields with data, all fields are read-only
                        document.getElementById('anamnesis').value = data.anamnesis || '';
                        document.getElementById('pemeriksaanFisik').value = data
                            .pemeriksaan_fisik || '';
                        document.getElementById('rencanaTerapi').value = data
                            .rencana_dan_terapi || '';
                        document.getElementById('diagnosis').value = data.diagnosis || '';
                        document.getElementById('edukasi').value = data.edukasi || '';
                        document.getElementById('kodeICD').value = data.kode_icd || '';
                        document.getElementById('kesanStatusGizi').value = data
                            .kesan_status_gizi || '';
                        document.getElementById('penanggungJawab').value = data
                            .penanggung_jawab || '';
                        modalHasilPeriksa.show();
                    })
                    .catch(error => {
                        toastr.error('Hasil periksa pasien tidak tersedia.');
                        // Ensure modal is hidden if previously shown
                        if (modalHasilPeriksa._isShown) {
                            modalHasilPeriksa.hide();
                        }
                    });
            });
        });

        document.querySelectorAll('.btnPeriksa').forEach(button => {
            button.addEventListener('click', function() {
                const pasienId = this.getAttribute('data-pasien-id');

                // Clear previous values
                document.getElementById('tagihanNamaPasien').value = '';
                document.getElementById('tagihanNomorRM').value = '';
                document.getElementById('tagihanPoliTujuan').value = '';
                document.querySelector('#tagihanObatTable tbody').innerHTML = '';
                document.getElementById('tagihanTotalBiaya').value = '';
                document.getElementById('tagihanStatus').value = '';

                fetch(`/kasir/tagihan/${pasienId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Data tagihan tidak ditemukan');
                        }
                        return response.json();
                    })
                    .then(data => {
                        document.getElementById('tagihanNamaPasien').value = data.nama_pasien || '';
                        document.getElementById('tagihanNomorRM').value = data.no_rekam_medis || '';
                        document.getElementById('tagihanPasienId').value = data.pasien_id || '';
                        document.getElementById('tagihanPoliTujuan').value = data.poli_tujuan || '';

                        const tbody = document.querySelector('#tagihanObatTable tbody');
                        tbody.innerHTML = '';

                        function formatPrice(price) {
                            if (typeof price === 'number' || typeof price === 'string') {
                                const num = typeof price === 'number' ? price : parseFloat(price);
                                if (!isNaN(num)) {
                                    return new Intl.NumberFormat('id-ID', {
                                        style: 'currency',
                                        currency: 'IDR'
                                    }).format(num);
                                }
                            }
                            return price || '';
                        }

                        if (Array.isArray(data.resep_obat)) {
                            data.resep_obat.forEach(item => {
                                const tr = document.createElement('tr');
                                const jumlah = parseFloat(item.jumlah) || 0;
                                const hargaSatuan = parseFloat(item.harga_satuan) || 0;
                                const totalHargaCalc = jumlah * hargaSatuan;
                                tr.innerHTML = `
                                        <td>${item.nama_obat}</td>
                                        <td>${item.bentuk_obat || ''}</td>
                                        <td>${jumlah}</td>
                                        <td>${formatPrice(hargaSatuan)}</td>
                                        <td>${formatPrice(totalHargaCalc)}</td>
                                    `;
                                tbody.appendChild(tr);
                            });
                        }

                        let totalBiayaKeseluruhan = 0;
                        if (Array.isArray(data.resep_obat)) {
                            data.resep_obat.forEach(item => {
                                const jumlah = parseFloat(item.jumlah) || 0;
                                const hargaSatuan = parseFloat(item.harga_satuan) || 0;
                                totalBiayaKeseluruhan += jumlah * hargaSatuan;
                            });
                        }
                        document.getElementById('tagihanTotalBiaya').value = formatPrice(totalBiayaKeseluruhan);
                        document.getElementById('tagihanStatus').value = data.status_pembayaran || '';

                        modalTagihan.show();
                    })
                    .catch(error => {
                        toastr.error('Tagihan pasien tidak tersedia.');
                        if (modalTagihan._isShown) {
                            modalTagihan.hide();
                        }
                    });
            });
        });

        document.getElementById('btnBayar').addEventListener('click', function() {
            const pasienId = document.getElementById('tagihanPasienId').value;
            const totalBiaya = document.getElementById('tagihanTotalBiaya').value;
            const poliTujuan = document.getElementById('tagihanPoliTujuan').value;
            const resepObatRows = document.querySelectorAll('#tagihanObatTable tbody tr');

            if (!pasienId) {
                toastr.error('Data pasien tidak valid.');
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                toastr.error('CSRF token tidak ditemukan. Pastikan meta tag CSRF ada di halaman.');
                return;
            }

            // Collect resep_obat data from table rows
            const resepObat = [];
            resepObatRows.forEach(row => {
                const cells = row.querySelectorAll('td');
                resepObat.push({
                    nama_obat: cells[0].textContent,
                    bentuk_obat: cells[1].textContent,
                    jumlah: cells[2].textContent,
                    harga_satuan: cells[3].textContent,
                    total_harga: cells[4].textContent,
                });
            });

            fetch('/kasir/tagihan/bayar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    },
                    body: JSON.stringify({
                        pasien_id: pasienId,
                        total_biaya: totalBiaya,
                        poli_tujuan: poliTujuan,
                        resep_obat: JSON.stringify(resepObat),
                    }),
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Gagal melakukan pembayaran.');
                    }
                    return response.json();
                })
                .then(data => {
                    toastr.success(data.message || 'Pembayaran berhasil');
                    // Update status pembayaran in modal
                    document.getElementById('tagihanStatus').value = 'Lunas';
                    // Close the modal after successful payment
                    const modalTagihanEl = document.getElementById('modalTagihan');
                    const modalTagihan = bootstrap.Modal.getInstance(modalTagihanEl);
                    if (modalTagihan) {
                        modalTagihan.hide();
                    }
                    // Refresh the antrian table to reflect updated status
                    const searchInput = document.getElementById('searchInput');
                    const query = searchInput ? searchInput.value.trim() : '';
                    fetch(`{{ route('kasir.antrian') }}?search=${encodeURIComponent(query)}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            const antrianTableBody = document.querySelector('table tbody');
                            antrianTableBody.innerHTML = '';
                            if (data.data && data.data.length > 0) {
                                data.data.forEach((antrian, index) => {
                                    const age = antrian.pasien.tanggal_lahir ? new Date().getFullYear() - new Date(antrian.pasien.tanggal_lahir).getFullYear() : '';
                                    const row = document.createElement('tr');
                                    row.innerHTML = `
                                        <td style="white-space: nowrap;">${index + 1}</td>
                                        <td style="white-space: nowrap;">${antrian.no_rekam_medis}</td>
                                        <td style="white-space: nowrap;">${antrian.pasien.nama_pasien}</td>
                                        <td style="white-space: nowrap;">${age} tahun</td>
                                        <td style="white-space: nowrap;">${antrian.pasien.jaminan_kesehatan}</td>
                                        <td style="white-space: nowrap;">${antrian.poli ? antrian.poli.nama_poli : 'Tidak ada'}</td>
                                        <td style="white-space: nowrap;"><span class="badge bg-danger">${antrian.status}</span></td>
                                        <td style="white-space: nowrap;">
                                            <button type="button" class="btn btn-success btn-sm rounded btnHasilPeriksa" data-pasien-id="${antrian.pasien.id}">Hasil Periksa</button>
                                            <button type="button" class="btn btn-warning btn-sm rounded btnPeriksa" data-bs-toggle="modal" data-bs-target="#modalTagihan" data-pasien-id="${antrian.pasien.id}">Tagihan</button>
                                        </td>
                                    `;
                                    antrianTableBody.appendChild(row);
                                });
                                // Reattach event listeners for new buttons after table refresh
                                document.querySelectorAll('.btnHasilPeriksa').forEach(button => {
                                    button.addEventListener('click', function() {
                                        const pasienId = this.getAttribute('data-pasien-id');
                                        // Clear previous values
                                        document.getElementById('anamnesis').value = '';
                                        document.getElementById('pemeriksaanFisik').value = '';
                                        document.getElementById('rencanaTerapi').value = '';
                                        document.getElementById('diagnosis').value = '';
                                        document.getElementById('edukasi').value = '';
                                        document.getElementById('kodeICD').value = '';
                                        document.getElementById('kesanStatusGizi').value = '';
                                        fetch(`/kasir/hasil-periksa/${pasienId}`)
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
                                                document.getElementById('penanggungJawab').value = data.penanggung_jawab || '';
                                                modalHasilPeriksa.show();
                                            })
                                            .catch(error => {
                                                toastr.error('Hasil periksa pasien tidak tersedia.');
                                                if (modalHasilPeriksa._isShown) {
                                                    modalHasilPeriksa.hide();
                                                }
                                            });
                                    });
                                });
                                document.querySelectorAll('.btnPeriksa').forEach(button => {
                                    button.addEventListener('click', function() {
                                        const pasienId = this.getAttribute('data-pasien-id');
                                        // Clear previous values
                                        document.getElementById('tagihanNamaPasien').value = '';
                                        document.getElementById('tagihanNomorRM').value = '';
                                        document.getElementById('tagihanPoliTujuan').value = '';
                                        document.querySelector('#tagihanObatTable tbody').innerHTML = '';
                                        document.getElementById('tagihanTotalBiaya').value = '';
                                        document.getElementById('tagihanStatus').value = '';
                                        fetch(`/kasir/tagihan/${pasienId}`)
                                            .then(response => {
                                                if (!response.ok) {
                                                    throw new Error('Data tagihan tidak ditemukan');
                                                }
                                                return response.json();
                                            })
                                            .then(data => {
                                                document.getElementById('tagihanNamaPasien').value = data.nama_pasien || '';
                                                document.getElementById('tagihanNomorRM').value = data.no_rekam_medis || '';
                                                document.getElementById('tagihanPasienId').value = data.pasien_id || '';
                                                document.getElementById('tagihanPoliTujuan').value = data.poli_tujuan || '';
                                                const tbody = document.querySelector('#tagihanObatTable tbody');
                                                tbody.innerHTML = '';

                                                function formatPrice(price) {
                                                    if (typeof price === 'number' || typeof price === 'string') {
                                                        const num = typeof price === 'number' ? price : parseFloat(price);
                                                        if (!isNaN(num)) {
                                                            return new Intl.NumberFormat('id-ID', {
                                                                style: 'currency',
                                                                currency: 'IDR'
                                                            }).format(num);
                                                        }
                                                    }
                                                    return price || '';
                                                }
                                                if (Array.isArray(data.resep_obat)) {
                                                    data.resep_obat.forEach(item => {
                                                        const tr = document.createElement('tr');
                                                        const jumlah = parseFloat(item.jumlah) || 0;
                                                        const hargaSatuan = parseFloat(item.harga_satuan) || 0;
                                                        const totalHargaCalc = jumlah * hargaSatuan;
                                                        tr.innerHTML = `
                                                            <td>${item.nama_obat}</td>
                                                            <td>${item.bentuk_obat || ''}</td>
                                                            <td>${jumlah}</td>
                                                            <td>${formatPrice(hargaSatuan)}</td>
                                                            <td>${formatPrice(totalHargaCalc)}</td>
                                                        `;
                                                        tbody.appendChild(tr);
                                                    });
                                                }
                                                let totalBiayaKeseluruhan = 0;
                                                if (Array.isArray(data.resep_obat)) {
                                                    data.resep_obat.forEach(item => {
                                                        const jumlah = parseFloat(item.jumlah) || 0;
                                                        const hargaSatuan = parseFloat(item.harga_satuan) || 0;
                                                        totalBiayaKeseluruhan += jumlah * hargaSatuan;
                                                    });
                                                }
                                                document.getElementById('tagihanTotalBiaya').value = formatPrice(totalBiayaKeseluruhan);
                                                document.getElementById('tagihanStatus').value = data.status_pembayaran || '';
                                                modalTagihan.show();
                                            })
                                            .catch(error => {
                                                toastr.error('Tagihan pasien tidak tersedia.');
                                                if (modalTagihan._isShown) {
                                                    modalTagihan.hide();
                                                }
                                            });
                                    });
                                });
                            } else {
                                antrianTableBody.innerHTML = '<tr><td colspan="8" class="text-center">Antrian pasien tidak ditemukan</td></tr>';
                            }
                        })
                        .catch(error => {
                            console.error('Error refreshing antrian table:', error);
                        });
                })
                .catch(error => {
                    toastr.error(error.message || 'Terjadi kesalahan saat melakukan pembayaran.');
                });
        });
    });
</script>
@endsection
@extends('dashboardKasir')

@section('kasir')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>Rekapan Tagihan Pasien</strong></h1>
    <div class="row">
        <div class="col-12 col-lg-12 col-xxl-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex justify-content-between">
                    <!-- Input Pencarian -->
                    <form method="GET" action="{{ route('kasir.dana') }}" class="d-flex align-items-center" style="gap: 10px;" id="searchForm">
                        <div class="input-group" style="width: 250px;">
                            <input type="text" name="search" class="form-control" id="searchInput" placeholder="Pencarian..." aria-label="Search" autocomplete="off" value="{{ request('search') }}">
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" id="filterButton" title="Filter Data Tagihan" data-bs-toggle="modal" data-bs-target="#filterModal">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('kasir.dana.exportPdf', request()->query()) }}" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1" title="Export PDF" target="_blank">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                        <a href="{{ route('kasir.dana.exportExcel', request()->query()) }}" class="btn btn-outline-success btn-sm d-flex align-items-center gap-1" title="Export Excel" target="_blank">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover my-0" id="kasirDana" style="font-size: 0.875rem;">
                        <thead>
                            <tr>
                                <th style="white-space: nowrap;">No.</th>
                                <th style="white-space: nowrap;">Hari/Tanggal</th>
                                <th style="white-space: nowrap;">No. RM</th>
                                <th style="white-space: nowrap;">Nama Pasien</th>
                                <th style="white-space: nowrap;">JamKes</th>
                                <!-- Removed Poli Tujuan column header -->
                                <th style="white-space: nowrap;">Total Biaya</th>
                                <th style="white-space: nowrap;">Status Pembayaran</th>
                                <th style="white-space: nowrap;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($tagihans->isEmpty())
                            <tr>
                                <td colspan="7" class="text-center">Data tagihan tidak ditemukan</td>
                            </tr>
                            @else
                            @foreach ($tagihans as $index => $tagihan)
                            <tr>
                                <td style="white-space: nowrap;">{{ $tagihans->firstItem() + $index }}</td>
                                <td style="white-space: nowrap;">
                                    {{ \Carbon\Carbon::parse($tagihan->created_at)->locale('id')->isoFormat('dddd, DD-MM-YYYY') }}
                                </td>
                                <td style="white-space: nowrap;">{{ $tagihan->pasien->no_rekam_medis ?? '-' }}</td>
                                <td style="white-space: nowrap;">{{ $tagihan->pasien->nama_pasien ?? '-' }}</td>
                                <td style="white-space: nowrap;">{{ $tagihan->pasien->jaminan_kesehatan ?? '-' }}</td>
                                <!-- Removed Poli Tujuan column data -->
                                <td style="white-space: nowrap;">{{ 'Rp ' . number_format($tagihan->total_biaya * 1000, 2, ',', '.') }}</td>
                                <td style="white-space: nowrap;">
                                    @if(strtolower($tagihan->status) == 'lunas')
                                    <span class="badge bg-success">Lunas</span>
                                    @else
                                    <span class="badge bg-warning text-dark">Belum Lunas</span>
                                    @endif
                                </td>
                                <td style="white-space: nowrap;">
                                    <button type="button" class="btn btn-primary btn-sm rounded btnSelengkapnya" data-pasien-id="{{ $tagihan->pasien_id }}" title="Selengkapnya">Selengkapnya</button>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center w-50">
                        <div class="small text-muted mb-2 text-start ps-3 pagination-info-text" style="max-width: 50%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            Showing {{ $tagihans->firstItem() }} to {{ $tagihans->lastItem() }} of {{ $tagihans->total() }} results
                        </div>
                        <nav class="d-flex justify-content-center">
                            <ul class="pagination d-flex flex-row gap-2" style="list-style-type: none; padding-left: 0; margin-bottom: 0; flex-wrap: nowrap; overflow-x: auto;">
                                {{-- Previous Page Link --}}
                                @if ($tagihans->onFirstPage())
                                <li class="page-item disabled" aria-disabled="true" aria-label="Previous">
                                    <span class="page-link" aria-hidden="true">&laquo;</span>
                                </li>
                                @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $tagihans->previousPageUrl() }}" rel="prev" aria-label="Previous">&laquo;</a>
                                </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @php
                                $totalPages = $tagihans->lastPage();
                                $currentPage = $tagihans->currentPage();
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
                                    <li class="page-item"><a class="page-link" href="{{ $tagihans->url($page) }}">{{ $page }}</a></li>
                                    @endif
                                    @endfor

                                    {{-- Next Page Link --}}
                                    @if ($tagihans->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $tagihans->nextPageUrl() }}" rel="next" aria-label="Next">&raquo;</a>
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

<!-- Modal Detail Tagihan -->
<div class="modal fade" id="modalTagihanDetail" tabindex="-1" aria-labelledby="modalTagihanDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
        <div class="modal-content" style="overflow-x: hidden;">
            <div class="modal-header d-flex justify-content-between">
                <h3 class="modal-title" id="modalTagihanDetailLabel"><strong>Detail Tagihan Pasien</strong></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body with Scroll -->
            <div class="modal-body p-3" style="max-height: 400px; overflow-y: auto;">
                <form>
                    <div class="container-fluid">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="detailHariTanggal" class="form-label">Hari/Tanggal</label>
                                <input type="text" class="form-control form-control-sm" id="detailHariTanggal" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="detailNoRM" class="form-label">No. Rekam Medis</label>
                                <input type="text" class="form-control form-control-sm" id="detailNoRM" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="detailNamaPasien" class="form-label">Nama Pasien</label>
                                <input type="text" class="form-control form-control-sm" id="detailNamaPasien" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="detailJaminanKesehatan" class="form-label">Jaminan Kesehatan</label>
                                <input type="text" class="form-control form-control-sm" id="detailJaminanKesehatan" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="detailTotalBiaya" class="form-label">Total Biaya</label>
                                <input type="text" class="form-control form-control-sm" id="detailTotalBiaya" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="detailStatusPembayaran" class="form-label">Status Pembayaran</label>
                                <input type="text" class="form-control form-control-sm" id="detailStatusPembayaran" readonly>
                            </div>
                        </div>
                        <hr>
                        <h6>Resep Obat</h6>
                        <table class="table table-bordered" id="resepObatTable" style="font-size: 0.82rem;">
                            <thead>
                                <tr>
                                    <th>Nama Obat</th>
                                    <th>Bentuk Obat</th>
                                    <th>Jumlah</th>
                                    <th>Harga Satuan</th>
                                    <th>Harga Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Filled dynamically -->
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-end mt-3">
            </div>
        </div>
    </div>
</div>

<!-- Modal Filter -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="GET" action="{{ route('kasir.dana') }}">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h3 class="modal-title" id="filterModalLabel"><strong>Filter Data Tagihan</strong></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
                            <input type="date" name="tanggal_awal" id="tanggal_awal" class="form-control" value="{{ request('tanggal_awal') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                            <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="jaminan_kesehatan" class="form-label">Jaminan Kesehatan</label>
                            <select name="jaminan_kesehatan" id="jaminan_kesehatan" class="form-select">
                                <option value="">Semua</option>
                                <option value="Umum" {{ request('jaminan_kesehatan') == 'Umum' ? 'selected' : '' }}>Umum</option>
                                <option value="BPJS Kesehatan" {{ request('jaminan_kesehatan') == 'BPJS Kesehatan' ? 'selected' : '' }}>BPJS Kesehatan</option>
                                <option value="Perusahaan" {{ request('jaminan_kesehatan') == 'Perusahaan' ? 'selected' : '' }}>Perusahaan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="status_pembayaran" class="form-label">Status Pembayaran</label>
                            <select name="status_pembayaran" id="status_pembayaran" class="form-select">
                                <option value="">Semua</option>
                                <option value="Lunas" {{ request('status_pembayaran') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                                <option value="Belum Lunas" {{ request('status_pembayaran') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="total_biaya_min" class="form-label">Total Biaya Minimum</label>
                            <input type="number" name="total_biaya_min" id="total_biaya_min" class="form-control" value="{{ request('total_biaya_min') }}" min="0" step="0.01" placeholder="0">
                        </div>
                        <div class="col-md-6">
                            <label for="total_biaya_max" class="form-label">Total Biaya Maksimum</label>
                            <input type="number" name="total_biaya_max" id="total_biaya_max" class="form-control" value="{{ request('total_biaya_max') }}" min="0" step="0.01" placeholder="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end mt-3" style="gap: 10px;">
                    <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.querySelector('form#searchForm');
        let debounceTimeout;

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => {
                searchForm.submit();
            }, 500); // delay 500ms after user stops typing
        });

        // Placeholder for filter modal apply button
        const applyFilterButton = document.getElementById('applyFilterButton');
        if (applyFilterButton) {
            applyFilterButton.addEventListener('click', function() {
                // Implement filter logic here
                alert('Fitur filter belum diimplementasikan.');
                // Close modal after applying filter
                const filterModal = bootstrap.Modal.getInstance(document.getElementById('filterModal'));
                filterModal.hide();
            });
        }

        const modal = new bootstrap.Modal(document.getElementById('modalTagihanDetail'));
        const detailNoRM = document.getElementById('detailNoRM');
        const detailNamaPasien = document.getElementById('detailNamaPasien');
        const detailPoliTujuan = document.getElementById('detailPoliTujuan');
        const detailTotalBiaya = document.getElementById('detailTotalBiaya');
        const detailStatusPembayaran = document.getElementById('detailStatusPembayaran');
        const resepObatTableBody = document.querySelector('#resepObatTable tbody');

        const btnSelengkapnya = document.querySelectorAll('.btnSelengkapnya');
        if (btnSelengkapnya) {
            btnSelengkapnya.forEach(button => {
                button.addEventListener('click', function() {
                    const pasienId = this.getAttribute('data-pasien-id');
                    fetch(`/kasir/tagihan/${pasienId}`)
                        .then(response => response.json())
                        .then(data => {
                            detailNoRM.value = data.no_rekam_medis || '-';
                            detailNamaPasien.value = data.nama_pasien || '-';
                            detailJaminanKesehatan.value = data.jaminan_kesehatan || '-';
                            detailHariTanggal.value = data.created_at ? new Date(data.created_at).toLocaleDateString('id-ID', {
                                weekday: 'long',
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit'
                            }) : '-';
                            detailTotalBiaya.value = data.total_biaya ? 'Rp ' + new Intl.NumberFormat('id-ID', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }).format(data.total_biaya * 1000) : '-';
                            detailStatusPembayaran.value = data.status_pembayaran || '-';

                            // Set background color of status field based on status
                            if (data.status_pembayaran && data.status_pembayaran.toLowerCase() === 'lunas') {
                                detailStatusPembayaran.classList.add('bg-success', 'text-white');
                            } else {
                                detailStatusPembayaran.classList.remove('bg-success', 'text-white');
                            }

                            // Clear previous rows
                            resepObatTableBody.innerHTML = '';
                            if (data.resep_obat && data.resep_obat.length > 0) {
                                data.resep_obat.forEach(item => {
                                    const row = document.createElement('tr');
                                    const hargaTotal = item.jumlah * item.harga_satuan;
                                    row.innerHTML = `
                                        <td>${item.nama_obat}</td>
                                        <td>${item.bentuk_obat}</td>
                                        <td>${item.jumlah}</td>
                                        <td>Rp ${new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(item.harga_satuan)}</td>
                                        <td>Rp ${new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(hargaTotal)}</td>
                                    `;
                                    resepObatTableBody.appendChild(row);
                                });
                            } else {
                                const row = document.createElement('tr');
                                row.innerHTML = '<td colspan="5" class="text-center">Tidak ada data resep obat</td>';
                                resepObatTableBody.appendChild(row);
                            }

                            modal.show();
                        })
                        .catch(error => {
                            alert('Gagal memuat data tagihan: ' + error.message);
                        });
                });
            });
        }
    });
</script>
@endsection
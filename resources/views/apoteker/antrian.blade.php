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
                                <td style="white-space: nowrap;">{{ $index + 1 }}</td>
                                <td style="white-space: nowrap;">{{ $antrian->no_rekam_medis }}</td>
                                <td style="white-space: nowrap;">{{ $antrian->pasien->nama_pasien }}</td>
                                <td style="white-space: nowrap;">{{ \Carbon\Carbon::parse($antrian->pasien->tanggal_lahir)->age }} tahun</td>
                                <td style="white-space: nowrap;">{{ $antrian->pasien->jaminan_kesehatan }}</td>
                                <td style="white-space: nowrap;">{{ $antrian->poli ? $antrian->poli->nama_poli : 'Tidak ada' }}</td>
                                <td style="white-space: nowrap;"><span class="badge bg-danger">{{ $antrian->status }}</span></td>
                                <td style="white-space: nowrap;">
                                    <button type="button" class="btn btn-success btn-sm rounded btnHasilPeriksa" data-bs-toggle="modal"
                                        data-bs-target="#modalHasilPeriksa" data-pasien-id="{{ $antrian->pasien->id }}">Hasil Periksa</button>
                                    <button type="button" class="btn btn-primary btn-sm rounded btnRacikObat"
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
                <button type="button" class="btn btn-primary" id="btnSiapkanObatAntrian">Siapkan Obat</button>
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

                    <div class="mb-3">
                        <label for="penanggungJawabVisible" class="form-label">Penanggung Jawab</label>
                        <input type="text" class="form-control" id="penanggungJawabVisible" readonly>
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
<!-- Tambahkan JS Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#selectObat').select2({
            placeholder: 'Cari dan pilih obat',
            minimumInputLength: 2,
            ajax: {
                url: '/dokter/search-obat', // Perbaikan URL sesuai route web.php
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalRacikObat = new bootstrap.Modal(document.getElementById('modalRacikObat'));

        document.querySelectorAll('.btnRacikObat').forEach(button => {
            button.addEventListener('click', function() {
                const pasienId = this.getAttribute('data-pasien-id');

                // Kosongkan isi modal sebelumnya
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
    });
</script>
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
                        document.getElementById('penanggungJawabVisible').value = data.penanggung_jawab_name || '';
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

            antrians.forEach((antrian, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td style="white-space: nowrap;">${index + 1}</td>
                    <td style="white-space: nowrap;">${antrian.no_rekam_medis}</td>
                    <td style="white-space: nowrap;">${antrian.pasien.nama_pasien}</td>
                    <td style="white-space: nowrap;">${antrian.pasien.tanggal_lahir ? new Date(antrian.pasien.tanggal_lahir).toLocaleDateString('id-ID') : 'Tanggal tidak tersedia'}</td>
                    <td style="white-space: nowrap;">${antrian.pasien.jaminan_kesehatan}</td>
                    <td style="white-space: nowrap;">${antrian.poli ? antrian.poli.nama_poli : 'Tidak ada'}</td>
                    <td style="white-space: nowrap;"><span class="badge bg-danger">${antrian.status}</span></td>
                    <td style="white-space: nowrap;">
                    <button type="button" class="btn btn-success btn-sm rounded btnHasilPeriksa" data-bs-toggle="modal"
                    data-bs-target="#modalHasilPeriksa" data-pasien-id="${antrian.pasien.id}">Hasil Periksa</button>
                    <button type="button" class="btn btn-primary btn-sm rounded btnRacikObat"
                    data-pasien-id="${antrian.pasien.id}">Racik Obat</button>
                    </td>
                `;
                antrianTableBody.appendChild(row);
            });
            // Pasang ulang event listener setelah render
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
                            document.getElementById('anamnesis').value = data.anamnesis || '';
                            document.getElementById('pemeriksaanFisik').value = data.pemeriksaan_fisik || '';
                            document.getElementById('rencanaTerapi').value = data.rencana_dan_terapi || '';
                            document.getElementById('diagnosis').value = data.diagnosis || '';
                            document.getElementById('edukasi').value = data.edukasi || '';
                            document.getElementById('kodeICD').value = data.kode_icd || '';
                            document.getElementById('kesanStatusGizi').value = data.kesan_status_gizi || '';
                            document.getElementById('penanggungJawabVisible').value = data.penanggung_jawab_name || '';
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
            // Pasang ulang event listener untuk btnRacikObat
            document.querySelectorAll('.btnRacikObat').forEach(button => {
                button.addEventListener('click', function() {
                    const pasienId = this.getAttribute('data-pasien-id');
                    const modalRacikObat = new bootstrap.Modal(document.getElementById('modalRacikObat'));
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
        }

        let debounceTimeout;
        searchInput.addEventListener('input', function() {
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

    // Store pasienId for use in Siapkan Obat button in antrian modal
    let currentPasienIdAntrian = null;

    document.querySelectorAll('.btnRacikObat').forEach(button => {
        button.addEventListener('click', function() {
            currentPasienIdAntrian = this.getAttribute('data-pasien-id');
        });
    });

    // Remove any existing event listeners before adding a new one to prevent looping
    const btnSiapkanObatAntrian = document.getElementById('btnSiapkanObatAntrian');
    if (btnSiapkanObatAntrian) {
        btnSiapkanObatAntrian.removeEventListener('click', siapkanObatHandler);
        btnSiapkanObatAntrian.addEventListener('click', siapkanObatHandler);
    }

    function siapkanObatHandler() {
        if (!currentPasienIdAntrian) {
            alert('Pasien tidak ditemukan.');
            return;
        }

        fetch(`/apoteker/antrian/update-status/${currentPasienIdAntrian}`, {
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
    }

    // Inisialisasi Select2 setiap kali modal dibuka
    $(document).ready(function() {
        $('#modalHasilPeriksa').on('shown.bs.modal', function () {
            var $selectObat = $('#selectObat');
            if ($selectObat.data('select2')) {
                $selectObat.select2('destroy');
            }
            $selectObat.select2({
                placeholder: 'Cari dan pilih obat',
                minimumInputLength: 0, // agar dropdown muncul saat klik
                ajax: {
                    url: '/dokter/search-obat',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                },
                width: '100%'
            });
            // Trigger dropdown saat klik field
            $selectObat.on('select2:open', function() {
                if (!$('.select2-results__option').length) {
                    $selectObat.select2('open');
                }
            });
        });
    });
</script>
@endsection

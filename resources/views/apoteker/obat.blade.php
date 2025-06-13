@extends('dashboardApotek')

@section('apoteker')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>Data Obat</strong></h1>
    <div class="row">
        <div class="col-12 col-lg-12 col-xxl-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex justify-content-between">
                    <!-- Input Pencarian dan Tombol Filter -->
                    <form method="GET" action="{{ route('apoteker.obat') }}" class="d-flex align-items-center"
                        style="gap: 10px;" id="searchForm">
                        <div class="input-group" style="width: 250px;">
                            <input type="text" name="search" class="form-control" id="searchInput"
                                placeholder="Pencarian..." aria-label="Search" value="{{ request('search') }}"
                                autocomplete="off">
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1"
                            data-bs-toggle="modal" data-bs-target="#filterModal">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <!-- Export buttons can be added here if needed -->
                    </form>
                    <button type="button" class="btn btn-success btn-lg" style="padding: 5px 10px; font-size: 0.9rem;"
                        data-bs-toggle="modal" data-bs-target="#modalTambahObat">
                        <i class="fas fa-plus"></i> Tambah Obat
                    </button>
                    <!-- Loading indicator removed as per user request -->
                    <!-- <div id="loadingIndicator" style="display:none; margin-left: 10px;">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Loading...
                    </div> -->

                    <!-- Modal Tambah Obat -->
                    <div class="modal fade" id="modalTambahObat" tabindex="-1" aria-labelledby="modalTambahObatLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
                            <div class="modal-content" style="overflow-x: hidden;">
                                <form method="POST" action="{{ route('apoteker.obat.store') }}" id="formTambahObat">
                                    @csrf
                                    <div class="modal-header d-flex justify-content-between">
                                        <h5 class="modal-title" id="modalTambahObatLabel">Tambah Data Obat</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="nama_obat" class="form-label">Nama Obat</label>
                                                <input type="text" name="nama_obat" id="nama_obat" class="form-control"
                                                    required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="jenis_obat" class="form-label">Jenis Obat</label>
                                                <input type="text" name="jenis_obat" id="jenis_obat"
                                                    class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="dosis" class="form-label">Dosis</label>
                                                <input type="text" name="dosis" id="dosis" class="form-control"
                                                    required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="bentuk_obat" class="form-label">Bentuk Obat</label>
                                                <input type="text" name="bentuk_obat" id="bentuk_obat"
                                                    class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="stok" class="form-label">Stok</label>
                                                <input type="number" name="stok" id="stok" class="form-control" required
                                                    min="0">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="harga_satuan" class="form-label">Harga Satuan</label>
                                                <input type="number" step="0.01" name="harga_satuan" id="harga_satuan"
                                                    class="form-control" required min="0">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="tanggal_kadaluarsa" class="form-label">Tanggal
                                                    Kadaluarsa</label>
                                                <input type="date" name="tanggal_kadaluarsa" id="tanggal_kadaluarsa"
                                                    class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="nama_pabrikan" class="form-label">Nama Pabrikan</label>
                                                <input type="text" name="nama_pabrikan" id="nama_pabrikan"
                                                    class="form-control" required>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="keterangan" class="form-label">Keterangan</label>
                                                <textarea name="keterangan" id="keterangan" class="form-control"
                                                    rows="3"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer d-flex justify-content-end mt-3" style="gap: 10px;">
                                        <button type="button" class="btn btn-danger"
                                            data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Filter -->
                    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form method="GET" action="{{ route('apoteker.obat') }}">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="filterModalLabel">Filter Data Obat</h5>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Add filter fields as needed -->
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="nama_obat" class="form-label">Nama Obat</label>
                                                <input type="text" name="nama_obat" id="nama_obat" class="form-control"
                                                    value="{{ request('nama_obat') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="jenis_obat" class="form-label">Jenis Obat</label>
                                                <select name="jenis_obat" id="jenis_obat" class="form-select">
                                                    <option value="">Semua</option>
                                                    <!-- Add options dynamically if needed -->
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="dosis" class="form-label">Dosis</label>
                                                <input type="text" name="dosis" id="dosis" class="form-control"
                                                    value="{{ request('dosis') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="bentuk_obat" class="form-label">Bentuk Obat</label>
                                                <input type="text" name="bentuk_obat" id="bentuk_obat"
                                                    class="form-control" value="{{ request('bentuk_obat') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="stok" class="form-label">Stok</label>
                                                <input type="number" name="stok" id="stok" class="form-control"
                                                    value="{{ request('stok') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="harga_satuan" class="form-label">Harga Satuan</label>
                                                <input type="number" step="0.01" name="harga_satuan" id="harga_satuan"
                                                    class="form-control" value="{{ request('harga_satuan') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="tanggal_kadaluarsa" class="form-label">Tanggal
                                                    Kadaluarsa</label>
                                                <input type="date" name="tanggal_kadaluarsa" id="tanggal_kadaluarsa"
                                                    class="form-control" value="{{ request('tanggal_kadaluarsa') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="nama_pabrikan" class="form-label">Nama Pabrikan</label>
                                                <select name="nama_pabrikan" id="nama_pabrikan" class="form-select">
                                                    <option value="">Semua</option>
                                                    <!-- Add options dynamically if needed -->
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer d-flex justify-content-end mt-3" style="gap: 10px;">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                                        <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                <table class="table table-hover my-0" id="dataObatTabel">
                    <thead>
                        <tr>
                            <th style="white-space: nowrap;">No.</th>
                            <th style="white-space: nowrap;">Nama Obat</th>
                            <th style="white-space: nowrap;">Jenis Obat</th>
                            <th style="white-space: nowrap;">Bentuk Obat</th>
                            <th style="white-space: nowrap;">Stok</th>
                            <th style="white-space: nowrap;">Harga Satuan</th>
                            <th style="white-space: nowrap;">Tanggal Kadaluarsa</th>
                            <th style="white-space: nowrap;">Aksi</th>
                        </tr>
                    </thead>
                        <tbody>
                        @foreach ($obats as $index => $obat)
                        <tr>
                            <td style="white-space: nowrap;">{{ $obats->firstItem() + $index }}</td>
                            <td style="white-space: nowrap;">{{ $obat->nama_obat }}</td>
                            <td style="white-space: nowrap;">{{ $obat->jenis_obat }}</td>
                            <td style="white-space: nowrap;">{{ $obat->bentuk_obat }}</td>
                            <td style="white-space: nowrap;">
                                @if ($obat->stok == 0)
                                <span class="badge bg-danger">{{ $obat->stok }}</span>
                                @elseif ($obat->stok < 20) <span class="badge bg-warning">{{ $obat->stok }}</span>
                                    @else
                                    <span class="badge bg-success">{{ $obat->stok }}</span>
                                    @endif
                            </td>
                            <td style="white-space: nowrap;">Rp. {{ rtrim(rtrim(number_format($obat->harga_satuan, 2, ',', '.'), '0'), ',') }}</td>
                            <td style="white-space: nowrap;">{{ \Carbon\Carbon::parse($obat->tanggal_kadaluarsa)->format('d-m-Y') }}</td>
                            <td style="white-space: nowrap;">
                                <button type="button" class="btn btn-primary btn-sm rounded btn-detail-obat"
                                    data-id="{{ $obat->id }}">
                                    Selengkapnya
                                </button>
                                <button type="button" class="btn btn-danger btn-sm rounded btn-hapus-obat"
                                    data-id="{{ $obat->id }}">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                </table>
                </div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center w-50">
                        <div class="small text-muted mb-2 text-start ps-3">
                            Showing {{ $obats->firstItem() }} to {{ $obats->lastItem() }} of
                            {{ $obats->total() }} results
                        </div>
                        <nav class="d-flex justify-content-center">
                            <ul class="pagination d-flex flex-row flex-wrap gap-2"
                                style="list-style-type: none; padding-left: 0; margin-bottom: 0;">
                                {{-- Previous Page Link --}}
                                @if ($obats->onFirstPage())
                                <li class="page-item disabled" aria-disabled="true" aria-label="Previous">
                                    <span class="page-link" aria-hidden="true">&laquo;</span>
                                </li>
                                @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $obats->previousPageUrl() }}" rel="prev"
                                        aria-label="Previous">&laquo;</a>
                                </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($obats->getUrlRange(1, $obats->lastPage()) as $page => $url)
                                @if ($page == $obats->currentPage())
                                <li class="page-item active" aria-current="page"><span
                                        class="page-link">{{ $page }}</span></li>
                                @else
                                <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($obats->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $obats->nextPageUrl() }}" rel="next"
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

<!-- Modal Detail Obat -->
<div class="modal fade" id="modalObatDetail" tabindex="-1" aria-labelledby="modalObatDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
        <div class="modal-content" style="overflow-x: hidden;">
            <div class="modal-header d-flex justify-content-between">
                <h3 class="modal-title" id="modalObatDetailLabel"><strong>Detail Obat</strong></h3>
                <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3" style="max-height: 400px; overflow-y: auto;">
                <form>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="modalNamaObat" class="form-label">Nama Obat</label>
                            <input type="text" class="form-control form-control-sm" id="modalNamaObat" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="modalJenisObat" class="form-label">Jenis Obat</label>
                            <input type="text" class="form-control form-control-sm" id="modalJenisObat" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="modalDosis" class="form-label">Dosis</label>
                            <input type="text" class="form-control form-control-sm" id="modalDosis" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="modalBentukObat" class="form-label">Bentuk Obat</label>
                            <input type="text" class="form-control form-control-sm" id="modalBentukObat" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="modalStok" class="form-label">Stok</label>
                            <input type="text" class="form-control form-control-sm" id="modalStok" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="modalHargaSatuan" class="form-label">Harga Satuan</label>
                            <input type="text" class="form-control form-control-sm" id="modalHargaSatuan" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="modalTanggalKadaluarsa" class="form-label">Tanggal Kadaluarsa</label>
                            <input type="text" class="form-control form-control-sm" id="modalTanggalKadaluarsa"
                                readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="modalNamaPabrikan" class="form-label">Nama Pabrikan</label>
                            <input type="text" class="form-control form-control-sm" id="modalNamaPabrikan" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="modalKeterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control form-control-sm" id="modalKeterangan" rows="3"
                            readonly></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-end mt-3" style="gap: 10px;">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="btnEditObat">Edit</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        var isEditMode = false;
        var currentObatId = null;

        function formatPrice(price) {
            // Convert to float
            let num = parseFloat(price);
            if (isNaN(num)) return price;
            // Format number with comma as thousand separator and no decimals if .00
            let formatted = num.toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            });
            return 'Rp. ' + formatted;
        }

        function unformatPrice(formattedPrice) {
            if (!formattedPrice) return '';
            // Remove "Rp." and dots, replace comma with dot for decimal
            let unformatted = formattedPrice.replace(/Rp\.?\s?/g, '').replace(/\./g, '').replace(/,/g, '.');
            return unformatted;
        }

        function toggleEditMode(enable) {
            isEditMode = enable;
            $('#modalObatDetail input, #modalObatDetail textarea').prop('readonly', !enable);

            // Handle tanggal_kadaluarsa input type toggle
            var tanggalInput = $('#modalTanggalKadaluarsa');
            if (enable) {
                // Change to date input for calendar picker
                var currentValue = tanggalInput.val();
                // Convert displayed date (d-m-Y) to yyyy-mm-dd for input[type=date]
                var parts = currentValue.split('-');
                if (parts.length === 3) {
                    var formattedDate = parts[2] + '-' + parts[1].padStart(2, '0') + '-' + parts[0].padStart(2,
                        '0');
                    tanggalInput.val(formattedDate);
                }
                tanggalInput.attr('type', 'date');
            } else {
                // Change back to text input with formatted date
                var dateValue = tanggalInput.val();
                if (dateValue) {
                    var dateObj = new Date(dateValue);
                    var day = String(dateObj.getDate()).padStart(2, '0');
                    var month = String(dateObj.getMonth() + 1).padStart(2, '0');
                    var year = dateObj.getFullYear();
                    var formattedDate = day + '-' + month + '-' + year;
                    tanggalInput.val(formattedDate);
                }
                tanggalInput.attr('type', 'text');
            }

            if (enable) {
                // Remove formatting from harga_satuan input for editing
                let rawPrice = unformatPrice($('#modalHargaSatuan').val());
                $('#modalHargaSatuan').val(rawPrice);

                $('#btnEditObat').text('Simpan').removeClass('btn-primary').addClass('btn-success');
            } else {
                // Format harga_satuan input for display
                let formattedPrice = formatPrice($('#modalHargaSatuan').val());
                $('#modalHargaSatuan').val(formattedPrice);

                $('#btnEditObat').text('Edit').removeClass('btn-success').addClass('btn-primary');
            }
        }

        // Use event delegation for dynamically added buttons
        $('#dataObatTabel').on('click', '.btn-detail-obat', function () {
            currentObatId = $(this).data('id');
            var modalElement = document.getElementById('modalObatDetail');
            var modal = new bootstrap.Modal(modalElement);

            function formatPrice(price) {
                // Convert to float
                let num = parseFloat(price);
                if (isNaN(num)) return price;
                // Format number with comma as thousand separator and no decimals if .00
                let formatted = num.toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2
                });
                return 'Rp. ' + formatted;
            }

            $.ajax({
                url: '/apoteker/obat/' + currentObatId,
                method: 'GET',
                success: function (data) {
                    $('#modalNamaObat').val(data.nama_obat);
                    $('#modalJenisObat').val(data.jenis_obat);
                    $('#modalDosis').val(data.dosis);
                    $('#modalBentukObat').val(data.bentuk_obat);
                    $('#modalStok').val(data.stok);
                    $('#modalHargaSatuan').val(formatPrice(data.harga_satuan));
                    $('#modalTanggalKadaluarsa').val(data.tanggal_kadaluarsa);
                    $('#modalNamaPabrikan').val(data.nama_pabrikan);
                    $('#modalKeterangan').val(data.keterangan);
                    toggleEditMode(false);
                    modal.show();
                },
                error: function () {
                    alert('Gagal mengambil data obat.');
                }
            });
        });

        $('#btnEditObat').on('click', function () {
            if (!isEditMode) {
                // Enable edit mode
                toggleEditMode(true);
            } else {
                // Save changes
                var updatedData = {
                    nama_obat: $('#modalNamaObat').val(),
                    jenis_obat: $('#modalJenisObat').val(),
                    dosis: $('#modalDosis').val(),
                    bentuk_obat: $('#modalBentukObat').val(),
                    stok: $('#modalStok').val(),
                    harga_satuan: unformatPrice($('#modalHargaSatuan').val()),
                    tanggal_kadaluarsa: $('#modalTanggalKadaluarsa').val(),
                    nama_pabrikan: $('#modalNamaPabrikan').val(),
                    keterangan: $('#modalKeterangan').val(),
                    _token: '{{ csrf_token() }}'
                };

                $.ajax({
                    url: '/apoteker/obat/' + currentObatId,
                    method: 'PUT',
                    data: updatedData,
                    success: function (response) {
                        toggleEditMode(false);
                        var modalElement = document.getElementById('modalObatDetail');
                        var modal = bootstrap.Modal.getInstance(modalElement);
                        modal.hide();
                        toastr.success('Data obat berhasil disimpan', '', {
                            timeOut: 3000,
                            extendedTimeOut: 1000,
                            closeButton: true,
                            progressBar: true,
                            onHidden: function () {
                                location.reload();
                            }
                        });
                    },
                    error: function () {
                        toastr.error('Gagal menyimpan data obat');
                    }
                });
            }
        });

        // AJAX form submission for Tambah Obat modal
        $('#formTambahObat').on('submit', function (e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var formData = form.serialize();

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                success: function (response) {
                    var modalElement = document.getElementById('modalTambahObat');
                    var modal = bootstrap.Modal.getInstance(modalElement);
                    modal.hide();
                    toastr.success('Data obat berhasil ditambahkan', '', {
                        timeOut: 3000,
                        extendedTimeOut: 1000,
                        closeButton: true,
                        progressBar: true,
                        onHidden: function () {
                            location.reload();
                        }
                    });
                },
                error: function (xhr) {
                    toastr.error('Gagal menambahkan data obat');
                }
            });
        });
    });

    // Tambahkan event listener untuk input pencarian dinamis
    $('#searchInput').on('input', function () {
        var query = $(this).val();
        $('#loadingIndicator').show();

        $.ajax({
            url: "{{ route('apoteker.obat') }}",
            type: 'GET',
            data: {
                search: query
            },
            success: function (response) {
                $('#loadingIndicator').hide();

                // Bangun ulang isi tabel berdasarkan data JSON yang diterima
                var tbody = $('#dataObatTabel tbody');
                tbody.empty();

                if (response.data.length === 0) {
                    tbody.append(
                        '<tr><td colspan="8" class="text-center">Tidak ada data ditemukan</td></tr>'
                        );
                } else {
                    $.each(response.data, function (index, obat) {
                        var stokBadge = '';
                        if (obat.stok == 0) {
                            stokBadge = '<span class="badge bg-danger">' + obat.stok +
                                '</span>';
                        } else if (obat.stok < 20) {
                            stokBadge = '<span class="badge bg-warning">' + obat.stok +
                                '</span>';
                        } else {
                            stokBadge = '<span class="badge bg-success">' + obat.stok +
                                '</span>';
                        }

                        var hargaFormatted = new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0
                        }).format(obat.harga_satuan);

                        var row = '<tr>' +
                            '<td style="white-space: nowrap;">' + (response.pagination.from + index) + '</td>' +
                            '<td style="white-space: nowrap;">' + obat.nama_obat + '</td>' +
                            '<td style="white-space: nowrap;">' + obat.jenis_obat + '</td>' +
                            '<td style="white-space: nowrap;">' + obat.bentuk_obat + '</td>' +
                            '<td style="white-space: nowrap;">' + stokBadge + '</td>' +
                            '<td style="white-space: nowrap;">' + hargaFormatted + '</td>' +
                            '<td style="white-space: nowrap;">' + new Date(obat.tanggal_kadaluarsa).toLocaleDateString(
                                'id-ID') + '</td>' +
                            '<td style="white-space: nowrap;">' +
                            '<button type="button" class="btn btn-primary btn-sm rounded btn-detail-obat" data-id="' +
                            obat.id + '">' +
                            'Selengkapnya' +
                            '</button>' +
                            '</td>' +
                            '</tr>';

                        tbody.append(row);
                    });
                }
            },
            error: function () {
                $('#loadingIndicator').hide();
                alert('Gagal mengambil data pencarian.');
            }
        });
    });

    // Prevent form submission on Enter key press
    $('#searchForm').on('submit', function (e) {
        e.preventDefault();
    });

    // Dynamic search on input
    $('#searchInput').on('input', function () {
        var query = $(this).val();

        $.ajax({
            url: "{{ route('apoteker.obat') }}",
            type: 'GET',
            data: {
                search: query
            },
            success: function (response) {

                var tbody = $('#dataObatTabel tbody');
                tbody.empty();

                if (response.data.length === 0) {
                    tbody.append(
                        '<tr><td colspan="8" class="text-center">Tidak ada data ditemukan</td></tr>'
                        );
                } else {
                    $.each(response.data, function (index, obat) {
                        var stokBadge = '';
                        if (obat.stok == 0) {
                            stokBadge = '<span class="badge bg-danger">' + obat.stok +
                                '</span>';
                        } else if (obat.stok < 20) {
                            stokBadge = '<span class="badge bg-warning">' + obat.stok +
                                '</span>';
                        } else {
                            stokBadge = '<span class="badge bg-success">' + obat.stok +
                                '</span>';
                        }

                        var hargaFormatted = new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0
                        }).format(obat.harga_satuan);

                        var row = '<tr>' +
                            '<td>' + (response.pagination.from + index) + '</td>' +
                            '<td>' + obat.nama_obat + '</td>' +
                            '<td>' + obat.jenis_obat + '</td>' +
                            '<td>' + obat.bentuk_obat + '</td>' +
                            '<td>' + stokBadge + '</td>' +
                            '<td>' + hargaFormatted + '</td>' +
                            '<td>' + new Date(obat.tanggal_kadaluarsa).toLocaleDateString(
                                'id-ID') + '</td>' +
                            '<td>' +
                            '<button type="button" class="btn btn-primary btn-sm rounded btn-detail-obat" data-id="' +
                            obat.id + '">' +
                            'Selengkapnya' +
                            '</button>' +
                            '</td>' +
                            '</tr>';

                        tbody.append(row);
                    });
                }
            },
            error: function () {
                alert('Gagal mengambil data pencarian.');
            }
        });
    });

    // Handle delete obat button click
    $('#dataObatTabel').on('click', '.btn-hapus-obat', function () {
        var obatId = $(this).data('id');
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin menghapus data obat ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/apoteker/obat/' + obatId,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        toastr.success(response.message, '', {
                            timeOut: 3000,
                            extendedTimeOut: 1000,
                            closeButton: true,
                            progressBar: true,
                            onHidden: function () {
                                location.reload();
                            }
                        });
                    },
                    error: function (xhr) {
                        toastr.error('Gagal menghapus data obat');
                    }
                });
            }
        });
    });

</script>
@endsection

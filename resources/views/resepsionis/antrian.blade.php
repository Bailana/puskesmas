@extends('dashboardResepsionis')

@section('resepsionis')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>Antrian Pasien</strong></h1>
    <div class="row">
        <div class="col-12 col-lg-12 col-xxl-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex justify-content-between">
                    <!-- Input Pencarian -->
                    <div class="input-group" style="width: 250px;">
                        <input type="text" class="form-control" id="searchInput" placeholder="Pencarian..."
                            aria-label="Search" autocomplete="off">
                    </div>
                    <!-- Tombol Buat Antrian -->
                    <button type="button" class="btn btn-success btn-lg" style="padding: 5px 10px; font-size: 0.9rem;"
                        data-bs-toggle="modal" data-bs-target="#modalTambahAntrian">
                        <i class="fas fa-plus"></i> Buat Antrian
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover my-0" id="antrianTable">
                        <thead>
                            <tr>
                                <th class="nowrap">No.</th>
                                <th class="nowrap">Nomor RM</th>
                                <th class="nowrap">Nama Pasien</th>
                                <th class="nowrap">Umur</th>
                                <th class="nowrap">JamKes</th>
                                <th class="nowrap">Poli Tujuan</th>
                                <th class="nowrap">Tgl. Berobat</th>
                                <th class="nowrap">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($antrians as $index => $antrian)
                            <tr>
                                <td class="nowrap">{{ $antrians->firstItem() + $index }}</td>
                                <td class="nowrap">{{ $antrian->no_rekam_medis }}</td>
                                <td class="nowrap">{{ $antrian->pasien->nama_pasien }}</td>
                                <td class="nowrap">{{ \Carbon\Carbon::parse($antrian->pasien->tanggal_lahir)->age }}
                                    Tahun</td>
                                <td class="nowrap">{{ $antrian->pasien->jaminan_kesehatan }}</td>
                                <td class="nowrap">{{ $antrian->poli ? $antrian->poli->nama_poli : 'Tidak ada' }}</td>
                                <td class="nowrap">
                                    {{ \Carbon\Carbon::parse($antrian->tanggal_berobat)->format('d-m-Y') }}</td>
                                <td class="nowrap">
                                    @if ($antrian->status == 'Perlu Analisa')
                                    <span class="badge bg-danger">{{ $antrian->status }}</span>
                                    @elseif ($antrian->status == 'Pemeriksaan')
                                    <span class="badge bg-warning">{{ $antrian->status }}</span>
                                    @elseif ($antrian->status == 'Farmasi')
                                    <span class="badge bg-primary">{{ $antrian->status }}</span>
                                    @else
                                    <span class="badge bg-info">{{ $antrian->status }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center w-50">
                        <div class="small text-muted mb-2 text-start ps-3 pagination-info-text">
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

<!-- Modal Buat Antrian -->
<div class="modal fade" id="modalTambahAntrian" tabindex="-1" aria-labelledby="modalTambahAntrianLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
        <div class="modal-content" style="overflow-x: hidden;">
            <div class="modal-header d-flex justify-content-between">
                <h3 class="modal-title" id="modalTambahAntrianLabel"><strong>Buat Antrian</strong></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    id="btnCloseAntrian"></button>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow-y: auto; padding: 10px;">
                <form id="formTambahAntrian">
                    <div class="mb-3">
                        <!-- <label for="noKepesertaan" class="form-label">Nomor Kepesertaan</label> -->
                        <input type="text" class="form-control" id="noKepesertaan"
                            placeholder="Masukkan Nomor Kepesertaan" required maxlength="16" pattern="\d*"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,16);">
                    </div>
                    <!-- Tampilkan data pasien di sini setelah pencarian -->
                    <div id="dataPasien" style="display: none;">
                        <div class="mb-3">
                            <label for="namaPasien" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="namaPasien" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="noRekamMedis" class="form-label">Nomor Rekam Medis</label>
                            <input type="text" class="form-control" id="noRekamMedis" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="umurPasien" class="form-label">Umur</label>
                            <input type="text" class="form-control" id="umurPasien" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="tanggalBerobat" class="form-label">Tanggal Berobat</label>
                            <input type="date" class="form-control" id="tanggalBerobat">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-success ms-2" id="btnCariPasien">Cari</button>
                <button type="button" class="btn btn-primary ms-2" id="btnSimpanAntrian" style="display: none;">Buat
                    Antrian</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const antrianTableBody = document.querySelector('#antrianTable tbody');

        function renderTableRows(antrians) {
            antrianTableBody.innerHTML = '';
            if (antrians.data.length === 0) {
                antrianTableBody.innerHTML =
                    '<tr><td colspan="8" class="text-center">Data antrian tidak ditemukan</td></tr>';
                return;
            }

            function calculateAge(birthDateString) {
                if (!birthDateString) return 'Tanggal tidak tersedia';
                const birthDate = new Date(birthDateString);
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const m = today.getMonth() - birthDate.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                return age + ' Tahun';
            }
            antrians.data.forEach((antrian, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="nowrap">${index + 1 + (antrians.current_page - 1) * antrians.per_page}</td>
                    <td class="nowrap">${antrian.no_rekam_medis}</td>
                    <td class="nowrap">${antrian.pasien.nama_pasien}</td>
                    <td class="nowrap">${calculateAge(antrian.pasien.tanggal_lahir)}</td>
                    <td class="nowrap">${antrian.pasien.jaminan_kesehatan}</td>
                    <td class="nowrap">${antrian.poli ? antrian.poli.nama_poli : 'Tidak ada'}</td>
                    <td class="nowrap">${antrian.tanggal_berobat ? new Date(antrian.tanggal_berobat).toLocaleDateString('id-ID') : 'Tanggal tidak tersedia'}</td>
                    <td class="nowrap">
                        <span class="badge ${
                            antrian.status === 'Perlu Analisa' ? 'bg-danger' :
                            antrian.status === 'Pemeriksaan' ? 'bg-warning' :
                            antrian.status === 'Farmasi' ? 'bg-primary' : 'bg-info'
                        }">${antrian.status}</span>
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
                fetch(`{{ route('resepsionis.antrian') }}?search=${encodeURIComponent(query)}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        renderTableRows(data);
                    })
                    .catch(error => {
                        console.error('Error fetching search results:', error);
                    });
            }, 300);
        });

        $('#btnCariPasien').click(function () {
            var nomorKepesertaan = $('#noKepesertaan').val();

            // Remove previous validation error if any
            $('#noKepesertaan').removeClass('is-invalid');
            $('#noKepesertaan').next('.invalid-feedback').remove();

            if (nomorKepesertaan === '') {
                // Show inline validation error instead of toastr
                $('#noKepesertaan').addClass('is-invalid');
                if ($('#noKepesertaan').next('.invalid-feedback').length === 0) {
                    $('#noKepesertaan').after(
                        '<div class="invalid-feedback" style="display:block; color: red;">Field ini wajib diisi.</div>'
                        );
                }
                return;
            }

            // AJAX untuk mencari pasien berdasarkan nomor kepesertaan
            $.ajax({
                url: '/cari-pasien/' + nomorKepesertaan,
                type: 'GET',
                success: function (response) {
                    if (response.success) {
                        // Tampilkan data pasien di modal
                        $('#namaPasien').val(response.pasien.nama_pasien);
                        $('#noRekamMedis').val(response.pasien.no_rekam_medis);
                        $('#umurPasien').val(response.pasien.umur);

                        // Make nomor rekam medis readonly and disable to prevent changes
                        $('#noRekamMedis').prop('readonly', true);
                        $('#noKepesertaan').prop('readonly', true);

                        // Tampilkan section data pasien dan ubah tombol
                        $('#dataPasien').show();
                        $('#btnCariPasien').hide();
                        $('#btnSimpanAntrian').show();
                    } else {
                        var audio = new Audio(
                            '/dokterAssets/sounds/gagal.mp3'); // Sesuaikan path file suara
                        audio.play();
                        toastr.error(response.message, "Pasien tidak ditemukan");
                        // Re-enable input if search failed

                    }
                },
                error: function () {
                    toastr.error("Terjadi kesalahan", "Error");
                    // Re-enable input on error

                }
            });
        });

        // SweetAlert konfirmasi ketika tombol "Tutup" ditekan setelah data pasien ditampilkan
        $('#btnTutupAntrian').click(function () {
            if ($('#btnSimpanAntrian').is(':visible')) {
                Swal.fire({
                    title: 'Anda yakin ingin menutup?',
                    text: "Data yang telah dimasukkan akan hilang!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, tutup!',
                    cancelButtonText: 'Lanjutkan'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#modalTambahAntrian').modal('hide');
                        resetModal();
                    }
                });
            } else {
                resetModal();
                $('#modalTambahAntrian').modal('hide');
            }
        });
    });

    function resetModal() {
        $('#noKepesertaan').val('');
        $('#noKepesertaan').removeClass('is-invalid');
        $('#noKepesertaan').next('.invalid-feedback').remove();
        $('#noKepesertaan').prop('readonly', false);
        $('#namaPasien').val('');
        $('#noRekamMedis').val('');
        $('#umurPasien').val('');
        $('#dataPasien').hide();
        $('#btnCariPasien').show();
        $('#btnSimpanAntrian').hide();
        $('#tujuanPoli').val(''); // Reset tujuanPoli select
    }

    // Add event listener to reset modal after it is fully hidden
    $('#modalTambahAntrian').on('hidden.bs.modal', function () {
        resetModal();
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Handle simpan antrian button click
    $('#btnSimpanAntrian').click(function () {
        var noRekamMedis = $('#noRekamMedis').val();
        var tanggalBerobat = $('#tanggalBerobat').val();
        var status = 'Perlu Analisa'; // Default status, can be adjusted
        var tujuanPoli = $('#tujuanPoli').val();

        if (!tanggalBerobat) {
            toastr.warning("Tanggal berobat harus diisi!", "Input Kosong");
            return;
        }

        $.ajax({
            url: '/antrian/store',
            type: 'POST',
            data: {
                no_rekam_medis: noRekamMedis,
                tanggal_berobat: tanggalBerobat,
                status: status,
                tujuan_poli: tujuanPoli
            },
            success: function (response) {
                toastr.success(response.message, "Berhasil");
                $('#modalTambahAntrian').modal('hide');
                // resetModal(); // No longer needed here because of hidden.bs.modal event
                // Reload the page to update the antrian list
                location.reload();
            },
            error: function (xhr) {
                var errorMessage = "Terjadi kesalahan saat menyimpan antrian";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                toastr.error(errorMessage, "Error");
            }
        });
    });

    // Set tanggalBerobat input to today's date in local timezone
    var today = new Date();
    var day = ("0" + today.getDate()).slice(-2);
    var month = ("0" + (today.getMonth() + 1)).slice(-2);
    var year = today.getFullYear();
    var localDate = year + "-" + month + "-" + day;
    $('#tanggalBerobat').val(localDate);

</script>

@endsection

@extends('dashboardadmin')

@section('admin')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>Pasien Rawat Jalan</strong></h1>
    <div class="row">
        <div class="col-12 col-lg-12 col-xxl-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex justify-content-between">
                    <form method="GET" action="{{ route('admin.rawatjalan') }}" class="d-flex flex-wrap align-items-center gap-2 m-0 p-0" id="searchForm">
                        <div class="input-group" style="width: 250px;">
                            <input type="text" name="search" class="form-control" id="searchInput" placeholder="Pencarian..." aria-label="Search" value="{{ request('search') }}" autocomplete="off">
                        </div>
                    </form>
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
                                <td class="nowrap">{{ \Carbon\Carbon::parse($antrian->pasien->tanggal_lahir)->age }} Tahun</td>
                                <td class="nowrap">{{ $antrian->pasien->jaminan_kesehatan }}</td>
                                <td class="nowrap">{{ $antrian->poli ? $antrian->poli->nama_poli : 'Tidak ada' }}</td>
                                <td class="nowrap">{{ \Carbon\Carbon::parse($antrian->tanggal_berobat)->format('d-m-Y') }}</td>
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
                <div class="mt-3 mb-2">
                    <div class="d-flex justify-content-between align-items-center w-100">
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


<!-- Modal Data Pasien -->
<div class="modal fade" id="modalPasienDetail" tabindex="-1" aria-labelledby="modalPasienDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
        <div class="modal-content" style="overflow-x: hidden;">
            <div class="modal-header d-flex justify-content-between">
                <h3 class="modal-title" id="modalPasienDetailLabel"><strong>Detail Pasien</strong></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-3" style="max-height: 400px; overflow-y: auto;">
                <form>
                    <div class="container-fluid">
                        <!-- Personal Info -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="modalNoRekamMedis" class="form-label">No. Rekam Medis</label>
                                <input type="text" class="form-control form-control-sm" id="modalNoRekamMedis" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalNikPasien" class="form-label">NIK</label>
                                <input type="text" class="form-control form-control-sm" id="modalNikPasien" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalNamaPasien" class="form-label">Nama Pasien</label>
                                <input type="text" class="form-control form-control-sm" id="modalNamaPasien" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="modalTempatLahir" class="form-label">Tempat Lahir</label>
                                <input type="text" class="form-control form-control-sm" id="modalTempatLahir" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalTanggalLahir" class="form-label">Tanggal Lahir</label>
                                <input type="text" class="form-control form-control-sm" id="modalTanggalLahir" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalJenisKelamin" class="form-label">Jenis Kelamin</label>
                                <input type="text" class="form-control form-control-sm" id="modalJenisKelamin" readonly>
                            </div>
                        </div>
                        <!-- Health Info -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="modalGolonganDarah" class="form-label">Golongan Darah</label>
                                <input type="text" class="form-control form-control-sm" id="modalGolonganDarah" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalAgama" class="form-label">Agama</label>
                                <input type="text" class="form-control form-control-sm" id="modalAgama" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalPekerjaan" class="form-label">Pekerjaan</label>
                                <input type="text" class="form-control form-control-sm" id="modalPekerjaan" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="modalStatusPernikahan" class="form-label">Status Pernikahan</label>
                                <input type="text" class="form-control form-control-sm" id="modalStatusPernikahan" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalKepalaKeluarga" class="form-label">Kepala Keluarga</label>
                                <input type="text" class="form-control form-control-sm" id="modalKepalaKeluarga" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalNoHp" class="form-label">Nomor Telepon</label>
                                <input type="text" class="form-control form-control-sm" id="modalNoHp" readonly>
                            </div>
                        </div>
                        <!-- Address Info -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="modalAlamat" class="form-label">Alamat</label>
                                <input type="text" class="form-control form-control-sm" id="modalAlamat" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="modalRt" class="form-label">RT</label>
                                <input type="text" class="form-control form-control-sm" id="modalRt" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalRw" class="form-label">RW</label>
                                <input type="text" class="form-control form-control-sm" id="modalRw" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalKelurahan" class="form-label">Kelurahan</label>
                                <input type="text" class="form-control form-control-sm" id="modalKelurahan" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="modalKecamatan" class="form-label">Kecamatan</label>
                                <input type="text" class="form-control form-control-sm" id="modalKecamatan" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalKabupaten" class="form-label">Kabupaten</label>
                                <input type="text" class="form-control form-control-sm" id="modalKabupaten" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalProvinsi" class="form-label">Provinsi</label>
                                <input type="text" class="form-control form-control-sm" id="modalProvinsi" readonly>
                            </div>
                        </div>
                        <!-- Insurance Info -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="modalJaminan" class="form-label">Jaminan Kesehatan</label>
                                <input type="text" class="form-control form-control-sm" id="modalJaminan" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="modalNoKepesertaan" class="form-label">No. Kepesertaan</label>
                                <input type="text" class="form-control form-control-sm" id="modalNoKepesertaan" readonly>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');
        const tableBody = document.querySelector('#rawatJalanTable tbody');
        let debounceTimeout = null;

        searchForm.addEventListener('submit', function(e) {
            e.preventDefault(); // prevent normal form submission
        });

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => {
                const query = searchInput.value;
                const url = searchForm.action + '?search=' + encodeURIComponent(query);

                fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        // Parse the returned HTML and extract the table body rows
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newTbody = doc.querySelector('#rawatJalanTable tbody');
                        if (newTbody) {
                            tableBody.innerHTML = newTbody.innerHTML;
                        }
                        // Optionally update pagination here if implemented
                    })
                    .catch(error => {
                        console.error('Error fetching search results:', error);
                    });
            }, 300); // delay 300ms after user stops typing
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalPasienDetail = document.getElementById('modalPasienDetail');
        if (!modalPasienDetail) {
            console.error('modalPasienDetail element not found');
            return;
        }
        modalPasienDetail.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var noRekamMedis = button.getAttribute('data-no_rekam_medis');
            var nik = button.getAttribute('data-nik');
            var nama = button.getAttribute('data-nama');
            var tempatLahir = button.getAttribute('data-tempat_lahir');
            var tanggalLahir = button.getAttribute('data-tanggal_lahir');
            var jenisKelamin = button.getAttribute('data-jenis_kelamin');
            var golDarah = button.getAttribute('data-gol_darah');
            var agama = button.getAttribute('data-agama');
            var pekerjaan = button.getAttribute('data-pekerjaan');
            var statusPernikahan = button.getAttribute('data-status_pernikahan');
            var alamat = button.getAttribute('data-alamat');
            var rt = button.getAttribute('data-rt');
            var rw = button.getAttribute('data-rw');
            var kelurahan = button.getAttribute('data-kelurahan');
            var kecamatan = button.getAttribute('data-kecamatan');
            var kabupaten = button.getAttribute('data-kabupaten');
            var provinsi = button.getAttribute('data-provinsi');
            var jaminan = button.getAttribute('data-jaminan');
            var noKepesertaan = button.getAttribute('data-no_kepesertaan');
            var kepalaKeluarga = button.getAttribute('data-kepala_keluarga');
            var noHp = button.getAttribute('data-no_hp');

            modalPasienDetail.querySelector('#modalNoRekamMedis').value = noRekamMedis || '';
            modalPasienDetail.querySelector('#modalNikPasien').value = nik || '';
            modalPasienDetail.querySelector('#modalNamaPasien').value = nama || '';
            modalPasienDetail.querySelector('#modalTempatLahir').value = tempatLahir || '';
            modalPasienDetail.querySelector('#modalTanggalLahir').value = tanggalLahir || '';
            modalPasienDetail.querySelector('#modalJenisKelamin').value = jenisKelamin || '';
            modalPasienDetail.querySelector('#modalGolonganDarah').value = golDarah || '';
            modalPasienDetail.querySelector('#modalAgama').value = agama || '';
            modalPasienDetail.querySelector('#modalPekerjaan').value = pekerjaan || '';
            modalPasienDetail.querySelector('#modalStatusPernikahan').value = statusPernikahan || '';
            modalPasienDetail.querySelector('#modalAlamat').value = alamat || '';
            modalPasienDetail.querySelector('#modalRt').value = rt || '';
            modalPasienDetail.querySelector('#modalRw').value = rw || '';
            modalPasienDetail.querySelector('#modalKelurahan').value = kelurahan || '';
            modalPasienDetail.querySelector('#modalKecamatan').value = kecamatan || '';
            modalPasienDetail.querySelector('#modalKabupaten').value = kabupaten || '';
            modalPasienDetail.querySelector('#modalProvinsi').value = provinsi || '';
            modalPasienDetail.querySelector('#modalJaminan').value = jaminan || '';
            modalPasienDetail.querySelector('#modalNoKepesertaan').value = noKepesertaan || '';
            modalPasienDetail.querySelector('#modalKepalaKeluarga').value = kepalaKeluarga || '';
            modalPasienDetail.querySelector('#modalNoHp').value = noHp || '';
        });
    });
</script>
@endsection
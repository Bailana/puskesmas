@extends('dashboardbidan')

@section('bidan')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>Data Pasien</strong></h1>
    <div class="row">
        <div class="col-12 col-lg-12 col-xxl-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex justify-content-between">
                    <!-- Input Pencarian -->
<form method="GET" action="{{ route('bidan.pasien') }}" class="d-flex align-items-center"
                        style="gap: 10px;">
                        <div class="input-group" style="width: 250px;">
                            <input type="text" name="search" class="form-control" id="searchInput"
                                placeholder="Pencarian..." aria-label="Search" value="{{ request('search') }}"
                                autocomplete="off">
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1"
                            id="filterButton" title="Filter Data Pasien" data-bs-toggle="modal"
                            data-bs-target="#filterModal">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </form>
                </div>
                <table class="table table-hover my-0" id="dokterPasien">
                    <thead>
                        <tr>
                            <th style="white-space: nowrap;">No.</th>
                            <th style="white-space: nowrap;">No. RM</th>
                            <th style="white-space: nowrap;">Nama Pasien</th>
                            <th style="white-space: nowrap;">Tempat, Tanggal Lahir</th>
                            <th style="white-space: nowrap;">Jenis Kelamin</th>
                            <th style="white-space: nowrap;">Gol.Darah</th>
                            <th style="white-space: nowrap;">JamKes</th>
                            <th style="white-space: nowrap;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pasiens as $index => $pasien)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $pasien->no_rekam_medis }}</td>
                            <td>{{ $pasien->nama_pasien}}</td>
                            <td>
{{ $pasien->tempat_lahir }},
{{ \Carbon\Carbon::parse($pasien->tanggal_lahir)->format('d-m-Y') ?? 'Tanggal tidak tersedia' }}
                            </td>
                            <td>{{ $pasien->jenis_kelamin }}</td>
                            <td>{{ $pasien->gol_darah }}</td>
                            <td>{{ $pasien->jaminan_kesehatan }}</td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm rounded" data-bs-toggle="modal"
                                    data-bs-target="#modalPasienDetail"
                                    data-no_rekam_medis="{{ $pasien->no_rekam_medis }}" data-nik="{{ $pasien->nik }}"
                                    data-nama="{{ $pasien->nama_pasien }}"
                                    data-tempat_lahir="{{ $pasien->tempat_lahir }}"
data-tanggal_lahir="{{ $pasien->tanggal_lahir ? \Carbon\Carbon::parse($pasien->tanggal_lahir)->format('d-m-Y') : 'Tanggal tidak tersedia' }}"
                                    data-jenis_kelamin="{{ $pasien->jenis_kelamin }}"
                                    data-gol_darah="{{ $pasien->gol_darah }}" data-agama="{{ $pasien->agama }}"
                                    data-pekerjaan="{{ $pasien->pekerjaan }}"
                                    data-status_pernikahan="{{ $pasien->status_pernikahan }}"
                                    data-alamat="{{ $pasien->alamat_jalan }}" data-rt="{{ $pasien->rt }}"
                                    data-rw="{{ $pasien->rw }}" data-kelurahan="{{ $pasien->kelurahan }}"
                                    data-kecamatan="{{ $pasien->kecamatan }}" data-kabupaten="{{ $pasien->kabupaten }}"
                                    data-provinsi="{{ $pasien->provinsi }}"
                                    data-jaminan="{{ $pasien->jaminan_kesehatan }}"
                                    data-no_kepesertaan="{{ $pasien->nomor_kepesertaan }}"
                                    data-kepala_keluarga="{{ $pasien->kepala_keluarga}}"
                                    data-no_hp="{{ $pasien->no_hp}}">
                                    Selengkapnya
                                </button>
                                <button type="button" class="btn btn-danger btn-sm rounded" data-bs-toggle="modal"
                                    data-bs-target="#modalRiwayatBerobat"
                                    data-no_rekam_medis="{{ $pasien->no_rekam_medis }}"
                                    data-nama="{{ $pasien->nama_pasien }}">
                                    Riwayat Berobat
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Modal Filter -->
            <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
<form method="GET" action="{{ route('perawat.pasien') }}">
                            <div class="modal-header">
                                <h5 class="modal-title" id="filterModalLabel">Filter Data Pasien</h5>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-select">
                                            <option value="">Semua</option>
                                            <option value="Laki-laki"
                                                {{ request('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>
                                                Laki-laki</option>
                                            <option value="Perempuan"
                                                {{ request('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>
                                                Perempuan</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gol_darah" class="form-label">Golongan Darah</label>
                                        <select name="gol_darah" id="gol_darah" class="form-select">
                                            <option value="">Semua</option>
                                            <option value="A" {{ request('gol_darah') == 'A' ? 'selected' : '' }}>A
                                            </option>
                                            <option value="B" {{ request('gol_darah') == 'B' ? 'selected' : '' }}>B
                                            </option>
                                            <option value="AB" {{ request('gol_darah') == 'AB' ? 'selected' : '' }}>AB
                                            </option>
                                            <option value="O" {{ request('gol_darah') == 'O' ? 'selected' : '' }}>O
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="jaminan_kesehatan" class="form-label">Jaminan Kesehatan</label>
                                        <select name="jaminan_kesehatan" id="jaminan_kesehatan" class="form-select">
                                            <option value="">Semua</option>
                                            <option value="Umum"
                                                {{ request('jaminan_kesehatan') == 'Umum' ? 'selected' : '' }}>Umum
                                            </option>
                                            <option value="BPJS Kesehatan"
                                                {{ request('jaminan_kesehatan') == 'BPJS Kesehatan' ? 'selected' : '' }}>
                                                BPJS Kesehatan</option>
                                            <option value="Perusahaan"
                                                {{ request('jaminan_kesehatan') == 'Perusahaan' ? 'selected' : '' }}>
                                                Perusahaan</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                                        <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control"
                                            value="{{ request('tempat_lahir') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="kecamatan" class="form-label">Kecamatan</label>
                                        <input type="text" name="kecamatan" id="kecamatan" class="form-control"
                                            value="{{ request('kecamatan') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="kelurahan" class="form-label">Kelurahan</label>
                                        <input type="text" name="kelurahan" id="kelurahan" class="form-control"
                                            value="{{ request('kelurahan') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="status_pernikahan" class="form-label">Status Pernikahan</label>
                                        <select name="status_pernikahan" id="status_pernikahan" class="form-select">
                                            <option value="">Semua</option>
                                            <option value="Belum Menikah"
                                                {{ request('status_pernikahan') == 'Belum Menikah' ? 'selected' : '' }}>
                                                Belum Menikah</option>
                                            <option value="Menikah"
                                                {{ request('status_pernikahan') == 'Menikah' ? 'selected' : '' }}>
                                                Menikah</option>
                                            <option value="Cerai"
                                                {{ request('status_pernikahan') == 'Cerai' ? 'selected' : '' }}>Cerai
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control"
                                            value="{{ request('tanggal_lahir') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer d-flex justify-content-end mt-3" style="gap: 10px;">
                                <button type="button" class="btn btn-danger " data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Data Pasien -->
<div class="modal fade" id="modalPasienDetail" tabindex="-1" aria-labelledby="modalPasienDetailLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
        <div class="modal-content" style="overflow-x: hidden;">
            <div class="modal-header d-flex justify-content-between">
                <h3 class="modal-title" id="modalPasienDetailLabel"><strong>Detail Pasien</strong></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body with Scroll -->
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
                                <input type="text" class="form-control form-control-sm" id="modalGolonganDarah"
                                    readonly>
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
                                <input type="text" class="form-control form-control-sm" id="modalStatusPernikahan"
                                    readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="modalKepalaKeluarga" class="form-label">Kepala Keluarga</label>
                                <input type="text" class="form-control form-control-sm" id="modalKepalaKeluarga"
                                    readonly>
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
                                <input type="text" class="form-control form-control-sm" id="modalNoKepesertaan"
                                    readonly>
                            </div>
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
    var pasienDetailModal = document.getElementById('modalPasienDetail');
    pasienDetailModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var noRekamMedis = button.getAttribute('data-no_rekam_medis');

        // Fetch patient detail from server
        fetch(`/bidan/pasien/detail/${noRekamMedis}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }
            // Update the modal's input fields with fetched data
            pasienDetailModal.querySelector('#modalNoRekamMedis').value = data.no_rekam_medis || '';
            pasienDetailModal.querySelector('#modalNikPasien').value = data.nik || '';
            pasienDetailModal.querySelector('#modalNamaPasien').value = data.nama_pasien || '';
            pasienDetailModal.querySelector('#modalTempatLahir').value = data.tempat_lahir || '';
            pasienDetailModal.querySelector('#modalTanggalLahir').value = data.tanggal_lahir ? new Date(data.tanggal_lahir).toLocaleDateString('id-ID') : '';
            pasienDetailModal.querySelector('#modalJenisKelamin').value = data.jenis_kelamin || '';
            pasienDetailModal.querySelector('#modalGolonganDarah').value = data.gol_darah || '';
            pasienDetailModal.querySelector('#modalAgama').value = data.agama || '';
            pasienDetailModal.querySelector('#modalPekerjaan').value = data.pekerjaan || '';
            pasienDetailModal.querySelector('#modalStatusPernikahan').value = data.status_pernikahan || '';
            pasienDetailModal.querySelector('#modalAlamat').value = data.alamat_jalan || '';
            pasienDetailModal.querySelector('#modalRt').value = data.rt || '';
            pasienDetailModal.querySelector('#modalRw').value = data.rw || '';
            pasienDetailModal.querySelector('#modalKelurahan').value = data.kelurahan || '';
            pasienDetailModal.querySelector('#modalKecamatan').value = data.kecamatan || '';
            pasienDetailModal.querySelector('#modalKabupaten').value = data.kabupaten || '';
            pasienDetailModal.querySelector('#modalProvinsi').value = data.provinsi || '';
            pasienDetailModal.querySelector('#modalJaminan').value = data.jaminan_kesehatan || '';
            pasienDetailModal.querySelector('#modalNoKepesertaan').value = data.nomor_kepesertaan || '';
            pasienDetailModal.querySelector('#modalKepalaKeluarga').value = data.kepala_keluarga || '';
            pasienDetailModal.querySelector('#modalNoHp').value = data.no_hp || '';
        })
        .catch(error => {
            console.error('Error fetching patient detail:', error);
            alert('Gagal mengambil data pasien.');
        });
    });

</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const pasienTableBody = document.querySelector('#dokterPasien tbody');

        function renderTableRows(pasiens) {
            pasienTableBody.innerHTML = '';
            if (pasiens.length === 0) {
                pasienTableBody.innerHTML =
                    '<tr><td colspan="8" class="text-center">Data pasien tidak ditemukan</td></tr>';
                return;
            }
            pasiens.forEach((pasien, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${pasien.no_rekam_medis}</td>
                            <td>${pasien.nama_pasien}</td>
                            <td>${pasien.tempat_lahir}, ${pasien.tanggal_lahir ? new Date(pasien.tanggal_lahir).toLocaleDateString('id-ID') : 'Tanggal tidak tersedia'}</td>
                            <td>${pasien.jenis_kelamin}</td>
                            <td>${pasien.gol_darah}</td>
                            <td>${pasien.jaminan_kesehatan}</td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm rounded" data-bs-toggle="modal"
                                    data-bs-target="#modalPasienDetail"
                                    data-no_rekam_medis="${pasien.no_rekam_medis}" data-nik="${pasien.nik}"
                                    data-nama="${pasien.nama_pasien}"
                                    data-tempat_lahir="${pasien.tempat_lahir}"
                                    data-tanggal_lahir="${pasien.tanggal_lahir ? new Date(pasien.tanggal_lahir).toLocaleDateString('id-ID') : 'Tanggal tidak tersedia'}"
                                    data-jenis_kelamin="${pasien.jenis_kelamin}"
                                    data-gol_darah="${pasien.gol_darah}" data-agama="${pasien.agama}"
                                    data-pekerjaan="${pasien.pekerjaan}"
                                    data-status_pernikahan="${pasien.status_pernikahan}"
                                    data-alamat="${pasien.alamat_jalan}" data-rt="${pasien.rt}"
                                    data-rw="${pasien.rw}" data-kelurahan="${pasien.kelurahan}"
                                    data-kecamatan="${pasien.kecamatan}" data-kabupaten="${pasien.kabupaten}"
                                    data-provinsi="${pasien.provinsi}"
                                    data-jaminan="${pasien.jaminan_kesehatan}"
                                    data-no_kepesertaan="${pasien.nomor_kepesertaan}"
                                    data-kepala_keluarga="${pasien.kepala_keluarga}"
                                    data-no_hp="${pasien.no_hp}">
                                    Selengkapnya
                                </button>
                                <button type="button" class="btn btn-success btn-sm rounded" data-bs-toggle="modal"
                                    data-bs-target="#modalRiwayatBerobat"
                                    data-no_rekam_medis="${pasien.no_rekam_medis}"
                                    data-nama="${pasien.nama_pasien}">
                                    Riwayat Berobat
                                </button>
                            </td>
                        `;
                pasienTableBody.appendChild(row);
            });
        }

        let debounceTimeout;
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => {
                const query = searchInput.value.trim();
fetch("{{ route('bidan.pasien') }}?search=" + encodeURIComponent(query), {
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
    });

</script>
@endsection

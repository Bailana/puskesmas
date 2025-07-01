@extends('dashboardadmin')

@section('admin')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>Data Pasien Rawat Jalan</strong></h1>
    <div class="row">
        <div class="col-12 col-lg-12 col-xxl-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex justify-content-between">
                    <form method="GET" action="{{ route('admin.rawatjalan') }}" class="w-50 d-flex" id="searchForm">
                        <input type="search" id="searchInput" name="search" class="form-control" placeholder="Cari pasien..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary ms-2">Cari</button>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover my-0" id="rawatJalanTable">
                        <thead>
                            <tr>
                                <th style="white-space: nowrap;">No.</th>
                                <th style="white-space: nowrap;">No. RM</th>
                                <th style="white-space: nowrap;">Nama Pasien</th>
                                <th style="white-space: nowrap;">Tempat, Tanggal Lahir</th>
                                <th style="white-space: nowrap;">JamKes</th>
                                <th style="white-space: nowrap;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pasiens as $index => $pasien)
                            <tr>
                                <td style="white-space: nowrap;">{{ $pasiens->firstItem() + $index }}</td>
                                <td style="white-space: nowrap;">{{ $pasien->no_rekam_medis }}</td>
                                <td style="white-space: nowrap;">{{ $pasien->nama_pasien }}</td>
                                <td style="white-space: nowrap;">{{ $pasien->tempat_lahir }}, {{ \Carbon\Carbon::parse($pasien->tanggal_lahir)->format('d-m-Y') ?? 'Tanggal tidak tersedia' }}</td>
                                <td style="white-space: nowrap;">{{ $pasien->jaminan_kesehatan }}</td>
                                <td style="white-space: nowrap;">
                                    <button type="button" class="btn btn-info btn-sm rounded" data-bs-toggle="modal" data-bs-target="#modalPasienDetail"
                                        data-no_rekam_medis="{{ $pasien->no_rekam_medis }}" data-nik="{{ $pasien->nik }}"
                                        data-nama="{{ $pasien->nama_pasien }}" data-tempat_lahir="{{ $pasien->tempat_lahir }}"
                                        data-tanggal_lahir="{{ $pasien->tanggal_lahir ? \Carbon\Carbon::parse($pasien->tanggal_lahir)->format('d-m-Y') : 'Tanggal tidak tersedia' }}"
                                        data-jenis_kelamin="{{ $pasien->jenis_kelamin }}" data-gol_darah="{{ $pasien->gol_darah }}"
                                        data-agama="{{ $pasien->agama }}" data-pekerjaan="{{ $pasien->pekerjaan }}"
                                        data-status_pernikahan="{{ $pasien->status_pernikahan }}" data-alamat="{{ $pasien->alamat_jalan }}"
                                        data-rt="{{ $pasien->rt }}" data-rw="{{ $pasien->rw }}" data-kelurahan="{{ $pasien->kelurahan }}"
                                        data-kecamatan="{{ $pasien->kecamatan }}" data-kabupaten="{{ $pasien->kabupaten }}"
                                        data-provinsi="{{ $pasien->provinsi }}" data-jaminan="{{ $pasien->jaminan_kesehatan }}"
                                        data-no_kepesertaan="{{ $pasien->nomor_kepesertaan }}" data-kepala_keluarga="{{ $pasien->kepala_keluarga }}"
                                        data-no_hp="{{ $pasien->no_hp }}">
                                        Selengkapnya
                                    </button>
                                    <a href="{{ route('admin.rawatjalan.edit', $pasien->id) }}" class="btn btn-warning btn-sm rounded ms-1">Ubah</a>
                                    <form action="{{ route('admin.rawatjalan.destroy', $pasien->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm rounded">Hapus</button>
                                    </form>
                                    <a href="{{ route('admin.rawatjalan.surat', $pasien->id) }}" class="btn btn-success btn-sm rounded ms-1">Surat</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

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
        </div>
    </div>
</div>
@endsection
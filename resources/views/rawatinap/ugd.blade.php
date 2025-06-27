@extends('dashboardrawatinap')

@section('rawatinap')
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3"><strong>Data Pasien Unit Gawat Darurat</strong></h1>
        <div class="row">
            <div class="col-12 col-lg-12 col-xxl-12 d-flex">
                <div class="card flex-fill">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div style="max-width: 300px;">
                            <input type="text" id="searchInput" class="form-control form-control-sm"
                                placeholder="Cari pasien...">
                        </div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#modalTambahPasien">
                            Tambah Pasien
                        </button>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover my-0" id="pasienTable">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Hari/Tanggal Masuk</th>
                                    <th>Nama Pasien</th>
                                    <th>Umur</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($pasiens_ugd) > 0)
                                    @foreach ($pasiens_ugd as $index => $pasien)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ \Carbon\Carbon::parse($pasien->tanggal_masuk)->translatedFormat('l, d-m-Y') }}
                                            </td>
                                            <td>{{ $pasien->nama_pasien }}</td>
                                            <td>
                                                @php
                                                    $umur = null;
                                                    if ($pasien->pasien_id) {
                                                        $pasienDb = \App\Models\Pasien::find($pasien->pasien_id);
                                                        if ($pasienDb && $pasienDb->tanggal_lahir) {
                                                            $umur = \Carbon\Carbon::parse($pasienDb->tanggal_lahir)->age;
                                                        }
                                                    }
                                                @endphp
                                                {{ $umur !== null ? $umur . ' tahun' : '-' }}
                                            </td>
                                            <td>
                                                @php
                                                    $status = $pasien->status ?: 'Perlu Analisa';
                                                    $badgeClass = 'bg-secondary';
                                                    if (strtolower($status) === 'perlu analisa') {
                                                        $badgeClass = 'bg-warning text-dark';
                                                    } elseif (strtolower($status) === 'rawat inap') {
                                                        $badgeClass = 'bg-primary';
                                                    } elseif (strtolower($status) === 'rawat jalan') {
                                                        $badgeClass = 'bg-success';
                                                    }
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-info btn-sm btn-selengkapnya" data-bs-toggle="modal"
                                                    data-bs-target="#modalDetailPasien"
                                                    data-no-rekam-medis="{{ $pasien->pasien ? $pasien->pasien->no_rekam_medis : '' }}">Selengkapnya</button>
                                                <button class="btn btn-warning btn-sm ms-1" data-bs-toggle="modal"
                                                    data-bs-target="#modalAnalisa" data-pasien="{{ json_encode($pasien) }}"
                                                    data-pasien-id="{{ $pasien->pasien_id }}">Analisa</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data pasien unit gawat darurat</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.getElementById('searchInput').addEventListener('keyup', function () {
                var filter = this.value.toLowerCase();
                var rows = document.querySelectorAll('#pasienTable tbody tr');

                rows.forEach(function (row) {
                    var text = row.textContent.toLowerCase();
                    row.style.display = text.indexOf(filter) > -1 ? '' : 'none';
                });
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var modalDetailPasien = document.getElementById('modalDetailPasien');
                if (modalDetailPasien) {
                    modalDetailPasien.addEventListener('show.bs.modal', function (event) {
                        var button = event.relatedTarget;
                        var noRekamMedis = button.getAttribute('data-no-rekam-medis');

                        // Clear modal fields before loading new data
                        var fields = [
                            'modalNoRekamMedis', 'modalNikPasien', 'modalNamaPasien', 'modalTempatLahir', 'modalTanggalLahir',
                            'modalJenisKelamin', 'modalGolonganDarah', 'modalAgama', 'modalPekerjaan', 'modalStatusPernikahan',
                            'modalKepalaKeluarga', 'modalNoHp', 'modalAlamat', 'modalRt', 'modalRw', 'modalKelurahan',
                            'modalKecamatan', 'modalKabupaten', 'modalProvinsi', 'modalJaminan', 'modalNoKepesertaan'
                        ];
                        fields.forEach(function (fieldId) {
                            var el = document.getElementById(fieldId);
                            if (el) el.value = '';
                        });

                        if (!noRekamMedis) {
                            console.error('No Rekam Medis not found on button');
                            return;
                        }

                        fetch('/rawatinap/ugd/detail/' + encodeURIComponent(noRekamMedis))
                            .then(response => response.json())
                            .then(data => {
                                if (data.success && data.data) {
                                    var pasien = data.data;
                                    document.getElementById('modalNoRekamMedis').value = pasien.no_rekam_medis || '';
                                    document.getElementById('modalNikPasien').value = pasien.nik || '';
                                    document.getElementById('modalNamaPasien').value = pasien.nama_pasien || '';
                                    document.getElementById('modalTempatLahir').value = pasien.tempat_lahir || '';
                                    document.getElementById('modalTanggalLahir').value = pasien.tanggal_lahir || '';
                                    document.getElementById('modalJenisKelamin').value = pasien.jenis_kelamin || '';
                                    document.getElementById('modalGolonganDarah').value = pasien.gol_darah || '';
                                    document.getElementById('modalAgama').value = pasien.agama || '';
                                    document.getElementById('modalPekerjaan').value = pasien.pekerjaan || '';
                                    document.getElementById('modalStatusPernikahan').value = pasien.status_pernikahan || '';
                                    document.getElementById('modalKepalaKeluarga').value = pasien.kepala_keluarga || '';
                                    document.getElementById('modalNoHp').value = pasien.no_hp || '';
                                    document.getElementById('modalAlamat').value = pasien.alamat_jalan || '';
                                    document.getElementById('modalRt').value = pasien.rt || '';
                                    document.getElementById('modalRw').value = pasien.rw || '';
                                    document.getElementById('modalKelurahan').value = pasien.kelurahan || '';
                                    document.getElementById('modalKecamatan').value = pasien.kecamatan || '';
                                    document.getElementById('modalKabupaten').value = pasien.kabupaten || '';
                                    document.getElementById('modalProvinsi').value = pasien.provinsi || '';
                                    document.getElementById('modalJaminan').value = pasien.jaminan_kesehatan || '';
                                    document.getElementById('modalNoKepesertaan').value = pasien.nomor_kepesertaan || '';
                                } else {
                                    alert('Data pasien tidak ditemukan.');
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching pasien data:', error);
                                alert('Gagal mengambil data pasien.');
                            });
                    });
                }
            });
        </script>
    </div>

    <!-- Modal Tambah Pasien -->
    <div class="modal fade" id="modalTambahPasien" tabindex="-1" aria-labelledby="modalTambahPasienLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="formTambahPasienUgd" method="POST" action="{{ url('/rawatinap/ugd/store') }}">
                    @csrf
                    <input type="hidden" id="pasien_id_cari" name="pasien_id" required>
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahPasienLabel">Tambah Pasien UGD</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 row align-items-end">
                            <div class="col-md-8">
                                <label for="nomor_kepesertaan_cari" class="form-label">Nomor Kepesertaan</label>
                                <input type="text" class="form-control" id="nomor_kepesertaan_cari"
                                    name="nomor_kepesertaan_cari" required maxlength="16" pattern="\d{1,16}"
                                    inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,16);">
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary w-100" id="btnCariPasien">Cari</button>
                            </div>
                        </div>
                        <div id="hasilCariPasien" style="display:none;">
                            <div class="mb-2">
                                <label class="form-label">Nama Pasien</label>
                                <input type="text" class="form-control" id="nama_pasien_cari" name="nama_pasien" readonly>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">No. Rekam Medis</label>
                                <input type="text" class="form-control" id="no_rekam_medis_cari" name="no_rekam_medis"
                                    readonly>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">NIK</label>
                                <input type="text" class="form-control" id="nik_pasien_cari" readonly>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="text" class="form-control" id="tgl_lahir_pasien_cari" readonly>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Jenis Kelamin</label>
                                <input type="text" class="form-control" id="jk_pasien_cari" readonly>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Alamat</label>
                                <input type="text" class="form-control" id="alamat_pasien_cari" readonly>
                            </div>
                        </div>
                        <div id="formFieldUgd" style="display:none;">
                            <div class="mb-3">
                                <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                                <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-success" id="btnTambahPasienUgd"
                            style="display:none;">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $('#btnCariPasien').on('click', function () {
                var nomor = $('#nomor_kepesertaan_cari').val().trim();
                if (!nomor) {
                    alert('Nomor Kepesertaan wajib diisi!');
                    return;
                }
                // AJAX cari pasien berdasarkan nomor kepesertaan
                $.get('/rawatinap/pasien/cari-nomor-kepesertaan', { nomor_kepesertaan: nomor }, function (res) {
                    if (res && res.id) {
                        // Set input hidden pasien_id sesuai hasil pencarian
                        $('#pasien_id_cari').val(res.id);
                        $('#hasilCariPasien').show();
                        $('#formFieldUgd').show();
                        $('#nama_pasien_cari').val(res.nama_pasien);
                        $('#no_rekam_medis_cari').val(res.no_rekam_medis);
                        $('#nik_pasien_cari').val(res.nik);
                        $('#tgl_lahir_pasien_cari').val(res.tanggal_lahir);
                        $('#jk_pasien_cari').val(res.jenis_kelamin);
                        $('#alamat_pasien_cari').val(res.alamat_jalan);
                        $('#pasien_id_cari').val(res.id); // PENTING: set value pasien_id
                        console.log('DEBUG: pasien_id di-set ke', res.id);
                        $('#btnCariPasien').hide();
                        $('#btnTambahPasienUgd').show();
                        $('#nomor_kepesertaan_cari').prop('readonly', true);
                    } else {
                        $('#pasien_id_cari').val(''); // Kosongkan jika tidak ditemukan
                        alert('Pasien tidak ditemukan!');
                        $('#hasilCariPasien').hide();
                        $('#formFieldUgd').hide();
                        $('#btnTambahPasienUgd').hide();
                    }
                }, 'json').fail(function (xhr, status, error) {
                    $('#pasien_id_cari').val(''); // Kosongkan jika error
                    alert('Gagal mencari pasien!');
                });
            });
            // Reset modal saat dibuka
            $('#modalTambahPasien').on('show.bs.modal', function () {
                $('#formTambahPasienUgd')[0].reset();
                $('#hasilCariPasien').hide();
                $('#formFieldUgd').hide();
                $('#btnTambahPasienUgd').hide();
                $('#btnCariPasien').show();
                $('#nomor_kepesertaan_cari').prop('readonly', false);

                // Set tanggal masuk otomatis ke hari ini saat modal dibuka
                var today = new Date();
                var yyyy = today.getFullYear();
                var mm = String(today.getMonth() + 1).padStart(2, '0');
                var dd = String(today.getDate()).padStart(2, '0');
                var todayStr = yyyy + '-' + mm + '-' + dd;
                $('#tanggal_masuk').val(todayStr);
            });

            // Ganti submit form menjadi AJAX
            $('#formTambahPasienUgd').off('submit').on('submit', function (e) {
                var pasienId = $('#pasien_id_cari').val();
                if (!pasienId) {
                    alert('Silakan cari dan pilih pasien terlebih dahulu!');
                    e.preventDefault();
                    return false;
                }
                // Debug: pastikan pasien_id benar-benar terkirim
                console.log('DEBUG SUBMIT: pasien_id yang dikirim:', pasienId);

                e.preventDefault();
                var form = this;
                var formData = $(form).serialize();
                var url = $(form).attr('action');
                var btn = $('#btnTambahPasienUgd');
                btn.prop('disabled', true).text('Menyimpan...');
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() },
success: function (res) {
    console.log('AJAX success response:', res);
    if (res.success && res.data) {
        // Tambahkan baris baru ke tabel
        var data = res.data;
        var rowCount = $('#pasienTable tbody tr').length;
        var newRow = '<tr>' +
            '<td>' + (rowCount + 1) + '</td>' +
            '<td>' + (data.tanggal_masuk || '') + '</td>' +
            '<td>' + (data.nama_pasien || '') + '</td>' +
            '<td>' + (data.umur || '') + '</td>' +
            '<td>' + (data.status || '') + '</td>' +
            '<td>' +
            '<button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalDetailPasien" data-pasien="' +
            encodeURIComponent(data.pasien_json) + '">Selengkapnya</button> ' +
            '<button class="btn btn-warning btn-sm ms-1" data-bs-toggle="modal" data-bs-target="#modalAnalisa" data-pasien="' +
            encodeURIComponent(data.pasien_json) + '">Analisa</button>' +
            '</td>' +
            '</tr>';
        $('#pasienTable tbody').append(newRow);
        // Reset dan tutup modal
        $('#modalTambahPasien').modal('hide');
        form.reset();
        $('#hasilCariPasien').hide();
        $('#formFieldUgd').hide();
        $('#btnTambahPasienUgd').hide();
        $('#btnCariPasien').show();
        $('#nomor_kepesertaan_cari').prop('readonly', false);
        // Optional: scroll ke baris baru
        // toastr.success('Pasien UGD berhasil ditambahkan!');
    } else {
        alert(res.message || 'Gagal menambah pasien UGD.');
    }
},
                    error: function (xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            var msg = Object.values(errors).map(function (e) { return e[0]; }).join('\n');
                            alert(msg);
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            alert(xhr.responseJSON.message);
                        } else {
                            alert('Terjadi kesalahan saat menyimpan data.');
                        }
                    },
                    complete: function () {
                        btn.prop('disabled', false).text('Tambah');
                    }
                });
            });
        });
    </script>

    <!-- Modal Detail Pasien UGD -->
    <div class="modal fade" id="modalDetailPasien" tabindex="-1" aria-labelledby="modalDetailPasienLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
            <div class="modal-content" style="overflow-x: hidden;">
                <div class="modal-header d-flex justify-content-between">
                    <h3 class="modal-title" id="modalDetailPasienLabel"><strong>Detail Pasien</strong></h3>
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
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Analisa -->
    <div class="modal fade" id="modalAnalisa" tabindex="-1" aria-labelledby="modalAnalisaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
            <div class="modal-content" style="overflow-x: hidden;">
                <div class="modal-header d-flex justify-content-between">
                    <h3 class="modal-title" id="modalAnalisaLabel">
                        <strong>Analisa <span class="nama-pasien" id="nama_pasien_display"></span></strong>
                    </h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 400px; overflow-y: auto; padding: 10px;">
                    @csrf
                    <form id="formAnalisa" method="POST" action="{{ route('rawatinap.hasilanalisa.store') }}">
                        @csrf
                        <input type="hidden" id="pasien_id" name="pasien_id">
                        <!-- Removed hidden input no_rekam_medis, hanya gunakan pasien_id -->
                        <!-- Tanda Vital -->
                        <div class="mb-3">
                            <h5>Tanda Vital</h5>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="tekananDarah" class="form-label">Tekanan Darah (mmHg)</label>
                                <input type="text" class="form-control form-control-sm" id="tekananDarah"
                                    name="tekanan_darah">
                            </div>
                            <div class="col-6">
                                <label for="frekuensiNadi" class="form-label">Frekuensi Nadi (/menit)</label>
                                <input type="text" class="form-control form-control-sm" id="frekuensiNadi"
                                    name="frekuensi_nadi">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="suhu" class="form-label">Suhu (°C)</label>
                                <input type="text" class="form-control form-control-sm" id="suhu" name="suhu">
                            </div>
                            <div class="col-6">
                                <label for="frekuensiNafas" class="form-label">Frekuensi Nafas (/menit)</label>
                                <input type="text" class="form-control form-control-sm" id="frekuensiNafas"
                                    name="frekuensi_nafas">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="skorNyeri" class="form-label">Skor Nyeri</label>
                                <input type="text" class="form-control form-control-sm" id="skorNyeri" name="skor_nyeri">
                            </div>
                            <div class="col-6">
                                <label for="skorJatuh" class="form-label">Skor Jatuh</label>
                                <input type="text" class="form-control form-control-sm" id="skorJatuh" name="skor_jatuh">
                            </div>
                        </div>
                        <hr>
                        <!-- Antropometri -->
                        <div class="mb-3">
                            <h5>Antropometri</h5>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="beratBadan" class="form-label">Berat Badan</label>
                                <input type="text" class="form-control form-control-sm" id="beratBadan" name="berat_badan">
                            </div>
                            <div class="col-6">
                                <label for="tinggiBadan" class="form-label">Tinggi Badan</label>
                                <input type="text" class="form-control form-control-sm" id="tinggiBadan"
                                    name="tinggi_badan">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="lingkarKepala" class="form-label">Lingkar Kepala</label>
                                <input type="text" class="form-control form-control-sm" id="lingkarKepala"
                                    name="lingkar_kepala">
                            </div>
                            <div class="col-6">
                                <label for="imt" class="form-label">IMT</label>
                                <input type="text" class="form-control form-control-sm" id="imt" name="imt">
                            </div>
                        </div>
                        <hr>
                        <!-- Fungsional -->
                        <div class="mb-3">
                            <h5>Fungsional</h5>

                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="alatBantu" class="form-label">Alat Bantu</label>
                                <input type="text" class="form-control form-control-sm" id="alatBantu" name="alat_bantu">
                            </div>
                            <div class="col-6">
                                <label for="prosthesa" class="form-label">Prosthesa</label>
                                <input type="text" class="form-control form-control-sm" id="prosthesa" name="prosthesa">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="cacatTubuh" class="form-label">Cacat Tubuh</label>
                                <input type="text" class="form-control form-control-sm" id="cacatTubuh" name="cacat_tubuh">
                            </div>
                            <div class="col-6">
                                <label for="adlMandiri" class="form-label">ADL Mandiri</label>
                                <input type="text" class="form-control form-control-sm" id="adlMandiri" name="adl_mandiri">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="riwayatJatuh" class="form-label">Riwayat Jatuh</label>
                                <input type="text" class="form-control form-control-sm" id="riwayatJatuh"
                                    name="riwayat_jatuh">
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-6">
                                <h5>Status Psikologi</h5>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkboxDepresi"
                                        name="status_psikologi[]" value="Depresi">
                                    <label class="form-check-label" for="checkboxDepresi">Depresi</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkboxTakut"
                                        name="status_psikologi[]" value="Takut">
                                    <label class="form-check-label" for="checkboxTakut">Takut</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkboxAgresif"
                                        name="status_psikologi[]" value="Agresif">
                                    <label class="form-check-label" for="checkboxAgresif">Agresif</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkboxMelukaiDiri"
                                        name="status_psikologi[]" value="Melukai diri sendiri/Orang lain">
                                    <label class="form-check-label" for="checkboxMelukaiDiri">Melukai diri sendiri/Orang
                                        lain</label>
                                </div>
                            </div>

                            <div class="col-6">
                                <h5>Hambatan Edukasi</h5>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkboxBahasa"
                                        name="hambatan_edukasi[]" value="Bahasa">
                                    <label class="form-check-label" for="checkboxBahasa">Bahasa</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkboxCacatFisik"
                                        name="hambatan_edukasi[]" value="Cacat Fisik">
                                    <label class="form-check-label" for="checkboxCacatFisik">Cacat Fisik</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkboxCacatKognitif"
                                        name="hambatan_edukasi[]" value="Cacat Kognitif">
                                    <label class="form-check-label" for="checkboxCacatKognitif">Cacat Kognitif</label>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="alergi" class="form-label">Alergi</label>
                                <textarea type="text" class="form-control form-control-sm" id="alergi"
                                    name="alergi"></textarea>
                            </div>
                            <div class="col-6">
                                <label for="catatan" class="form-label">Catatan</label>
                                <textarea type="text" class="form-control form-control-sm" id="catatan"
                                    name="catatan"></textarea>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="checkboxRawatInap" name="rawat_inap"
                                        value="1">
                                    <label class="form-check-label" for="checkboxRawatInap">Rawat Inap</label>
                                </div>
                                <h5>Ruangan</h5>
                                <input type="text" class="form-control" id="selectPoli" name="ruangan" placeholder="Masukkan Ruangan" disabled>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-success ms-2" id="btnSimpanAnalisa">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modalAnalisa = document.getElementById('modalAnalisa');
        if (!modalAnalisa) {
            console.error('modalAnalisa element not found');
            return;
        }
        var btnSimpanAnalisa = modalAnalisa.querySelector('#btnSimpanAnalisa');
        var checkboxRawatInap = document.getElementById('checkboxRawatInap');
        var inputRuangan = document.getElementById('selectPoli');

        // Modal show: set pasien_id dan nama pasien
        modalAnalisa.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var pasienData = button.getAttribute('data-pasien');
            var pasienId = button.getAttribute('data-pasien-id');
            var pasien = pasienData ? JSON.parse(pasienData) : null;

            // Set pasien_id
            var pasienIdInput = modalAnalisa.querySelector('#pasien_id');
            if (pasienIdInput) {
                pasienIdInput.value = pasienId || (pasien ? pasien.pasien_id : '');
            }

            // Set nama pasien di header
            var namaPasienDisplay = modalAnalisa.querySelector('#nama_pasien_display');
            if (namaPasienDisplay) {
                namaPasienDisplay.textContent = pasien ? (pasien.nama_pasien || '') : '';
            }

            // Aktifkan tombol simpan hanya jika pasien_id terisi
            if (btnSimpanAnalisa) {
                btnSimpanAnalisa.disabled = !(pasienIdInput && pasienIdInput.value);
            }
        });

        // Atur enable/disable ruangan sesuai checkbox Rawat Inap
        if (checkboxRawatInap && inputRuangan) {
            function toggleRuangan() {
                if (checkboxRawatInap.checked) {
                    inputRuangan.disabled = false;
                } else {
                    inputRuangan.value = '';
                    inputRuangan.disabled = true;
                }
            }
            checkboxRawatInap.addEventListener('change', toggleRuangan);
            toggleRuangan(); // initial state
        }

        // Gabungkan seluruh logic submit formAnalisa di sini (hanya satu handler!)
        $('#formAnalisa').off('submit').on('submit', function (e) {
            // Validasi: jika ruangan diisi, checkbox harus dicentang
            var ruangan = inputRuangan ? inputRuangan.value.trim() : '';
            if (ruangan && (!checkboxRawatInap || !checkboxRawatInap.checked)) {
                alert('Jika ingin mengisi ruangan, centang dulu Rawat Inap!');
                if (checkboxRawatInap) checkboxRawatInap.focus();
                e.preventDefault();
                return false;
            }
            e.preventDefault();
            var form = this;
            var formData = $(form).serialize();
            var url = $(form).attr('action');
            var btn = $(form).find('button[type="submit"]');
            btn.prop('disabled', true).text('Menyimpan...');
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                dataType: 'json',
                headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() },
                success: function (res) {
                    if (res.success) {
                        toastr.options = {
                            "positionClass": "toast-top-right",
                            "timeOut": "2000",
                            "closeButton": true,
                            "progressBar": true
                        };
                        toastr.success(res.message || 'Data analisa berhasil disimpan.');
                        $('#modalAnalisa').modal('hide');
                        // Reload tabel data pasien UGD (jika pakai DataTables, gunakan .ajax.reload(); jika tidak, reload halaman)
                        if ($.fn.DataTable && $('#pasienTable').hasClass('dataTable')) {
                            $('#pasienTable').DataTable().ajax.reload(null, false);
                        } else {
                            // fallback: reload halaman
                            setTimeout(function() { location.reload(); }, 1200);
                        }
                        form.reset();
                    } else {
                        toastr.error(res.message || 'Gagal menyimpan data analisa.');
                    }
                },
                error: function (xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        var msg = Object.values(errors).map(function (e) { return e[0]; }).join('\n');
                        toastr.error(msg);
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        toastr.error(xhr.responseJSON.message);
                    } else {
                        toastr.error('Terjadi kesalahan saat menyimpan data.');
                    }
                },
                complete: function () {
                    btn.prop('disabled', false).text('Simpan');
                }
            });
        });
    });
</script>

<!-- Pastikan jQuery dimuat sebelum script custom -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
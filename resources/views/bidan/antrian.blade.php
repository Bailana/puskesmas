@extends('dashboardbidan')

@section('bidan')
<!-- Include toastr CSS and JS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>Antrian Pasien</strong></h1>
    <div class="row">
        <div class="col-12 col-lg-12 col-xxl-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex justify-content-between">
                    <!-- Input Pencarian -->
                    <div class="input-group" style="width: 250px;">
                        <input type="text" class="form-control" id="searchInput" placeholder="Pencarian..."
                            aria-label="Search">
                    </div>
                </div>

                <table class="table table-hover my-0" id="antrianTable">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nomor RM</th>
                            <th>Nama Pasien</th>
                            <th>Umur</th>
                            <th>JamKes</th>
                            <th>Poli Tujuan</th>
                            <th>Tgl. Berobat</th>
                            <th>Status</th>
                            <th>Aksi</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($antrians as $index => $antrian)
                        @if ($antrian->status == 'Perlu Analisa')
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $antrian->no_rekam_medis }}</td>
                            <td>{{ $antrian->pasien ? $antrian->pasien->nama_pasien : 'Data Pasien Tidak Ditemukan' }}
                            </td>
                            <td>{{ $antrian->pasien ? \Carbon\Carbon::parse($antrian->pasien->tanggal_lahir)->age . ' tahun' : '-' }}
                            </td>
                            <td>{{ $antrian->pasien ? $antrian->pasien->jaminan_kesehatan : '-' }}</td>
                            <td>{{ $antrian->poli ? $antrian->poli->nama_poli : 'Tidak ada' }}</td>
                            <td>{{ \Carbon\Carbon::parse($antrian->tanggal_berobat)->format('d-m-Y') }}</td>
                            <td><span class="badge bg-danger">{{ $antrian->status }}</span></td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm rounded btn-analisa"
                                    data-rekam-medis="{{ $antrian->no_rekam_medis }}" data-bs-toggle="modal"
                                    data-bs-target="#modalAnalisa">Analisa</button>
                                <button type="button" class="btn btn-info btn-sm rounded btn-selengkapnya"
                                    data-bs-toggle="modal" data-bs-target="#modalPasienDetail"
                                    data-no-rekam-medis="{{ $antrian->no_rekam_medis }}"
                                    data-nik="{{ $antrian->pasien ? $antrian->pasien->nik : '' }}"
                                    data-nama="{{ $antrian->pasien ? $antrian->pasien->nama_pasien : '' }}"
                                    data-tempat-lahir="{{ $antrian->pasien ? $antrian->pasien->tempat_lahir : '' }}"
                                    data-tanggal-lahir="{{ $antrian->pasien ? $antrian->pasien->tanggal_lahir : '' }}"
                                    data-jenis-kelamin="{{ $antrian->pasien ? $antrian->pasien->jenis_kelamin : '' }}"
                                    data-golongan-darah="{{ $antrian->pasien ? $antrian->pasien->golongan_darah : '' }}"
                                    data-agama="{{ $antrian->pasien ? $antrian->pasien->agama : '' }}"
                                    data-pekerjaan="{{ $antrian->pasien ? $antrian->pasien->pekerjaan : '' }}"
                                    data-status-pernikahan="{{ $antrian->pasien ? $antrian->pasien->status_pernikahan : '' }}"
                                    data-kepala-keluarga="{{ $antrian->pasien ? $antrian->pasien->kepala_keluarga : '' }}"
                                    data-no-hp="{{ $antrian->pasien ? $antrian->pasien->no_hp : '' }}"
                                    data-alamat="{{ $antrian->pasien ? $antrian->pasien->alamat : '' }}"
                                    data-rt="{{ $antrian->pasien ? $antrian->pasien->rt : '' }}"
                                    data-rw="{{ $antrian->pasien ? $antrian->pasien->rw : '' }}"
                                    data-kelurahan="{{ $antrian->pasien ? $antrian->pasien->kelurahan : '' }}"
                                    data-kecamatan="{{ $antrian->pasien ? $antrian->pasien->kecamatan : '' }}"
                                    data-kabupaten="{{ $antrian->pasien ? $antrian->pasien->kabupaten : '' }}"
                                    data-provinsi="{{ $antrian->pasien ? $antrian->pasien->provinsi : '' }}"
                                    data-jaminan="{{ $antrian->pasien ? $antrian->pasien->jaminan_kesehatan : '' }}"
                                    data-no-kepesertaan="{{ $antrian->pasien ? $antrian->pasien->no_kepesertaan : '' }}">
                                    Selengkapnya
                                </button>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAnalisa" tabindex="-1" aria-labelledby="modalAnalisaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
        <div class="modal-content" style="overflow-x: hidden;">
            <div class="modal-header d-flex justify-content-between">
                <h3 class="modal-title" id="modalAnalisaLabel">
                    <strong>Analisa <span class="nama-pasien" id="nama_pasien_display"></span></strong>
                </h3>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow-y: auto; padding: 10px;">
                @csrf

                <form id="formAnalisa" method="POST" action="{{ route('bidan.hasilanalisa.store') }}">
                    @csrf
                    <input type="hidden" id="no_rekam_medis" name="no_rekam_medis">
                    <input type="hidden" id="nama_user" name="nama_user" value="{{ auth()->user()->name }}">
                    <!-- Tanda Vital -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="checkboxTandaVital">
                        <label class="form-check-label" for="checkboxTandaVital">
                            <h5>Tanda Vital</h5>
                        </label>
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
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="checkboxAntropometri">
                        <label class="form-check-label" for="checkboxAntropometri">
                            <h5>Antropometri</h5>
                        </label>
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
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="checkboxFungsional">
                        <label class="form-check-label" for="checkboxFungsional">
                            <h5>Fungsional</h5>

                        </label>
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
                            <h5>Pilih Poli Tujuan</h5>
                            <select class="form-select" id="selectPoli" name="poli_tujuan" required>
                                <option value="" disabled selected>Pilih Poli</option>
                                <option value="1">Poli Umum</option>
                                <option value="2">Poli Lansia</option>
                                <option value="3">Poli KB</option>
                                <option value="4">Poli KIA</option>
                                <option value="5">Poli Anak</option>
                                <option value="6">Poli Gigi</option>
                                <option value="7">Poli Physiotherapy</option>
                            </select>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-danger" id="btnTutup">Tutup</button>
                <button type="button" class="btn btn-success ms-2" id="btnSimpanAnalisa">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Set no_rekam_medis hidden input and patient name display when modalAnalisa is shown
    var modalAnalisa = document.getElementById('modalAnalisa');
    modalAnalisa.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var noRekamMedis = button.getAttribute('data-rekam-medis');
        modalAnalisa.querySelector('#no_rekam_medis').value = noRekamMedis;

        // Set patient name display in modal header
        var patientName = button.closest('tr').querySelector('td:nth-child(3)').textContent.trim();
        modalAnalisa.querySelector('#nama_pasien_display').textContent = patientName;
    });

    // Handle save button click with AJAX form submission
    document.getElementById('btnSimpanAnalisa').addEventListener('click', function () {
        var form = document.getElementById('formAnalisa');
        var url = form.action;
        var formData = new FormData(form);

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errData => {
                    throw errData;
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                toastr.success(data.message);
                // Close modal
                var modal = bootstrap.Modal.getInstance(modalAnalisa);
                modal.hide();
                // Optionally reload or update the table here
                location.reload();
            } else {
                toastr.error('Gagal menyimpan data analisa.');
            }
        })
        .catch(errorData => {
            if (errorData.errors) {
                for (const key in errorData.errors) {
                    if (errorData.errors.hasOwnProperty(key)) {
                        toastr.error(errorData.errors[key][0]);
                    }
                }
            } else {
                toastr.error('Terjadi kesalahan saat menyimpan data.');
            }
        });
    });

    // Handle close button click to hide modal
    document.getElementById('btnTutup').addEventListener('click', function () {
        var modal = bootstrap.Modal.getInstance(modalAnalisa);
        modal.hide();
    });
</script>

@endsection

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

<script>
    // JavaScript untuk mengisi data modal ketika modalPasienDetail ditampilkan
    var modalPasienDetail = document.getElementById('modalPasienDetail');
    modalPasienDetail.addEventListener('show.bs.modal', function (event) {
        // Tombol yang memicu modal
        var button = event.relatedTarget;

        // Mengambil data dari atribut data pada tombol
        var noRekamMedis = button.getAttribute('data-no-rekam-medis');
        var nik = button.getAttribute('data-nik');
        var nama = button.getAttribute('data-nama');
        var tempatLahir = button.getAttribute('data-tempat-lahir');
        var tanggalLahir = button.getAttribute('data-tanggal-lahir');
        var jenisKelamin = button.getAttribute('data-jenis-kelamin');
        var golonganDarah = button.getAttribute('data-golongan-darah');
        var agama = button.getAttribute('data-agama');
        var pekerjaan = button.getAttribute('data-pekerjaan');
        var statusPernikahan = button.getAttribute('data-status-pernikahan');
        var kepalaKeluarga = button.getAttribute('data-kepala-keluarga');
        var noHp = button.getAttribute('data-no-hp');
        var alamat = button.getAttribute('data-alamat');
        var rt = button.getAttribute('data-rt');
        var rw = button.getAttribute('data-rw');
        var kelurahan = button.getAttribute('data-kelurahan');
        var kecamatan = button.getAttribute('data-kecamatan');
        var kabupaten = button.getAttribute('data-kabupaten');
        var provinsi = button.getAttribute('data-provinsi');
        var jaminan = button.getAttribute('data-jaminan');
        var noKepesertaan = button.getAttribute('data-no-kepesertaan');

        // Mengisi input modal dengan data yang diambil
        modalPasienDetail.querySelector('#modalNoRekamMedis').value = noRekamMedis;
        modalPasienDetail.querySelector('#modalNikPasien').value = nik;
        modalPasienDetail.querySelector('#modalNamaPasien').value = nama;
        modalPasienDetail.querySelector('#modalTempatLahir').value = tempatLahir;
        modalPasienDetail.querySelector('#modalTanggalLahir').value = tanggalLahir;
        modalPasienDetail.querySelector('#modalJenisKelamin').value = jenisKelamin;
        modalPasienDetail.querySelector('#modalGolonganDarah').value = golonganDarah;
        modalPasienDetail.querySelector('#modalAgama').value = agama;
        modalPasienDetail.querySelector('#modalPekerjaan').value = pekerjaan;
        modalPasienDetail.querySelector('#modalStatusPernikahan').value = statusPernikahan;
        modalPasienDetail.querySelector('#modalKepalaKeluarga').value = kepalaKeluarga;
        modalPasienDetail.querySelector('#modalNoHp').value = noHp;
        modalPasienDetail.querySelector('#modalAlamat').value = alamat;
        modalPasienDetail.querySelector('#modalRt').value = rt;
        modalPasienDetail.querySelector('#modalRw').value = rw;
        modalPasienDetail.querySelector('#modalKelurahan').value = kelurahan;
        modalPasienDetail.querySelector('#modalKecamatan').value = kecamatan;
        modalPasienDetail.querySelector('#modalKabupaten').value = kabupaten;
        modalPasienDetail.querySelector('#modalProvinsi').value = provinsi;
        modalPasienDetail.querySelector('#modalJaminan').value = jaminan;
        modalPasienDetail.querySelector('#modalNoKepesertaan').value = noKepesertaan;
    });

</script>

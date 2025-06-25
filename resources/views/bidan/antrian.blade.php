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

                <div class="table-responsive">
                    <table class="table table-hover my-0" id="antrianTable" style="font-size: 14px;">
                        <thead>
                            <tr>
                                <th style="white-space: nowrap;">No.</th>
                                <th style="white-space: nowrap;">Nomor RM</th>
                                <th style="white-space: nowrap;">Nama Pasien</th>
                                <th style="white-space: nowrap;">Umur</th>
                                <th style="white-space: nowrap;">JamKes</th>
                                <th style="white-space: nowrap;">Status</th>
                                <th style="white-space: nowrap;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(isset($antrians))
                        @forelse($antrians as $index => $antrian)
                        <tr>
                            <td style="white-space: nowrap;">{{ $antrians->firstItem() + $index }}</td>
                            <td style="white-space: nowrap;">{{ $antrian->no_rekam_medis }}</td>
                            <td style="white-space: nowrap;">{{ $antrian->pasien ? $antrian->pasien->nama_pasien : 'Data Pasien Tidak Ditemukan' }}</td>
                            <td style="white-space: nowrap;">
                                @if($antrian->pasien && $antrian->pasien->tanggal_lahir)
                                {{ \Carbon\Carbon::parse($antrian->pasien->tanggal_lahir)->age }} tahun
                                @else
                                -
                                @endif
                            </td>
                            <td style="white-space: nowrap;">{{ $antrian->pasien ? $antrian->pasien->jaminan_kesehatan : '-' }}</td>
                            <td style="white-space: nowrap;"><span class="badge bg-danger">{{ $antrian->status }}</span></td>
                            <td style="white-space: nowrap;">
                                <button type="button" class="btn btn-success btn-sm rounded btn-hasilanalisa"
                                    data-rekam-medis="{{ $antrian->no_rekam_medis }}"
                                    data-bs-toggle="modal" data-bs-target="#modalAnalisa"
                                    @if(!$antrian->pasien) disabled @endif>Hasil Analisa</button>
                                <button type="button" class="btn btn-primary btn-sm rounded btn-periksa"
                                    data-rekam-medis="{{ $antrian->no_rekam_medis }}"
                                    @if(!$antrian->pasien) disabled @endif>Periksa</button>
                                <button type="button" class="btn btn-danger btn-sm rounded btn-riwayat"
                                    data-rekam-medis="{{ $antrian->no_rekam_medis }}"
                                    data-nama="{{ $antrian->pasien ? $antrian->pasien->nama_pasien : '' }}"
                                    data-bs-toggle="modal" data-bs-target="#modalRiwayatBerobat"
                                    @if(!$antrian->pasien) disabled @endif>Riwayat Berobat</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Antrian pasien tidak ditemukan</td>
                        </tr>
                        @endforelse
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 mb-2">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="small text-muted mb-2 text-start ps-3 pagination-info-text-antrian" style="max-width: 50%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            Showing {{ $antrians->firstItem() }} to {{ $antrians->lastItem() }} of {{ $antrians->total() }} result
                        </div>
                        <nav class="d-flex justify-content-center">
                            <ul class="pagination d-flex flex-row flex-wrap gap-2" style="list-style-type: none; padding-left: 0; margin-bottom: 0;">
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
                                        if ($start < 1) $start = 1;
                                        if ($end > $totalPages) $end = $totalPages;
                                    }
                                @endphp
                                @for ($i = $start; $i <= $end; $i++)
                                    @if ($i == $currentPage)
                                        <li class="page-item active" aria-current="page"><span class="page-link">{{ $i }}</span></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="{{ $antrians->url($i) }}">{{ $i }}</a></li>
                                    @endif
                                @endfor
                                {{-- Next Page Link --}}
                                @if ($antrians->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $antrians->nextPageUrl() }}" rel="next" aria-label="Next">&raquo;</a>
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

<!-- Modal Hasil Analisa (readonly, style identik perawat) -->
<div class="modal fade" id="modalAnalisa" tabindex="-1" aria-labelledby="modalAnalisaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
        <div class="modal-content modal-analisa-position-relative" style="overflow-x: hidden;">
            <div class="modal-header d-flex justify-content-between">
                <h3 class="modal-title" id="modalAnalisaLabel">
                    <strong>Hasil Analisa <span class="nama-pasien" id="nama_pasien_display"></span></strong>
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-analisa-body-scroll" style="max-height: 400px; overflow-y: auto; padding: 16px 24px 16px 24px;">
                <form>
                    <input type="hidden" id="modalAnalisaNoRekamMedis" readonly>
                    <!-- Tanda Vital -->
                    <div class="mb-3">
                        <h5 class="mb-3">Tanda Vital</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Tekanan Darah (mmHg)</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaTekananDarah" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Frekuensi Nadi (/menit)</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaFrekuensiNadi" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Suhu (°C)</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaSuhu" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Frekuensi Nafas (/menit)</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaFrekuensiNafas" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Skor Nyeri</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaSkorNyeri" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Skor Jatuh</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaSkorJatuh" readonly>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!-- Antropometri -->
                    <div class="mb-3">
                        <h5 class="mb-3">Antropometri</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Berat Badan</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaBeratBadan" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tinggi Badan</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaTinggiBadan" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lingkar Kepala</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaLingkarKepala" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">IMT</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaIMT" readonly>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!-- Fungsional -->
                    <div class="mb-3">
                        <h5 class="mb-3">Fungsional</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Alat Bantu</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaAlatBantu" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Prosthesa</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaProsthesa" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Cacat Tubuh</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaCacatTubuh" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ADL Mandiri</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaADLMandiri" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Riwayat Jatuh</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaRiwayatJatuh" readonly>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!-- Status Psikologi & Hambatan Edukasi -->
                    <div class="mb-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <h5>Status Psikologi</h5>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaStatusPsikologi" readonly>
                            </div>
                            <div class="col-md-6">
                                <h5>Hambatan Edukasi</h5>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaHambatanEdukasi" readonly>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!-- Alergi & Catatan -->
                    <div class="mb-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Alergi</label>
                                <textarea class="form-control form-control-sm" id="modalAnalisaAlergi" readonly></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Catatan</label>
                                <textarea class="form-control form-control-sm" id="modalAnalisaCatatan" readonly></textarea>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!-- Poli Tujuan & Penanggung Jawab -->
                    <div class="mb-1">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Poli Tujuan</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaPoliTujuan" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Penanggung Jawab</label>
                                <input type="text" class="form-control form-control-sm" id="modalAnalisaPenanggungJawab" readonly>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Modal footer dihapus agar tidak ada tombol tutup di bawah -->
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

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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

    document.querySelectorAll('.btn-hasilanalisa').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var noRekamMedis = this.getAttribute('data-rekam-medis');
            // Kosongkan semua field modal analisa
            [
                'modalAnalisaTekananDarah', 'modalAnalisaFrekuensiNadi', 'modalAnalisaSuhu', 'modalAnalisaFrekuensiNafas',
                'modalAnalisaSkorNyeri', 'modalAnalisaSkorJatuh', 'modalAnalisaBeratBadan', 'modalAnalisaTinggiBadan',
                'modalAnalisaLingkarKepala', 'modalAnalisaIMT', 'modalAnalisaAlatBantu', 'modalAnalisaProsthesa',
                'modalAnalisaCacatTubuh', 'modalAnalisaADLMandiri', 'modalAnalisaRiwayatJatuh', 'modalAnalisaStatusPsikologi',
                'modalAnalisaHambatanEdukasi', 'modalAnalisaAlergi', 'modalAnalisaCatatan', 'modalAnalisaPoliTujuan', 'modalAnalisaPenanggungJawab'
            ].forEach(function(id) {
                var el = document.getElementById(id);
                if (el) {
                    if (el.tagName === 'TEXTAREA' || el.tagName === 'INPUT') {
                        el.value = '';
                    } else {
                        el.textContent = '';
                    }
                }
            });
            // Fetch data hasil analisa
            fetch(`/bidan/hasilanalisa/${noRekamMedis}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.hasil) {
                        let hasil = data.hasil;
                        document.getElementById('modalAnalisaTekananDarah').value = hasil.tekanan_darah || '-';
                        document.getElementById('modalAnalisaFrekuensiNadi').value = hasil.frekuensi_nadi || '-';
                        document.getElementById('modalAnalisaSuhu').value = hasil.suhu || '-';
                        document.getElementById('modalAnalisaFrekuensiNafas').value = hasil.frekuensi_nafas || '-';
                        document.getElementById('modalAnalisaSkorNyeri').value = hasil.skor_nyeri || '-';
                        document.getElementById('modalAnalisaSkorJatuh').value = hasil.skor_jatuh || '-';
                        document.getElementById('modalAnalisaBeratBadan').value = hasil.berat_badan || '-';
                        document.getElementById('modalAnalisaTinggiBadan').value = hasil.tinggi_badan || '-';
                        document.getElementById('modalAnalisaLingkarKepala').value = hasil.lingkar_kepala || '-';
                        document.getElementById('modalAnalisaIMT').value = hasil.imt || '-';
                        document.getElementById('modalAnalisaAlatBantu').value = hasil.alat_bantu || '-';
                        document.getElementById('modalAnalisaProsthesa').value = hasil.prosthesa || '-';
                        document.getElementById('modalAnalisaCacatTubuh').value = hasil.cacat_tubuh || '-';
                        document.getElementById('modalAnalisaADLMandiri').value = hasil.adl_mandiri || '-';
                        document.getElementById('modalAnalisaRiwayatJatuh').value = hasil.riwayat_jatuh || '-';
                        document.getElementById('modalAnalisaStatusPsikologi').value = hasil.status_psikologi || '-';
                        document.getElementById('modalAnalisaHambatanEdukasi').value = hasil.hambatan_edukasi || '-';
                        document.getElementById('modalAnalisaAlergi').value = hasil.alergi || '-';
                        document.getElementById('modalAnalisaCatatan').value = hasil.catatan || '-';
                        document.getElementById('modalAnalisaPoliTujuan').value = hasil.nama_poli || '-';
                        document.getElementById('modalAnalisaPenanggungJawab').value = hasil.nama_penanggung_jawab || '-';
                    } else {
                        // Jika gagal, isi semua field dengan '-'
                        [
                            'modalAnalisaTekananDarah', 'modalAnalisaFrekuensiNadi', 'modalAnalisaSuhu', 'modalAnalisaFrekuensiNafas',
                            'modalAnalisaSkorNyeri', 'modalAnalisaSkorJatuh', 'modalAnalisaBeratBadan', 'modalAnalisaTinggiBadan',
                            'modalAnalisaLingkarKepala', 'modalAnalisaIMT', 'modalAnalisaAlatBantu', 'modalAnalisaProsthesa',
                            'modalAnalisaCacatTubuh', 'modalAnalisaADLMandiri', 'modalAnalisaRiwayatJatuh', 'modalAnalisaStatusPsikologi',
                            'modalAnalisaHambatanEdukasi', 'modalAnalisaAlergi', 'modalAnalisaCatatan', 'modalAnalisaPoliTujuan', 'modalAnalisaPenanggungJawab'
                        ].forEach(function(id) {
                            var el = document.getElementById(id);
                            if (el) el.value = '-';
                        });
                    }
                })
                .catch(() => {
                    // Jika error, isi semua field dengan '-'
                    [
                        'modalAnalisaTekananDarah', 'modalAnalisaFrekuensiNadi', 'modalAnalisaSuhu', 'modalAnalisaFrekuensiNafas',
                        'modalAnalisaSkorNyeri', 'modalAnalisaSkorJatuh', 'modalAnalisaBeratBadan', 'modalAnalisaTinggiBadan',
                        'modalAnalisaLingkarKepala', 'modalAnalisaIMT', 'modalAnalisaAlatBantu', 'modalAnalisaProsthesa',
                        'modalAnalisaCacatTubuh', 'modalAnalisaADLMandiri', 'modalAnalisaRiwayatJatuh', 'modalAnalisaStatusPsikologi',
                        'modalAnalisaHambatanEdukasi', 'modalAnalisaAlergi', 'modalAnalisaCatatan', 'modalAnalisaPoliTujuan', 'modalAnalisaPenanggungJawab'
                    ].forEach(function(id) {
                        var el = document.getElementById(id);
                        if (el) el.value = '-';
                    });
                });
        });
    });
});
</script>
@endsection

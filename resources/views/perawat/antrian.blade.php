@extends('dashboardPerawat')

@section('perawat')
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
                            <td>{{ $antrian->pasien->nama_pasien }}</td>
                            <td>{{ \Carbon\Carbon::parse($antrian->pasien->tanggal_lahir)->age }} tahun</td>
                            <td>{{ $antrian->pasien->jaminan_kesehatan }}</td>
                            <td>{{ $antrian->poli ? $antrian->poli->nama_poli : 'Tidak ada' }}</td>
                            <td>{{ \Carbon\Carbon::parse($antrian->tanggal_berobat)->format('d-m-Y') }}</td>
                            <td><span class="badge bg-danger">{{ $antrian->status }}</span></td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm rounded btn-analisa"
                                    data-rekam-medis="{{ $antrian->no_rekam_medis }}" data-bs-toggle="modal"
                                    data-bs-target="#modalAnalisa">Analisa</button>
                                <button type="button" class="btn btn-success btn-sm rounded">Riwayat Berobat</button>
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

                <form id="formAnalisa">
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
                            <input type="text" class="form-control form-control-sm" id="tekananDarah" disabled>
                        </div>
                        <div class="col-6">
                            <label for="frekuensiNadi" class="form-label">Frekuensi Nadi (/menit)</label>
                            <input type="text" class="form-control form-control-sm" id="frekuensiNadi" disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="suhu" class="form-label">Suhu (°C)</label>
                            <input type="text" class="form-control form-control-sm" id="suhu" disabled>
                        </div>
                        <div class="col-6">
                            <label for="frekuensiNafas" class="form-label">Frekuensi Nafas (/menit)</label>
                            <input type="text" class="form-control form-control-sm" id="frekuensiNafas" disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="skorNyeri" class="form-label">Skor Nyeri</label>
                            <input type="text" class="form-control form-control-sm" id="skorNyeri" disabled>
                        </div>
                        <div class="col-6">
                            <label for="skorJatuh" class="form-label">Skor Jatuh</label>
                            <input type="text" class="form-control form-control-sm" id="skorJatuh" disabled>
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
                            <input type="text" class="form-control form-control-sm" id="beratBadan" disabled>
                        </div>
                        <div class="col-6">
                            <label for="tinggiBadan" class="form-label">Tinggi Badan</label>
                            <input type="text" class="form-control form-control-sm" id="tinggiBadan" disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="lingkarKepala" class="form-label">Lingkar Kepala</label>
                            <input type="text" class="form-control form-control-sm" id="lingkarKepala" disabled>
                        </div>
                        <div class="col-6">
                            <label for="imt" class="form-label">IMT</label>
                            <input type="text" class="form-control form-control-sm" id="imt" disabled>
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
                            <input type="text" class="form-control form-control-sm" id="alatBantu" disabled>
                        </div>
                        <div class="col-6">
                            <label for="prosthesa" class="form-label">Prosthesa</label>
                            <input type="text" class="form-control form-control-sm" id="prosthesa" disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="cacatTubuh" class="form-label">Cacat Tubuh</label>
                            <input type="text" class="form-control form-control-sm" id="cacatTubuh" disabled>
                        </div>
                        <div class="col-6">
                            <label for="adlMandiri" class="form-label">ADL Mandiri</label>
                            <input type="text" class="form-control form-control-sm" id="adlMandiri" disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="riwayatJatuh" class="form-label">Riwayat Jatuh</label>
                            <input type="text" class="form-control form-control-sm" id="riwayatJatuh" disabled>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-6">
                            <h5>Status Psikologi</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxDepresi">
                                <label class="form-check-label" for="checkboxDepresi">Depresi</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxTakut">
                                <label class="form-check-label" for="checkboxTakut">Takut</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxAgresif">
                                <label class="form-check-label" for="checkboxAgresif">Agresif</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxMelukaiDiri">
                                <label class="form-check-label" for="checkboxMelukaiDiri">Melukai diri sendiri/Orang
                                    lain</label>
                            </div>
                        </div>

                        <div class="col-6">
                            <h5>Hambatan Edukasi</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxBahasa">
                                <label class="form-check-label" for="checkboxBahasa">Bahasa</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxCacatFisik">
                                <label class="form-check-label" for="checkboxCacatFisik">Cacat Fisik</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxCacatKognitif">
                                <label class="form-check-label" for="checkboxCacatKognitif">Cacat Kognitif</label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="alergi" class="form-label">Alergi</label>
                            <textarea type="text" class="form-control form-control-sm" id="alergi"></textarea>
                        </div>
                        <div class="col-6">
                            <label for="catatan" class="form-label">Catatan</label>
                            <textarea type="text" class="form-control form-control-sm" id="catatan"></textarea>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-12">
                            <h5>Pilih Poli Tujuan</h5>
                            <select class="form-select" id="selectPoli" name="selectPoli" required>
                                <option value="" disabled selected>Pilih Poli</option>
                                <option value="Umum">Poli Umum</option>
                                <option value="Lansia">Poli Lansia</option>
                                <option value="KB">Poli KB</option>
                                <option value="KIA">Poli KIA</option>
                                <option value="Anak">Poli Anak</option>
                                <option value="Gigi">Poli Gigi</option>
                                <option value="Physiotherapy">Poli Physiotherapy</option>
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

@endsection

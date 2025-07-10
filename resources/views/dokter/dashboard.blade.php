@extends('dashboardDokter')

@section('dokter')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>Dashboard Dokter</strong></h1>
    <div class="row">
        <div class="col-xl-12 col-xxl-12 d-flex">
            <div class="w-100">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col mt-0">
                                        <h5 class="card-title">Total Antrian</h5>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat text-primary">
                                            <i class="align-middle" data-feather="user-check"></i>
                                        </div>
                                    </div>
                                </div>
                                <h1 class="mt-1 mb-3">{{ $totalAntrianCount }}</h1>
                                <div class="mb-0">
                                    <span class="text-muted">Antrian</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col mt-0">
                                        <h5 class="card-title">Antrian Selesai</h5>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat text-primary">
                                            <i class="align-middle" data-feather="check"></i>
                                        </div>
                                    </div>
                                </div>
                                <h1 class="mt-1 mb-3">{{ $totalAntrianSelesaiCount }}</h1>
                                <div class="mb-0">
                                    <span class="text-muted">Antrian</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-12">
            <div class="card flex-fill w-100">
                <div class="card-header">
                    <h5 class="card-title">Pasien Poli Umum / Bulan</h5>
                </div>
                <div class="card-body">
                    <div class="chart chart-sm">
                        <canvas id="chartjs-line"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Periksa Pasien -->
<div class="modal fade" id="modalPeriksaPasien" tabindex="-1" aria-labelledby="modalPeriksaPasienLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 100%;">
        <div class="modal-content" style="overflow-x: hidden;">
            <div class="modal-header d-flex justify-content-between">
                <h3 class="modal-title" id="modalPeriksaPasienLabel"><strong>Periksa Pasien</strong></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow-y: auto; padding: 10px;">
                <form id="formPeriksaPasien">
                    <!-- Anamnesis -->
                    <div class="mb-3">
                        <label for="anamnesis" class="form-label">Anamnesis</label>
                        <textarea class="form-control" id="anamnesis" rows="3" required></textarea>
                    </div>

                    <!-- Pemeriksaan Fisik -->
                    <div class="mb-3">
                        <label for="pemeriksaanFisik" class="form-label">Pemeriksaan Fisik</label>
                        <textarea class="form-control" id="pemeriksaanFisik" rows="3" required></textarea>
                    </div>

                    <!-- Rencana dan Terapi -->
                    <div class="mb-3">
                        <label for="rencanaTerapi" class="form-label">Rencana dan Terapi</label>
                        <textarea class="form-control" id="rencanaTerapi" rows="3" required></textarea>
                    </div>

                    <!-- Diagnosis -->
                    <div class="mb-3">
                        <label for="diagnosis" class="form-label">Diagnosis</label>
                        <textarea class="form-control" id="diagnosis" rows="3" required></textarea>
                    </div>

                    <!-- Edukasi -->
                    <div class="mb-3">
                        <label for="edukasi" class="form-label">Edukasi</label>
                        <textarea class="form-control" id="edukasi" rows="3" required></textarea>
                    </div>

                    <!-- Code ICD -->
                    <div class="mb-3">
                        <label for="kodeICD" class="form-label">Kode ICD</label>
                        <input type="text" class="form-control form-control-sm" id="kodeICD" required>
                    </div>

                    <!-- Kesan Status Gizi -->
                    <div class="mb-3">
                        <label for="kesanStatusGizi" class="form-label">Kesan Status Gizi</label>
                        <select class="form-control form-control-sm" id="kesanStatusGizi" required>
                            <option value="" disabled selected>Pilih Status Gizi</option>
                            <option value="Gizi Kurang/Buruk">Gizi Kurang/Buruk</option>
                            <option value="Gizi Cukup">Gizi Cukup</option>
                            <option value="Gizi Lebih">Gizi Lebih</option>
                        </select>
                    </div>
                    <div id="resepObatContainer" class="d-flex flex-column mb-2 gap-2">
                        <div class="resep-obat-item d-flex gap-2">
                            <div class="flex-grow-1 d-flex flex-column equal-width">
                                <label for="resepObat" class="form-label text-start">Resep Obat</label>
                                <select class="form-select" name="resep_obat[]" required>
                                    <option value="" disabled selected>Pilih Obat</option>
                                    @foreach ($obats as $obat)
                                    <option value="{{ $obat->id }}">{{ $obat->nama_obat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1 d-flex flex-column input-container equal-width">
                                <label for="bentukObat" class="form-label">Bentuk Obat</label>
                                <input type="text" class="form-control" name="bentuk_obat[]" value="" required readonly>
                            </div>

                            <div class="flex-grow-1 d-flex flex-column input-container">
                                <label for="jumlahObat" class="form-label">Jumlah Obat</label>
                                <input type="number" class="form-control" name="jumlah_obat[]" min="1" value="" required>
                            </div>

                            <div class="btn-remove-container d-flex align-items-end" style="margin-bottom: 2px;">
                                <button type="button" class="btn btn-danger btn-sm btnRemoveResep" title="Hapus Resep Obat" style="height: 28px; width: 28px; padding: 0; font-size: 1.2rem; line-height: 1;">&times;</button>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="catatanObat" class="form-label">Catatan Obat</label>
                        <textarea class="form-control" id="catatanObat" rows="3"></textarea>
                    </div>
                    <button type="button" class="btn btn-primary d-flex align-items-center gap-2 mt-2" id="btnTambahResep" title="Tambah Resep Obat" style="padding: 0 8px; min-width: auto; height: 28px; font-size: 0.875rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                        </svg>
                        Tambah Resep Obat Baru
                    </button>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-success ms-2" id="btnSimpanPeriksa">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Prepare data from PHP variable
        const pasienPerBulan = @json(array_values($pasienPerBulanFull));

        // Line chart
        new Chart(document.getElementById("chartjs-line"), {
            type: "line",
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct",
                    "Nov", "Dec"
                ],
                datasets: [{
                    label: "Jumlah Pasien Poli Umum",
                    fill: true,
                    backgroundColor: "transparent",
                    borderColor: window.theme.primary,
                    data: pasienPerBulan
                }]
            },
            options: {
                maintainAspectRatio: false,
                legend: {
                    display: false
                },
                tooltips: {
                    intersect: false
                },
                hover: {
                    intersect: true
                },
                plugins: {
                    filler: {
                        propagate: false
                    }
                },
                scales: {
                    xAxes: [{
                        reverse: true,
                        gridLines: {
                            color: "rgba(0,0,0,0.05)"
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            stepSize: 1
                        },
                        display: true,
                        borderDash: [5, 5],
                        gridLines: {
                            color: "rgba(0,0,0,0)",
                            fontColor: "#fff"
                        }
                    }]
                }
            }
        });
    });
</script>
@endsection

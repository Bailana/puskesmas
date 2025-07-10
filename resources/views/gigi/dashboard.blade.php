@extends('dashboardGigi')

@section('gigi')
<div class="container-fluid p-0">

    <h1 class="h3 mb-3"><strong>Dashboard Dokter Gigi</strong></h1>

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
                    <h5 class="card-title">Pasien Poli Gigi / Bulan</h5>
                    <!-- <h6 class="card-subtitle text-muted">A line chart is a way of plotting data points on a line.</h6> -->
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
            </div>
            <div class="modal-body" style="max-height: 400px; overflow-y: auto; padding: 10px;">
                <form id="formPeriksaPasien">
                    <input type="hidden" id="pasienId" name="pasien_id" value="">
                    <input type="hidden" id="tanggalPeriksa" name="tanggal_periksa" value="">

                    <!-- Pemeriksaan Subjektif -->
                    <div class="mb-3">
                        <label for="pemeriksaanSubjektif" class="form-label">Pemeriksaan Subjektif<span
                                style="color: red;">*</span></label>
                        <textarea class="form-control" id="pemeriksaanSubjektif" name="pemeriksaan_subjektif" rows="3"
                            required></textarea>
                    </div>

                    <!-- Pemeriksaan Objektif & Penunjang -->
                    <div class="mb-3">
                        <label for="pemeriksaanObjektif" class="form-label">Pemeriksaan Objektif & Penunjang</label>
                        <textarea class="form-control" id="pemeriksaanObjektif" name="pemeriksaan_objektif" rows="3"
                            required></textarea>
                    </div>

                    <!-- Diagnosa -->
                    <div class="mb-3">
                        <label for="diagnosa" class="form-label">Diagnosa</label>
                        <textarea class="form-control" id="diagnosa" name="diagnosa" rows="3" required></textarea>
                    </div>

                    <!-- Terapi & Anjuran -->
                    <div class="mb-3">
                        <label for="terapiAnjuran" class="form-label">Terapi & Anjuran</label>
                        <textarea class="form-control" id="terapiAnjuran" name="terapi_anjuran" rows="3"
                            required></textarea>
                    </div>

                    <!-- Catatan Paramedis -->
                    <div class="mb-3">
                        <label for="catatanParamedis" class="form-label">Catatan Paramedis</label>
                        <textarea class="form-control" id="catatanParamedis" name="catatan" rows="3"
                            required></textarea>
                    </div>

                    <!-- Odontogram Section -->
                    <div class="mb-3">
                        <label class="form-label">Odontogram</label>
                        <div id="odontogram" class="d-flex flex-wrap" style="max-width: 600px;">
                            <!-- Teeth will be generated by JS -->
                        </div>
                        <input type="hidden" id="odontogramData" name="odontogram" value="{}">
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-danger" id="btnTutupModal" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success ms-2" id="btnSimpanPeriksa">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Line chart with dynamic data
        const pasienPerBulan = @json(array_values($pasienPerBulanFull));
        const labels = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        new Chart(document.getElementById("chartjs-line"), {
            type: "line",
            data: {
                labels: labels,
                datasets: [{
                    label: "Jumlah Pasien Poli Gigi",
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
<!-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        var periksaButtons = document.querySelectorAll('.btn-periksa');
        var modalElement = document.getElementById('modalPeriksaPasien');
        var modal = new bootstrap.Modal(modalElement);

        periksaButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                modal.show();
            });
        });

    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const odontogram = document.getElementById('odontogram');
        const odontogramDataInput = document.getElementById('odontogramData');

        // Define tooth states and colors
        const toothStates = ['healthy', 'decayed', 'filled'];
        const toothColors = {
            healthy: '#a8d5a2',
            decayed: '#e57373',
            filled: '#ffb74d'
        };

        // Initialize odontogram data
        let odontogramData = {};

        // Generate 32 teeth (1 to 32)
        for (let i = 1; i <= 32; i++) {
            const tooth = document.createElement('div');
            tooth.classList.add('tooth');
            tooth.style.width = '40px';
            tooth.style.height = '40px';
            tooth.style.margin = '4px';
            tooth.style.border = '1px solid #ccc';
            tooth.style.borderRadius = '4px';
            tooth.style.display = 'flex';
            tooth.style.alignItems = 'center';
            tooth.style.justifyContent = 'center';
            tooth.style.cursor = 'pointer';
            tooth.style.userSelect = 'none';
            tooth.style.backgroundColor = toothColors['healthy'];
            tooth.textContent = i;
            tooth.dataset.toothNumber = i;
            tooth.dataset.stateIndex = 0; // healthy

            tooth.addEventListener('click', () => {
                // Cycle through states
                let currentIndex = parseInt(tooth.dataset.stateIndex);
                let nextIndex = (currentIndex + 1) % toothStates.length;
                tooth.dataset.stateIndex = nextIndex;
                let state = toothStates[nextIndex];
                tooth.style.backgroundColor = toothColors[state];
                odontogramData[tooth.dataset.toothNumber] = state;
                odontogramDataInput.value = JSON.stringify(odontogramData);
            });

            odontogram.appendChild(tooth);
            odontogramData[i] = 'healthy';
        }

        odontogramDataInput.value = JSON.stringify(odontogramData);
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var periksaButtons = document.querySelectorAll('.btn-periksa');
        var modalElement = document.getElementById('modalPeriksaPasien');
        var modal = new bootstrap.Modal(modalElement);

        periksaButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const pasienId = button.getAttribute('data-pasien-id');
                document.getElementById('pasienId').value = pasienId;
                document.getElementById('tanggalPeriksa').value = new Date().toISOString()
                    .slice(0, 10);

                document.getElementById('formPeriksaPasien').reset();

                const odontogram = document.getElementById('odontogram');
                const teeth = odontogram.querySelectorAll('.tooth');
                teeth.forEach(tooth => {
                    tooth.dataset.stateIndex = 0;
                    tooth.style.backgroundColor = '#a8d5a2'; // warna sehat
                });
                document.getElementById('odontogramData').value = JSON.stringify({});

                modal.show();
            });
        });

        const btnSimpanPeriksa = document.getElementById('btnSimpanPeriksa');
        btnSimpanPeriksa.addEventListener('click', function() {
            btnSimpanPeriksa.disabled = true; // nonaktifkan tombol untuk mencegah klik ganda
            const form = document.getElementById('formPeriksaPasien');
            const formData = new FormData(form);

            fetch("{{ route('gigi.hasilperiksagigi.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    toastr.success(data.message || 'Data berhasil disimpan', '', {
                        timeOut: 3000,
                        extendedTimeOut: 3000
                    });
                    modal.hide();
                    btnSimpanPeriksa.disabled = false; // aktifkan kembali tombol

                    // Perbarui tabel antrian pasien secara AJAX tanpa reload halaman
                    fetch(window.location.href, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.text())
                        .then(html => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            const newTableBody = doc.querySelector('table.table tbody');
                            const currentTableBody = document.querySelector(
                                'table.table tbody');
                            if (newTableBody && currentTableBody) {
                                currentTableBody.innerHTML = newTableBody.innerHTML;
                            }
                        });
                })
                .catch(error => {
                    toastr.error('Terjadi kesalahan saat menyimpan data');
                    console.error(error);
                    btnSimpanPeriksa.disabled = false; // aktifkan kembali tombol
                });
        });
    });
</script> -->

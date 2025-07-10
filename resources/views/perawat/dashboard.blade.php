@extends('dashboardperawat')

@section('perawat')
<div class="container-fluid p-0">

    <h1 class="h3 mb-3"><strong>Dashboard Perawat</strong></h1>

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
                                <h1 class="mt-1 mb-3">{{ $totalAntrianPerluAnalisa ?? 0 }}</h1>
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
                                <h1 class="mt-1 mb-3">{{ $totalAntrianSelesai ?? 0 }}</h1>
                                <div class="mb-0">
                                    <span class="text-muted">Pasien</span>
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
                    <h5 class="card-title">Pasien Dianalisa / Bulan</h5>
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
                    label: "Jumlah Antrian Pasien",
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

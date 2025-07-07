@extends('dashboardadmin')

@section('admin')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>Dashboard Admin</strong></h1>
    <div class="row">
        <div class="col-xl-12 col-xxl-12 d-flex">
            <div class="w-100">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col mt-0">
                                        <h5 class="card-title">Total Pengguna</h5>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat text-primary">
                                            <i class="align-middle" data-feather="truck"></i>
                                        </div>
                                    </div>
                                </div>
                                <h1 class="mt-1 mb-3">{{ $totalUsers }}</h1>
                                <div class="mb-0">
                                    <span class="text-muted">Pengguna</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col mt-0">
                                        <h5 class="card-title">Total Data Pasien</h5>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat text-primary">
                                            <i class="align-middle" data-feather="dollar-sign"></i>
                                        </div>
                                    </div>
                                </div>
                                <h1 class="mt-1 mb-3">{{ $totalPasiens }}</h1>
                                <div class="mb-0">
                                    <span class="text-muted">Pasien</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col mt-0">
                                        <h5 class="card-title">Pengguna Aktif</h5>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat text-primary">
                                            <i class="align-middle" data-feather="dollar-sign"></i>
                                        </div>
                                    </div>
                                </div>
                                <h1 class="mt-1 mb-3">{{ $activeUsers }}</h1>
                                <div class="mb-0">
                                    <span class="text-muted">Pengguna</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col mt-0">
                                        <h5 class="card-title">Pengguna Nonaktif</h5>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat text-primary">
                                            <i class="align-middle" data-feather="dollar-sign"></i>
                                        </div>
                                    </div>
                                </div>
                                <h1 class="mt-1 mb-3">{{ $inactiveUsers }}</h1>
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

    <div class="container-fluid p-0">
        <h1 class="h3 mb-3"><strong>Kajian Pasien</strong></h1>
        <div class="row">
            <div class="col-12 col-lg-12 col-xxl-12 d-flex">
                <div class="card flex-fill">
                    <!-- Table content removed to avoid undefined variable error -->
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="card flex-fill w-100">
                <div class="card-header">
                    <h5 class="card-title">Jumlah Pasien Baru / Bulan</h5>
                    <!-- <h6 class="card-subtitle text-muted">A line chart is a way of plotting data points on a line.</h6> -->
                </div>
                <div class="card-body">
                    <div class="chart chart-sm">
                        <canvas id="chartjs-line"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card ">
                <div class="card-header">
                    <h5 class="card-title">Jumlah Pengguna / Role</h5>
                    <!-- <h6 class="card-subtitle text-muted">Doughnut charts are excellent at showing the relational proportions
                    between data.</h6> -->
                </div>
                <div class="card-body">
                    <div class="chart chart-sm">
                        <canvas id="chartjs-doughnut"></canvas>
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
        // Doughnut chart with dynamic data from backend
        var roles = @json($roles);
        var counts = @json($counts);

        // Define colors for the chart, repeat if roles exceed colors
        var backgroundColors = [
            window.theme.primary,
            window.theme.success,
            window.theme.warning,
            window.theme.danger,
            window.theme.info,
            window.theme.secondary,
            "#8E3E63",
            "#3E8E63",
            "#63638E",
            "#8E638E"
        ];

        new Chart(document.getElementById("chartjs-doughnut"), {
            type: "doughnut",
            data: {
                labels: roles,
                datasets: [{
                    data: counts,
                    backgroundColor: backgroundColors.slice(0, roles.length),
                    borderColor: "transparent"
                }]
            },
            options: {
                maintainAspectRatio: false,
                legend: {
                    display: false
                }
            }
        });

        // Line chart for patient counts per month
        var months = @json($months);
        var patientCounts = @json($patientCounts);

        new Chart(document.getElementById("chartjs-line"), {
            type: "line",
            data: {
                labels: months,
                datasets: [{
                    label: "Jumlah Pasien",
                    fill: true,
                    backgroundColor: "transparent",
                    borderColor: window.theme.primary,
                    data: patientCounts
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
                        gridLines: {
                            color: "rgba(0,0,0,0.05)"
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            stepSize: 1,
                            beginAtZero: true
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
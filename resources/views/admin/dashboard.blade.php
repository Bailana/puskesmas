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
                    <h5 class="card-title">Line Chart</h5>
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
                    <h5 class="card-title">Pasien Poli</h5>
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
@extends('dashboardrawatinap')

@section('rawatinap')
<div class="container-fluid p-0">

    <h1 class="h3 mb-3"><strong>Dashboard Rawat Inap</strong></h1>

    <div class="row">
        <div class="col-xl-12 col-xxl-12 d-flex">
            <div class="w-100">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col mt-0">
                                        <h5 class="card-title">Pasien UGD</h5>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat text-primary">
                                            <i class="align-middle" data-feather="truck"></i>
                                        </div>
                                    </div>
                                </div>
                                <h1 class="mt-1 mb-3">{{ $totalPasienUGD ?? 0 }}</h1>
                                <div class="mb-0">
                                    <span class="text-muted">Pasien</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col mt-0">
                                        <h5 class="card-title">Pasien R. Inap</h5>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat text-primary">
                                            <i class="align-middle" data-feather="dollar-sign"></i>
                                        </div>
                                    </div>
                                </div>
                                <h1 class="mt-1 mb-3">{{ $totalPasienRawatInap ?? 0 }}</h1>
                                <div class="mb-0">
                                    <span class="text-muted">Pasien</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col mt-0">
                                        <h5 class="card-title">Perlu Analisa</h5>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat text-primary">
                                            <i class="align-middle" data-feather="dollar-sign"></i>
                                        </div>
                                    </div>
                                </div>
                                <h1 class="mt-1 mb-3">{{ $totalPasienPerluAnalisa ?? 0 }}</h1>
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
        <!-- <h2 class="h4 mb-3 mt-4"><strong>Data Pasien Unit Gawat Darurat</strong></h2>
        <div class="row">
            <div class="col-12">
                <div class="card flex-fill">
                    <div class="card-body">
                        <table class="table table-hover my-0" id="ugdTable">
                            <thead>
                                <tr>
                                    <th class="nowrap">No.</th>
                                    <th class="nowrap">Hari/Tanggal Masuk</th>
                                    <th class="nowrap">Nama Pasien</th>
                                    <th class="nowrap">Umur</th>
                                    <th class="nowrap">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($ugd_pasien) > 0)
                                @foreach ($ugd_pasien as $index => $pasien)
                                <tr>
                                    <td class="nowrap">{{ $index + 1 }}</td>
                                    <td class="nowrap">{{ \Carbon\Carbon::parse($pasien->tanggal_masuk)->locale('id')->translatedFormat('l, d F Y') }}
                                    </td>
                                    <td class="nowrap">{{ $pasien->nama_pasien }}</td>
                                    <td class="nowrap">
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
                                    <td class="nowrap">
                                @php
                                    $status = $pasien->status ?: 'Perlu Analisa';
                                    $badgeClass = 'bg-secondary';
                                    if (strtolower($status) === 'perlu analisa') {
                                        $badgeClass = 'bg-warning text-dark';
                                    } elseif (strtolower($status) === 'ugd') {
                                        $badgeClass = 'bg-warning text-dark';
                                    } elseif (strtolower($status) === 'rawat inap') {
                                        $badgeClass = 'bg-primary';
                                    } elseif (strtolower($status) === 'rawat jalan') {
                                        $badgeClass = 'bg-success';
                                    }
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                            </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data pasien UGD</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="h4 mb-3 mt-4"><strong>Data Pasien Rawat Inap</strong></h2>
        <div class="row">
            <div class="col-12">
                <div class="card flex-fill">
                    <div class="card-body">
                        <table class="table table-hover my-0" id="rawatinapTable">
                            <thead>
                                <tr>
                                    <th class="nowrap">No.</th>
                                    <th class="nowrap">Hari/Tanggal Masuk</th>
                                    <th class="nowrap">Nama Pasien</th>
                                    <th class="nowrap">Umur</th>
                                    <th class="nowrap">Ruangan</th>
                                    <th class="nowrap">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($rawatinap_pasien) > 0)
                                @foreach ($rawatinap_pasien as $index => $pasien)
                                <tr>
                                    <td class="nowrap">{{ $index + 1 }}</td>
                                    <td class="nowrap">{{ \Carbon\Carbon::parse($pasien->tanggal_masuk)->locale('id')->translatedFormat('l, d F Y') }}
                                    </td>
                                    <td class="nowrap">{{ $pasien->nama_pasien }}</td>
                                    <td class="nowrap">
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
                                    <td class="nowrap">
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
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data pasien Rawat Inap</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> -->
        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="card flex-fill w-100">
                    <div class="card-header">
                        <h5 class="card-title">Jumlah Pasien Dalam Ruangan</h5>
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
</div>
@endsection
@section('scripts')

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Prepare data for line chart
        const roomNames = @json($roomNames);
        const roomPatientCounts = @json($roomPatientCounts);

        // Line chart
        new Chart(document.getElementById("chartjs-line"), {
            type: "line",
            data: {
                labels: roomNames,
                datasets: [{
                    label: "Jumlah Pasien per Ruangan",
                    fill: true,
                    backgroundColor: "transparent",
                    borderColor: window.theme.primary,
                    data: roomPatientCounts
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
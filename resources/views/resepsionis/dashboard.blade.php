@extends('dashboardResepsionis')

@section('resepsionis')
<div class="container-fluid p-0">

    <h1 class="h3 mb-3"><strong>Dashboard Resepsionis</strong></h1>

    <div class="row">
        <div class="col-xl-12 col-xxl-12 d-flex">
            <div class="w-100">
                <div class="row">
                    <div class="col-6 col-md-3 d-flex">
                        <div class="card flex-fill">
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
                    <div class="col-6 col-md-3 d-flex">
                        <div class="card flex-fill">
                            <div class="card-body d-flex flex-column">
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
                    <div class="col-6 col-md-3 d-flex">
                        <div class="card flex-fill">
                            <div class="card-body d-flex flex-column">
                                <div class="row">
                                    <div class="col mt-0">
                                        <h5 class="card-title">Rawat Inap</h5>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat text-primary">
                                            <i class="align-middle" data-feather="home"></i>
                                        </div>
                                    </div>
                                </div>
                                <h1 class="mt-1 mb-3">{{ $totalRawatInapCount }}</h1>
                                <div class="mb-0">
                                    <span class="text-muted">Pasien</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 d-flex">
                        <div class="card flex-fill">
                            <div class="card-body d-flex flex-column">
                                <div class="row">
                                    <div class="col mt-0">
                                        <h5 class="card-title">UGD</h5>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat text-primary">
                                            <i class="align-middle" data-feather="alert-circle"></i>
                                        </div>
                                    </div>
                                </div>
                                <h1 class="mt-1 mb-3">{{ $totalUgdCount }}</h1>
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

    <!-- <div class="container-fluid p-0">
        <h1 class="h3 mb-3"><strong>Antrian Pasien</strong></h1>
        <div class="row">
            <div class="col-12 col-lg-12 col-xxl-12 d-flex">
                <div class="card flex-fill">
                    <div class="table-responsive">
                        <table class="table table-hover my-0">
                            <thead>
                                <tr>
                                    <th class="nowrap">No.</th>
                                    <th class="nowrap">Nomor RM</th>
                                    <th class="nowrap">Nama Pasien</th>
                                    <th class="nowrap">Umur</th>
                                    <th class="nowrap">JamKes</th>
                                    <th class="nowrap">Poli Tujuan</th>
                                    <th class="nowrap">Tgl. Berobat</th>
                                    <th class="nowrap">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($antrians as $index => $antrian)
                                <tr>
                                    <td class="nowrap">{{ $antrians->firstItem() + $index }}</td>
                                    <td class="nowrap">{{ $antrian->no_rekam_medis }}</td>
                                    <td class="nowrap">{{ $antrian->pasien->nama_pasien }}</td>
                                    <td class="nowrap">{{ \Carbon\Carbon::parse($antrian->pasien->tanggal_lahir)->age }} tahun</td>
                                    <td class="nowrap">{{ $antrian->pasien->jaminan_kesehatan }}</td>
                                    <td class="nowrap">{{ $antrian->poli ? $antrian->poli->nama_poli : 'Tidak ada' }}</td>
                                    <td class="nowrap">{{ \Carbon\Carbon::parse($antrian->tanggal_berobat)->format('d-m-Y') }}</td>
                                    <td class="nowrap">
                                        @if ($antrian->status == 'Perlu Analisa')
                                        <span class="badge bg-danger">{{ $antrian->status }}</span>
                                        @elseif ($antrian->status == 'Pemeriksaan')
                                        <span class="badge bg-warning">{{ $antrian->status }}</span>
                                        @elseif ($antrian->status == 'Farmasi')
                                        <span class="badge bg-primary">{{ $antrian->status }}</span>
                                        @else
                                        <span class="badge bg-info">{{ $antrian->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 mb-2">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div class="small text-muted mb-2 text-start ps-3 pagination-info-text">
                                Showing {{ $antrians->firstItem() }} to {{ $antrians->lastItem() }} of
                                {{ $antrians->total() }} results
                            </div>
                            <nav class="ms-3">
                                <ul class="pagination pagination-sm mb-2" style="list-style-type: none;"
                                    id="antrian-pagination">
                                    {{-- Previous Page Link --}}
                                    @if ($antrians->onFirstPage())
                                    <li class="page-item disabled" aria-disabled="true" aria-label="Previous">
                                        <span class="page-link" aria-hidden="true">&laquo;</span>
                                    </li>
                                    @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $antrians->previousPageUrl() }}" rel="prev"
                                            aria-label="Previous">&laquo;</a>
                                    </li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @php
                                    $totalPages = $antrians->lastPage();
                                    $currentPage = $antrians->currentPage();
                                    $maxButtons = 3;

                                    if ($totalPages <= $maxButtons) {
                                        $start=1;
                                        $end=$totalPages;
                                        } else {
                                        if ($currentPage==1) {
                                        $start=1;
                                        $end=3;
                                        } elseif ($currentPage==$totalPages) {
                                        $start=$totalPages - 2;
                                        $end=$totalPages;
                                        } else {
                                        $start=$currentPage - 1;
                                        $end=$currentPage + 1;
                                        }
                                        }
                                        @endphp

                                        @for ($page=$start; $page <=$end; $page++)
                                        @if ($page==$currentPage)
                                        <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                                        @else
                                        <li class="page-item"><a class="page-link" href="{{ $antrians->url($page) }}">{{ $page }}</a></li>
                                        @endif
                                        @endfor

                                        {{-- Next Page Link --}}
                                        @if ($antrians->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $antrians->nextPageUrl() }}" rel="next"
                                                aria-label="Next">&raquo;</a>
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
    </div> -->

    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="card flex-fill w-100">
                <div class="card-header">
                    <h5 class="card-title">Pasien Selesai/Bulan</h5>
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
                    <h5 class="card-title">Pasien Poli Selesai/Bulan</h5>
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
        // Doughnut chart with center labels
        const ctx = document.getElementById("chartjs-doughnut").getContext('2d');
        const data = {
            labels: @json($poliLabels),
            datasets: [{
                data: @json($poliData),
                backgroundColor: [
                    window.theme.primary,
                    window.theme.success,
                    window.theme.warning
                ],
                borderColor: "transparent"
            }]
        };

        const centerTextPlugin = {
            id: 'centerText',
            afterDraw(chart) {
                // Removed center text display as per user request
            }
        };

        new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: {
                maintainAspectRatio: false,
                cutoutPercentage: 65,
                legend: {
                    display: false
                }
            },
            plugins: [centerTextPlugin]
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Line chart
        new Chart(document.getElementById("chartjs-line"), {
            type: "line",
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct",
                    "Nov", "Dec"
                ],
                datasets: [{
                    label: "Jumlah Pasien Selesai",
                    fill: true,
                    backgroundColor: "transparent",
                    borderColor: window.theme.primary,
                    data: @json(array_values($monthlyData))
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
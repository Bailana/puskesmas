@extends('dashboardadmin')

@section('admin')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>Log Aktifitas</strong></h1>
    <div class="row">
        <div class="col-12 col-lg-12 col-xxl-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex justify-content-between">
                    <form method="GET" action="{{ route('admin.log') }}" class="d-flex flex-wrap align-items-center gap-2 m-0 p-0" id="searchForm">
                        <div class="input-group" style="width: 250px;">
                            <input type="text" name="search" class="form-control" id="searchInput" placeholder="Pencarian..." aria-label="Search" value="{{ request('search') }}" autocomplete="off">
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover my-0" id="logTable">
                        <thead>
                            <tr>
                                <th style="white-space: nowrap;">No.</th>
                                <th style="white-space: nowrap;">Akun</th>
                                <th style="white-space: nowrap;">Aksi</th>
                                <th style="white-space: nowrap;">Deskripsi</th>
                                <th style="white-space: nowrap;">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $index => $log)
                            <tr>
                                <td style="white-space: nowrap;">{{ $logs->firstItem() + $index }}</td>
                                <td style="white-space: nowrap;">{{ $log->user ? $log->user->name : 'System' }}</td>
                                <td style="white-space: nowrap;">{{ $log->action }}</td>
                                <td style="white-space: nowrap;">{{ $log->description }}</td>
                                <td style="white-space: nowrap;">{{ $log->created_at->diffForHumans() }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada log aktifitas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if ($logs->count() > 0)
                    <div class="mt-3 mb-2">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div class="small text-muted mb-2 text-start ps-3 pagination-info-text">
                                Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of
                                {{ $logs->total() }} results
                            </div>
                            <nav class="d-flex justify-content-center">
                                <ul class="pagination d-flex flex-row flex-wrap gap-2"
                                    style="list-style-type: none; padding-left: 0; margin-bottom: 0;">
                                    {{-- Previous Page Link --}}
                                    @if ($logs->onFirstPage())
                                    <li class="page-item disabled" aria-disabled="true" aria-label="Previous">
                                        <span class="page-link" aria-hidden="true">&laquo;</span>
                                    </li>
                                    @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $logs->previousPageUrl() }}" rel="prev"
                                            aria-label="Previous">&laquo;</a>
                                    </li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @php
                                    $totalPages = $logs->lastPage();
                                    $currentPage = $logs->currentPage();
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
                                    <li class="page-item"><a class="page-link" href="{{ $logs->url($page) }}">{{ $page }}</a></li>
                                    @endif
                                    @endfor

                                    {{-- Next Page Link --}}
                                    @if ($logs->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $logs->nextPageUrl() }}" rel="next"
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
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

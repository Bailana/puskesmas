@extends('dashboardadmin')

@section('admin')
<div class="container">
    <h1 class="mb-4">Log Aktifitas</h1>

    @if ($logs->count() > 0)
    <table class="table table-striped">
        <thead>
            <tr>
                <th>User</th>
                <th>Aksi</th>
                <th>Deskripsi</th>
                <th>Waktu</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
            <tr>
                <td>{{ $log->user ? $log->user->name : 'System' }}</td>
                <td>{{ $log->action }}</td>
                <td>{{ $log->description }}</td>
                <td>{{ $log->created_at->diffForHumans() }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $logs->links() }}
    @else
    <p>Tidak ada log aktifitas.</p>
    @endif
</div>
@endsection

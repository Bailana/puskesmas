@extends('dashboardbidan')

@section('bidan')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>Riwayat Berobat Pasien</strong></h1>
    <div class="card">
        <div class="card-header">
            <strong>Pasien:</strong> {{ $pasien->nama_pasien }}<br>
            <strong>No. Rekam Medis:</strong> {{ $pasien->no_rekam_medis }}<br>
            <strong>NIK:</strong> {{ $pasien->nik }}
        </div>
        <div class="card-body">
            @if($riwayat->isEmpty())
                <div class="alert alert-info">Belum ada riwayat berobat untuk pasien ini.</div>
            @else
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Tanggal Kunjungan</th>
                            <th>Poli</th>
                            <th>Diagnosa</th>
                            <th>Dokter/Bidan</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayat as $item)
                        <tr>
                            <td>{{ $item->tanggal_kunjungan ? \Carbon\Carbon::parse($item->tanggal_kunjungan)->format('d-m-Y') : '-' }}</td>
                            <td>{{ $item->poli->nama_poli ?? '-' }}</td>
                            <td>{{ $item->diagnosa ?? '-' }}</td>
                            <td>{{ $item->petugas->nama ?? '-' }}</td>
                            <td>{{ $item->keterangan ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
            <a href="{{ route('bidan.pasien') }}" class="btn btn-secondary mt-3">Kembali ke Data Pasien</a>
        </div>
    </div>
</div>
@endsection

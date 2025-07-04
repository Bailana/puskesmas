@extends('dashboardbidan')

@section('bidan')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>Jadwal Dokter</strong></h1>

    <div class="row g-3">
        @foreach ($jadwalDokters as $jadwal)
        <div class="col-md-6 col-lg-3">
            <div class="card shadow border-0 rounded-4 overflow-hidden h-100" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h5 class="card-title mb-1 fw-bold text-white">{{ $jadwal['nama_dokter'] }}</h5>
                    <small class="text-light fst-italic">{{ ucfirst($jadwal['poliklinik']) }}</small>
                </div>
                <div class="card-body p-3">
                    <table class="table table-sm mb-0" style="font-size: 14px;">
                        <thead>
                            <tr>
                                <th>Hari</th>
                                <th>Jam</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'] as $day)
                            <tr>
                                <td>{{ ucfirst($day) }}</td>
                                <td>{{ $jadwal[$day] ?: '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', function() {
            const filter = searchInput.value.toLowerCase();

            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let textContent = '';
                for (let j = 0; j < cells.length; j++) {
                    textContent += cells[j].textContent.toLowerCase() + ' ';
                }
                if (textContent.indexOf(filter) > -1) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        });
    });
</script>
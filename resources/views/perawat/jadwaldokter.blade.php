@extends('dashboardperawat')

@section('perawat')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>Jadwal Dokter</strong></h1>

    <div class="mb-2 w-50">
        <input type="search" id="searchInput" class="form-control" placeholder="Cari jadwal dokter...">
    </div>
    <div id="cardsContainer" class="row g-3">
        @foreach ($jadwalDokters as $jadwal)
        <div class="col-md-4 jadwal-card">
            <div class="card h-100">
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Nama:</strong> {{ $jadwal->nama_dokter }}</li>
                        <li class="list-group-item"><strong>Poli Klinik:</strong> {{ $jadwal->poliklinik }}</li>
                        <li class="list-group-item"><strong>Senin:</strong> {{ $jadwal->senin }}</li>
                        <li class="list-group-item"><strong>Selasa:</strong> {{ $jadwal->selasa }}</li>
                        <li class="list-group-item"><strong>Rabu:</strong> {{ $jadwal->rabu }}</li>
                        <li class="list-group-item"><strong>Kamis:</strong> {{ $jadwal->kamis }}</li>
                        <li class="list-group-item"><strong>Jumat:</strong> {{ $jadwal->jumat }}</li>
                        <li class="list-group-item"><strong>Sabtu:</strong> {{ $jadwal->sabtu }}</li>
                        <li class="list-group-item"><strong>Minggu:</strong> {{ $jadwal->minggu }}</li>
                    </ul>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const cards = document.querySelectorAll('#cardsContainer .jadwal-card');

        searchInput.addEventListener('input', function () {
            const filter = searchInput.value.toLowerCase();

            cards.forEach(card => {
                const textContent = card.textContent.toLowerCase();
                if (textContent.indexOf(filter) > -1) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
</script>
@endsection

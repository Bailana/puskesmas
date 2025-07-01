@extends('dashboardadmin')

@section('admin')
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3"><strong>Jadwal Dokter</strong></h1>

        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <input type="search" id="searchInput" class="form-control w-50" placeholder="Cari jadwal dokter...">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addJadwalModal">
                    Tambah Jadwal Dokter
                </button>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="jadwalTable">
                    <thead>
                        <tr>
                            <th>Nama Dokter</th>
                            <th>Poliklinik</th>
                            <th>Senin</th>
                            <th>Selasa</th>
                            <th>Rabu</th>
                            <th>Kamis</th>
                            <th>Jumat</th>
                            <th>Sabtu</th>
                            <th>Minggu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jadwalDokters as $jadwal)
                            <tr>
                                <td>{{ $jadwal->nama_dokter }}</td>
                                <td>{{ $jadwal->poliklinik }}</td>
                                <td>{{ $jadwal->senin }}</td>
                                <td>{{ $jadwal->selasa }}</td>
                                <td>{{ $jadwal->rabu }}</td>
                                <td>{{ $jadwal->kamis }}</td>
                                <td>{{ $jadwal->jumat }}</td>
                                <td>{{ $jadwal->sabtu }}</td>
                                <td>{{ $jadwal->minggu }}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary btn-edit" data-id="{{ $jadwal->id }}">Edit</button>
                                    <form action="{{ route('admin.jadwaldokter.destroy', $jadwal->id) }}" method="POST"
                                        style="display:inline;"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit Jadwal Dokter -->
    <div class="modal fade" id="addJadwalModal" tabindex="-1" aria-labelledby="addJadwalModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="jadwalForm" action="{{ route('admin.jadwaldokter.store') }}" method="POST">
                @csrf
                <input type="hidden" id="jadwalId" name="jadwalId" value="">
                <input type="hidden" id="methodField" name="_method" value="">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addJadwalModalLabel">Tambah Jadwal Dokter</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_dokter" class="form-label">Nama Dokter</label>
                            <input type="text" class="form-control" id="nama_dokter" name="nama_dokter" required>
                        </div>
                        <div class="mb-3">
                            <label for="poliklinik" class="form-label">Poliklinik</label>
                            <select class="form-select" id="poliklinik" name="poliklinik" required>
                                <option value="" selected disabled>Pilih Poliklinik</option>
                                <option value="umum">Umum</option>
                                <option value="gigi">Gigi</option>
                                <option value="KIA">KIA</option>
                                <option value="anak ibu hamil">Anak Ibu Hamil</option>
                                <option value="lansia">Lansia</option>
                            </select>
                        </div>
                        @foreach (['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'] as $day)
                            <div class="mb-3">
                                <label class="form-label">{{ ucfirst($day) }}</label>
                                <div class="row">
                                    <div class="col">
                                        <input type="time" class="form-control" id="{{ $day }}_masuk" name="{{ $day }}_masuk" placeholder="Jam Masuk">
                                    </div>
                                    <div class="col">
                                        <input type="time" class="form-control" id="{{ $day }}_keluar" name="{{ $day }}_keluar" placeholder="Jam Keluar">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const jadwalTable = document.getElementById('jadwalTable');
            const rows = jadwalTable.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            searchInput.addEventListener('input', function () {
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

            // Edit button click handler
            const editButtons = document.querySelectorAll('.btn-edit');
            const jadwalForm = document.getElementById('jadwalForm');
            const modalTitle = document.getElementById('addJadwalModalLabel');
            const submitBtn = document.getElementById('submitBtn');
            const jadwalIdInput = document.getElementById('jadwalId');
            const methodField = document.getElementById('methodField');

            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    fetch(`/admin/jadwaldokter/edit/${id}`)
                        .then(response => response.json())
                        .then(data => {
                            modalTitle.textContent = 'Edit Jadwal Dokter';
                            submitBtn.textContent = 'Update';
                            jadwalForm.action = `/admin/jadwaldokter/update/${id}`;
                            jadwalIdInput.value = id;
                            methodField.value = 'PUT';
                            document.getElementById('nama_dokter').value = data.nama_dokter;
                            document.getElementById('poliklinik').value = data.poliklinik;
                            document.getElementById('senin_masuk').value = data.senin_masuk;
                            document.getElementById('senin_keluar').value = data.senin_keluar;
                            document.getElementById('selasa_masuk').value = data.selasa_masuk;
                            document.getElementById('selasa_keluar').value = data.selasa_keluar;
                            document.getElementById('rabu_masuk').value = data.rabu_masuk;
                            document.getElementById('rabu_keluar').value = data.rabu_keluar;
                            document.getElementById('kamis_masuk').value = data.kamis_masuk;
                            document.getElementById('kamis_keluar').value = data.kamis_keluar;
                            document.getElementById('jumat_masuk').value = data.jumat_masuk;
                            document.getElementById('jumat_keluar').value = data.jumat_keluar;
                            document.getElementById('sabtu_masuk').value = data.sabtu_masuk;
                            document.getElementById('sabtu_keluar').value = data.sabtu_keluar;
                            document.getElementById('minggu_masuk').value = data.minggu_masuk;
                            document.getElementById('minggu_keluar').value = data.minggu_keluar;
                            var addJadwalModal = new bootstrap.Modal(document.getElementById('addJadwalModal'));
                            addJadwalModal.show();
                        });
                });
            });

            // Reset modal on close
            var addJadwalModalEl = document.getElementById('addJadwalModal');
            addJadwalModalEl.addEventListener('hidden.bs.modal', function () {
                modalTitle.textContent = 'Tambah Jadwal Dokter';
                submitBtn.textContent = 'Simpan';
                jadwalForm.action = '{{ route("admin.jadwaldokter.store") }}';
                jadwalIdInput.value = '';
                methodField.value = '';
                jadwalForm.reset();
                // Clear the new inputs as well
                @foreach (['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'] as $day)
                    document.getElementById('{{ $day }}_masuk').value = '';
                    document.getElementById('{{ $day }}_keluar').value = '';
                @endforeach
            });
        });
    </script>
@endsection
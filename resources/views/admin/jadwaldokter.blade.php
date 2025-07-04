@extends('dashboardadmin')

@section('admin')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>Jadwal Dokter</strong></h1>

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="input-group" style="width: 250px;">
                <input type="text" name="search" class="form-control" id="searchInput"
                    placeholder="Pencarian..." aria-label="Search" value="{{ request('search') }}"
                    autocomplete="off">
            </div>
            <button type="button" class="btn btn-success btn-lg" style="padding: 5px 10px; font-size: 0.9rem;" data-bs-toggle="modal" data-bs-target="#addJadwalModal">
                <i class="fas fa-plus"></i> Tambah Jadwal Dokter
            </button>
        </div>
        <div class="tabel-responsive">
            <table class="table table-striped" id="jadwalTable">
                <thead>
                    <tr>
                        <th>No.</th>
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
                    @foreach ($jadwalDokters as $index => $jadwal)
                    <tr>
                        <td>{{ $index + 1 }}.</td>
                        <td>{{ $jadwal['nama_dokter'] }}</td>
                        <td>{{ ucfirst($jadwal['poliklinik']) }}</td>
                        <td>{{ $jadwal['senin'] ?: '-' }}</td>
                        <td>{{ $jadwal['selasa'] ?: '-' }}</td>
                        <td>{{ $jadwal['rabu'] ?: '-' }}</td>
                        <td>{{ $jadwal['kamis'] ?: '-' }}</td>
                        <td>{{ $jadwal['jumat'] ?: '-' }}</td>
                        <td>{{ $jadwal['sabtu'] ?: '-' }}</td>
                        <td>{{ $jadwal['minggu'] ?: '-' }}</td>
                        <td>
                            <button class="btn btn-sm rounded btn-primary btn-edit me-2" data-nama-dokter="{{ $jadwal['nama_dokter'] }}" data-poliklinik="{{ $jadwal['poliklinik'] }}">Edit</button>
                            <form action="{{ route('admin.jadwaldokter.destroy', $jadwal['ids'][0] ?? '') }}" method="POST"
                                style="display:inline;"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm rounded btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Jadwal Dokter -->
<div class="modal fade" id="addJadwalModal" tabindex="-1" aria-labelledby="addJadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="jadwalForm" action="{{ route('admin.jadwaldokter.store') }}" method="POST">
            @csrf
            <input type="hidden" id="jadwalId" name="jadwalId" value="">
            <input type="hidden" id="methodField" name="_method" value="">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h3 class="modal-title" id="addJadwalModalLabel"><strong>Jadwal Dokter</strong></h3>
                    <button type="button" class="btn btn-close mb-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_dokter" class="form-label">Nama Dokter</label>
                        <select class="form-select" id="nama_dokter" name="nama_dokter" required>
                            <option value="" selected disabled>Pilih Nama Dokter</option>
                            @foreach($users as $user)
                            <option value="{{ $user->name }}" {{ old('nama_dokter') == $user->name ? 'selected' : '' }}>
                                {{ $user->name }} ({{ ucfirst($user->role) }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="poliklinik" class="form-label">Poliklinik</label>
                        <select class="form-select" id="poliklinik" name="poliklinik" required>
                            <option value="" selected disabled>Pilih Poliklinik</option>
                            <option value="umum">Umum</option>
                            <option value="gigi">Gigi</option>
                            <option value="KIA">KIA</option>
                        </select>
                    </div>
                    <div id="hariContainer">
                        <div class="row mb-3 hari-row">
                            <div class="col">
                                <label for="hari" class="form-label">Hari</label>
                                <select class="form-select" name="hari[]" required>
                                    <option value="" selected disabled>Pilih Hari</option>
                                    <option value="senin">Senin</option>
                                    <option value="selasa">Selasa</option>
                                    <option value="rabu">Rabu</option>
                                    <option value="kamis">Kamis</option>
                                    <option value="jumat">Jumat</option>
                                    <option value="sabtu">Sabtu</option>
                                    <option value="minggu">Minggu</option>
                                </select>
                            </div>
                            <div class="col">
                                <label for="jam_masuk" class="form-label">Jam Masuk</label>
                                <input type="time" class="form-control" name="jam_masuk[]" placeholder="Jam Masuk" required>
                            </div>
                            <div class="col">
                                <label for="jam_keluar" class="form-label">Jam Keluar</label>
                                <div class="d-flex align-items-center">
                                    <input type="time" class="form-control" name="jam_keluar[]" placeholder="Jam Keluar" required>
                                    <button type="button" class="btn btn-danger rounded btn-sm ms-2 btn-remove-hari" title="Hapus Hari">&times;</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-success rounded btn-sm mb-3" id="btnAddHari">Tambah Hari</button>
                </div>
                <div class="modal-footer d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary" id="submitBtn">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Jadwal Dokter -->
<div class="modal fade" id="editJadwalModal" tabindex="-1" aria-labelledby="editJadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editJadwalForm" method="POST">
            @csrf
            <input type="hidden" id="editJadwalId" name="jadwalId" value="">
            <input type="hidden" id="editMethodField" name="_method" value="">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h3 class="modal-title" id="editJadwalModalLabel"><strong>Edit Jadwal Dokter</strong></h3>
                    <button type="button" class="btn btn-close mb-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nama_dokter" class="form-label">Nama Dokter</label>
                        <select class="form-select" id="edit_nama_dokter" name="nama_dokter" required>
                            <option value="" selected disabled>Pilih Nama Dokter</option>
                            @foreach($users as $user)
                            <option value="{{ $user->name }}">
                                {{ $user->name }} ({{ ucfirst($user->role) }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_poliklinik" class="form-label">Poliklinik</label>
                        <select class="form-select" id="edit_poliklinik" name="poliklinik" required>
                            <option value="" selected disabled>Pilih Poliklinik</option>
                            <option value="umum">Umum</option>
                            <option value="gigi">Gigi</option>
                            <option value="KIA">KIA</option>
                        </select>
                    </div>
                    <div id="editHariContainer">
                        <!-- Dynamic hari rows will be added here by JavaScript -->
                    </div>
                    <button type="button" class="btn btn-success rounded btn-sm mb-3" id="btnEditAddHari">Tambah Hari</button>
                </div>
                <div class="modal-footer d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary" id="editSubmitBtn">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const jadwalTable = document.getElementById('jadwalTable');
        const rows = jadwalTable.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

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

        // Edit button click handler
        const editButtons = document.querySelectorAll('.btn-edit');
        const editJadwalForm = document.getElementById('editJadwalForm');
        const editModalTitle = document.getElementById('editJadwalModalLabel');
        const editSubmitBtn = document.getElementById('editSubmitBtn');
        const editJadwalIdInput = document.getElementById('editJadwalId');
        const editMethodField = document.getElementById('editMethodField');
        const editHariContainer = document.getElementById('editHariContainer');
        const editNamaDokterSelect = document.getElementById('edit_nama_dokter');
        const editPoliklinikSelect = document.getElementById('edit_poliklinik');
        const editAddHariBtn = document.getElementById('btnEditAddHari');

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const namaDokter = this.getAttribute('data-nama-dokter');
                const poliklinik = this.getAttribute('data-poliklinik');
                fetch(`/admin/jadwaldokter/edit-group/${encodeURIComponent(namaDokter)}/${encodeURIComponent(poliklinik)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data[0]?.id) {
                            alert('Data jadwal dokter tidak ditemukan untuk diedit.');
                            return;
                        }
                        editModalTitle.textContent = 'Edit Jadwal Dokter';
                        editSubmitBtn.textContent = 'Update';
                        editJadwalForm.action = `/admin/jadwaldokter/update/${data[0].id}`;
                        editJadwalIdInput.value = data[0].id;
                        editMethodField.value = 'PUT';
                        if (data.length > 0) {
                            editNamaDokterSelect.value = data[0].nama_dokter;
                            editPoliklinikSelect.value = data[0].poliklinik;
                        }
                        // Clear existing hari rows
                        editHariContainer.innerHTML = '';
                        // Populate hari rows from data
                        if (data.length > 0) {
                            data.forEach(item => {
                                // If item.hari, jam_masuk, jam_keluar are arrays, iterate and add rows for each
                                if (Array.isArray(item.hari) && Array.isArray(item.jam_masuk) && Array.isArray(item.jam_keluar)) {
                                    for (let i = 0; i < item.hari.length; i++) {
                                        window.addHariRowEdit(item.hari[i], item.jam_masuk[i], item.jam_keluar[i]);
                                    }
                                } else {
                                    window.addHariRowEdit(item.hari, item.jam_masuk, item.jam_keluar);
                                }
                            });
                        } else {
                            window.addHariRowEdit();
                        }
                        var editJadwalModal = new bootstrap.Modal(document.getElementById('editJadwalModal'));
                        editJadwalModal.show();
                    });
            });
        });

    // Define the function to add a hari row in the edit modal
    window.addHariRowEdit = function(hari = '', jamMasuk = '', jamKeluar = '') {
        const hariContainer = document.getElementById('editHariContainer');

        const row = document.createElement('div');
        row.classList.add('row', 'mb-3', 'hari-row');

        // Kolom hari
        const colHari = document.createElement('div');
        colHari.classList.add('col');
        const labelHari = document.createElement('label');
        labelHari.classList.add('form-label');
        labelHari.textContent = 'Hari';
        const selectHari = document.createElement('select');
        selectHari.classList.add('form-select');
        selectHari.name = 'hari[]';
        selectHari.required = true;

        const hariOptions = ['', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
        const hariOptionTexts = ['Pilih Hari', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        hariOptions.forEach((value, index) => {
            const option = document.createElement('option');
            option.value = value;
            option.textContent = hariOptionTexts[index];
            if (value === '') {
                option.selected = true;
                option.disabled = true;
            }
            if (value === hari) {
                option.selected = true;
            }
            selectHari.appendChild(option);
        });

        colHari.appendChild(labelHari);
        colHari.appendChild(selectHari);

        // Kolom jam masuk
        const colJamMasuk = document.createElement('div');
        colJamMasuk.classList.add('col');
        const labelJamMasuk = document.createElement('label');
        labelJamMasuk.classList.add('form-label');
        labelJamMasuk.textContent = 'Jam Masuk';
        const inputJamMasuk = document.createElement('input');
        inputJamMasuk.type = 'time';
        inputJamMasuk.classList.add('form-control');
        inputJamMasuk.name = 'jam_masuk[]';
        inputJamMasuk.placeholder = 'Jam Masuk';
        inputJamMasuk.required = true;
        inputJamMasuk.value = jamMasuk;

        colJamMasuk.appendChild(labelJamMasuk);
        colJamMasuk.appendChild(inputJamMasuk);

        // Kolom jam keluar
        const colJamKeluar = document.createElement('div');
        colJamKeluar.classList.add('col');
        const labelJamKeluar = document.createElement('label');
        labelJamKeluar.classList.add('form-label');
        labelJamKeluar.textContent = 'Jam Keluar';
        const divInputGroup = document.createElement('div');
        divInputGroup.classList.add('d-flex', 'align-items-center');
        const inputJamKeluar = document.createElement('input');
        inputJamKeluar.type = 'time';
        inputJamKeluar.classList.add('form-control');
        inputJamKeluar.name = 'jam_keluar[]';
        inputJamKeluar.placeholder = 'Jam Keluar';
        inputJamKeluar.required = true;
        inputJamKeluar.value = jamKeluar;

        const btnRemove = document.createElement('button');
        btnRemove.type = 'button';
        btnRemove.classList.add('btn', 'btn-danger', 'btn-sm', 'ms-2', 'btn-remove-hari', 'rounded');
        btnRemove.title = 'Hapus Hari';
        btnRemove.textContent = '×';

        btnRemove.addEventListener('click', function() {
            row.remove();
        });

        divInputGroup.appendChild(inputJamKeluar);
        divInputGroup.appendChild(btnRemove);

        colJamKeluar.appendChild(labelJamKeluar);
        colJamKeluar.appendChild(divInputGroup);

        row.appendChild(colHari);
        row.appendChild(colJamMasuk);
        row.appendChild(colJamKeluar);

        hariContainer.appendChild(row);
    };

    // Add event listener for "Tambah Hari" button in edit modal
    const btnEditAddHari = document.getElementById('btnEditAddHari');
    if (btnEditAddHari) {
        btnEditAddHari.addEventListener('click', function() {
            window.addHariRowEdit();
        });
    }

    });

    // Reset modal form fields immediately when close button is clicked
    const closeButton = document.querySelector('#addJadwalModal .btn-close');
    if (closeButton) {
        closeButton.addEventListener('click', function() {
            modalTitle.textContent = 'Tambah Jadwal Dokter';
            submitBtn.textContent = 'Simpan';
            jadwalForm.action = '{{ route("admin.jadwaldokter.store") }}';
            jadwalIdInput.value = '';
            methodField.value = '';
            jadwalForm.reset();
            document.getElementById('nama_dokter').value = '';
            document.getElementById('poliklinik').value = '';
            // Clear all dynamic hari rows except the first one
            const hariContainer = document.getElementById('hariContainer');
            hariContainer.innerHTML = '';
            // Add one empty row
            addHariRow();
        });
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi untuk menambahkan baris input hari, jam masuk, dan jam keluar
        function addHariRow(hari = '', jamMasuk = '', jamKeluar = '') {
            const hariContainer = document.getElementById('hariContainer');

            const row = document.createElement('div');
            row.classList.add('row', 'mb-3', 'hari-row');

            // Kolom hari
            const colHari = document.createElement('div');
            colHari.classList.add('col');
            const labelHari = document.createElement('label');
            labelHari.classList.add('form-label');
            labelHari.textContent = 'Hari';
            const selectHari = document.createElement('select');
            selectHari.classList.add('form-select');
            selectHari.name = 'hari[]';
            selectHari.required = true;

            const hariOptions = ['', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
            const hariOptionTexts = ['Pilih Hari', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

            hariOptions.forEach((value, index) => {
                const option = document.createElement('option');
                option.value = value;
                option.textContent = hariOptionTexts[index];
                if (value === '') {
                    option.selected = true;
                    option.disabled = true;
                }
                if (value === hari) {
                    option.selected = true;
                }
                selectHari.appendChild(option);
            });

            colHari.appendChild(labelHari);
            colHari.appendChild(selectHari);

            // Kolom jam masuk
            const colJamMasuk = document.createElement('div');
            colJamMasuk.classList.add('col');
            const labelJamMasuk = document.createElement('label');
            labelJamMasuk.classList.add('form-label');
            labelJamMasuk.textContent = 'Jam Masuk';
            const inputJamMasuk = document.createElement('input');
            inputJamMasuk.type = 'time';
            inputJamMasuk.classList.add('form-control');
            inputJamMasuk.name = 'jam_masuk[]';
            inputJamMasuk.placeholder = 'Jam Masuk';
            inputJamMasuk.required = true;
            inputJamMasuk.value = jamMasuk;

            colJamMasuk.appendChild(labelJamMasuk);
            colJamMasuk.appendChild(inputJamMasuk);

            // Kolom jam keluar
            const colJamKeluar = document.createElement('div');
            colJamKeluar.classList.add('col');
            const labelJamKeluar = document.createElement('label');
            labelJamKeluar.classList.add('form-label');
            labelJamKeluar.textContent = 'Jam Keluar';
            const divInputGroup = document.createElement('div');
            divInputGroup.classList.add('d-flex', 'align-items-center');
            const inputJamKeluar = document.createElement('input');
            inputJamKeluar.type = 'time';
            inputJamKeluar.classList.add('form-control');
            inputJamKeluar.name = 'jam_keluar[]';
            inputJamKeluar.placeholder = 'Jam Keluar';
            inputJamKeluar.required = true;
            inputJamKeluar.value = jamKeluar;

            const btnRemove = document.createElement('button');
            btnRemove.type = 'button';
            btnRemove.classList.add('btn', 'btn-danger', 'btn-sm', 'ms-2', 'btn-remove-hari', 'rounded');
            btnRemove.title = 'Hapus Hari';
            btnRemove.textContent = '×';

            btnRemove.addEventListener('click', function() {
                row.remove();
            });

            divInputGroup.appendChild(inputJamKeluar);
            divInputGroup.appendChild(btnRemove);

            colJamKeluar.appendChild(labelJamKeluar);
            colJamKeluar.appendChild(divInputGroup);

            row.appendChild(colHari);
            row.appendChild(colJamMasuk);
            row.appendChild(colJamKeluar);

            hariContainer.appendChild(row);
        }

        // Event listener tombol tambah hari
        const btnAddHari = document.getElementById('btnAddHari');
        if (btnAddHari) {
            btnAddHari.addEventListener('click', function() {
                addHariRow();
            });
        }
    });
</script>
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        toastr.options = {
            "positionClass": "toast-top-right",
            "timeOut": "3000",
            "closeButton": true,
            "progressBar": true,
        };
        toastr.success("{{ session('success') }}");
    });
</script>
@endif
@endsection
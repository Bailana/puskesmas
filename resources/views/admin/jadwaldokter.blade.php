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
                        <td>{{ $jadwal['poliklinik'] }}</td>
                        <td>{{ $jadwal['senin'] ?: '-' }}</td>
                        <td>{{ $jadwal['selasa'] ?: '-' }}</td>
                        <td>{{ $jadwal['rabu'] ?: '-' }}</td>
                        <td>{{ $jadwal['kamis'] ?: '-' }}</td>
                        <td>{{ $jadwal['jumat'] ?: '-' }}</td>
                        <td>{{ $jadwal['sabtu'] ?: '-' }}</td>
                        <td>{{ $jadwal['minggu'] ?: '-' }}</td>
                        <td>
                            {{-- Edit and delete buttons may need adjustment since $jadwal is now an array --}}
                            <button class="btn btn-sm btn-primary btn-edit" data-nama-dokter="{{ $jadwal['nama_dokter'] }}" data-poliklinik="{{ $jadwal['poliklinik'] }}">Edit</button>
                            <form action="{{ route('admin.jadwaldokter.destroy', $jadwal['ids'][0] ?? '') }}" method="POST"
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
                                    <button type="button" class="btn btn-danger btn-sm ms-2 btn-remove-hari" title="Hapus Hari">&times;</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-success btn-sm mb-3" id="btnAddHari">Tambah Hari</button>
                </div>
                <div class="modal-footer d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary" id="submitBtn">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

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
        const jadwalForm = document.getElementById('jadwalForm');
        const modalTitle = document.getElementById('addJadwalModalLabel');
        const submitBtn = document.getElementById('submitBtn');
        const jadwalIdInput = document.getElementById('jadwalId');
        const methodField = document.getElementById('methodField');

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const namaDokter = this.getAttribute('data-nama-dokter');
                const poliklinik = this.getAttribute('data-poliklinik');
                fetch(`/admin/jadwaldokter/edit-group/${encodeURIComponent(namaDokter)}/${encodeURIComponent(poliklinik)}`)
                    .then(response => response.json())
                    .then(data => {
                        modalTitle.textContent = 'Edit Jadwal Dokter';
                        submitBtn.textContent = 'Update';
                        jadwalForm.action = `/admin/jadwaldokter/update/${data[0]?.id || ''}`;
                        jadwalIdInput.value = data[0]?.id || '';
                        methodField.value = 'PUT';
                        if (data.length > 0) {
                            document.getElementById('nama_dokter').value = data[0].nama_dokter;
                            document.getElementById('poliklinik').value = data[0].poliklinik;
                        }
                        // Clear existing hari rows
                        const hariContainer = document.getElementById('hariContainer');
                        hariContainer.innerHTML = '';
                        // Populate hari rows from data
                        if (data.length > 0) {
                            data.forEach(item => {
                                addHariRow(item.hari, item.jam_masuk, item.jam_keluar);
                            });
                        } else {
                            addHariRow();
                        }
                        var addJadwalModal = new bootstrap.Modal(document.getElementById('addJadwalModal'));
                        addJadwalModal.show();
                    });
            });
        });

        // Update modal form for new fields
        // Remove this old event listener to prevent conflict with new edit button handler
        // document.querySelectorAll('.btn-edit').forEach(button => {
        //     button.addEventListener('click', function() {
        //         const id = this.getAttribute('data-id');
        //         fetch(`/admin/jadwaldokter/edit/${id}`)
        //             .then(response => response.json())
        //             .then(data => {
        //                 // Clear existing hari rows
        //                 const hariContainer = document.getElementById('hariContainer');
        //                 hariContainer.innerHTML = '';
        //                 // Populate hari rows from data
        //                 if (data.hari && data.jam_masuk && data.jam_keluar) {
        //                     for (let i = 0; i < data.hari.length; i++) {
        //                         addHariRow(data.hari[i], data.jam_masuk[i], data.jam_keluar[i]);
        //                     }
        //                 } else {
        //                     addHariRow();
        //                 }
        //             });
        //     });
        // });
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
            btnRemove.classList.add('btn', 'btn-danger', 'btn-sm', 'ms-2', 'btn-remove-hari');
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

        // Reset modal form fields ketika tombol close diklik
        const closeButton = document.querySelector('#addJadwalModal .btn-close');
        const modalTitle = document.getElementById('addJadwalModalLabel');
        const submitBtn = document.getElementById('submitBtn');
        const jadwalForm = document.getElementById('jadwalForm');
        const jadwalIdInput = document.getElementById('jadwalId');
        const methodField = document.getElementById('methodField');

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
                // Clear all dynamic hari rows kecuali satu
                const hariContainer = document.getElementById('hariContainer');
                hariContainer.innerHTML = '';
                // Tambah satu baris kosong
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

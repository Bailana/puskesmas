@extends('dashboardadmin')

@section('admin')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>Data Obat</strong></h1>

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <input type="search" id="searchInput" class="form-control w-50" placeholder="Cari Obat...">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addObatModal">
                Tambah Data Obat
            </button>
        </div>
        <div class="card-body">
            <table class="table table-striped" id="obatTable">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Obat</th>
                        <th>Jenis Obat</th>
                        <th>Dosis</th>
                        <th>Bentuk Obat</th>
                        <th>Stok</th>
                        <th>Harga Satuan</th>
                        <th>Tanggal Kadaluarsa</th>
                        <th>Nama Pabrikan</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="obatTableBody">
                    @foreach ($obats as $obat)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $obat->nama_obat }}</td>
                        <td>{{ $obat->jenis_obat }}</td>
                        <td>{{ $obat->dosis }}</td>
                        <td>{{ $obat->bentuk_obat }}</td>
                        <td>{{ $obat->stok }}</td>
                        <td>{{ number_format($obat->harga_satuan, 2, ',', '.') }}</td>
                        <td>{{ $obat->tanggal_kadaluarsa ? \Carbon\Carbon::parse($obat->tanggal_kadaluarsa)->format('d-m-Y') : '-' }}</td>
                        <td>{{ $obat->nama_pabrikan }}</td>
                        <td>{{ $obat->keterangan }}</td>
                        <td>
                            <button class="btn btn-info btn-sm detail-btn"
                                data-id="{{ $obat->id_obat }}"
                                data-nama="{{ $obat->nama_obat }}"
                                data-jenis="{{ $obat->jenis_obat }}"
                                data-dosis="{{ $obat->dosis }}"
                                data-bentuk="{{ $obat->bentuk_obat }}"
                                data-stok="{{ $obat->stok }}"
                                data-harga="{{ number_format($obat->harga_satuan, 2, ',', '.') }}"
                                data-tanggalkadaluarsa="{{ $obat->tanggal_kadaluarsa ? \Carbon\Carbon::parse($obat->tanggal_kadaluarsa)->format('d-m-Y') : '-' }}"
                                data-pabrikan="{{ $obat->nama_pabrikan }}"
                                data-keterangan="{{ $obat->keterangan }}"
                            >Detail</button>
                            <button class="btn btn-warning btn-sm edit-btn"
                                data-id="{{ $obat->id_obat }}"
                                data-nama="{{ $obat->nama_obat }}"
                                data-jenis="{{ $obat->jenis_obat }}"
                                data-dosis="{{ $obat->dosis }}"
                                data-bentuk="{{ $obat->bentuk_obat }}"
                                data-stok="{{ $obat->stok }}"
                                data-harga="{{ $obat->harga_satuan }}"
                                data-tanggalkadaluarsa="{{ $obat->tanggal_kadaluarsa ? \Carbon\Carbon::parse($obat->tanggal_kadaluarsa)->format('Y-m-d') : '' }}"
                                data-pabrikan="{{ $obat->nama_pabrikan }}"
                                data-keterangan="{{ $obat->keterangan }}"
                            >Edit</button>
                            <form method="POST" action="" class="d-inline hapus-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm hapus-btn">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Modal Detail Obat -->
<div class="modal fade" id="detailObatModal" tabindex="-1" aria-labelledby="detailObatModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailObatModalLabel">Detail Obat</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
            <tbody>
                <tr><th>Nama Obat</th><td id="detail-nama"></td></tr>
                <tr><th>Jenis Obat</th><td id="detail-jenis"></td></tr>
                <tr><th>Dosis</th><td id="detail-dosis"></td></tr>
                <tr><th>Bentuk Obat</th><td id="detail-bentuk"></td></tr>
                <tr><th>Stok</th><td id="detail-stok"></td></tr>
                <tr><th>Harga Satuan</th><td id="detail-harga"></td></tr>
                <tr><th>Tanggal Kadaluarsa</th><td id="detail-tanggalkadaluarsa"></td></tr>
                <tr><th>Nama Pabrikan</th><td id="detail-pabrikan"></td></tr>
                <tr><th>Keterangan</th><td id="detail-keterangan"></td></tr>
            </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Edit Data Obat -->
<div class="modal fade" id="editObatModal" tabindex="-1" aria-labelledby="editObatModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form id="editObatForm" method="POST" class="modal-content">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title" id="editObatModalLabel">Edit Data Obat</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <div class="mb-3">
              <label for="edit_nama_obat" class="form-label">Nama Obat</label>
              <input type="text" class="form-control" id="edit_nama_obat" name="nama_obat" required>
          </div>
          <div class="mb-3">
              <label for="edit_jenis_obat" class="form-label">Jenis Obat</label>
              <input type="text" class="form-control" id="edit_jenis_obat" name="jenis_obat" required>
          </div>
          <div class="mb-3">
              <label for="edit_dosis" class="form-label">Dosis</label>
              <input type="text" class="form-control" id="edit_dosis" name="dosis" required>
          </div>
          <div class="mb-3">
              <label for="edit_bentuk_obat" class="form-label">Bentuk Obat</label>
              <input type="text" class="form-control" id="edit_bentuk_obat" name="bentuk_obat" required>
          </div>
          <div class="mb-3">
              <label for="edit_stok" class="form-label">Stok</label>
              <input type="number" class="form-control" id="edit_stok" name="stok" required>
          </div>
          <div class="mb-3">
              <label for="edit_harga_satuan" class="form-label">Harga Satuan</label>
              <input type="number" step="0.01" class="form-control" id="edit_harga_satuan" name="harga_satuan" required>
          </div>
          <div class="mb-3">
              <label for="edit_tanggal_kadaluarsa" class="form-label">Tanggal Kadaluarsa</label>
              <input type="date" class="form-control" id="edit_tanggal_kadaluarsa" name="tanggal_kadaluarsa" required>
          </div>
          <div class="mb-3">
              <label for="edit_nama_pabrikan" class="form-label">Nama Pabrikan</label>
              <input type="text" class="form-control" id="edit_nama_pabrikan" name="nama_pabrikan" required>
          </div>
          <div class="mb-3">
              <label for="edit_keterangan" class="form-label">Keterangan</label>
              <textarea class="form-control" id="edit_keterangan" name="keterangan"></textarea>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Tambah Data Obat -->
<div class="modal fade" id="addObatModal" tabindex="-1" aria-labelledby="addObatModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form action="{{ route('admin.obat.store') }}" method="POST" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="addObatModalLabel">Tambah Data Obat</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <div class="mb-3">
              <label for="nama_obat" class="form-label">Nama Obat</label>
              <input type="text" class="form-control" id="nama_obat" name="nama_obat" required>
          </div>
          <div class="mb-3">
              <label for="jenis_obat" class="form-label">Jenis Obat</label>
              <input type="text" class="form-control" id="jenis_obat" name="jenis_obat" required>
          </div>
          <div class="mb-3">
              <label for="dosis" class="form-label">Dosis</label>
              <input type="text" class="form-control" id="dosis" name="dosis" required>
          </div>
          <div class="mb-3">
              <label for="bentuk_obat" class="form-label">Bentuk Obat</label>
              <input type="text" class="form-control" id="bentuk_obat" name="bentuk_obat" required>
          </div>
          <div class="mb-3">
              <label for="stok" class="form-label">Stok</label>
              <input type="number" class="form-control" id="stok" name="stok" required>
          </div>
          <div class="mb-3">
              <label for="harga_satuan" class="form-label">Harga Satuan</label>
              <input type="number" step="0.01" class="form-control" id="harga_satuan" name="harga_satuan" required>
          </div>
          <div class="mb-3">
              <label for="tanggal_kadaluarsa" class="form-label">Tanggal Kadaluarsa</label>
              <input type="date" class="form-control" id="tanggal_kadaluarsa" name="tanggal_kadaluarsa" required>
          </div>
          <div class="mb-3">
              <label for="nama_pabrikan" class="form-label">Nama Pabrikan</label>
              <input type="text" class="form-control" id="nama_pabrikan" name="nama_pabrikan" required>
          </div>
          <div class="mb-3">
              <label for="keterangan" class="form-label">Keterangan</label>
              <textarea class="form-control" id="keterangan" name="keterangan"></textarea>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const tableBody = document.getElementById('obatTableBody');
        const rows = tableBody.getElementsByTagName('tr');

        searchInput.addEventListener('input', function () {
            const filter = searchInput.value.toLowerCase();

            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let textContent = '';
                for (let j = 0; j < cells.length - 1; j++) { // exclude last column (Aksi)
                    textContent += cells[j].textContent.toLowerCase() + ' ';
                }
                if (textContent.indexOf(filter) > -1) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        });

        // Detail button click handler
        const detailModal = new bootstrap.Modal(document.getElementById('detailObatModal'));
        document.getElementById('obatTableBody').addEventListener('click', function(event) {
            let target = event.target;
            // If the clicked element is an icon inside the button, get the button element
            if (target.tagName === 'I' && target.parentElement.classList.contains('detail-btn')) {
                target = target.parentElement;
            }
            if (target && target.classList.contains('detail-btn')) {
                console.log('Detail button clicked');
                    document.getElementById('detail-nama').textContent = target.getAttribute('data-nama');
                document.getElementById('detail-jenis').textContent = target.getAttribute('data-jenis');
                document.getElementById('detail-dosis').textContent = target.getAttribute('data-dosis');
                document.getElementById('detail-bentuk').textContent = target.getAttribute('data-bentuk');
                document.getElementById('detail-stok').textContent = target.getAttribute('data-stok');
                document.getElementById('detail-harga').textContent = target.getAttribute('data-harga');
                document.getElementById('detail-tanggalkadaluarsa').textContent = target.getAttribute('data-tanggalkadaluarsa');
                document.getElementById('detail-pabrikan').textContent = target.getAttribute('data-pabrikan');
                document.getElementById('detail-keterangan').textContent = target.getAttribute('data-keterangan');
                detailModal.show();
            }
        });

        // Edit button click handler to populate and set form action
        const editModalElement = document.getElementById('editObatModal');
        const editModal = new bootstrap.Modal(editModalElement);
        const editForm = document.getElementById('editObatForm');
        document.getElementById('obatTableBody').addEventListener('click', function(event) {
            let target = event.target;
            if (target.tagName === 'I' && target.parentElement.classList.contains('edit-btn')) {
                target = target.parentElement;
            }
            if (target && target.classList.contains('edit-btn')) {
                console.log('Edit button clicked');
                event.preventDefault();
                document.getElementById('edit_nama_obat').value = target.getAttribute('data-nama');
                document.getElementById('edit_jenis_obat').value = target.getAttribute('data-jenis');
                document.getElementById('edit_dosis').value = target.getAttribute('data-dosis');
                document.getElementById('edit_bentuk_obat').value = target.getAttribute('data-bentuk');
                document.getElementById('edit_stok').value = target.getAttribute('data-stok');
                document.getElementById('edit_harga_satuan').value = target.getAttribute('data-harga');
                document.getElementById('edit_tanggal_kadaluarsa').value = target.getAttribute('data-tanggalkadaluarsa');
                document.getElementById('edit_nama_pabrikan').value = target.getAttribute('data-pabrikan');
                document.getElementById('edit_keterangan').value = target.getAttribute('data-keterangan');

                // Build update URL manually in JavaScript
                const baseUpdateUrl = "{{ url('admin/obat/update') }}";
                const id = target.getAttribute('data-id');
                console.log('Edit button data-id:', id);
                editForm.action = baseUpdateUrl + '/' + id;
                console.log('Edit form action URL:', editForm.action);

                editModal.show();
            }
        });

        // Add client-side validation and feedback for edit form submission
        editForm.addEventListener('submit', function(event) {
            event.preventDefault();
            // Simple validation example: check required fields
            const requiredFields = [
                'edit_nama_obat',
                'edit_jenis_obat',
                'edit_dosis',
                'edit_bentuk_obat',
                'edit_stok',
                'edit_harga_satuan',
                'edit_tanggal_kadaluarsa',
                'edit_nama_pabrikan'
            ];
            let valid = true;
            requiredFields.forEach(function(fieldId) {
                const field = document.getElementById(fieldId);
                if (!field || !field.value.trim()) {
                    valid = false;
                    if (field) field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            if (!valid) {
                alert('Mohon lengkapi semua field yang wajib diisi.');
                return;
            }
            // Submit the form via AJAX if valid
            const formData = new FormData(editForm);
            const actionUrl = editForm.action;
            fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'Accept': 'application/json',
                    'X-HTTP-Method-Override': 'PUT',
                },
                body: formData,
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Show success alert
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Data obat berhasil diperbarui.',
                    timer: 2000,
                    showConfirmButton: false,
                });
                // Update the table row with new data
                const id = data.id;
                const row = document.querySelector(`#obatTableBody tr button.edit-btn[data-id="${id}"]`).closest('tr');
                if (row) {
                    row.children[1].textContent = data.nama_obat;
                    row.children[2].textContent = data.jenis_obat;
                    row.children[3].textContent = data.dosis;
                    row.children[4].textContent = data.bentuk_obat;
                    row.children[5].textContent = data.stok;
                    row.children[6].textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(data.harga_satuan);
                    row.children[7].textContent = data.tanggal_kadaluarsa;
                    row.children[8].textContent = data.nama_pabrikan;
                    row.children[9].textContent = data.keterangan;
                    // Update data attributes on buttons
                    const editBtn = row.querySelector('button.edit-btn');
                    editBtn.setAttribute('data-nama', data.nama_obat);
                    editBtn.setAttribute('data-jenis', data.jenis_obat);
                    editBtn.setAttribute('data-dosis', data.dosis);
                    editBtn.setAttribute('data-bentuk', data.bentuk_obat);
                    editBtn.setAttribute('data-stok', data.stok);
                    editBtn.setAttribute('data-harga', data.harga_satuan);
                    editBtn.setAttribute('data-tanggalkadaluarsa', data.tanggal_kadaluarsa);
                    editBtn.setAttribute('data-pabrikan', data.nama_pabrikan);
                    editBtn.setAttribute('data-keterangan', data.keterangan);
                    const detailBtn = row.querySelector('button.detail-btn');
                    detailBtn.setAttribute('data-nama', data.nama_obat);
                    detailBtn.setAttribute('data-jenis', data.jenis_obat);
                    detailBtn.setAttribute('data-dosis', data.dosis);
                    detailBtn.setAttribute('data-bentuk', data.bentuk_obat);
                    detailBtn.setAttribute('data-stok', data.stok);
                    detailBtn.setAttribute('data-harga', new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(data.harga_satuan));
                    detailBtn.setAttribute('data-tanggalkadaluarsa', data.tanggal_kadaluarsa);
                    detailBtn.setAttribute('data-pabrikan', data.nama_pabrikan);
                    detailBtn.setAttribute('data-keterangan', data.keterangan);
                }
                editModal.hide();
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan saat memperbarui data obat.',
                });
                console.error('Error:', error);
            });
        });

        // Hapus button click handler placeholder
        document.getElementById('obatTableBody').addEventListener('click', function(event) {
            let target = event.target;
            // If the clicked element is an icon inside the button, get the button element
            if (target.tagName === 'I' && target.parentElement.classList.contains('hapus-btn')) {
                target = target.parentElement;
            }
            if (target && target.classList.contains('hapus-btn')) {
                console.log('Hapus button clicked');
                if (confirm('Apakah Anda yakin ingin menghapus data obat ini?')) {
                    // Placeholder: implement delete logic here, e.g., submit a form or AJAX request
                    alert('Fungsi hapus belum diimplementasikan.');
                }
            }
        });

        // Set action URL for delete form dynamically
        document.getElementById('obatTableBody').addEventListener('click', function(event) {
            let target = event.target;
            if (target.tagName === 'I' && target.parentElement.classList.contains('hapus-btn')) {
                target = target.parentElement;
            }
            if (target && target.classList.contains('hapus-btn')) {
                event.preventDefault();
                if (confirm('Apakah Anda yakin ingin menghapus data obat ini?')) {
                    const form = target.closest('.hapus-form');
                    const id = target.closest('tr').querySelector('.edit-btn').getAttribute('data-id');
                    const baseDeleteUrl = "{{ url('admin/obat/delete') }}";
                    form.action = baseDeleteUrl + '/' + id;
                    form.submit();
                }
            }
        });
    });
</script>
@endsection

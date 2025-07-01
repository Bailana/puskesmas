@extends('dashboardadmin')

@section('admin')
<div class="container-fluid p-0">
  <h1 class="h3 mb-3"><strong>Data User</strong></h1>
  <div class="row">
    <div class="col-12 col-lg-12 col-xxl-12 d-flex">
      <div class="card flex-fill">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div class="d-flex flex-wrap align-items-center gap-2">
            <form class="d-flex flex-wrap align-items-center gap-2 m-0 p-0" style="width: 250%;">
              <div class="input-group" style="width: 100%;">
                <input type="text" id="searchInput" name="search" class="form-control" placeholder="Pencarian..." aria-label="Search" autocomplete="off">
              </div>
            </form>
          </div>
          <button type="button" class="btn btn-success btn-lg" style="padding: 5px 10px; font-size: 0.9rem;" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-plus"></i> Tambah Data User
          </button>
        </div>

        <div class="table-responsive">
          <table class="table table-hover my-0" id="usersTable">
            <thead>
              <tr>
                <th class="nowrap">No.</th>
                <th class="nowrap">Role</th>
                <th class="nowrap">Name</th>
                <th class="nowrap">Email</th>
                <th class="nowrap">Hari/Tanggal Pembuatan</th>
                <th class="nowrap">Aksi</th>
              </tr>
            </thead>
            <tbody id="usersTableBody">
              @foreach ($users as $index => $user)
              <tr>
                <td class="nowrap">{{ $index + 1 }}</td>
                <td class="nowrap">{{ ucfirst($user->role) }}</td>
                <td class="nowrap">{{ $user->name }}</td>
                <td class="nowrap">{{ $user->email }}</td>
                <td class="nowrap">{{ \Carbon\Carbon::parse($user->created_at)->locale('id')->isoFormat('dddd, DD-MM-YYYY') }}</td>
                <td>
                  <button type="button" class="btn btn-primary btn-sm rounded" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                    Edit
                  </button>
                  <!-- Button trigger delete confirmation modal -->
                  <button type="button" class="btn btn-danger btn-sm rounded" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user->id }}">
                    Hapus
                  </button>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Modal Edit User -->
<div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ url('/admin/users/edit/' . $user->id) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editUserModalLabel{{ $user->id }}">Edit Data User</h5>
          <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="role{{ $user->id }}" class="form-label">Role</label>
            <select class="form-select" id="role{{ $user->id }}" name="role" required>
              <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
              <option value="perawat" {{ $user->role == 'perawat' ? 'selected' : '' }}>Perawat</option>
              <option value="dokter" {{ $user->role == 'dokter' ? 'selected' : '' }}>Dokter</option>
              <option value="resepsionis" {{ $user->role == 'resepsionis' ? 'selected' : '' }}>Resepsionis</option>
              <option value="kasir" {{ $user->role == 'kasir' ? 'selected' : '' }}>Kasir</option>
              <option value="apoteker" {{ $user->role == 'apoteker' ? 'selected' : '' }}>Apoteker</option>
              <option value="rawat inap" {{ $user->role == 'rawat inap' ? 'selected' : '' }}>Rawat Inap</option>
              <option value="bidan" {{ $user->role == 'bidan' ? 'selected' : '' }}>Bidan</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="name{{ $user->id }}" class="form-label">Nama</label>
            <input type="text" class="form-control" id="name{{ $user->id }}" name="name" value="{{ $user->name }}" required>
          </div>
          <div class="mb-3">
            <label for="email{{ $user->id }}" class="form-label">Email</label>
            <input type="email" class="form-control" id="email{{ $user->id }}" name="email" value="{{ $user->email }}" required>
          </div>
          <div class="mb-3">
            <label for="password{{ $user->id }}" class="form-label">Password (kosongkan jika tidak diubah)</label>
            <input type="password" class="form-control" id="password{{ $user->id }}" name="password">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>
<!-- Modal Tambah User -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="addUserForm" action="{{ url('/admin/users/create') }}" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addUserModalLabel">Tambah Data User</h5>
          <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select class="form-select" id="role" name="role" required>
              <option value="" selected disabled>Pilih Role</option>
              <option value="admin">Admin</option>
              <option value="perawat">Perawat</option>
              <option value="dokter">Dokter</option>
              <option value="resepsionis">Resepsionis</option>
              <option value="kasir">Kasir</option>
              <option value="apoteker">Apoteker</option>
              <option value="rawat inap">Rawat Inap</option>
              <option value="bidan">Bidan</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="name" class="form-label">Nama</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
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
    const tableBody = document.getElementById('usersTableBody');
    const rows = tableBody.getElementsByTagName('tr');

    searchInput.addEventListener('input', function() {
      const filter = searchInput.value.toLowerCase();

      for (let i = 0; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        let textContent = '';
        for (let j = 0; j < cells.length - 1; j++) { // exclude last column (actions)
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
@endsection
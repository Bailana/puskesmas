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
                <th class="nowrap">Nama</th>
                <th class="nowrap">Email</th>
                <th class="nowrap">Hari/Tanggal Pembuatan</th>
                <th class="nowrap">Aksi</th>
              </tr>
            </thead>
            <tbody id="usersTableBody">
              @foreach ($users as $index => $user)
              <tr>
                <td class="nowrap">{{ $users->firstItem() + $index }}.</td>
                <td class="nowrap">{{ ucfirst($user->role) }}</td>
                <td class="nowrap">{{ $user->name }}</td>
                <td class="nowrap">{{ $user->email }}</td>
                <td class="nowrap">{{ \Carbon\Carbon::parse($user->created_at)->locale('id')->isoFormat('dddd, DD-MM-YYYY') }}</td>
                <td>
                  <!-- <button type="button" class="btn btn-primary btn-sm rounded" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                    Edit
                  </button> -->
                  <button type="button" class="btn btn-primary btn-sm rounded ms-1 user-detail-btn" data-user-id="{{ $user->id }}">
                    Selengkapnya
                  </button>
                  <!-- Button trigger delete confirmation modal -->
                  <!-- <button type="button" class="btn btn-danger btn-sm rounded" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user->id }}">
                    Hapus
                  </button> -->
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="mt-3 mb-2">
          <div class="d-flex justify-content-between align-items-center w-100">
        <div class="small text-muted mb-2 text-start ps-3 pagination-info-text">
          Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of
          {{ $users->total() }} results
        </div>
        <nav class="d-flex justify-content-center">
          <ul class="pagination d-flex flex-row flex-wrap gap-2"
            style="list-style-type: none; padding-left: 0; margin-bottom: 0;">
            {{-- Previous Page Link --}}
            @if ($users->onFirstPage())
            <li class="page-item disabled" aria-disabled="true" aria-label="Previous">
              <span class="page-link" aria-hidden="true">&laquo;</span>
            </li>
            @else
            <li class="page-item">
              <a class="page-link" href="{{ $users->previousPageUrl() }}" rel="prev"
                aria-label="Previous">&laquo;</a>
            </li>
            @endif

            {{-- Pagination Elements --}}
            @php
            $totalPages = $users->lastPage();
            $currentPage = $users->currentPage();
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
              <li class="page-item"><a class="page-link" href="{{ $users->url($page) }}">{{ $page }}</a></li>
              @endif
              @endfor

              {{-- Next Page Link --}}
              @if ($users->hasMorePages())
              <li class="page-item">
                <a class="page-link" href="{{ $users->nextPageUrl() }}" rel="next"
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
        <div class="modal-header d-flex align-items-center justify-content-between">
          <h3 class="modal-title" id="addUserModalLabel"><strong>Data User</strong></h3>
          <button type="button" class="btn btn-close mb-2" data-bs-dismiss="modal" aria-label="Close"></button>
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
        <div class="modal-footer d-flex justify-content-end">
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>
<!-- User Detail Modal -->
<div class="modal fade" id="userDetailModal" tabindex="-1" aria-labelledby="userDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header d-flex justify-content-between align-items-center">
        <h3 class="modal-title" id="userDetailModalLabel"><strong>User</strong></h3>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="d-flex align-items-start gap-2">
          <div style="position: relative; width: 200px; height: 200px;">
            <img id="detailUserPhoto" src="" alt="Foto Profil" class="rounded" style="width: 200px; height: 200px; object-fit: cover;">
            <button type="button" id="editPhotoButton" title="Ubah Foto Profil" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); border: none; background: rgba(0,0,0,0.5); color: white; border-radius: 50%; width: 40px; height: 40px; display: none; align-items: center; justify-content: center; cursor: pointer;">
              <i class="fas fa-pencil-alt"></i>
            </button>
            <input type="file" id="photoInput" accept="image/*" style="display: none;" />
          </div>
          <div class="text-start flex-grow-1">
            <div class="row">
              <div class="col-md-6">
                <label for="detailUserIdInput" class="form-label">ID:</label>
                <input type="text" readonly class="form-control" id="detailUserIdInput" />
              </div>
              <div class="col-md-6">
                <label for="detailUserRoleSelect" class="form-label">Role:</label>
                <select class="form-select" id="detailUserRoleSelect" disabled>
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
            </div>
            <div class="row" style="margin-top: 0.5rem;">
              <div class="col-md-6">
                <label for="detailUserNameInput" class="form-label">Nama:</label>
                <input type="text" readonly class="form-control" id="detailUserNameInput" />
              </div>
              <div class="col-md-6">
                <label for="detailUserEmailInput" class="form-label">Email:</label>
                <input type="email" readonly class="form-control" id="detailUserEmailInput" />
              </div>
            </div>
            <div class="row" style="margin-top: 0.5rem;">
              <div class="col-md-12">
                <label for="detailUserCreatedInput" class="form-label">Tanggal Pembuatan:</label>
                <input type="text" readonly class="form-control" id="detailUserCreatedInput" />
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer d-flex justify-content-end">
        <button type="button" class="btn btn-primary rounded btn-sm me-1" id="editUserButton">Edit</button>
        <button type="button" class="btn btn-danger rounded btn-sm" id="deleteUserButton">Hapus</button>
      </div>
    </div>
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

    // New code for fetching user details dynamically
    const userDetailModal = document.getElementById('userDetailModal');
    const userDetailButtons = document.querySelectorAll('.user-detail-btn');
    const editUserButton = document.getElementById('editUserButton');
    const editPhotoButton = document.getElementById('editPhotoButton');
    const photoInput = document.getElementById('photoInput');
    const detailUserPhoto = document.getElementById('detailUserPhoto');

    userDetailButtons.forEach(button => {
      button.addEventListener('click', function() {
        const userId = this.getAttribute('data-user-id');

        fetch(`/admin/users/${userId}`)
          .then(response => response.json())
          .then(user => {
            if (user.error) {
              alert(user.error);
              return;
            }

            userDetailModal.querySelector('#detailUserIdInput').value = user.id || '';
            userDetailModal.querySelector('#detailUserRoleSelect').value = user.role || '';
            userDetailModal.querySelector('#detailUserNameInput').value = user.name || '';
            userDetailModal.querySelector('#detailUserEmailInput').value = user.email || '';
            userDetailModal.querySelector('#detailUserCreatedInput').value = new Date(user.created_at).toLocaleDateString('id-ID', {
              weekday: 'long',
              year: 'numeric',
              month: '2-digit',
              day: '2-digit'
            }) || '';

            if (user.profile_photo_url) {
              detailUserPhoto.src = user.profile_photo_url;
            } else {
              detailUserPhoto.src = "{{ url('resepsionisAssets/img/avatars/avatar.jpg') }}";
            }

            // Set all inputs to readonly initially
            const inputs = userDetailModal.querySelectorAll('input');
            inputs.forEach(input => {
              input.readOnly = true;
            });
            // Disable the role select initially
            userDetailModal.querySelector('#detailUserRoleSelect').disabled = true;

            // Set button text to Edit initially
            editUserButton.textContent = 'Edit';

            // Hide edit photo button and clear photo input
            editPhotoButton.style.display = 'none';
            photoInput.value = '';

            // Show the modal
            var modal = new bootstrap.Modal(userDetailModal);
            modal.show();
          })
          .catch(error => {
            console.error('Error fetching user data:', error);
            alert('Gagal mengambil data user.');
          });
      });
    });

    // Toggle edit/save mode on button click
    editUserButton.addEventListener('click', function() {
      const inputs = userDetailModal.querySelectorAll('input');
      const roleSelect = userDetailModal.querySelector('#detailUserRoleSelect');

      if (this.textContent === 'Edit') {
        // Make inputs editable except ID and Created date
        inputs.forEach(input => {
          if (input.id !== 'detailUserIdInput' && input.id !== 'detailUserCreatedInput') {
            input.readOnly = false;
          }
        });
        // Enable the role select
        roleSelect.disabled = false;
        // Show edit photo button
        editPhotoButton.style.display = 'flex';
        this.textContent = 'Simpan';
      } else {
        // Save updated data via AJAX PUT request with photo upload
        const userId = userDetailModal.querySelector('#detailUserIdInput').value;
        const role = roleSelect.value;
        const name = userDetailModal.querySelector('#detailUserNameInput').value;
        const email = userDetailModal.querySelector('#detailUserEmailInput').value;
        const photoFile = photoInput.files[0];

        const formData = new FormData();
        formData.append('role', role);
        formData.append('name', name);
        formData.append('email', email);
        if (photoFile) {
          formData.append('profile_photo', photoFile);
        }

        fetch(`/admin/users/edit/${userId}`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'X-HTTP-Method-Override': 'PUT',
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
          })
          .then(async response => {
            const contentType = response.headers.get('content-type');
            if (!response.ok) {
              let errorMessage = 'Gagal memperbarui data user.';
              if (contentType && contentType.indexOf('application/json') !== -1) {
                const errorData = await response.json();
                errorMessage = errorData.message || errorMessage;
              } else {
                const errorText = await response.text();
                errorMessage = errorText || errorMessage;
              }
              throw new Error(errorMessage);
            }
            if (contentType && contentType.indexOf('application/json') !== -1) {
              return response.json();
            } else {
              throw new Error('Response is not JSON');
            }
          })
          .then(data => {
            // On success, make inputs readonly and change button text
            inputs.forEach(input => {
              input.readOnly = true;
            });
            // Disable the role select
            roleSelect.disabled = true;
            // Hide edit photo button
            editPhotoButton.style.display = 'none';
            // Clear photo input
            photoInput.value = '';
            // Update photo preview if new photo uploaded
            if (data.profile_photo_url) {
              detailUserPhoto.src = data.profile_photo_url;
            }
            this.textContent = 'Edit';
            // Show toastr success message
            if (typeof toastr !== 'undefined') {
              toastr.success('Data user berhasil diperbarui.');
            } else {
              alert('Data user berhasil diperbarui.');
            }
            // Close the modal
            const modalInstance = bootstrap.Modal.getInstance(userDetailModal);
            if (modalInstance) {
              modalInstance.hide();
            }
          })
          .catch(error => {
            alert(error.message);
          });
      }
    });

    // New event listener: clicking pencil icon triggers file input click
    editPhotoButton.addEventListener('click', function() {
      photoInput.click();
    });

    // New event listener: update photo preview when a new file is selected
    photoInput.addEventListener('change', function() {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          detailUserPhoto.src = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    });

    // New code: pre-select "rawat inap" role when "Tambah Data User" button is clicked
    const addUserModal = document.getElementById('addUserModal');
    const addUserButton = document.querySelector('button[data-bs-target="#addUserModal"]');
    const addUserRoleSelect = document.getElementById('role');

    // addUserButton.addEventListener('click', function() {
    //   if (addUserRoleSelect) {
    //     addUserRoleSelect.value = 'rawat inap';
    //   }
    // });

    // New code: show toastr success notification if session has success message
    @if(session('success'))
      if (typeof toastr !== 'undefined') {
        toastr.success("{{ session('success') }}");
      } else {
        alert("{{ session('success') }}");
      }
    @endif
  });
</script>
@endsection

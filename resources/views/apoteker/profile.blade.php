@extends('dashboardApotek')

@section('apoteker')
<div class="container-fluid p-0">
    <h2 class="h3 mb-3"><strong>Profil Apoteker</strong></h2>

    <div class="card">
        <div class="card-body">
            <form id="profileForm" method="POST" action="{{ route('apoteker.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="mb-3 row">
                    <label for="name" class="col-sm-2 col-form-label">Nama</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="email" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="profile_photo" class="col-sm-2 col-form-label">Foto Profil</label>
                    <div class="col-sm-10">
                        @if(auth()->user()->profile_photo_path)
                            <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="Foto Profil" class="img-thumbnail mb-2" style="max-width: 150px;">
                        @else
                            <img src="{{ url('dokterAssets/img/avatars/avatar.jpg') }}" alt="Foto Profil" class="img-thumbnail mb-2" style="max-width: 150px;">
                        @endif
                        <input type="file" class="form-control @error('profile_photo') is-invalid @enderror" id="profile_photo" name="profile_photo" accept="image/*">
                        @error('profile_photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr>

                <h4>Ubah Password</h4>

                <div class="mb-3 row">
                    <label for="current_password" class="col-sm-2 col-form-label">Password Lama</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="new_password" class="col-sm-2 col-form-label">Password Baru</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password">
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="new_password_confirmation" class="col-sm-2 col-form-label">Konfirmasi Password Baru</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation">
                    </div>
                </div>

                <div class="mb-3 row">
                    <div class="offset-sm-2 col-sm-10">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@section('scripts')
    @if(session('status'))
    <script>
        toastr.options = {
            "positionClass": "toast-top-right",
            "timeOut": "3000",
            "closeButton": true,
            "progressBar": true
        };
        let message = "{{ session('status') }}";
        toastr.success(message);
    </script>
    @endif

    @if(session('error'))
    <script>
        toastr.options = {
            "positionClass": "toast-top-right",
            "timeOut": "3000",
            "closeButton": true,
            "progressBar": true
        };
        toastr.error("{{ session('error') }}");
    </script>
    @endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const profileForm = document.getElementById('profileForm');
        if (!profileForm) return;

        profileForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const newPassword = document.getElementById('new_password').value.trim();
            const currentPassword = document.getElementById('current_password').value.trim();

            function submitForm() {
                const formData = new FormData(profileForm);

                fetch("{{ route('apoteker.profile.update') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData,
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {throw err;});
                    }
                    return response.json();
                })
                .then(data => {
                    toastr.success(data.message);
                    document.getElementById('new_password').value = '';
                    document.getElementById('new_password_confirmation').value = '';

                    // Update navbar profile name and photo dynamically
                    if (data.name) {
                        const navbarName = document.querySelector('.nav-link.dropdown-toggle span.text-dark');
                        if (navbarName) {
                            navbarName.textContent = data.name;
                        }
                    }
                    if (data.profile_photo_path) {
                        const navbarImg = document.querySelector('.nav-link.dropdown-toggle img.avatar');
                        if (navbarImg) {
                            navbarImg.src = '/storage/' + data.profile_photo_path + '?t=' + new Date().getTime();
                        }
                        const profilePageImg = document.querySelector('img.img-thumbnail.mb-2');
                        if (profilePageImg) {
                            profilePageImg.src = '/storage/' + data.profile_photo_path + '?t=' + new Date().getTime();
                        }
                    }
                })
                .catch(error => {
                    if (error.errors) {
                        let messages = [];
                        for (const key in error.errors) {
                            messages.push(error.errors[key].join(' '));
                        }
                        toastr.error(messages.join('<br>'));
                    } else if (error.message) {
                        toastr.error(error.message);
                    } else {
                        toastr.error('Terjadi kesalahan saat menyimpan data.');
                    }
                });
            }

            if (currentPassword !== '' && newPassword !== '') {
                Swal.fire({
                    title: 'Konfirmasi Perubahan Password',
                    text: 'Apakah Anda yakin ingin mengubah password lama menjadi password baru?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, ubah password',
                    cancelButtonText: 'Tidak, batalkan',
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitForm();
                    }
                });
            } else {
                submitForm();
            }
        });
    });
</script>
@endsection

</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.getElementById('profileForm').addEventListener('submit', function(e) {
    const currentPassword = document.getElementById('current_password').value.trim();
    const newPassword = document.getElementById('new_password').value.trim();

    // Jika field password diisi, tampilkan konfirmasi
    if (currentPassword !== '' && newPassword !== '') {
        e.preventDefault(); // mencegah submit form

        Swal.fire({
            title: 'Konfirmasi Perubahan Password',
            text: 'Apakah Anda yakin ingin mengubah password lama menjadi password baru?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, ubah password',
            cancelButtonText: 'Tidak, batalkan',
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form jika disetujui
                e.target.submit();
            }
            // jika batal, tidak melakukan apa-apa
        });
    }
    // jika field password kosong, langsung submit form
});
</script>
@endsection

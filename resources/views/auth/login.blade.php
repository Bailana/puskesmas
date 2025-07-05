<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/4.5.10-0/css/ionicons.min.css">


    <link rel="stylesheet" href="{{url('template/css/style.css')}}">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    <title>Login | UPT Puskesmas Pujud</title>
    <link rel="icon" type="image/png" href="{{ url('template/images/LogoRohil.png') }}">
</head>

<body class="img js-fullheight" style="background-image: url('{{ asset('template/images/bag-pujud.jpg') }}')">
    <section class="ftco-section">
        <div class="container">
            <div class="row justify-content-center pt-4">
                <div class="col-md-6 col-lg-6 p-3 mt-3"
                    style="background-color: rgba(0, 0, 0, 0.4); border-radius: 20px">
                    <div class="login-wrap p-0">
                        <div class="row justify-content-center align-items-center">
                            <div class="col-6 text-center">
                                <img src="{{ url('template/images/LogoRohil.png') }}" alt=""
                                    style="max-width: 30%; height: auto;">
                            </div>
                        </div>
                        <h3 class="mb-4 text-center">Masukkan Akun Anda!</h3>
                        {{-- Removed inline error messages to use toastr instead --}}
                        {{-- @if ($errors->has('loginError'))
                        <div class="alert alert-danger custom-error-message">
                            {{ $errors->first('loginError') }}
                        </div>
                        @endif
                        @if ($errors->any() && !$errors->has('loginError'))
                        <div class="alert alert-danger custom-error-message">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif --}}
                        <form action="{{ route('login') }}" method="POST" class="signin-form" autocomplete="off"
                            novalidate>
                            @csrf
                            <div id="login-inputs">
                                <div class="form-group">
                                    <input type="text" name="email" class="form-control" placeholder="Email" required
                                        autocomplete="off" value="" autocorrect="off" autocapitalize="off"
                                        spellcheck="false">
                                </div>
                                <div class="form-group">
                                    <input id="password-field" type="password" name="password" class="form-control"
                                        placeholder="Password" required autocomplete="new-password" value=""
                                        autocorrect="off" autocapitalize="off" spellcheck="false">
                                    <span toggle="#password-field"
                                        class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="form-control btn btn-primary submit px-3">Masuk</button>
                            </div>
                            <div class="form-group d-md-flex">
                                <div class="w-50">
                                    <label class="checkbox-wrap checkbox-primary"
                                        style="display: flex; align-items: center;">
                                        <input type="checkbox" id="remember-checkbox" name="remember"
                                            {{ old('remember') ? 'checked' : '' }}>
                                        <span style="margin-left: 8px;">Ingat Selalu</span>
                                    </label>
                                </div>
                                <div class="w-50 text-md-right">
                                    <a href="{{ route('password.request') }}" style="color: #fff">Lupa Password</a>
                                </div>
                            </div>
                        </form>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const checkbox = document.getElementById('remember-checkbox');
                                const emailInput = document.querySelector('input[name="email"]');
                                const passwordInput = document.querySelector('input[name="password"]');

                                // Load saved values if remember is checked
                                if (localStorage.getItem('remember') === 'true') {
                                    checkbox.checked = true;
                                    emailInput.value = localStorage.getItem('email') || '';
                                    passwordInput.value = localStorage.getItem('password') || '';
                                } else {
                                    checkbox.checked = false;
                                    emailInput.value = '';
                                    passwordInput.value = '';
                                }

                                // Save or clear inputs based on checkbox state
                                function updateStorage() {
                                    if (checkbox.checked) {
                                        localStorage.setItem('remember', 'true');
                                        localStorage.setItem('email', emailInput.value);
                                        localStorage.setItem('password', passwordInput.value);
                                    } else {
                                        localStorage.setItem('remember', 'false');
                                        localStorage.removeItem('email');
                                        localStorage.removeItem('password');
                                    }
                                }

                                // Update storage on checkbox change
                                checkbox.addEventListener('change', function() {
                                    if (!checkbox.checked) {
                                        emailInput.value = '';
                                        passwordInput.value = '';
                                    }
                                    updateStorage();
                                });

                                // Update storage on input change
                                emailInput.addEventListener('input', function() {
                                    if (checkbox.checked) {
                                        localStorage.setItem('email', emailInput.value);
                                    }
                                });
                                passwordInput.addEventListener('input', function() {
                                    if (checkbox.checked) {
                                        localStorage.setItem('password', passwordInput.value);
                                    }
                                });
                            });
                        </script>
                        <p class="w-100 text-center">&mdash; Atau Masuk Menggunakan &mdash;</p>
                        <div class="social d-flex text-center">
                            <a href="{{ route('auth.google') }}" class="px-2 py-2 mr-md-1"
                                style="color: #db4437; border-radius:20px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="3.05em" height="1em"
                                    viewBox="0 0 512 168">
                                    <path fill="#ff302f"
                                        d="m496.052 102.672l14.204 9.469c-4.61 6.79-15.636 18.44-34.699 18.44c-23.672 0-41.301-18.315-41.301-41.614c0-24.793 17.816-41.613 39.308-41.613c21.616 0 32.206 17.193 35.633 26.475l1.869 4.735l-55.692 23.049c4.236 8.348 10.84 12.584 20.183 12.584c9.345 0 15.823-4.61 20.495-11.525M452.384 87.66l37.19-15.45c-2.056-5.17-8.16-8.845-15.45-8.845c-9.281 0-22.176 8.223-21.74 24.295" />
                                    <path fill="#20b15a" d="M407.407 4.931h17.94v121.85h-17.94z" />
                                    <path fill="#3686f7"
                                        d="M379.125 50.593h17.318V124.6c0 30.711-18.128 43.357-39.558 43.357c-20.183 0-32.33-13.58-36.878-24.606l15.885-6.604c2.865 6.79 9.78 14.827 20.993 14.827c13.767 0 22.24-8.535 22.24-24.482v-5.98h-.623c-4.112 4.983-11.961 9.468-21.928 9.468c-20.807 0-39.87-18.128-39.87-41.488c0-23.486 19.063-41.8 39.87-41.8c9.905 0 17.816 4.423 21.928 9.282h.623zm1.245 38.499c0-14.702-9.78-25.417-22.239-25.417c-12.584 0-23.174 10.715-23.174 25.417c0 14.514 10.59 25.042 23.174 25.042c12.46.063 22.24-10.528 22.24-25.042" />
                                    <path fill="#ff302f"
                                        d="M218.216 88.78c0 23.984-18.688 41.613-41.613 41.613c-22.924 0-41.613-17.691-41.613-41.613c0-24.108 18.689-41.675 41.613-41.675c22.925 0 41.613 17.567 41.613 41.675m-18.19 0c0-14.95-10.84-25.23-23.423-25.23S153.18 73.83 153.18 88.78c0 14.826 10.84 25.23 23.423 25.23c12.584 0 23.423-10.404 23.423-25.23" />
                                    <path fill="#ffba40"
                                        d="M309.105 88.967c0 23.984-18.689 41.613-41.613 41.613c-22.925 0-41.613-17.63-41.613-41.613c0-24.108 18.688-41.613 41.613-41.613c22.924 0 41.613 17.443 41.613 41.613m-18.253 0c0-14.95-10.839-25.23-23.423-25.23s-23.423 10.28-23.423 25.23c0 14.826 10.84 25.23 23.423 25.23c12.646 0 23.423-10.466 23.423-25.23" />
                                    <path fill="#3686f7"
                                        d="M66.59 112.328c-26.102 0-46.534-21.056-46.534-47.158c0-26.101 20.432-47.157 46.534-47.157c14.079 0 24.357 5.544 31.957 12.646l12.522-12.521C100.479 7.984 86.338.258 66.59.258C30.833.259.744 29.414.744 65.17s30.089 64.912 65.846 64.912c19.312 0 33.889-6.354 45.289-18.19c11.711-11.712 15.324-28.158 15.324-41.489c0-4.174-.498-8.472-1.059-11.649H66.59v17.318h42.423c-1.246 10.84-4.672 18.253-9.718 23.298c-6.105 6.168-15.76 12.958-32.705 12.958" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <script src="{{url('template/js/jquery.min.js')}}"></script>
    <script src="{{url('template/js/popper.js')}}"></script>
    <script src="{{url('template/js/bootstrap.min.js')}}"></script>
    <script src="{{url('template/js/main.js')}}"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    @if(session('status'))
    <script>
        toastr.options = {
            "positionClass": "toast-top-right",
            "timeOut": "3000",
            "closeButton": true,
            "progressBar": true
        };
        toastr.success("{{ session('status') }}");
    </script>
    @endif

    @if($errors->any())
    <script>
        toastr.options = {
            "positionClass": "toast-top-right",
            "timeOut": "5000",
            "closeButton": true,
            "progressBar": true
        };
        toastr.error("{{ $errors->all()[0] }}");
    </script>
    @endif

</body>
</html>
</create_file>
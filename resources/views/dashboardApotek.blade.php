<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">
    <meta name="author" content="AdminKit">
    <meta name="keywords"
        content="adminkit, bootstrap, bootstrap 5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <!-- <link rel="shortcut icon" href="{{url('dokterAssets/img/icons/icon-48x48.png')}}" /> -->

    <link rel="canonical" href="https://demo-basic.adminkit.io/" />
    <title>Farmasi | UPT Puskesmas Pujud</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ url('template/images/LogoRohil.png') }}">

    <link href="{{url('dokterAssets/css/app.css')}}" rel="stylesheet">
    <link href="{{url('dokterAssets/css/custom-pagination.css')}}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="{{ url('resources/css/custom-navbar.css') }}" rel="stylesheet">
    <!-- SweetAlert JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

</head>

<body>
    <div class="wrapper">
        <nav id="sidebar" class="sidebar js-sidebar">
            <div class="sidebar-content js-simplebar">
                <div class="sidebar-brand d-flex align-items-center" href="{{ url('/resepsionis') }}">
                    <img src="{{ url('template/images/LogoRohil.png') }}" alt="Logo"
                        style="width: 50px; height: 50px; margin-right: 10px;">
                    <span class="align-middle">UPT PUSKESMAS PUJUD</span>
                </div>
                <ul class="sidebar-nav">
                    <li class="sidebar-item {{ Request::is('apoteker') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ url('/apoteker') }}">
                            <i class="align-middle" data-feather="sliders"></i>
                            <span class="align-middle">Dashboard</span>
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('apoteker/pasien') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ url('/apoteker/pasien') }}">
                            <i class="align-middle" data-feather="user"></i>
                            <span class="align-middle">Data Pasien</span>
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('apoteker/obat') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ url('/apoteker/obat') }}">
                            <i class="align-middle" data-feather="package"></i>
                            <span class="align-middle">Data Obat</span>
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('apoteker/antrian') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ url('/apoteker/antrian') }}">
                            <i class="align-middle" data-feather="users"></i>
                            <span class="align-middle">Antrian</span>
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('apoteker/jadwal') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ url('apoteker/jadwal') }}">
                            <i class="align-middle" data-feather="clipboard"></i>
                            <span class="align-middle">Jadwal Dokter</span>
                        </a>
                    </li>
                </ul>

            </div>
        </nav>

        <div class="main">
            <nav class="navbar navbar-expand navbar-light navbar-bg">
                <a class="sidebar-toggle js-sidebar-toggle">
                    <i class="hamburger align-self-center"></i>
                </a>

                <div class="navbar-collapse collapse">
                    <ul class="navbar-nav navbar-align live-time text-dark fw-bold ms-auto me-0">
                        <!-- Element untuk menampilkan live time -->
                        <li class="nav-item">
                            <div id="live-time" class="text-dark fw-bold"></div>
                        </li>
                    </ul>


                    <ul class="navbar-nav navbar-align">
                        <li class="nav-item dropdown">
                            <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#"
                                data-bs-toggle="dropdown">
                                <i class="align-middle" data-feather="settings"></i>
                            </a>

                            <button class="nav-link dropdown-toggle d-none d-sm-inline-block" type="button" aria-expanded="false" data-bs-toggle="dropdown" role="button" style="background: none; border: none; padding: 0;">
                                @if(Auth::user()->profile_photo_path)
                                <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}"
                                    class="avatar img-fluid rounded me-1" alt="{{ Auth::user()->name }}"
                                    style="width: 40px; height: 40px; object-fit: cover;" />
                                @else
                                <img src="{{ url('dokterAssets/img/avatars/avatar.jpg') }}"
                                    class="avatar img-fluid rounded me-1" alt="{{ Auth::user()->name }}"
                                    style="width: 40px; height: 40px; object-fit: cover;" />
                                @endif
                                <span class="text-dark">{{ Auth::user()->name }}</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{ route('profileapoteker') }}">
                                    <i class="align-middle me-1" data-feather="user"></i> Profile
                                </a>
                                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                    @csrf
                                    <button type="button" class="dropdown-item" onclick="logoutConfirmation()">
                                        <i class="align-middle me-1" data-feather="log-out"></i> Log out
                                    </button>
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="content">
                @yield('apoteker')
            </main>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row text-muted">
                        <div class="col-6 text-start">
                            <p class="mb-0">
                                <a class="text-muted"><strong>R.B-Dev</strong></a>&copy;
                            </p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{url('dokterAssets/js/app.js')}}"></script>

    <!-- Tambahkan Bootstrap JS setelah jQuery -->
    <!-- Removed older Bootstrap 5.1.3 to avoid duplication -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Doughnut chart
            new Chart(document.getElementById("chartjs-doughnut"), {
                type: "doughnut",
                data: {
                    labels: ["Umum", "Gigi", "KIA", "Lansia", "KB", "Anak", "Physiotheraphy"],
                    datasets: [{
                        data: [260, 125, 54, 146, 23, 234, 123],
                        backgroundColor: [
                            window.theme.primary,
                            window.theme.success,
                            window.theme.warning,
                            window.theme.danger,
                            window.theme.info,
                            window.theme.secondary,
                            "#8E3E63"
                        ],
                        borderColor: "transparent"
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    cutoutPercentage: 65,
                    legend: {
                        display: false
                    }
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            // Line chart
            new Chart(document.getElementById("chartjs-line"), {
                type: "line",
                data: {
                    labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct",
                        "Nov", "Dec"
                    ],
                    datasets: [{
                        label: "Sales ($)",
                        fill: true,
                        backgroundColor: "transparent",
                        borderColor: window.theme.primary,
                        data: [2115, 1562, 1584, 1892, 1487, 2223, 2966, 2448, 2905, 3838, 2917,
                            3327
                        ]
                    }, {
                        label: "Orders",
                        fill: true,
                        backgroundColor: "transparent",
                        borderColor: "#adb5bd",
                        borderDash: [4, 4],
                        data: [958, 724, 629, 883, 915, 1214, 1476, 1212, 1554, 2128, 1466,
                            1827
                        ]
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    tooltips: {
                        intersect: false
                    },
                    hover: {
                        intersect: true
                    },
                    plugins: {
                        filler: {
                            propagate: false
                        }
                    },
                    scales: {
                        xAxes: [{
                            reverse: true,
                            gridLines: {
                                color: "rgba(0,0,0,0.05)"
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                stepSize: 500
                            },
                            display: true,
                            borderDash: [5, 5],
                            gridLines: {
                                color: "rgba(0,0,0,0)",
                                fontColor: "#fff"
                            }
                        }]
                    }
                }
            });
        });
    </script>

    <script>
        function logoutConfirmation() {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan keluar dari sistem!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, keluar!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika pengguna mengkonfirmasi logout, submit form logout
                    document.getElementById('logout-form').submit();
                }
            })
        }

        function updateTime() {
            const timeElement = document.getElementById('live-time');
            const now = new Date();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const seconds = now.getSeconds().toString().padStart(2, '0');

            timeElement.textContent = `${hours}:${minutes}:${seconds}`;
        }

        setInterval(updateTime, 1000); // Perbarui setiap detik
        updateTime(); // Panggil sekali saat halaman dimuat
    </script>
    @yield('scripts')
</body>

</html>
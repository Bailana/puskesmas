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
    <title>Resepsionis | UPT Puskesmas Pujud</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ url('template/images/logo_puskesmas.png') }}">

    <link href="{{url('dokterAssets/css/app.css')}}" rel="stylesheet">
    <link href="{{url('dokterAssets/css/custom-pagination.css')}}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- SweetAlert JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">

    <!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script> -->
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">


</head>

<body>
    <div class="wrapper">
        <nav id="sidebar" class="sidebar js-sidebar">
            <div class="sidebar-content js-simplebar">
                <div class="sidebar-brand d-flex align-items-center" href="{{ url('/resepsionis') }}">
                    <img src="{{ url('template/images/logo_puskesmas.png') }}" alt="Logo"
                        style="width: 50px; height: 50px; margin-right: 10px;">
                    <span class="align-middle">UPT PUSKESMAS PUJUD</span>
                </div>
                <ul class="sidebar-nav">
                    <li class="sidebar-item {{ Request::is('resepsionis') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ url('/resepsionis') }}">
                            <i class="align-middle" data-feather="sliders"></i>
                            <span class="align-middle">Dashboard</span>
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('pasienResepsionis') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ url('/pasienResepsionis') }}">
                            <i class="align-middle" data-feather="user"></i>
                            <span class="align-middle">Data Pasien</span>
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('antrian') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ url('/antrian') }}">
                            <i class="align-middle" data-feather="users"></i>
                            <span class="align-middle">Antrian</span>
                        </a>
                    </li>

                    <li class="sidebar-item {{ Request::is('jadwal') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ url('/jadwal') }}">
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
                            <a class="nav-icon dropdown-toggle" href="#" id="alertsDropdown" data-bs-toggle="dropdown"
                                role="button">
                                <div class="position-relative">
                                    <i class="align-middle" data-feather="bell"></i>
                                    <span class="indicator">4</span>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end py-0"
                                aria-labelledby="alertsDropdown">
                                <div class="dropdown-menu-header">
                                    4 New Notifications
                                </div>
                                <div class="list-group">
                                    <a href="#" class="list-group-item">
                                        <div class="row g-0 align-items-center">
                                            <div class="col-2">
                                                <i class="text-danger" data-feather="alert-circle"></i>
                                            </div>
                                            <div class="col-10">
                                                <div class="text-dark">Update completed</div>
                                                <div class="text-muted small mt-1">Restart server 12 to complete the
                                                    update.</div>
                                                <div class="text-muted small mt-1">30m ago</div>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#" class="list-group-item">
                                        <div class="row g-0 align-items-center">
                                            <div class="col-2">
                                                <i class="text-warning" data-feather="bell"></i>
                                            </div>
                                            <div class="col-10">
                                                <div class="text-dark">Lorem ipsum</div>
                                                <div class="text-muted small mt-1">Aliquam ex eros, imperdiet vulputate
                                                    hendrerit et.</div>
                                                <div class="text-muted small mt-1">2h ago</div>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#" class="list-group-item">
                                        <div class="row g-0 align-items-center">
                                            <div class="col-2">
                                                <i class="text-primary" data-feather="home"></i>
                                            </div>
                                            <div class="col-10">
                                                <div class="text-dark">Login from 192.186.1.8</div>
                                                <div class="text-muted small mt-1">5h ago</div>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#" class="list-group-item">
                                        <div class="row g-0 align-items-center">
                                            <div class="col-2">
                                                <i class="text-success" data-feather="user-plus"></i>
                                            </div>
                                            <div class="col-10">
                                                <div class="text-dark">New connection</div>
                                                <div class="text-muted small mt-1">Christina accepted your request.
                                                </div>
                                                <div class="text-muted small mt-1">14h ago</div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="dropdown-menu-footer">
                                    <a href="#" class="text-muted">Show all notifications</a>
                                </div>
                            </div>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#"
                                data-bs-toggle="dropdown" role="button">
                                <i class="align-middle" data-feather="settings"></i>
                            </a>

                            <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#"
                                data-bs-toggle="dropdown" role="button">
                            @if(auth()->user()->profile_photo_path)
                                <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}"
                                    class="avatar img-fluid rounded me-1" alt="{{ Auth::user()->name }}" style="width: 40px; height: 40px; object-fit: cover;" />
                            @else
                                <img src="{{ url('resepsionisAssets/img/avatars/avatar.jpg') }}"
                                    class="avatar img-fluid rounded me-1" alt="{{ Auth::user()->name }}" style="width: 40px; height: 40px; object-fit: cover;" />
                            @endif
                                <span class="text-dark">{{ Auth::user()->name }}</span>

                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{ url('/resepsionis/profile') }}">
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
                @yield('resepsionis')
            </main>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row text-muted">
                        <div class="col-6 text-start">
                            <p class="mb-0">
                                <a class="text-muted" href="https://adminkit.io/"
                                    target="_blank"><strong>AdminKit</strong></a> - <a class="text-muted"
                                    href="https://adminkit.io/" target="_blank"><strong>Bootstrap Admin
                                        Template</strong></a> &copy;
                            </p>
                        </div>
                        <div class="col-6 text-end">
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <a class="text-muted" href="https://adminkit.io/" target="_blank">Support</a>
                                </li>
                                <li class="list-inline-item">
                                    <a class="text-muted" href="https://adminkit.io/" target="_blank">Help Center</a>
                                </li>
                                <li class="list-inline-item">
                                    <a class="text-muted" href="https://adminkit.io/" target="_blank">Privacy</a>
                                </li>
                                <li class="list-inline-item">
                                    <a class="text-muted" href="https://adminkit.io/" target="_blank">Terms</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Add jQuery back as many scripts depend on it -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{url('dokterAssets/js/app.js')}}"></script>
    <!-- Tambahkan Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    @yield('scripts')

    @if(session('status'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            toastr.options = {
                "positionClass": "toast-top-right",
                "timeOut": "3000",
                "closeButton": true,
                "progressBar": true
            };
            toastr.success("{{ session('status') }}");
        });
    </script>
    @endif
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize all dropdowns
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });
        });

    </script>

    <script>
        $(document).ready(function () {
            // Fungsi Pencarian
            $("#searchInput").on("keyup", function () {
                var value = $(this).val().toLowerCase(); // Ambil input pencarian
                /*
                $("#antrianTable tbody tr").filter(function () {
                    $(this).toggle(
                        $(this).text().toLowerCase().indexOf(value) > -1
                    );
                });
                */
                $("#dataPasienTabel tbody tr").filter(function () {
                    $(this).toggle(
                        $(this).text().toLowerCase().indexOf(value) > -1
                    );
                });
            });
        });

    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
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

    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
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

    <script>
        function logoutConfirmation() {
            Swal.fire({
                title: 'Yakin ingin keluar?',
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

    </script>

    
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

    </script>


</body>

</html>

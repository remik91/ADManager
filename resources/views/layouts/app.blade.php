<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @yield('css')

</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand logo ps-3" href="{{ url('/') }}"> AD<span>MANAGER</span></a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i
                class="fas fa-bars"></i></button>
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto me-0 me-md-3 my-2 my-md-0">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="{{ route('admin.profil') }}">Mon Profil</a></li>
                    <li><a class="dropdown-item" href="#!">Historique</a></li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li><a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                      document.getElementById('logout-form').submit();">
                            {{ __('Déconnexion') }} </a></li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Annuaire</div>
                        <a class="nav-link" href="/">
                            <div class="sb-nav-link-icon"><i class="fas fa-fw fa-house"></i></div>
                            Accueil
                        </a>
                        <a class="nav-link" href="{{ route('user.index') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-fw fa-building-user"></i></div>
                            Utilisateurs
                        </a>
                        <a class="nav-link" href="{{ route('computer.index') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-fw fa-computer"></i></div>
                            Ordinateurs
                        </a>
                        <a class="nav-link" href="{{ route('group.index') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-fw fa-users"></i></div>
                            Groupes
                        </a>

                        <div class="sb-sidenav-menu-heading">Création</div>
                        <a class="nav-link" href="{{ route('user.import') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-user-plus"></i></div> Création d'utilisateur
                        </a>


                        <div class="sb-sidenav-menu-heading">Serveur de fichier</div>
                        <a class="nav-link" href="{{ route('service.index') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-users-between-lines"></i></div>
                            Services (GG)
                        </a>
                        <a class="nav-link" href="{{ route('partage.index') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-folder-tree"></i></div>
                            Partages (GL)
                        </a>
                        <a class="nav-link" href="{{ route('group.procedure') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-book me-1"></i></div>
                            Procédure (AGDLP)
                        </a>

                        <div class="sb-sidenav-menu-heading">Ressources</div>
                        <a class="nav-link" href="{{ route('outil') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                            Outils
                        </a>

                        <div class="sb-sidenav-menu-heading">Administration</div>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                            data-bs-target="#collapseAdmin" aria-expanded="false" aria-controls="collapseAdmin">
                            <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                            Administration
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseAdmin" aria-labelledby="headingOne"
                            data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="{{ route('admin.index') }}">Administrateur</a>
                            </nav>
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="{{ route('admin.history') }}">Historique</a>
                            </nav>
                        </div>

                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="dropdown">
                        <a href="{{ route('admin.profil') }}"
                            class="d-flex align-items-center text-white text-decoration-none">
                            <img src="{{ asset('images/avatars/avatar_' . Auth::user()->avatar . '.png') }}"
                                alt="" width="32" height="32" class="rounded-circle me-2">
                            <strong>{{ Auth::user()->name }}</strong>
                        </a>
                    </div>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">

                    <div class="row">
                        <div class="col-xl-12">
                            <div class="breadcrumb-holder ">
                                <h1 class="main-title float-start"><i class="@yield('icon')"></i> @yield('h1')
                                </h1>
                                <ol class="breadcrumb float-end hidden-mobile">
                                    <li class="breadcrumb-item">Accueil</li>
                                    <li class="breadcrumb-item active" style="display: block;"><i
                                            class="@yield('icon')"></i> @yield('h1')</li>
                                </ol>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>

                    @yield('content')

                </div>
            </main>
            <footer class="py-3 bg-light mt-auto">
                <div class="container-fluid px-3">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; ADMANAGER 2023</div>
                        <div>
                            <a href="#">Rémi Koutchinski</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    @yield('scriptjs')

    <script type="module">
        @if (Session::has('message'))
            toastr.options = {
                "closeButton": true,
                "positionClass": "toast-bottom-right",
                "progressBar": true
            }
            toastr.success("{{ session('message') }}");
        @endif

        @if (Session::has('error'))
            toastr.options = {
                "closeButton": true,
                "positionClass": "toast-bottom-right",
                "progressBar": true
            }
            toastr.error("{{ session('error') }}");
        @endif

        @if (Session::has('info'))
            toastr.options = {
                "closeButton": true,
                "positionClass": "toast-bottom-right",
                "progressBar": true
            }
            toastr.info("{{ session('info') }}");
        @endif

        @if (Session::has('warning'))
            toastr.options = {
                "closeButton": true,
                "positionClass": "toast-bottom-right",
                "progressBar": true
            }
            toastr.warning("{{ session('warning') }}");
        @endif

        $(document).ready(function() {
            // Activer le lien actif sur la page courante
            $(".nav-link").each(function() {
                if (this.href === window.location.href) {
                    $(this).addClass("active");
                    $(this).closest(".collapse").addClass("show");
                    $(this).closest(".collapse").prev(".nav-link").addClass("active");
                }
            });
        });
    </script>

</body>

</html>

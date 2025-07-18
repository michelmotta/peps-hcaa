<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon icon-->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('icons/logo-icon.png') }}">

    <!-- Libs CSS -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css"
        integrity="sha512-dPXYcDub/aeb08c63jRq/k6GaKccl256JQy/AnOq7CAnEZ9FzSL9wSbcZkMp4R26vBsMLFYH4kQ67/bbV8XaCQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/7.4.47/css/materialdesignicons.min.css"
        integrity="sha512-/k658G6UsCvbkGRB3vPXpsPHgWeduJwiWGPCGS14IQw3xpr63AEMdA8nMYG2gmYkXitQxDTn6iiK/2fD4T87qA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simplebar/6.3.1/simplebar.min.css"
        integrity="sha512-rptDreZF629VL73El0GaBEH9tlYEKDJFUr+ysb+9whgSGbwYfGGA61dVtQFL0qC8/SZv/EQFW5JtwEFf+8zKYg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Theme CSS -->
    @vite(['resources/css/theme.min.css', 'resources/sass/dashboard.scss'])

    <title>HCAA | PEPS - Dashboard</title>
</head>

<body>
    <main id="main-wrapper" class="main-wrapper">
        <div class="header">
            <!-- navbar -->
            <div class="navbar-custom navbar navbar-expand-lg">
                <div class="container-fluid px-0">
                    <a class="navbar-brand d-block d-md-none" href="{{ route('dashboard.index') }}">
                        <img src="{{ asset('images/logo.png') }}" alt="Image" />
                    </a>
                    <a id="nav-toggle" href="#!" class="ms-auto ms-md-0 me-0 me-lg-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor"
                            class="bi bi-text-indent-left text-muted" viewBox="0 0 16 16">
                            <path
                                d="M2 3.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm.646 2.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1 0 .708l-2 2a.5.5 0 0 1-.708-.708L4.293 8 2.646 6.354a.5.5 0 0 1 0-.708zM7 6.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm-5 3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z" />
                        </svg>
                    </a>
                    <h1 class="fs-4 mt-2">PEPS - Programa de Educação Permanente em Saúde</h1>
                    <ul
                        class="navbar-nav navbar-right-wrap ms-lg-auto d-flex nav-top-wrap align-items-center ms-4 ms-lg-0">
                        <!-- List -->
                        <li class="dropdown ms-2">
                            <a class="rounded-circle" href="#!" role="button" id="dropdownUser"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <div class="avatar avatar-md avatar-indicators avatar-online">
                                    <img alt="avatar" src="{{ asset('storage/' . Auth::user()->file->path) }}"
                                        class="rounded-circle" />
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownUser">
                                <div class="px-4 pb-0 pt-2">
                                    <div class="lh-1">
                                        <h5 class="mb-1">{{ Auth::user()->name }}</h5>
                                        <span class="text-inherit fs-6">{{ Auth::user()->email }}</span>
                                    </div>
                                    <div class="dropdown-divider mt-3 mb-2"></div>
                                </div>
                                <ul class="list-unstyled">
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center"
                                            href="{{ route('dashboard.users.edit', Auth::id()) }}">
                                            <i class="me-2 icon-xxs dropdown-item-icon" data-feather="user"></i>
                                            Perfil
                                        </a>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('web.logout-post') }}">
                                            @csrf
                                            <button class="dropdown-item text-danger" type="submit">
                                                <i class="me-2 icon-xxs dropdown-item-icon" data-feather="power"></i>
                                                Sair
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- navbar vertical -->
        <div class="app-menu"><!-- Sidebar -->
            <div class="navbar-vertical navbar nav-dashboard">
                <div class="h-100 ps-3 pe-3" data-simplebar>
                    <!-- Brand logo -->
                    <a class="navbar-brand text-center" href="{{ route('dashboard.index') }}">
                        <img src="{{ asset('images/logo.png') }}"
                            alt="Logo" />
                    </a>
                    <!-- Navbar nav -->
                    <ul class="navbar-nav flex-column" id="sideNavbar">
                        <!-- Nav item -->
                        <li class="nav-item">
                            <div class="navbar-heading">Menu</div>
                        </li>
                        <!-- Nav item -->
                        <li class="nav-item">
                            <a class="nav-link has-arrow {{ request()->routeIs('dashboard.index') ? 'menu-active' : '' }}"
                                href="{{ route('dashboard.index') }}">
                                <i data-feather="layout" class="nav-icon me-3 icon-xs"></i>
                                Dashboard
                            </a>
                        </li>
                        <!-- Nav item -->
                        <li class="nav-item">
                            <a class="nav-link has-arrow " href="{{ route('web.index') }}">
                                <i data-feather="arrow-left" class="nav-icon me-3 icon-xs"></i>
                                Plataforma
                            </a>
                        </li>
                        <!-- Nav item -->
                        <li class="nav-item">
                            <a class="nav-link has-arrow {{ !request()->routeIs('dashboard.lessons.*') ? 'collapsed' : '' }}"
                                href="#!" data-bs-toggle="collapse" data-bs-target="#lesson"
                                aria-expanded="false" aria-controls="specialty">
                                <i data-feather="layers" class="nav-icon me-3 icon-xs"></i>
                                Aulas
                            </a>
                            <div id="lesson"
                                class="collapse {{ request()->routeIs('dashboard.lessons.*') ? 'show' : '' }}"
                                data-bs-parent="#sideNavbar">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link has-arrow {{ request()->routeIs('dashboard.lessons.*') && !request()->routeIs('dashboard.lessons.create') ? 'menu-active' : '' }}"
                                            href="{{ route('dashboard.lessons.index') }}">
                                            <i data-feather="corner-down-right" class="nav-icon me-2 icon-xs"></i>
                                            Gerenciar
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link has-arrow {{ request()->routeIs('dashboard.lessons.create') ? 'menu-active' : '' }}"
                                            href="{{ route('dashboard.lessons.create') }}">
                                            <i data-feather="corner-down-right" class="nav-icon me-2 icon-xs"></i>
                                            Nova
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <!-- Nav item -->
                        <li class="nav-item">
                            <a class="nav-link has-arrow {{ !request()->routeIs('dashboard.suggestions.*') ? 'collapsed' : '' }}"
                                href="#!" data-bs-toggle="collapse" data-bs-target="#suggestion"
                                aria-expanded="false" aria-controls="specialty">
                                <i data-feather="message-circle" class="nav-icon me-3 icon-xs"></i>
                                Sugestões
                            </a>
                            <div id="suggestion"
                                class="collapse {{ request()->routeIs('dashboard.suggestions.*') ? 'show' : '' }}"
                                data-bs-parent="#sideNavbar">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link has-arrow {{ request()->routeIs('dashboard.suggestions.*') && !request()->routeIs('dashboard.suggestions.create') ? 'menu-active' : '' }}"
                                            href="{{ route('dashboard.suggestions.index') }}">
                                            <i data-feather="corner-down-right" class="nav-icon me-2 icon-xs"></i>
                                            Gerenciar
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link has-arrow {{ request()->routeIs('dashboard.suggestions.create') ? 'menu-active' : '' }}"
                                            href="{{ route('dashboard.suggestions.create') }}">
                                            <i data-feather="corner-down-right" class="nav-icon me-2 icon-xs"></i>
                                            Novo
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        @can('isCoordenador')
                            <!-- Nav item -->
                            <li class="nav-item">
                                <a class="nav-link has-arrow {{ !request()->routeIs('dashboard.reports.*') ? 'collapsed' : '' }}"
                                    href="#!" data-bs-toggle="collapse" data-bs-target="#reports"
                                    aria-expanded="false" aria-controls="reports">
                                    <i data-feather="pie-chart" class="nav-icon me-3 icon-xs"></i>
                                    Relatórios
                                </a>
                                <div id="reports"
                                    class="collapse {{ request()->routeIs('dashboard.reports.*') ? 'show' : '' }}"
                                    data-bs-parent="#sideNavbar">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link has-arrow {{ request()->routeIs('dashboard.reports.students') ? 'menu-active' : '' }}"
                                                href="{{ route('dashboard.reports.students') }}">
                                                <i data-feather="corner-down-right" class="nav-icon me-2 icon-xs"></i>
                                                Estudantes
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link has-arrow {{ request()->routeIs('dashboard.reports.teachers') ? 'menu-active' : '' }}"
                                                href="{{ route('dashboard.reports.teachers') }}">
                                                <i data-feather="corner-down-right" class="nav-icon me-2 icon-xs"></i>
                                                Professores
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link has-arrow {{ request()->routeIs('dashboard.reports.lessons') ? 'menu-active' : '' }}"
                                                href="{{ route('dashboard.reports.lessons') }}">
                                                <i data-feather="corner-down-right" class="nav-icon me-2 icon-xs"></i>
                                                Aulas
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <!-- Nav item -->
                            <li class="nav-item">
                                <a class="nav-link has-arrow {{ !request()->routeIs('dashboard.specialties.*') ? 'collapsed' : '' }}"
                                    href="#!" data-bs-toggle="collapse" data-bs-target="#specialty"
                                    aria-expanded="false" aria-controls="specialty">
                                    <i data-feather="bookmark" class="nav-icon me-3 icon-xs"></i>
                                    Especialidades
                                </a>
                                <div id="specialty"
                                    class="collapse {{ request()->routeIs('dashboard.specialties.*') ? 'show' : '' }}"
                                    data-bs-parent="#sideNavbar">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link has-arrow {{ request()->routeIs('dashboard.specialties.*') && !request()->routeIs('dashboard.specialties.create') ? 'menu-active' : '' }}"
                                                href="{{ route('dashboard.specialties.index') }}">
                                                <i data-feather="corner-down-right" class="nav-icon me-2 icon-xs"></i>
                                                Gerenciar
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link has-arrow {{ request()->routeIs('dashboard.specialties.create') ? 'menu-active' : '' }}"
                                                href="{{ route('dashboard.specialties.create') }}">
                                                <i data-feather="corner-down-right" class="nav-icon me-2 icon-xs"></i>
                                                Novo
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <!-- Nav item -->
                            <li class="nav-item">
                                <a class="nav-link has-arrow {{ !request()->routeIs('dashboard.libraries.*') ? 'collapsed' : '' }}"
                                    href="#!" data-bs-toggle="collapse" data-bs-target="#library"
                                    aria-expanded="false" aria-controls="library">
                                    <i data-feather="file-text" class="nav-icon me-3 icon-xs"></i>
                                    Biblioteca
                                </a>

                                <div id="library"
                                    class="collapse {{ request()->routeIs('dashboard.libraries.*') ? 'show' : '' }}"
                                    data-bs-parent="#sideNavbar">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link has-arrow {{ request()->routeIs('dashboard.libraries.*') && !request()->routeIs('dashboard.libraries.create') ? 'menu-active' : '' }}"
                                                href="{{ route('dashboard.libraries.index') }}">
                                                <i data-feather="corner-down-right" class="nav-icon me-2 icon-xs"></i>
                                                Gerenciar
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link has-arrow {{ request()->routeIs('dashboard.libraries.create') ? 'menu-active' : '' }}"
                                                href="{{ route('dashboard.libraries.create') }}">
                                                <i data-feather="corner-down-right" class="nav-icon me-2 icon-xs"></i>
                                                Novo
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <!-- Nav item -->
                            <li class="nav-item">
                                <a class="nav-link has-arrow {{ !request()->routeIs('dashboard.users.*') ? 'collapsed' : '' }}"
                                    href="#!" data-bs-toggle="collapse" data-bs-target="#navecommerce"
                                    aria-expanded="false" aria-controls="navecommerce">
                                    <i data-feather="users" class="nav-icon me-3 icon-xs"></i>
                                    Usuários
                                </a>
                                <div id="navecommerce"
                                    class="collapse {{ request()->routeIs('dashboard.users.*') ? 'show' : '' }}"
                                    data-bs-parent="#sideNavbar">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link has-arrow {{ request()->routeIs('dashboard.users.*') && !request()->routeIs('dashboard.users.create') ? 'menu-active' : '' }}"
                                                href="{{ route('dashboard.users.index') }}">
                                                <i data-feather="corner-down-right" class="nav-icon me-2 icon-xs"></i>
                                                Gerenciar
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link has-arrow {{ request()->routeIs('dashboard.users.create') ? 'menu-active' : '' }}"
                                                href="{{ route('dashboard.users.create') }}">
                                                <i data-feather="corner-down-right" class="nav-icon me-2 icon-xs"></i>
                                                Novo
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endcan
                        <!-- Nav item -->
                        <li class="nav-item">
                            <a class="nav-link has-arrow {{ !request()->routeIs('dashboard.guidebooks.*') ? 'collapsed' : '' }}"
                                href="#!" data-bs-toggle="collapse" data-bs-target="#guidebooks"
                                aria-expanded="false" aria-controls="guidebooks">
                                <i data-feather="book" class="nav-icon me-3 icon-xs"></i>
                                Manuais
                            </a>
                            <div id="guidebooks"
                                class="collapse {{ request()->routeIs('dashboard.guidebooks.*') ? 'show' : '' }} {{ request()->routeIs('dashboard.guidebook-categories.*') ? 'show' : '' }}"
                                data-bs-parent="#sideNavbar">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link has-arrow {{ request()->routeIs('dashboard.guidebooks.*') && !request()->routeIs('dashboard.guidebooks.create') ? 'menu-active' : '' }}"
                                            href="{{ route('dashboard.guidebooks.index') }}">
                                            <i data-feather="corner-down-right" class="nav-icon me-2 icon-xs"></i>
                                            Gerenciar
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link has-arrow {{ request()->routeIs('dashboard.guidebooks.create') ? 'menu-active' : '' }}"
                                            href="{{ route('dashboard.guidebooks.create') }}">
                                            <i data-feather="corner-down-right" class="nav-icon me-2 icon-xs"></i>
                                            Novo
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link has-arrow {{ request()->routeIs('dashboard.guidebook-categories.*') ? 'menu-active' : '' }}"
                                            href="{{ route('dashboard.guidebook-categories.index') }}">
                                            <i data-feather="corner-down-right" class="nav-icon me-2 icon-xs"></i>
                                            Categorias
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Page content -->
        <div id="app-content">
            <!-- Container fluid -->
            <div class="app-content-area">
                @include('dashboard.includes.alerts')
                <!-- Main Content -->
                <main>
                    @yield('content')
                </main>
            </div>
        </div>
    </main>
    <!-- Scripts -->
    <!-- Libs JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"
        integrity="sha512-7Pi/otdlbbCR+LnW+F7PwFcSDJOuUJB3OxtEHbg4vSMvzvJjde4Po1v4BR9Gdc9aXNUNFVUY+SK51wWT8WF0Gg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.2/feather.min.js"
        integrity="sha512-zMm7+ZQ8AZr1r3W8Z8lDATkH05QG5Gm2xc6MlsCdBz9l6oE8Y7IXByMgSm/rdRQrhuHt99HAYfMljBOEZ68q5A=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/simplebar/6.3.0/simplebar.min.js"
        integrity="sha512-YumGHjm0sYk55Xdh6t6Uo/mHqBhDBNrW46HZKSBwkjq3X1Knnj7e3UUom2SE9zPpfjlTyJqSHnd4No1ca156cQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/quill/2.0.2/quill.min.js"
        integrity="sha512-1nmY9t9/Iq3JU1fGf0OpNCn6uXMmwC1XYX9a6547vnfcjCY1KvU9TE5e8jHQvXBoEH7hcKLIbbOjneZ8HCeNLA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"
        integrity="sha512-K/oyQtMXpxI4+K0W7H25UopjM8pzq0yrVdFdG21Fh5dBe91I40pDd9A4lzNlHPHBIP2cwZuoxaUSX0GJSObvGA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Theme JS -->
    @vite(['resources/js/app.js'])

</body>

</html>

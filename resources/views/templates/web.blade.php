<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Favicon icon-->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('icons/logo-icon.png') }}">
    <title>Plataforma de Educação Permanente em Saúde - PEPS | HCAA</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css"
        integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Laravel Vite -->
    @vite(['resources/sass/web.scss', 'resources/js/app.js'])
</head>

<body>
    <!-- Header -->
    <header>
        <div class="navbar-wrapper">
            <nav class="fixed-top navbar navbar-expand-lg navbar-custom">
                <div class="container">
                    <a class="navbar-brand text-white" href="{{ route('web.index') }}">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo">
                    </a>
                    <button class="navbar-toggler text-white border-white" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarContent">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-center" id="navbarContent">
                        <ul class="navbar-nav mb-2 mb-lg-0 text-center">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('web.index') ? 'active' : '' }}"
                                    href="{{ route('web.index') }}">
                                    <i class="bi bi-house-door-fill"></i> Início
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('web.classes*') ? 'active' : '' }} {{ request()->routeIs('web.class*') ? 'active' : '' }}"
                                    href="{{ route('web.classes') }}">
                                    <i class="bi bi-easel2-fill"></i> Aulas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('web.teachers*') ? 'active' : '' }}"
                                    href="{{ route('web.teachers') }}">
                                    <i class="bi bi-people-fill"></i> Professores
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('web.informations') ? 'active' : '' }}"
                                    href="{{ route('web.informations') }}">
                                    <i class="bi bi-info-circle-fill"></i> Informações
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('web.suggestions') ? 'active' : '' }}"
                                    href="{{ route('web.suggestions') }}">
                                    <i class="bi bi-lightbulb-fill"></i> Sugestões
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('web.library') ? 'active' : '' }}"
                                    href="{{ route('web.library') }}">
                                    <i class="bi bi-stack"></i> Biblioteca
                                </a>
                            </li>
                            @auth
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle {{ request()->routeIs(['web.myClasses', 'web.perfil']) ? 'active' : '' }}"
                                        href="#" id="menuDropdown" role="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi bi-person-circle"></i>
                                        {{ Auth::user()->name }}
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="menuDropdown">
                                        @can('isCoordenadorOrProfessor')
                                            <li>
                                                <a class="dropdown-item" href="{{ route('dashboard.index') }}">
                                                    <i class="bi bi-layout-wth me-2"></i> Dashboard
                                                </a>
                                            </li>
                                        @endcan
                                        <li>
                                            <a class="dropdown-item {{ request()->routeIs('web.myClasses') ? 'active' : '' }}"
                                                href="{{ route('web.myClasses') }}">
                                                <i class="bi bi-collection-play-fill me-2"></i> Minhas Aulas
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item {{ request()->routeIs('web.perfil') ? 'active' : '' }}"
                                                href="{{ route('web.perfil') }}">
                                                <i class="bi bi-person-fill me-2"></i> Perfil
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('logout-post') }}">
                                                @csrf
                                                <button class="dropdown-item" type="submit">
                                                    <i class="bi bi-box-arrow-right me-2"></i>
                                                    Sair
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            @endauth
                            @guest
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}"
                                        href="{{ route('login') }}">
                                        <i class="bi bi-box-arrow-in-right"></i> Login
                                    </a>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>
    <!-- Main Content -->
    <main>
        @include('web.includes.alerts')
        @yield('content')
    </main>
    <!-- Footer -->
    <footer class="footer mt-5 pt-5 pb-3 text-white">
        <div class="container">
            <div class="row text-center text-md-start">
                <div class="col-md-4 mb-4 d-flex flex-column align-items-center text-center">
                    <img src="{{ asset('images/logo-home.png') }}" alt="Logo" class="footer-logo mb-4">
                    <p class="mb-1">PEPS | HCAA</p>
                    <p class="small mb-0">Plataforma de Educação Permanente em Saúde</p>
                    <p class="small">Hospital de Câncer Alfredo Abrão</p>
                </div>
                <div class="col-md-4 mb-4 text-center">
                    <h5 class="mb-3">Menu</h5>
                    <ul class="list-unstyled">
                        <li>
                            <a href="{{ route('web.index') }}" class="footer-link">
                                Início
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('web.classes') }}" class="footer-link">
                                Aulas
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('web.teachers') }}" class="footer-link">
                                Professores
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('web.informations') }}" class="footer-link">
                                Informações
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('web.suggestions') }}" class="footer-link">
                                Sugerir Temas
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('web.library') }}" class="footer-link">
                                Biblioteca Digital
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4 text-center">
                    <h5 class="mb-3">Links Úteis</h5>
                    <ul class="list-unstyled">
                        <li>
                            <a href="{{ route('web.validate.certificate') }}" class="footer-link">
                                Validar Certificado
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('web.user.terms') }}" class="footer-link">
                                Termos de Uso
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('web.informations') }}" class="footer-link">
                                Ajuda
                            </a>
                        </li>
                        <li>
                            <a href="#" class="footer-link">
                                Política de Privacidade
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="text-center border-top pt-3 mt-4 small text-white">
                © {{ date('Y') }} Hospital de Câncer de Campo Grande - Alfredo Abrão. Todos os direitos reservados.
            </div>
        </div>
    </footer>
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"
        integrity="sha512-bPs7Ae6pVvhOSiIcyUClR7/q2OAsRiovw4vAkX+zJbw3ShAeeqezq50RIIcIURq7Oa20rW2n2q+fyXBNcU9lrw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        $(() => {
            const owl = $('.owl-carousel');

            owl.owlCarousel({
                loop: true,
                responsiveClass: true,
                autoplay: true,
                margin: 10,
                nav: false,
                dots: false,
                responsive: {
                    0: {
                        items: 1
                    },
                    600: {
                        items: 2
                    },
                    1000: {
                        items: 4
                    },
                },
            });

            $('.owl-prev').click(function() {
                owl.trigger('prev.owl.carousel');
            });

            $('.owl-next').click(function() {
                owl.trigger('next.owl.carousel');
            });
        });
    </script>

</html>

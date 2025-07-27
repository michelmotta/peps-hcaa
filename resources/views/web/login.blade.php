@extends('templates.web')

@section('content')
    <section>
        <div class="content-title">
            <h1>Acessar</h1>
            <p class="sub-title">Faça login para acessar seus conteúdos salvos</p>
        </div>
    </section>

    <section class="login-page-section mt-5">
        <div class="container">
            <div class="login-card-wrapper">
                <div class="login-welcome-panel">
                    <div class="welcome-content">
                        <img src="{{ asset('images/logo-home.png') }}" alt="Logo HCAA" class="welcome-logo">
                        <h3 class="welcome-title">Bem-vindo(a)!</h3>
                        <p>Acesse sua conta para continuar seus estudos e acompanhar seu progresso.</p>
                        <hr class="welcome-divider">
                        <p class="small">Não tem uma conta?</p>
                        <a href="{{ route('web.perfil') }}" class="btn btn-outline-light mt-2">
                            <i class="bi bi-person-plus-fill me-2"></i> Criar Conta Agora
                        </a>
                    </div>
                </div>
                <div class="login-form-panel">
                    <h4 class="form-title">Acessar a Plataforma</h4>
                    <form method="POST" action="{{ route('login-post') }}">
                        @csrf
                        @error('username')
                            <div class="alert alert-danger small p-2 text-center mb-3">
                                {{ $message }}
                            </div>
                        @enderror
                        <div class="mb-3">
                            <label for="username" class="form-label">Usuário ou E-mail</label>
                            <div class="input-group">
                                <span
                                    class="input-group-text @error('username') border border-danger text-danger @enderror"><i
                                        class="bi bi-person"></i></span>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                    id="username" name="username" placeholder="Digite seu usuário" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Senha</label>
                            <div class="input-group">
                                <span
                                    class="input-group-text @error('password') border border-danger text-danger @enderror"><i
                                        class="bi bi-lock"></i></span>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" placeholder="Digite sua senha" required>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label" for="remember">
                                    Lembrar-me
                                </label>
                            </div>
                            <a href="{{ route('password.forgot') }}" class="forgot-link">Esqueceu sua senha?</a>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Entrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

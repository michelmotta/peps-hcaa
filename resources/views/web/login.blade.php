@extends('templates.web')
@section('content')
    <section>
        <div class="content-title">
            <h1>Acessar</h1>
            <p class="sub-title">Faça login para acessar seus conteúdos salvos</p>
        </div>
    </section>
    <section class="login-section">
        <div class="login-card d-flex">
            <!-- Login Form (Bootstrap Column) -->
            <div class="col-12 col-md-6 login-form">
                <h4>Acessar a Plataforma</h4>
                <form method="POST" action="{{ route('web.login-post') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuário</label>
                        <div class="input-group">
                            <span class="input-group-text" id="username-icon"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control @error('username') is-invalid @enderror"
                                id="username" name="username" placeholder="Digite seu usuário" required>
                            @error('username')
                                <div class="invalid-feedback d-block mt-2">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <div class="input-group">
                            <span class="input-group-text" id="password-icon"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" placeholder="Digite sua senha" required>
                            @error('password')
                                <div class="invalid-feedback d-block mt-2">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Entrar
                        </button>
                    </div>
                    <div class="text-center">
                        <a href="#" class="forgot-link">Esqueceu sua senha?</a>
                    </div>
                </form>
            </div>

            <!-- Vertical Separator (Only for larger screens) -->
            <div class="separator col-12 col-md-1 d-none d-md-block"></div>

            <!-- Create Account Section (Bootstrap Column) -->
            <div class="col-12 col-md-5 create-account">
                <h5>Não tem uma conta?</h5>
                <p>Crie uma conta para acessar a plataforma e aproveitar todo o conteúdo.</p>
                <a href="{{ route('web.perfil') }}" class="btn btn-outline-primary">
                    <i class="bi bi-person-plus"></i> Criar conta
                </a>
            </div>
        </div>
    </section>
@endsection

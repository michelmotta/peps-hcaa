@extends('templates.web')

@section('content')
    <section>
        <div class="content-title">
            <h1>Redefinir Senha</h1>
            <p class="sub-title">Esqueceu sua senha? Sem problemas. Digite seu e-mail abaixo.</p>
        </div>
    </section>

    <section class="password-reset-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="reset-card">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <i class="bi bi-shield-check card-icon"></i>
                                <h4 class="card-title">Recuperação de Senha</h4>
                                <p class="card-text text-muted">Nós enviaremos um link seguro para o seu e-mail para que
                                    você possa criar uma nova senha.</p>
                            </div>

                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('password.forgot') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="email" class="form-label">Seu endereço de e-mail cadastrado na
                                        plataforma</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope-at"></i></span>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email" value="{{ old('email') }}"
                                            placeholder="exemplo@email.com" required>
                                    </div>
                                    @error('email')
                                        <div class="invalid-feedback d-block mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-send"></i> Enviar Link de Redefinição
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer text-center">
                            <a href="{{ route('login') }}" class="footer-link">Lembrou a senha? Voltar para o Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@extends('templates.web')
@section('content')
    <section>
        <div class="content-title">
            <h1>{{ isset($user) ? 'Atualizar Conta' : 'Criar Conta' }}</h1>
            <p class="sub-title">
                {{ isset($user) ? 'Atualize as informações de sua conta' : 'Crie uma conta e aproveite todas as aulas disponibilizadas' }}
            </p>
        </div>
    </section>
    <section class="create-account-section container">
        <div class="create-account-card">
            <div class="create-account-form">
                <div class="account-header">
                    <h4>{{ isset($user) ? 'Atualização de Conta' : 'Criação de Conta' }}</h4>
                </div>
                @isset($user)
                    <div class="account-thumb">
                        <img src="{{ asset('storage/' . $user->file->path) }}" alt="">
                    </div>
                @endisset
                <div class="account-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <form method="POST"
                        action="{{ isset($user) ? route('web.perfil-update', $user->id) : route('web.perfil-create') }}"
                        enctype="multipart/form-data">
                        @csrf
                        @isset($user)
                            @method('PATCH')
                        @endisset

                        <div class="row">
                            <h4 class="text-center mb-4">Informações Pessoais</h4>
                            <div class="col-md-6">
                                <!-- Nome -->
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nome</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" placeholder="Digite seu nome" name="name"
                                        value="{{ !isset($user) ? old('name') : $user->name }}" required>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">E-mail</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" placeholder="Digite seu e-mail" name="email"
                                        value="{{ !isset($user) ? old('email') : $user->email }}" required>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- CPF -->
                                <div class="mb-3">
                                    <label for="cpf" class="form-label">CPF</label>
                                    <input type="text" class="form-control @error('cpf') is-invalid @enderror"
                                        id="cpf" placeholder="Digite seu CPF" name="cpf"
                                        value="{{ !isset($user) ? old('cpf') : $user->cpf }}" required>
                                    @error('cpf')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Perfil -->
                                <div class="mb-3">
                                    <label for="file" class="form-label">Foto de Perfil</label>
                                    <input type="file" class="form-control @error('file') is-invalid @enderror"
                                        id="file" name="file">
                                    <small>Formatos permitidos: JPG|JPEG|PNG|GIF. Tamanho máximo: 2MB</small>
                                    @error('file')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <h4 class="text-center mt-5">Segurança</h4>
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Usuário -->
                                <div class="mb-3">
                                    <label for="username" class="form-label">Usuário</label>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                                        id="username" placeholder="Escolha um nome de usuário"
                                        value="{{ !isset($user) ? old('username') : $user->username }}" name="username"
                                        required>
                                    @error('username')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Senha -->
                                <div class="mb-3">
                                    <label for="password" class="form-label">Senha</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" placeholder="Digite sua senha" name="password"
                                        {{ !isset($user) ? 'required' : '' }}>
                                    @isset($user)
                                        <small>Preencha este campo apenas se você desejar atualizar sua senha.</small>
                                    @endisset
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Confirmação de Senha -->
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirme sua Senha</label>
                                    <input type="password"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        id="password_confirmation" placeholder="Confirme sua senha"
                                        name="password_confirmation" {{ !isset($user) ? 'required' : '' }}>
                                    @isset($user)
                                        <small>Preencha este campo apenas se você desejar atualizar sua senha.</small>
                                    @endisset
                                    @error('password_confirmation')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <!-- Botão Criar Conta -->
                                <div class="text-center mt-5">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Salvar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

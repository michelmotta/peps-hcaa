@extends('templates.web')

@section('content')
    <section>
        <div class="content-title">
            <h1>{{ isset($user) ? 'Meu Perfil' : 'Criar Conta' }}</h1>
            <p class="sub-title">
                {{ isset($user) ? 'Atualize as informações da sua conta' : 'Crie uma conta e aproveite todas as aulas' }}
            </p>
        </div>
    </section>
    <section class="profile-section container">
        <form method="POST" action="{{ isset($user) ? route('web.perfil-update', $user->id) : route('web.perfil-create') }}"
            enctype="multipart/form-data">
            @csrf
            @isset($user)
                @method('PATCH')
            @endisset
            <div class="row">
                <div class="col-lg-4">
                    <div class="profile-sidebar-card">
                        <div class="profile-picture-wrapper">
                            <label for="file" class="picture-upload-label">
                                <img id="profile-picture-preview"
                                    src="{{ isset($user) && $user->file ? asset('storage/' . $user->file->path) : 'https://placehold.co/400x400/EBF0F6/7F92B0?text=Foto' }}"
                                    alt="Foto de Perfil">
                                <div class="upload-overlay">
                                    <i class="bi bi-camera-fill"></i>
                                    <span>Trocar Foto</span>
                                </div>
                            </label>
                            <input type="file" name="file" id="file"
                                class="d-none @error('file') is-invalid @enderror"
                                onchange="document.getElementById('profile-picture-preview').src = window.URL.createObjectURL(this.files[0])">
                        </div>
                        <h5 class="profile-name text-center mt-3">{{ $user->name ?? 'Novo Usuário' }}</h5>
                        <p class="profile-email text-center text-muted">{{ $user->email ?? ' ' }}</p>
                        @error('file')
                            <div class="invalid-feedback d-block text-center">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="form-card">
                        <div class="card-header">
                            <i class="bi bi-person-badge"></i>
                            <h4>Informações Pessoais</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="name" class="form-label">Nome Completo</label>
                                    <div class="input-group">
                                        <span class="input-group-text @error('name') border border-danger @enderror"><i
                                                class="bi bi-person-fill"></i></span>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ $user->name ?? old('name') }}" required>
                                    </div>
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">E-mail</label>
                                    <div class="input-group">
                                        <span class="input-group-text @error('email') border border-danger @enderror"><i
                                                class="bi bi-envelope-at-fill"></i></span>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email" value="{{ $user->email ?? old('email') }}"
                                            required>
                                    </div>
                                    @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cpf" class="form-label">CPF</label>
                                    <div class="input-group">
                                        <span class="input-group-text @error('cpf') border border-danger @enderror"><i
                                                class="bi bi-person-vcard"></i></span>
                                        <input type="text" class="form-control @error('cpf') is-invalid @enderror"
                                            id="cpf" name="cpf" value="{{ $user->cpf ?? old('cpf') }}" required>
                                    </div>
                                    @error('cpf')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="biography" class="form-label">Resumo Profissional</label>
                                    <textarea class="form-control @error('biography') is-invalid @enderror" id="biography" name="biography"
                                        style="height:100px">{{ $user->biography ?? old('biography') }}</textarea>
                                    @error('biography')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-card mt-4">
                        <div class="card-header">
                            <i class="bi bi-shield-lock-fill"></i>
                            <h4>Segurança</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="username" class="form-label">Nome de Usuário</label>
                                    <div class="input-group">
                                        <span
                                            class="input-group-text @error('username') border border-danger @enderror">@</span>
                                        <input type="text" class="form-control @error('username') is-invalid @enderror"
                                            id="username" name="username" value="{{ $user->username ?? old('username') }}"
                                            required>
                                    </div>
                                    @error('username')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Nova Senha</label>
                                    <div class="input-group">
                                        <span class="input-group-text @error('password') border border-danger @enderror"><i
                                                class="bi bi-key-fill"></i></span>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                            id="password" name="password" {{ !isset($user) ? 'required' : '' }}>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    @isset($user)
                                        <small class="form-text text-muted">Preencha apenas se desejar alterar sua senha.</small>
                                    @endisset
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation" {{ !isset($user) ? 'required' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @unless (isset($user))
                        <div class="mt-4 p-3 bg-light rounded border">
                            <div class="form-check">
                                <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox"
                                    value="1" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">
                                    Eu li e concordo com os <a href="#" target="_blank">Termos de Uso</a>.
                                </label>
                                @error('terms')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endunless
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ isset($user) ? 'Salvar Alterações' : 'Criar Conta' }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </section>
@endsection

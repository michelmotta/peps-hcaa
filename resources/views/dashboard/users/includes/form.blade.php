@csrf
<section>
    <div class="row">
        @can('isCoordenador')
            <div class="col-md-12">
                <div class="roles mb-3 text-center">
                    <h3 class="form-label">Atribuir Perfil</h3>
                    <div class="form-check d-flex align-items-center justify-content-center flex-wrap gap-4">
                        @foreach ($profiles as $profile)
                            @php
                                $selectedProfiles = collect(
                                    old('profiles', isset($user) ? $user->profiles->pluck('id')->toArray() : []),
                                );
                            @endphp

                            <label for="profile_{{ $profile->id }}" class="role-card">
                                <input type="checkbox" id="profile_{{ $profile->id }}" name="profiles[]"
                                    value="{{ $profile->id }}"
                                    class="form-check-input @error('profiles') is-invalid @enderror"
                                    {{ $selectedProfiles->contains($profile->id) ? 'checked' : '' }}>
                                <span class="form-check-label">{{ $profile->name }}</span>
                            </label>
                        @endforeach
                    </div>

                    @error('profiles')
                        <div class="invalid-feedback d-block mt-2">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
        @endcan
        <div class="col-md-4">
            <div class="mb-3">
                <label for="name" class="form-label">Nome</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                    placeholder="Digite seu nome" name="name"
                    value="@if(isset($user)){{ $user->name }}@else{{ old('name') }}@endif"
                    required>
                @error('name')
                    <span class="invalid-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                    placeholder="Digite seu e-mail" name="email"
                    value="@if (isset($user)){{ $user->email }}@else{{ old('email') }}@endif"
                    required>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="cpf" class="form-label">CPF</label>
                <input type="text" class="form-control @error('cpf') is-invalid @enderror" id="cpf"
                    placeholder="Digite seu CPF" name="cpf"
                    value="@if(isset($user)){{ $user->cpf }}@else{{ old('cpf') }}@endif"
                    required>
                @error('cpf')
                    <span class="invalid-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="sector" class="form-label">Setor de Trabalho</label>
                <select name="sector_id" id="sector_id" class="form-select">
                    <option value="">-- Selecione um setor</option>
                    @foreach ($sectors as $sector)
                        <option value="{{ $sector->id }}" @if(isset($user) && $user->sector_id == $sector->id) selected @endif>{{ $sector->name }}</option>
                    @endforeach
                </select>
                @error('expertise')
                    <span class="invalid-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="expertise" class="form-label">Especialidade</label>
                <input type="text" class="form-control @error('expertise') is-invalid @enderror" id="expertise"
                    placeholder="Digite sua especialidade" name="expertise"
                    value="@if(isset($user)){{ $user->expertise }}@else{{ old('expertise') }}@endif">
                @error('expertise')
                    <span class="invalid-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="file" class="form-label">Foto de Perfil</label>
                <input type="file" class="form-control @error('file') is-invalid @enderror" id="file"
                    name="file">
                <small>Formatos permitidos: JPG|JPEG|PNG|GIF. Tamanho máximo: 2MB</small>
                @error('file')
                    <span class="invalid-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="username" class="form-label">Usuário</label>
                <input type="text" class="form-control @error('username') is-invalid @enderror" id="username"
                    placeholder="Digite seu nome de usuário" name="username"
                    value="@if(isset($user)){{ $user->username }}@else{{ old('username') }}@endif"
                    required>
                @error('username')
                    <span class="invalid-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="password" class="form-label">Senha</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                    placeholder="Digite sua senha" name="password">
                @isset($edit)
                    <small>Preencha este campo apenas se quiser alterar a senha.</small>
                @endisset
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirme sua Senha</label>
                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                    id="password_confirmation" placeholder="Confirme sua senha" name="password_confirmation">
                @isset($edit)
                    <small>Preencha este campo apenas se quiser alterar a senha.</small>
                @endisset
                @error('password_confirmation')
                    <span class="invalid-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-12">
            <div class="mt-2 mb-4">
                <label class="form-label">Biografia</label>
                <div id="editor">@if(isset($user)){!! $user->biography !!}@else{!! old('biography') !!}@endif</div>
                <input type="hidden" name="biography" id="biography" value="@if(isset($user)){{ $user->biography }}@else{{ old('biography') }}@endif">
            </div>
        </div>
    </div>
    <div class="text-center mt-5">
        <button type="submit" class="btn btn-primary btn-lg">
            <i data-feather="save" class="me-2 icon-xs"></i>
            Salvar
        </button>
    </div>
</section>

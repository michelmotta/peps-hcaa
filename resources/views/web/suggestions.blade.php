@extends('templates.web')
@section('content')
    <section>
        <div class="content-title">
            <h1>Sugerir Temas</h1>
            <p class="sub-title">Vote nos temas que você mais gosta ou sugira um novo!</p>
        </div>
    </section>
    @include('web.includes.search_form', [
        'title' => 'Pesquisar sugestões...',
        'action' => route('web.suggestions'),
    ])
    <section class="suggestion-board-section">
        <div class="container">
            @auth
                <div class="d-flex justify-content-center mb-5">
                    <button class="btn novo-tema-btn" data-bs-toggle="modal" data-bs-target="#themeModal">
                        <i class="bi bi-plus-lg me-2"></i>Sugerir Novo Tema
                    </button>
                </div>
            @endauth
            @if ($topSuggestions->isNotEmpty() && !request('page') && !request('q'))
                <div class="leaderboard">
                    <h2 class="section-title"><span>Temas em Destaque</span></h2>
                    <div class="row justify-content-center">
                        @foreach ($topSuggestions as $index => $suggestion)
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="leaderboard-card rank-{{ $index + 1 }}">
                                    <div class="rank-badge">#{{ $index + 1 }}</div>
                                    <div class="card-content">
                                        <div class="card-main-content">
                                            <h5 class="card-title">{{ $suggestion->name }}</h5>
                                            <p class="card-author">Sugerido por: {{ $suggestion->user->name }}</p>
                                            <div class="vote-count">{{ $suggestion->votes }} votos</div>
                                        </div>
                                        @auth
                                            <form method="POST" action="{{ route('web.suggestion-update', $suggestion->id) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn vote-btn">
                                                    <i class="bi bi-hand-thumbs-up"></i> Votar
                                                </button>
                                            </form>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            <div class="suggestion-feed">
                <h2 class="section-title">
                    <span>{{ request('q') ? 'Resultados da Busca' : 'Todas as Sugestões' }}</span>
                </h2>
                @forelse ($suggestions as $suggestion)
                    <div class="suggestion-list-item">
                        <div class="vote-control">
                            @auth
                                <form method="POST" action="{{ route('web.suggestion-update', $suggestion->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="vote-btn-list">
                                        <i class="bi bi-hand-thumbs-up"></i> Votar
                                    </button>
                                </form>
                            @endauth
                            <div class="vote-count-list">{{ $suggestion->votes }} Votos</div>
                        </div>
                        <div class="suggestion-details">
                            <h5 class="suggestion-title">{{ $suggestion->name }}</h5>
                            <p class="suggestion-description">{{ $suggestion->description }}</p>
                            <small class="suggestion-meta">
                                Sugerido por {{ $suggestion->user->name }} &middot;
                                {{ $suggestion->created_at_formatted }}
                            </small>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-search display-4 text-muted"></i>
                        <h4 class="mt-3">Nenhuma sugestão encontrada</h4>
                        <p class="text-muted">Tente ajustar os termos da sua pesquisa ou seja o primeiro a sugerir um tema!
                        </p>
                    </div>
                @endforelse
            </div>
            <div class="pagination-wrapper">
                {{ $suggestions->links() }}
            </div>
        </div>
    </section>
    @auth
        <div class="modal fade" id="themeModal" tabindex="-1" aria-labelledby="themeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content suggestion-modal-content">
                    <div class="modal-body">
                        <div class="modal-icon-header">
                            <i class="bi bi-lightbulb"></i>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <h4 class="modal-title text-center" id="themeModalLabel">Sugerir Novo Tema</h4>
                        <p class="text-center text-muted mb-4">Compartilhe sua ideia para uma nova aula. As melhores sugestões
                            podem ser produzidas!</p>
                        <form method="POST" action="{{ route('web.suggestion-create') }}">
                            @csrf
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="suggestionName" name="name"
                                    placeholder="Título do tema" required>
                                <label for="suggestionName">Título do tema</label>
                            </div>
                            <div class="form-floating mb-4">
                                <textarea class="form-control" id="suggestionDescription" name="description" placeholder="Descrição"
                                    style="height: 120px" required></textarea>
                                <label for="suggestionDescription">Descrição</label>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg submit-suggestion-btn">
                                    <i class="bi bi-send me-2"></i>Enviar Sugestão
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endauth
@endsection

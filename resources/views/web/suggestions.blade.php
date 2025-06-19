@extends('templates.web')
@section('content')
    <section>
        <div class="content-title">
            <h1>Sugerir Temas</h1>
            <p class="sub-title">Faça login para sugerir temas ou votar em temas sugeridos.</p>
        </div>
    </section>
    @include('web.includes.search_form', [
        'title' => 'Pesquisar sugestões...',
        'action' => '',
    ])
    <section class="theme-voting-section py-4">
        <div class="container">
            @auth
                <!-- Right-aligned Novo Tema Button with hover effect -->
                <div class="d-flex justify-content-end mb-4">
                    <button class="btn novo-tema-btn " data-bs-toggle="modal" data-bs-target="#themeModal">
                        <i class="bi bi-plus-lg me-2"></i>Novo Tema
                    </button>
                </div>
            @endauth
            <!-- Voting Cards Grid -->
            <div class="row g-4 mt-3">
                @foreach ($suggestions as $suggestion)
                    <div class="col-md-6 col-lg-4">
                        <div class="theme-card p-4 h-100">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <small class="text-muted"><i
                                        class="bi bi-person-circle me-1"></i>{{ $suggestion->user->name }}</small>
                                <small class="text-muted">{{ $suggestion->created_at_formatted }}</small>
                            </div>
                            <h5 class="fw-bold mb-3">{{ $suggestion->name }}</h5>
                            <p class="text-muted small mb-4">{!! $suggestion->description !!}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="vote-count fw-bold">
                                    <i class="bi bi-hand-thumbs-up me-1"></i> {{ $suggestion->votes }} votos
                                </div>
                                @auth
                                    <form method="POST" action="{{ route('web.suggestion-update', $suggestion->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn vote-btn text-white btn-sm rounded-pill px-3">
                                            Votar
                                        </button>
                                    </form>
                                    
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <!-- Theme Submission Modal -->
        <div class="modal fade" id="themeModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header text-white" style="background-color: #052c52;">
                        <h5 class="modal-title"><i class="bi bi-lightbulb me-2"></i>Sugerir Novo Tema</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('web.suggestion-create') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Título do tema</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <textarea class="form-control" rows="4" name="description" required></textarea>
                            </div>
                            <button type="submit" class="btn w-100 py-2 text-white" style="background-color: #052c52;">
                                <i class="bi bi-send me-2"></i>Enviar Sugestão
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="pagination-wrapper">
        {{ $suggestions->links() }}
    </div>
@endsection

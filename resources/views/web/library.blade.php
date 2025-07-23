@extends('templates.web')

@section('content')
    <section>
        <div class="content-title">
            <h1>Biblioteca Digital</h1>
            <p class="sub-title">Artigos e publicações recentes</p>
        </div>
    </section>
    @include('web.includes.search_form', [
        'action' => route('web.library'),
        'title' => 'Pesquisar por título...',
    ])
    <section class="library-content-wrapper">
        <div class="container">
            <div class="library-grid">
                @foreach ($libraryItems as $item)
                    <div class="library-card">
                        <a href="{{ asset('storage/' . $item->file->path) }}" class="card-thumbnail-link" target="_blank">
                            @if ($item->file && $item->file->thumbnail_path)
                                <img src="{{ asset('storage/' . $item->file->thumbnail_path) }}"
                                    alt="Capa de {{ $item->title }}" class="card-thumbnail-image">
                            @else
                                <div class="card-thumbnail-icon">
                                    @if (Str::contains($item->file->mime_type, 'pdf'))
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    @elseif(Str::contains($item->file->mime_type, 'word'))
                                        <i class="bi bi-file-earmark-word"></i>
                                    @else
                                        <i class="bi bi-file-earmark-text"></i>
                                    @endif
                                </div>
                            @endif
                        </a>
                        <div class="card-body">
                            <h3 class="card-title">{{ $item->title }}</h3>
                            <div class="card-meta">
                                <span><i class="bi bi-file-earmark"></i>
                                    {{ strtoupper($item->file->extension ?? 'N/A') }}</span>
                                <span><i class="bi bi-hdd"></i> {{ number_format($item->file->size / 1024, 1) }}
                                    KB</span>
                            </div>
                            <a href="{{ asset('storage/' . $item->file->path) }}" class="btn-download" download>
                                <i class="bi bi-download"></i>
                                <span>Baixar Arquivo</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            @if ($libraryItems->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <h3 class="empty-state-title">Nenhum resultado encontrado.</h3>
                </div>
            @endif
            <div class="pagination-wrapper">
                {{ $libraryItems->links() }}
            </div>
        </div>
    </section>
@endsection

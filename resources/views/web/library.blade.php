@extends('templates.web')

@section('content')
    <section>
        <div class="content-title">
            <h1>Biblioteca Digital</h1>
            <p class="sub-title">Artigos e publicações recentes</p>
        </div>
    </section>
    <section class="library-content-wrapper">
        <div class="container mb-5">
            <form type="GET" action="{{ route('web.library') }}" class="p-4 bg-white border rounded-3">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-4 col-md-12">
                        <label for="q" class="form-label">O que você gostaria de ler?</label>
                        <input type="text" class="form-control" id="q" name="q" value="{{ request('q') }}"
                            placeholder="Ex: Anatomia, Cuidados Paliativos...">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label for="specialty_id" class="form-label">Especialidade</label>
                        <select class="form-select" id="specialty_id" name="specialty_id">
                            <option value="">Todas</option>
                            @foreach ($specialties as $specialty)
                                <option value="{{ $specialty->id }}" @selected(request('specialty_id') == $specialty->id)>
                                    {{ $specialty->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label for="sort_by" class="form-label">Ordenar por</label>
                        <select class="form-select" id="sort_by" name="sort_by">
                            <option value="newest" @selected(request('sort_by', 'newest') == 'newest')>Mais Recentes</option>
                            <option value="oldest" @selected(request('sort_by') == 'oldest')>Mais Antigas</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6 d-grid gap-2 d-md-flex">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-filter me-1"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
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

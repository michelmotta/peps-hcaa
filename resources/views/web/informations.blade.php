@extends('templates.web')
@section('content')
    <section>
        <div class="content-title">
            <h1>Informações</h1>
            <p class="sub-title">Veja as perguntas frequentes</p>
        </div>
    </section>
    @include('web.includes.search_form', [
        'action' => route('web.informations'),
        'title' => 'Pesquisar informações...',
    ])
    <section class="faq-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        @forelse ($information as $info)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq-{{ $info->id }}">
                                        <i class="bi bi-patch-question me-2"></i> {{ $info->title }}
                                    </button>
                                </h2>
                                <div id="faq-{{ $info->id }}" class="accordion-collapse collapse"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        {!! $info->description !!}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="bi bi-search display-4 text-muted"></i>
                                <h4 class="mt-3">Nenhuma informação encontrada</h4>
                                <p class="text-muted">Tente ajustar os termos da sua pesquisa.</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="pagination-wrapper">
                        {{ $information->links() }}
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="sidebar-card sticky-top">
                        <div class="card-body">
                            <h4 class="card-title"><i class="bi bi-headset me-2"></i>Ainda com dúvidas?</h4>
                            <p class="card-text">Se não encontrou o que procurava, nossa equipe está pronta para ajudar.</p>
                            <ul class="list-unstyled mt-4">
                                <li class="mb-2"><i class="bi bi-envelope me-2"></i> email@email.com</li>
                                <li><i class="bi bi-telephone me-2"></i> (67) 3333-3333</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

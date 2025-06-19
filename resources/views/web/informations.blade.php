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
    <section>
        <div class="container faqs">
            <div class="row">
                <!-- FAQ Section -->
                <div class="col-lg-9">
                    <div class="accordion" id="faqAccordion">
                        @foreach ($information as $info)
                            <div class="accordion-item mb-3 border-0 rounded-3 overflow-hidden">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed shadow-none" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faq-{{ $info->id }}">
                                        <i class="fas fa-graduation-cap me-2"></i> {{ $info->title }}
                                    </button>
                                </h2>
                                <div id="faq-{{ $info->id }}" class="accordion-collapse collapse"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body bg-light">
                                        {!! $info->description !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- Sidebar -->
                <div class="col-lg-3">
                    <div class="card shadow-sm border-0 rounded-3 sticky-top" style="top: 20px;">
                        <div class="card-body">
                            <h4 class="card-title">
                                <i class="fas fa-question-circle me-2"></i> Ainda com dúvidas?
                            </h4>
                            <p class="card-text">Entre em contato com nosso suporte através do e-mail ou telefone</p>

                            <div class="mb-3">
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="bi bi-envelope me-2"></i> email@email.com
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-telephone me-2"></i> (67) 3333-3333
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="pagination-wrapper">
        {{ $information->links() }}
    </div>
@endsection

@extends('templates.web')
@section('content')
    <section>
        <div class="content-title">
            <h1>Minhas Aulas</h1>
            <p class="sub-title">Veja as aulas que você se inscreveu</p>
        </div>
    </section>
    @include('web.includes.search_form', [
        'title' => 'Pesquisar minhas aulas...',
        'action' => ''
    ])
    @include('web.includes.class_template')
    <div class="pagination-wrapper">
        {{ $lessons->links() }}
    </div>
@endsection

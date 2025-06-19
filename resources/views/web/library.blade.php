@extends('templates.web')
@section('content')
    <section>
        <div class="content-title">
            <h1>Biblioteca</h1>
            <p class="sub-title">Artigos e publicações recentes</p>
        </div>
    </section>
    @include('web.includes.search_form', [
        'action' => route('web.library'),
        'title' => 'Pesquisar publicaçõe...',
    ])
    <section>

    </section>
@endsection

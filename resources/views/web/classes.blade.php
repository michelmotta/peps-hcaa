@extends('templates.web')
@section('content')
    <section>
        <div class="content-title">
            <h1>Aulas</h1>
            <p class="sub-title">Descubra aulas e aprenda conforme suas necessidades</p>
        </div>
    </section>
    @include('web.includes.class_filter_form', [
        'specialties' => $specialties,
        'teachers' => $teachers,
    ])
    @include('web.includes.class_template', ['lessons' => $lessons])
    <div class="pagination-wrapper">
        {{ $lessons->links() }}
    </div>
@endsection

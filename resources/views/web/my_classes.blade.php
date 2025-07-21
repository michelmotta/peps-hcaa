@extends('templates.web')

@section('content')
    <section>
        <div class="content-title">
            <h1>Minhas Aulas</h1>
            <p class="sub-title">Veja as aulas que você se inscreveu</p>
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

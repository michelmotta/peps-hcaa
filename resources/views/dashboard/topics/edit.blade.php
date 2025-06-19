@extends('templates.dashboard')
@section('content')
    <section>
        <div class="bg-primary rounded-3 mt-n6 mx-n4">
            <div class="mb-5 p-10">
                <h1 class="mb-0 text-white text-center ">
                    <i data-feather="list" class="nav-icon me-2 icon-md"></i>
                    Tópico da Aula: {{ $lesson->name }}
                </h1>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-12">
                    <a href="{{ route('dashboard.lessons.topics.index', $lesson) }}" class="btn btn-primary btn-md mb-5">
                        <i data-feather="arrow-left" class="nav-icon me-2 icon-xs"></i>
                        Voltar
                    </a>
                    <h4 class="mb-5 fs-3">
                        <i data-feather="chevrons-right" class="me-2 icon-xs"></i>
                        Editar Tópico
                    </h4>
                    <form method="POST" action="{{ route('dashboard.lessons.topics.update', [$lesson, $topic]) }}"
                        enctype="multipart/form-data">
                        @method('PATCH')
                        @include('dashboard.topics.includes.form', ['edit' => true])
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@extends('templates.dashboard')
@section('content')
    <section>
        <div class="bg-primary rounded-3 mt-n6 mx-n4">
            <div class="p-10">
                <h1 class="mb-0 text-white text-center ">
                    <i data-feather="list" class="nav-icon me-2 icon-md"></i>
                    Tópico da Aula: {{ $lesson->name }}
                </h1>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-12">
                    <a href="{{ route('dashboard.lessons.topics.index', $lesson) }}" class="btn btn-outline-primary btn-md mb-5">
                        <i data-feather="arrow-left" class="nav-icon me-2 icon-xs"></i>
                        Voltar
                    </a>
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

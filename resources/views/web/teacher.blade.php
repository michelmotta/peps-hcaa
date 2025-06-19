@extends('templates.web')
@section('content')
    <section>
        <div class="content-title">
            <h1>Professor</h1>
            <p class="sub-title">Saiba mais sobre nossos professores</p>
        </div>
    </section>
    <section class="professor-profile py-5 bg-light">
        <div class="container">
            <!-- Hero Section -->
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="row g-0 align-items-center">
                        <!-- Professor Thumbnail -->
                        <div class="col-md-3">
                            <div class="professor-thumbnail-wrapper">
                                <img src="{{ asset('storage/' . $teacher->file->path) }}" class="img-fluid professor-thumbnail">
                            </div>
                        </div>
                        <!-- Professor Info -->
                        <div class="col-md-9">
                            <div class="p-4">
                                <h3>{{ $teacher->name }}</h3>
                                <h5 class="text-muted">{{ $teacher->expertise }}</h5>
                                <p class="mt-3 text-muted">
                                    {!! $teacher->biography !!}
                                </p>

                                <!-- Estudantes and Aulas Section -->
                                <div class="mt-4">
                                    <span class="me-3">
                                        <i class="bi bi-mortarboard"></i> 
                                        {{ $teacher->createdLessons->flatMap->students->unique('id')->count() }} Estudantes
                                    </span>
                                    <span>
                                        <i class="bi bi-journal-text"></i> 
                                        {{ $teacher->createdLessons->count() }} Aulas
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <hr>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="container recent-classes">
            <div class="col-md-12">
                <h3 class="mt-5">Aulas do Professor</h3>
            </div>
        </div>
    </section>
    @include('web.includes.class_template', $lessons)
    <div class="pagination-wrapper">
        {{ $lessons->links() }}
    </div>
@endsection

@extends('templates.web')
@section('content')
    <section class="home-banner">
        <div class="container-fluid">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <img src="{{ asset('images/logo-home.png') }}" alt="">
                        <h1>HCAA | PEPS</h1>
                        <h2>Programa de Educação Permanente em Saúde</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="cards-infos">
        <div class="container-fluid">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card-info card-yellow">
                            <div>
                                <i class="bi bi-collection-play"></i>
                            </div>
                            <div>
                                <span>Aulas Publicadas</span>
                                <strong>{{$lessonsCount}}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-info card-green">
                            <div>
                                <i class="bi bi-mortarboard"></i>
                            </div>
                            <div>
                                <span>Estudantes</span>
                                <strong>{{$studentsCount}}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-info card-orange">
                            <div>
                                <i class="bi bi-person-vcard"></i>
                            </div>
                            <div>
                                <span>Professores</span>
                                <strong>{{$teachersCount}}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="especialidades-slider">
        <div class="container-fluid">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h3>Navegue por Especialidades</h3>
                        <div class="custom-owl-controls text-center">
                            <button class="btn btn-outline-primary me-2 owl-prev">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button class="btn btn-outline-primary owl-next">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                        <div class="owl-carousel owl-theme">
                            @foreach ($specialties as $specialty)
                                <a href="{{ route('web.classes', ['specialty_id' => $specialty->id]) }}">
                                    <div class="item">
                                        <img src="{{ asset('storage/' . optional($specialty->file)->path ?? 'images/default-specialty.jpg') }}" alt="">
                                        <h4>{{ $specialty->name }}</h4>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="container recent-classes">
            <div class="col-md-12">
                <h3>Aulas Recentes</h3>
            </div>
        </div>
    </section>
    @include('web.includes.class_template')
@endsection

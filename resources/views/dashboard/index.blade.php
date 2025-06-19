@extends('templates.dashboard')
@section('content')
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="row mb-5 ">
            <div class="col-lg-12 col-md-12 col-12">
                <div class="p-6 d-lg-flex justify-content-between align-items-center ">
                    <div class="d-md-flex align-items-center">
                        <img src="{{ asset('storage/' . Auth::user()->file->path) }}" alt="Image"
                            class="rounded-circle avatar avatar-xl">
                        <div class="ms-md-4 mt-3 mt-md-0 lh-1">
                            <h3 class="text-white mb-0">Seja bem-vindo(a), {{ Auth::user()->name }}</h3>
                            <small class="text-white">{{ Auth::user()->profiles->pluck('name')->implode(' | ') }}</small>
                        </div>
                        
                    </div>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-12">
                <!-- Page header -->
                <div class="mb-5 text-center">
                    <div class="mb-15">
                        <h2 class="mb-0 text-white" style="font-size: 55px">HCAA | PEPS</h2>
                        <h3 class="text-white">Programa de Educação Permanente em Saúde</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-n10">
        <div class="row">
            <div class="col-xl-3 col-lg-6 col-md-12 col-12 mb-5">
                <!-- card -->
                <div class="card h-100 card-lift">
                    <!-- card body -->
                    <div class="card-body">
                        <!-- heading -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h3 class="mb-0">Aulas Publicadas</h3>
                            </div>
                            <div class="icon-shape icon-md bg-primary-soft text-primary rounded-2">
                                <i data-feather="youtube" height="20" width="20"></i>
                            </div>
                        </div>
                        <!-- project number -->
                        <div class="lh-1">
                            <h1 class="mb-1 fw-bold text-center">100+</h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-12 col-12 mb-5">
                <!-- card -->
                <div class="card h-100 card-lift">
                    <!-- card body -->
                    <div class="card-body">
                        <!-- heading -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h3 class="mb-0">Estudantes</h3>
                            </div>
                            <div class="icon-shape icon-md bg-primary-soft text-primary rounded-2">
                                <i data-feather="users" height="20" width="20"></i>
                            </div>
                        </div>
                        <!-- project number -->
                        <div class="lh-1">
                            <h1 class="mb-1 fw-bold text-center">{{ $studentsCount }}</h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-12 col-12 mb-5">
                <!-- card -->
                <div class="card h-100 card-lift">
                    <!-- card body -->
                    <div class="card-body">
                        <!-- heading -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h3 class="mb-0">Professores</h3>
                            </div>
                            <div class="icon-shape icon-md bg-primary-soft text-primary rounded-2">
                                <i data-feather="user" height="20" width="20"></i>
                            </div>
                        </div>
                        <!-- project number -->
                        <div class="lh-1">
                            <h1 class="mb-1 fw-bold text-center">{{ $teachersCount }}</h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-12 col-12 mb-5">
                <!-- card -->
                <div class="card h-100 card-lift">
                    <!-- card body -->
                    <div class="card-body">
                        <!-- heading -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h3 class="mb-0">Sugestões de Aulas</h3>
                            </div>
                            <div class="icon-shape icon-md bg-primary-soft text-primary rounded-2">
                                <i data-feather="hash" height="20" width="20"></i>
                            </div>
                        </div>
                        <!-- project number -->
                        <div class="lh-1">
                            <h1 class="mb-1 fw-bold text-center">{{ $suggestionsCount }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

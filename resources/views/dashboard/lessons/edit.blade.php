@extends('templates.dashboard')
@section('content')
    <section>
        <div class="bg-primary rounded-3 mt-n6 mx-n4">
            <div class="mb-5 p-10">
                <h1 class="mb-0 text-white text-center ">
                    <i data-feather="bookmark" class="nav-icon me-2 icon-md"></i>
                    Gerenciar Aulas
                </h1>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-12">
                    <!-- Card -->
                    <div class="card border-0 mb-4">
                        <!-- Card header -->
                        <div class="card-header">
                            <h4 class="mb-0 fs-3">
                                <i data-feather="chevrons-right" class="me-2 icon-xs"></i>
                                Editar Aula
                            </h4>
                        </div>
                        <!-- Card body -->
                        <div class="card-body">
                            <div class="mt-4">
                                <form method="POST" action="{{ route('dashboard.lessons.update', $lesson) }}"
                                    enctype="multipart/form-data">
                                    @method('PATCH')
                                    @include('dashboard.lessons.includes.form', ['edit' => true])
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

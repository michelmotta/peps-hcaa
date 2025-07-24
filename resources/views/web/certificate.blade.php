@extends('templates.web')

@section('content')
    <section class="certificate-validator-page">
        <div class="content-title">
            <h1>Validar Certificado</h1>
            <p class="sub-title">Insira o código de autenticidade para verificar a validade de um certificado.</p>
        </div>

        <div class="container">
            <div class="validator-form-wrapper">
                <div class="form-header">
                    <i class="bi bi-shield-lock form-header-icon"></i>
                    <h3 class="form-title">Validação de Certificado</h3>
                    <p class="form-subtitle">Digite o código de verificação único encontrado em seu certificado.</p>
                </div>

                <form action="{{ route('web.validate.certificate') }}" method="POST" class="validator-form" id="uuidSearchForm">
                    @csrf
                    <div class="input-group-lg d-flex flex-column">
                        <div class="d-flex">
                            <input type="text" name="uuid" id="uuidInput"
                                class="form-control @error('uuid') is-invalid @enderror"
                                placeholder="Insira o código de validação aqui..." value="{{ old('uuid', $searchedUuid) }}"
                                required>
                            <button class="btn btn-primary" type="submit" aria-label="Verificar">
                                <i class="bi bi-search"></i>
                                <span>Validar</span>
                            </button>
                        </div>
                        @error('uuid')
                            <small class="text-danger mt-1">{{ $message }}</small>
                        @enderror
                    </div>
                </form>
            </div>
        </div>

        @if (request()->has('uuid'))
            <div class="modal fade" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-body p-0">
                            @if ($certificate)
                                <div class="result-card is-valid">
                                    <div class="card-header">
                                        <i class="bi bi-check-circle-fill"></i>
                                        Certificado Autêntico
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="card-body">
                                        <p class="intro-text">Confirmamos a autenticidade do certificado com os seguintes
                                            dados:</p>
                                        <div class="detail-item">
                                            <span class="detail-label">
                                                {{ $certificate->type === 'teacher' ? 'Professor(a):' : 'Estudante:' }}
                                            </span>
                                            <span
                                                class="detail-value">{{ $certificate->user->name ?? 'Não especificado' }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Aula Concluída:</span>
                                            <span
                                                class="detail-value">{{ $certificate->lesson->name ?? 'Não especificado' }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Carga Horária:</span>
                                            <span class="detail-value">{{ $certificate->lesson->workload }} Horas</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Data de Emissão:</span>
                                            <span class="detail-value">
                                                {{ $certificate->issued_at ? \Carbon\Carbon::parse($certificate->issued_at)->format('d/m/Y') : $certificate->created_at->format('d/m/Y') }}
                                            </span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Código de Verificação:</span>
                                            <span class="detail-value uuid">{{ $certificate->uuid }}</span>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        Emitido por: Hospital de Câncer de Campo Grande - Alfredo Abrão
                                    </div>
                                </div>
                            @else
                                <div class="result-card is-invalid">
                                    <div class="card-header">
                                        <i class="bi bi-x-octagon-fill"></i>
                                        Certificado Inválido
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="card-body">
                                        <p class="intro-text">Não foi possível encontrar um certificado com o código
                                            informado:</p>
                                        <p class="invalid-uuid">{{ $searchedUuid }}</p>
                                        <p class="suggestion-text">Verifique se o código foi digitado corretamente e tente
                                            novamente.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>
@endsection

@if (request()->has('uuid') && !$errors->has('uuid'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const resultModalEl = document.getElementById('resultModal');
            if (resultModalEl) {
                const resultModal = new bootstrap.Modal(resultModalEl);
                resultModal.show();
            }
        });
    </script>
@endif

<section class="avaliacao-section">
    <div class="container text-center pt-5 pb-5">
        <h2 class="mb-3">Certificado de Conclusão</h2>
        @if (Gate::allows('generateCertificate', $lesson))
            <a href="{{ route('web.certificates.generate', $lesson->id) }}"
                class="btn btn-primary btn-lg d-inline-flex align-items-center gap-2" target="_blank" rel="noopener">
                <i class="bi bi-patch-check"></i> Gerar Certificado
            </a>
        @else
            <p class="mb-4">
                Você poderá gerar o certificado assim que concluir o quiz.
            </p>
        @endif
    </div>
</section>

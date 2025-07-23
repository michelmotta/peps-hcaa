<section class="certificate-section">
    <div class="container text-center py-5">
        @if (Gate::allows('canGenerateStudentCertificate', $lesson))
            <div class="certificate-content available">
                <div class="certificate-icon">
                    <i class="bi bi-award"></i>
                </div>
                <h2 class="certificate-title">Certificado de Conclusão</h2>
                <p class="certificate-text">Parabéns! Você concluiu os requisitos desta aula e seu certificado está
                    disponível.</p>
                <a href="{{ route('web.certificates.generate', $lesson->id) }}"
                    class="btn btn-primary btn-lg generate-cert-btn" target="_blank" rel="noopener">
                    <i class="bi bi-download"></i>
                    Baixar Certificado
                </a>
            </div>
        @else
            <div class="certificate-content unavailable">
                <div class="certificate-icon">
                    <i class="bi bi-lock-fill"></i>
                </div>
                <h2 class="certificate-title">Certificado Pendente</h2>
                <p class="certificate-text">
                    Para desbloquear seu certificado, você precisa completar e ser aprovado no quiz desta aula.
                </p>
            </div>
        @endif
    </div>
</section>

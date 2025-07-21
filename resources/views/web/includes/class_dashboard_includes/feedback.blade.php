<section class="avaliacao-section">
    <h2 class="text-center mb-4">Avaliação da Aula</h2>
    <div id="avaliacao-wrapper">
        <p class="text-center">Contribua com a melhoria da qualidade desta aula. Aqui você
            pode sugerir melhorias e dar um feedback diretamenta ao professor. Essa
            avaliação ficará visível apenas para você e o professor.</p>
        @if ($feedback === null)
            <div id="avaliacao-status" class="mb-4"></div>
            <form id="avaliacao-form" data-lesson-id="{{ $lesson->id }}" class="avaliacao-form mx-auto">
                @csrf
                <div class="mb-4 text-center">
                    <label class="form-label d-block">Dê a sua nota:</label>
                    <div class="star-rating">
                        @for ($i = 5; $i >= 1; $i--)
                            <input type="radio" name="rating" id="star{{ $i }}"
                                value="{{ $i }}">
                            <label for="star{{ $i }}">&#9733;</label>
                        @endfor
                    </div>
                </div>
                <div class="mb-4">
                    <label for="comentario" class="form-label">Comentário
                        (opcional)</label>
                    <textarea class="form-control" name="comentario" id="comentario" rows="4"></textarea>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Enviar
                        Avaliação</button>
                </div>
            </form>
        @else
            <div class="text-center">
                <p class="mb-2">Você avaliou esta aula com:</p>
                <div class="star-rating read-only mb-3">
                    @for ($i = 5; $i >= 1; $i--)
                        <span style="color: {{ $i <= $feedback->rating ? '#ffc107' : '#ccc' }}">&#9733;</span>
                    @endfor
                </div>

                @if ($feedback->comentario)
                    <div class="alert alert-light border text-muted">
                        <strong>Comentário:</strong><br>
                        {{ $feedback->comentario }}
                    </div>
                @endif
            </div>
        @endif
</section>

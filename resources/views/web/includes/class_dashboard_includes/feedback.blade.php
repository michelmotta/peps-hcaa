<section class="feedback-component">
    <div class="feedback-header">
        <h2 class="title"><span>Avaliação da Aula</span></h2>
        <p class="subtitle">Sua perspectiva é fundamental para aprimorarmos a qualidade do nosso conteúdo.</p>
    </div>

    <div class="feedback-body">
        @if ($feedback === null)
            <div id="feedback-status"></div>
            <form id="feedback-form" data-lesson-id="{{ $lesson->id }}" novalidate>
                @csrf
                <div class="form-group">
                    <label class="group-label">1. Qual a sua avaliação geral?</label>
                    <div class="star-rating">
                        @for ($i = 5; $i >= 1; $i--)
                            <input class="star-input" type="radio" name="rating" id="star{{ $i }}"
                                value="{{ $i }}" required>
                            <label class="star-label" for="star{{ $i }}"
                                title="{{ $i }} estrelas"><i class="bi bi-star-fill"></i></label>
                        @endfor
                    </div>
                </div>
                <div class="form-group">
                    <label for="comment-textarea" class="group-label">2. Deixe um comentário (opcional)</label>
                    <textarea id="comment-textarea" name="comentario" class="textarea" rows="4" placeholder="Escreva sua sugestão..."></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="submit-button">
                        <i class="bi bi-send-fill"></i>
                        <span>Enviar Avaliação</span>
                    </button>
                </div>
            </form>
        @else
            <div class="feedback-summary">
                <div class="summary-header">
                    <i class="bi bi-check-circle-fill"></i>
                    <h3>Sua avaliação foi registrada. Obrigado!</h3>
                </div>
                <div class="summary-content">
                    <div class="summary-item">
                        <h4 class="item-title">Sua Avaliação</h4>
                        <div class="stars-display">
                            @for ($i = 1; $i <= 5; $i++)
                                <i
                                    class="bi bi-star-fill star-display {{ $i <= $feedback->rating ? 'is-filled' : '' }}"></i>
                            @endfor
                        </div>
                    </div>
                    @if ($feedback->comentario)
                        <div class="summary-item">
                            <h4 class="item-title">Seu Comentário</h4>
                            <blockquote class="comment-display">{{ $feedback->comentario }}</blockquote>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</section>

<section class="comunicados-section">
    <div class="container">
        <h2 class="section-title">
            <span>Comunicados do Professor</span>
        </h2>

        <div class="comunicados-feed">
            @forelse ($lesson->messages as $message)
                <div class="comunicado-card">
                    <div class="comunicado-header">
                        <img src="{{ $lesson->teacher->file ? asset('storage/' . $lesson->teacher->file->path) : 'https://placehold.co/48x48/133b6a/FFFFFF?text=' . strtoupper(substr($lesson->teacher->name, 0, 1)) }}"
                            alt="{{ $lesson->teacher->name }}" class="comunicado-avatar">
                        <div class="comunicado-meta">
                            <h3 class="comunicado-subject">{{ $message->subject }}</h3>
                            <p class="comunicado-author">
                                Enviado por <strong>{{ $lesson->teacher->name }}</strong> em
                                {{ $message->created_at->format('d/m/Y \à\s H:i') }}
                            </p>
                        </div>
                    </div>
                    <div class="comunicado-body">
                        {!! $message->description !!}
                    </div>
                </div>
            @empty
                <div class="comunicado-empty">
                    <i class="bi bi-bell-slash"></i>
                    <h4>Nenhum comunicado por aqui ainda</h4>
                    <p>Quando o professor enviar uma mensagem, ela aparecerá aqui.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<x-app-layout>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center">
                <i class="fas fa-gavel fa-2x text-primary me-3"></i>
                <div>
                    <h1 class="h3 mb-1 fw-bold">👩‍⚖️ Évaluation Rapports (Grille Cameroun)</h1>
                    <p class="text-muted mb-0">
                        {{ $reports->count() }} rapport(s) à évaluer
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-start border-success border-5" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>{{ session('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($reports->count() === 0)
        <div class="text-center py-12">
            <i class="fas fa-file-circle-question fa-4x text-muted mb-4 opacity-75"></i>
            <h3 class="h4 text-muted mb-3">📭 Aucun rapport affecté</h3>
            <p class="text-muted lead">Les rapports vous seront assignés après validation enseignant.</p>
        </div>
    @else
        @foreach($reports as $report)
        <div class="card shadow-lg border-0 mb-6 overflow-hidden">
            <div class="card-header bg-gradient-primary text-white position-relative overflow-hidden">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="h4 mb-1 fw-bold">
                            <i class="fas fa-file-alt me-2 opacity-75"></i>
                            {{ Str::limit($report->title, 60) }}
                        </h3>
                        <div class="d-flex flex-wrap gap-2 small">
                            <span class="badge bg-light text-dark">
                                👨‍🎓 {{ $report->student->name ?? 'Étudiant' }}
                            </span>
                            <span class="badge bg-info">
                                👨‍🏫 {{ $report->teacher->name ?? 'Enseignant' }}
                            </span>
                            <span class="badge bg-warning text-dark">
                                {{ $report->created_at->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end mt-2 mt-md-0">
                        <a href="{{ asset('storage/' . $report->file_path) }}"
                           class="btn btn-outline-light btn-sm px-4" target="_blank">
                            <i class="fas fa-file-pdf me-1"></i> PDF
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <!-- Commentaire Enseignant -->
                @if($report->teacher_comment)
                <div class="p-5 bg-light border-bottom">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="fas fa-comment-dots me-2"></i>Commentaire Enseignant
                    </h6>
                    <div class="bg-white p-4 rounded border-start border-primary border-4">
                        <p class="mb-2">"{{ Str::limit($report->teacher_comment, 200) }}"</p>
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            {{ $report->updated_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                </div>
                @endif

                <!-- Formulaire Évaluation -->
                <form method="POST" action="{{ url('/reports/' . $report->id . '/jury-evaluate') }}" class="p-6">
                    @csrf

                    <div class="row g-4 mb-5">
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-primary mb-3">
                                📝 Technique (sur 20)
                            </label>
                            <input type="number" name="jury_technical_note"
                                   min="0" max="20" step="0.5" required
                                   class="form-control form-control-lg border-2 border-primary-subtle rounded-3 shadow-sm focus-ring focus-ring-purple"
                                   placeholder="Ex: 15.5">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-success mb-3">
                                🎨 Présentation (sur 20)
                            </label>
                            <input type="number" name="jury_presentation_note"
                                   min="0" max="20" step="0.5" required
                                   class="form-control form-control-lg border-2 border-success-subtle rounded-3 shadow-sm focus-ring focus-ring-purple"
                                   placeholder="Ex: 14.0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-info mb-3">
                                📚 Contenu (sur 20)
                            </label>
                            <input type="number" name="jury_content_note"
                                   min="0" max="20" step="0.5" required
                                   class="form-control form-control-lg border-2 border-info-subtle rounded-3 shadow-sm focus-ring focus-ring-purple"
                                   placeholder="Ex: 16.5">
                        </div>
                    </div>

                    <div class="row g-4 mb-5">
                        <div class="col-md-8">
                            <label class="form-label fw-bold text-secondary mb-3">
                                💬 Commentaire détaillé
                            </label>
                            <textarea name="jury_comment" rows="4"
                                      class="form-control border-2 border-secondary-subtle rounded-3 shadow-sm focus-ring focus-ring-purple"
                                      placeholder="Analyse détaillée de la qualité du rapport..."></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-danger mb-3">
                                ⚖️ Décision finale
                            </label>
                            <select name="jury_decision" class="form-select form-select-lg border-2 border-danger-subtle rounded-3 shadow-sm focus-ring focus-ring-purple" required>
                                <option value="">Choisir décision</option>
                                <option value="Validé">✅ Validé</option>
                                <option value="Rejeté">❌ Rejeté</option>
                                <option value="À revoir">⚠️ À revoir</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-center pt-4">
                        <button type="submit"
                                class="btn btn-primary btn-lg px-8 py-3 fw-bold rounded-pill shadow-lg border-0">
                            <i class="fas fa-graduation-cap me-2"></i>
                            FINALISER ÉVALUATION
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
    @endif
</div>
</x-app-layout>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.focus-ring:focus {
    box-shadow: 0 0 0 0.25rem rgba(147, 51, 234, 0.25) !important;
}

.focus-ring-purple:focus {
    box-shadow: 0 0 0 0.375rem rgba(139, 69, 193, 0.25) !important;
}
</style>

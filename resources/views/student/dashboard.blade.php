<x-app-layout>
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h3 mb-1 fw-bold">📋 Mes Rapports de Stage</h1>
                    <p class="text-muted mb-0">{{ $reports->count() }} rapport(s) au total</p>
                </div>
                <a href="{{ url('/student/reports/create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nouveau Rapport
                </a>
            </div>
        </div>
    </div>

    <!-- Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-start border-success border-5 mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <strong>✅ {{ session('success') }}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($reports->count() === 0)
        <div class="text-center py-8 bg-light rounded-3">
            <i class="fas fa-file-plus fa-4x text-muted mb-4 opacity-75"></i>
            <h4 class="text-muted mb-3">Aucun rapport soumis</h4>
            <p class="lead text-muted mb-4">Commencez par soumettre votre premier rapport de stage.</p>
            <a href="{{ url('/student/reports/create') }}" class="btn btn-primary btn-lg px-5">
                <i class="fas fa-file-upload me-2"></i>Soumettre Rapport
            </a>
        </div>
    @else
        @foreach($reports as $report)
        <div class="card shadow-lg border-0 mb-5 overflow-hidden">
            <!-- Header Rapport -->
            <div class="card-header bg-gradient-primary text-white p-4 position-relative">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <h4 class="mb-2 fw-bold">{{ $report->title }}</h4>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-dark fs-6 px-3 py-2">
                                <i class="fas fa-circle me-1 text-success small"></i>
                                {{ $report->status }}
                            </span>
                            <span class="badge bg-warning fs-6 px-3 py-2">
                                {{ $report->latestVersion->version ?? 'v1' }}
                            </span>
                            @if($report->teacher)
                            <span class="badge bg-info text-white fs-6 px-3 py-2">
                                👨‍🏫 {{ $report->teacher->name }}
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-5 text-md-end mt-2 mt-md-0">
                        <div class="btn-group" role="group">
                            <a href="{{ asset('storage/' . ($report->latestVersion->file_path ?? $report->file_path)) }}"
                               class="btn btn-outline-light btn-sm px-3" target="_blank">
                                <i class="fas fa-file-pdf me-1"></i>Version Actuelle
                            </a>
                            @if($report->jury_final_score)
                            <button class="btn btn-success btn-sm px-3" data-bs-toggle="modal"
                                    data-bs-target="#noteModal{{ $report->id }}">
                                <i class="fas fa-award me-1"></i>{{ $report->jury_final_score }}/20
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <!-- Historique Versions -->
                <div class="p-4 border-bottom bg-light">
                    <h6 class="fw-bold text-primary mb-4">
                        <i class="fas fa-history me-2"></i>📜 Historique des Versions
                    </h6>
                    <div class="row g-3">
                        @forelse($report->versions->take(6) as $version)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm hover-shadow-lg">
                                <div class="card-body py-3 px-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <span class="badge bg-{{ $version->action === 'validé' ? 'success' : ($version->action === 'commenté' ? 'info' : 'warning') }}">
                                            {{ $version->version }}
                                        </span>
                                        <small class="text-muted">{{ $version->created_at->diffForHumans() }}</small>
                                    </div>
                                    <div class="fw-bold mb-2">{{ ucfirst($version->action) }}</div>
                                    <div class="text-muted small mb-3">{{ $version->user->name }}</div>
                                    @if($version->comment)
                                    <div class="small text-truncate mb-2" style="max-height: 40px; overflow: hidden;">
                                        "{{ Str::limit($version->comment, 50) }}"
                                    </div>
                                    @endif
                                    <a href="{{ asset('storage/' . $version->file_path) }}"
                                       class="btn btn-sm btn-outline-primary w-100" target="_blank">
                                        📥 Télécharger
                                    </a>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-clock fa-2x mb-3 opacity-50"></i>
                                <p>Aucune version disponible</p>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Commentaire Enseignant -->
                @if($report->teacher_comment)
                <div class="p-5 bg-gradient-info bg-opacity-10 border-start border-info border-4">
                    <div class="row align-items-center">
                        <div class="col-md-1 text-center">
                            <i class="fas fa-comment-dots fa-2x text-info"></i>
                        </div>
                        <div class="col-md-11">
                            <h6 class="fw-bold text-info mb-3">💬 Commentaire Enseignant</h6>
                            <blockquote class="mb-0">
                                <p class="lead">"{{ $report->teacher_comment }}"</p>
                            </blockquote>
                            <small class="text-muted">
                                <i class="fas fa-user-tie me-1"></i>
                                {{ $report->teacher->name ?? 'Enseignant' }}
                                • {{ $report->updated_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Action: Corriger -->
                @if(in_array($report->status, ['En attente étudiant', 'À corriger']))
                <div class="p-5 bg-warning bg-opacity-10 border-0">
                    <div class="alert alert-warning border-0 shadow-sm">
                        <h5 class="alert-heading fw-bold mb-4">
                            <i class="fas fa-edit me-2 text-warning"></i>
                            📝 Version à corriger
                        </h5>
                        <form method="POST" action="{{ url('/student/reports/' . $report->id . '/resubmit') }}"
                              enctype="multipart/form-data" class="row g-4 align-items-end">
                            @csrf
                            <div class="col-lg-8">
                                <label class="form-label fw-bold fs-5 mb-3 text-warning">
                                    📤 Nouvelle version corrigée (PDF uniquement)
                                </label>
                                <input type="file" name="file" class="form-control form-control-lg"
                                       accept=".pdf,.docx" required>
                                <div class="form-text">Max 10Mo - Corrigez selon les commentaires de l'enseignant</div>
                            </div>
                            <div class="col-lg-4">
                                <button type="submit" class="btn btn-warning btn-lg w-100 py-3 fw-bold shadow-lg">
                                    <i class="fas fa-upload me-2"></i>
                                    🚀 Envoyer Correction
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

                <!-- Note Finale -->
                @if($report->jury_final_score)
                <div class="p-4 bg-gradient-success bg-opacity-10 border-0 text-center">
                    <div class="alert alert-success shadow-lg border-0">
                        <h3 class="display-5 fw-bold mb-2 text-success">
                            <i class="fas fa-graduation-cap"></i> {{ $report->jury_final_score }}/20
                        </h3>
                        <div class="h2 fw-bold text-success mb-3">
                            {{ $report->jury_appreciation ?? 'Excellent' }}
                        </div>
                        <p class="lead mb-0">
                            <i class="fas fa-trophy text-warning me-2"></i>
                            Félicitations ! Rapport validé final
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    @endif
</div>
</x-app-layout>

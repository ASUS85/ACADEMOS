<x-app-layout>
    @php
    $currentUser = auth()->user();
    @endphp

    <style>
        .jury-hero {
            background: linear-gradient(135deg, #111827 0%, #1d4ed8 55%, #0f766e 100%);
            color: #fff;
        }

        .jury-panel {
            border: 0;
            border-radius: 1.5rem;
            box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.1), 0 10px 10px -5px rgba(15, 23, 42, 0.04);
        }

        .jury-preview-frame {
            width: 100%;
            height: 64vh;
            border: 0;
            background: #fff;
        }

        .jury-pill {
            border-radius: 999px;
            padding: .35rem .75rem;
            font-size: .75rem;
            font-weight: 600;
        }

        .jury-pill-primary {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .jury-pill-success {
            background: #dcfce7;
            color: #166534;
        }

        .jury-pill-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .jury-pill-muted {
            background: #e5e7eb;
            color: #374151;
        }

    </style>

    <div class="container-fluid py-4">
        <div class="card jury-hero border-0 shadow-lg rounded-4 overflow-hidden mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <span class="badge bg-white bg-opacity-20 text-white px-3 py-2 mb-3">Jury / Enseignant</span>
                        <h1 class="display-6 fw-bold mb-2">Évaluation des rapports</h1>
                        <p class="lead mb-0 opacity-90">Chaque membre du jury note le rapport individuellement. La moyenne finale se calcule automatiquement quand tous ont validé.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('reports.index') }}" class="btn btn-light btn-lg px-4 py-3 shadow-sm">
                            <i class="fas fa-sync-alt me-2"></i>Actualiser
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
            <i class="fas fa-circle-check me-2"></i>{{ session('success') }}
        </div>
        @endif

        @if ($reports->count() === 0)
        <div class="card jury-panel">
            <div class="card-body py-5 text-center">
                <i class="fas fa-file-circle-question fa-3x text-muted mb-3"></i>
                <h3 class="h5 fw-bold mb-2">Aucun rapport à évaluer</h3>
                <p class="text-muted mb-0">Les rapports apparaîtront ici dès qu’un jury vous sera attribué.</p>
            </div>
        </div>
        @else
        <div class="card jury-panel overflow-hidden">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h2 class="h5 fw-bold mb-1">Mes rapports à évaluer</h2>
                    <div class="text-muted small">{{ $reports->total() }} rapport(s) • page {{ $reports->currentPage() }} / {{ $reports->lastPage() }}</div>
                </div>
                <span class="jury-pill jury-pill-muted">{{ $currentUser->name }}</span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Étudiant</th>
                            <th>Titre</th>
                            <th>Progression</th>
                            <th>Ma note</th>
                            <th>Finale</th>
                            <th>Date</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                        @php
                        $juryTotal = $report->juryGroup?->members?->count() ?? 0;
                        $submitted = $report->juryEvaluations?->count() ?? 0;
                        $completion = $juryTotal > 0 ? round(($submitted / $juryTotal) * 100) : 0;
                        $myEvaluation = $report->juryEvaluations?->firstWhere('user_id', $currentUser->id);
                        $previewFile = $report->latestVersion?->file_path ?? $report->file_path;
                        $previewUrl = route('reports.preview', $report);
                        $downloadUrl = route('reports.download', $report);
                        $canPreview = $previewFile && str_ends_with(strtolower($previewFile), '.pdf');
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $report->id }}</td>
                            <td>
                                <div class="fw-semibold">{{ $report->student?->name ?? '-' }}</div>
                                <div class="text-muted small">{{ $report->student?->filiere?->name ?? '-' }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ \Illuminate\Support\Str::limit($report->title, 48) }}</div>
                                <div class="text-muted small">Encadreur: {{ $report->teacher?->name ?? 'Non affecté' }}</div>
                            </td>
                            <td>
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span>{{ $submitted }}/{{ $juryTotal }}</span>
                                    <span>{{ $completion }}%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $completion }}%"></div>
                                </div>
                            </td>
                            <td>
                                @if ($myEvaluation)
                                <span class="jury-pill jury-pill-success">{{ $myEvaluation->final_score }}/20</span>
                                @else
                                <span class="jury-pill jury-pill-warning">Non noté</span>
                                @endif
                            </td>
                            <td>
                                @if ($report->jury_final_score)
                                <div class="fw-semibold">{{ number_format($report->jury_final_score, 2) }}/20</div>
                                <div class="text-muted small">{{ $report->jury_appreciation ?? 'Note finale' }}</div>
                                @else
                                <span class="jury-pill jury-pill-muted">En attente</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $report->created_at->format('d/m/Y') }}</div>
                                <div class="text-muted small">{{ $report->status }}</div>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#juryModal{{ $report->id }}">
                                    <i class="fas fa-marker me-1"></i>{{ $myEvaluation ? 'Modifier' : 'Évaluer' }}
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $reports->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

    @foreach ($reports as $report)
    @php
    $juryTotal = $report->juryGroup?->members?->count() ?? 0;
    $submitted = $report->juryEvaluations?->count() ?? 0;
    $myEvaluation = $report->juryEvaluations?->firstWhere('user_id', $currentUser->id);
    $previewFile = $report->latestVersion?->file_path ?? $report->file_path;
    $previewUrl = route('reports.preview', $report);
    $downloadUrl = route('reports.download', $report);
    $canPreview = $previewFile && str_ends_with(strtolower($previewFile), '.pdf');
    @endphp
    <div class="modal fade" id="juryModal{{ $report->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg">
                <div class="modal-header bg-dark text-white border-0">
                    <div>
                        <h5 class="modal-title mb-0">{{ $report->title }}</h5>
                        <small class="text-white-50">Évaluer, prévisualiser et valider votre note</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-0">
                    <div class="row g-0">
                        <div class="col-lg-8 border-end">
                            <div class="p-4 border-bottom bg-light">
                                <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                                    <span class="jury-pill jury-pill-primary">{{ $report->status }}</span>
                                    <span class="jury-pill jury-pill-muted">{{ $submitted }}/{{ $juryTotal }} membre(s) ont noté</span>
                                    @if ($report->jury_final_score)
                                    <span class="jury-pill jury-pill-success">Finale: {{ number_format($report->jury_final_score, 2) }}/20</span>
                                    @endif
                                </div>
                                <div class="d-flex flex-wrap gap-2 small text-muted">
                                    <span><strong>Étudiant:</strong> {{ $report->student?->name ?? '-' }}</span>
                                    <span class="vr"></span>
                                    <span><strong>Encadreur:</strong> {{ $report->teacher?->name ?? 'Non affecté' }}</span>
                                    <span class="vr"></span>
                                    <span><strong>Filière:</strong> {{ $report->student?->filiere?->name ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="p-3">
                                @if ($canPreview)
                                <iframe src="{{ $previewUrl }}" class="jury-preview-frame rounded-4 shadow-sm mb-3"></iframe>
                                @else
                                <div class="alert alert-light border mb-3">Aperçu disponible uniquement pour les PDF.</div>
                                @endif

                                <div class="d-flex gap-2 flex-wrap">
                                    @if ($downloadUrl)
                                    <a href="{{ $downloadUrl }}" target="_blank" class="btn btn-outline-primary">
                                        <i class="fas fa-download me-2"></i>Télécharger
                                    </a>
                                    @endif
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="p-4">
                                <h6 class="fw-bold mb-3">Ma notation</h6>
                                <form method="POST" action="{{ url('/reports/' . $report->id . '/jury-evaluate') }}" class="vstack gap-3">
                                    @csrf
                                    <div>
                                        <label class="form-label fw-semibold">Technique /20</label>
                                        <input type="number" name="jury_technical_note" min="0" max="20" step="0.5" required class="form-control" value="{{ old('jury_technical_note', $myEvaluation?->technical_note) }}">
                                    </div>
                                    <div>
                                        <label class="form-label fw-semibold">Présentation /20</label>
                                        <input type="number" name="jury_presentation_note" min="0" max="20" step="0.5" required class="form-control" value="{{ old('jury_presentation_note', $myEvaluation?->presentation_note) }}">
                                    </div>
                                    <div>
                                        <label class="form-label fw-semibold">Contenu /20</label>
                                        <input type="number" name="jury_content_note" min="0" max="20" step="0.5" required class="form-control" value="{{ old('jury_content_note', $myEvaluation?->content_note) }}">
                                    </div>
                                    <div>
                                        <label class="form-label fw-semibold">Décision</label>
                                        <select name="jury_decision" class="form-select" required>
                                            <option value="">Choisir</option>
                                            <option value="Validé" @selected(old('jury_decision', $myEvaluation?->decision) === 'Validé')>Validé</option>
                                            <option value="Rejeté" @selected(old('jury_decision', $myEvaluation?->decision) === 'Rejeté')>Rejeté</option>
                                            <option value="À revoir" @selected(old('jury_decision', $myEvaluation?->decision) === 'À revoir')>À revoir</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form-label fw-semibold">Commentaire</label>
                                        <textarea name="jury_comment" rows="5" class="form-control" placeholder="Commentaire détaillé...">{{ old('jury_comment', $myEvaluation?->comment) }}</textarea>
                                    </div>
                                    <div class="alert alert-info border-0 mb-0">
                                        <strong>Progression:</strong> {{ $submitted }}/{{ $juryTotal }} membre(s) ont déjà noté.
                                        @if ($report->jury_final_score)
                                        <br><strong>Note finale:</strong> {{ number_format($report->jury_final_score, 2) }}/20
                                        @endif
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-check me-2"></i>{{ $myEvaluation ? 'Mettre à jour' : 'Valider ma note' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</x-app-layout>

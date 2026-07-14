<x-app-layout>
    @php
    $currentUser = auth()->user();
    @endphp

    <style>
        .jury-teacher-hero {
            background: linear-gradient(135deg, #111827 0%, #1d4ed8 55%, #0f766e 100%);
            color: #fff;
        }

        .jury-teacher-panel {
            border: 0;
            border-radius: 1.5rem;
            box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.1), 0 10px 10px -5px rgba(15, 23, 42, 0.04);
        }

        .jury-teacher-table thead th {
            background: #f8fafc;
            border-bottom: 0;
            color: #475569;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .jury-teacher-badge {
            border-radius: 999px;
            padding: .35rem .75rem;
            font-size: .75rem;
            font-weight: 600;
        }

        .jury-teacher-badge-primary {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .jury-teacher-badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .jury-teacher-badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .jury-teacher-badge-muted {
            background: #e5e7eb;
            color: #374151;
        }

        .jury-teacher-preview-frame {
            width: 100%;
            height: 64vh;
            border: 0;
            background: #fff;
        }

        .jury-score-modal {
            z-index: 1065;
        }

        .jury-score-backdrop {
            z-index: 1060;
        }

    </style>

    <div class="container-fluid py-4">
        <div class="card jury-teacher-hero border-0 shadow-lg rounded-4 overflow-hidden mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <span class="badge bg-white bg-opacity-20 text-white px-3 py-2 mb-3">Jury enseignant</span>
                        <h1 class="display-6 fw-bold mb-2">Rapports du jury</h1>
                        <p class="lead mb-0 opacity-90">Prévisualisez, téléchargez puis notez les rapports dans un modal superposé à la fiche de lecture.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('teacher.jury.index') }}" class="btn btn-light btn-lg px-4 py-3 shadow-sm">
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
        <div class="card jury-teacher-panel">
            <div class="card-body py-5 text-center">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h3 class="h5 fw-bold mb-2">Aucun rapport à noter</h3>
                <p class="text-muted mb-0">Les rapports de jury auxquels vous appartenez apparaîtront ici.</p>
            </div>
        </div>
        @else
        <div class="card jury-teacher-panel overflow-hidden">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h2 class="h5 fw-bold mb-1">Mes rapports de jury</h2>
                    <div class="text-muted small">{{ $reports->total() }} rapport(s) • page {{ $reports->currentPage() }} / {{ $reports->lastPage() }}</div>
                </div>
                <span class="jury-teacher-badge jury-teacher-badge-muted">{{ $currentUser->name }}</span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0 jury-teacher-table">
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
                        $reportPayloadData = base64_encode(json_encode($report->loadMissing(['student.filiere', 'teacher', 'latestVersion', 'comments.user', 'juryGroup.members', 'juryEvaluations.user'])));
                        $statusClass = match ($report->status) {
                        'Validé final', 'Validé' => 'jury-teacher-badge-success',
                        'En attente jury' => 'jury-teacher-badge-primary',
                        'Soumis', 'Affecté', 'commenté' => 'jury-teacher-badge-warning',
                        default => 'jury-teacher-badge-muted',
                        };
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
                                <span class="jury-teacher-badge jury-teacher-badge-success">{{ $myEvaluation->final_score }}/20</span>
                                @else
                                <span class="jury-teacher-badge jury-teacher-badge-warning">Non noté</span>
                                @endif
                            </td>
                            <td>
                                @if ($report->jury_final_score)
                                <div class="fw-semibold">{{ number_format($report->jury_final_score, 2) }}/20</div>
                                <div class="text-muted small">{{ $report->jury_appreciation ?? 'Note finale' }}</div>
                                @else
                                <span class="jury-teacher-badge jury-teacher-badge-muted">En attente</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $report->created_at->format('d/m/Y') }}</div>
                                <div class="text-muted small">{{ $report->status }}</div>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm px-3" data-report="{{ $reportPayloadData }}" data-preview-url="{{ $previewUrl }}" data-download-url="{{ $downloadUrl }}" data-can-preview="{{ $canPreview ? 1 : 0 }}" onclick="openJuryReportDetails(this, false)">
                                        <i class="fas fa-eye me-1"></i>Voir
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm px-3" data-report="{{ $reportPayloadData }}" data-preview-url="{{ $previewUrl }}" data-download-url="{{ $downloadUrl }}" data-can-preview="{{ $canPreview ? 1 : 0 }}" onclick="openJuryReportDetails(this, true)">
                                        <i class="fas fa-star me-1"></i>Noter
                                    </button>
                                </div>
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
    $reportPayloadData = base64_encode(json_encode($report->loadMissing(['student.filiere', 'teacher', 'latestVersion', 'comments.user', 'juryGroup.members', 'juryEvaluations.user'])));
    @endphp
    <div class="modal fade" id="teacherJuryDetailsModal{{ $report->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg">
                <div class="modal-header bg-dark text-white border-0">
                    <div>
                        <h5 class="modal-title mb-0">{{ $report->title }}</h5>
                        <small class="text-white-50">Prévisualiser, télécharger puis ouvrir le modal de notation</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-0">
                    <div class="row g-0">
                        <div class="col-lg-8 border-end">
                            <div class="p-4 border-bottom bg-light">
                                <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                                    <span class="jury-teacher-badge {{ $statusClass }}">{{ $report->status }}</span>
                                    <span class="jury-teacher-badge jury-teacher-badge-muted">{{ $submitted }}/{{ $juryTotal }} membre(s) ont noté</span>
                                    @if ($report->jury_final_score)
                                    <span class="jury-teacher-badge jury-teacher-badge-success">Finale: {{ number_format($report->jury_final_score, 2) }}/20</span>
                                    @endif
                                </div>
                                <div class="d-flex flex-wrap gap-2 small text-muted">
                                    <span><strong>Étudiant:</strong> {{ $report->student?->name ?? '-' }}</span>
                                    <span class="vr"></span>
                                    <span><strong>Encadreur:</strong> {{ $report->teacher?->name ?? 'Non affecté' }}</span>
                                    <span class="vr"></span>
                                    <span><strong>Filière:</strong> {{ $report->student?->filiere?->name ?? '-' }}</span>
                                </div>
                                <div class="mt-3">
                                    <div class="small text-muted mb-2">Membres du jury</div>
                                    <div class="d-flex flex-wrap gap-2">
                                        @forelse ($report->juryGroup?->members ?? collect() as $member)
                                        <span class="badge bg-light text-dark border px-3 py-2">{{ $member->name }} • {{ $member->pivot->role }}</span>
                                        @empty
                                        <span class="text-muted small">Aucun membre affecté</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <div class="p-3">
                                @if ($canPreview)
                                <iframe src="{{ $previewUrl }}" class="jury-teacher-preview-frame rounded-4 shadow-sm mb-3"></iframe>
                                @else
                                <div class="alert alert-light border mb-3">Aperçu disponible uniquement pour les PDF.</div>
                                @endif

                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="{{ $downloadUrl }}" target="_blank" class="btn btn-outline-primary">
                                        <i class="fas fa-download me-2"></i>Télécharger
                                    </a>
                                    <button type="button" class="btn btn-success" onclick="openJuryScoreModal({{ $report->id }})">
                                        <i class="fas fa-star me-2"></i>Noter
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="p-4">
                                <h6 class="fw-bold mb-3">Synthèse</h6>
                                <div class="small text-muted mb-2">Ma note</div>
                                <div class="fw-semibold mb-3">{{ $myEvaluation ? number_format((float) $myEvaluation->final_score, 2) . '/20' : 'Non noté' }}</div>

                                <div class="small text-muted mb-2">Décision</div>
                                <div class="fw-semibold mb-3">{{ $myEvaluation?->decision ?? 'En attente' }}</div>

                                <div class="small text-muted mb-2">Commentaire</div>
                                <div class="fw-semibold mb-3">{{ \Illuminate\Support\Str::limit($myEvaluation?->comment ?? 'Aucun commentaire', 90) }}</div>

                                <div class="alert alert-info border-0 mb-0">
                                    <strong>Progression:</strong> {{ $submitted }}/{{ $juryTotal }} membre(s) ont déjà noté.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade jury-score-modal" id="teacherJuryScoreModal{{ $report->id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg">
                <div class="modal-header bg-dark text-white border-0">
                    <div>
                        <h5 class="modal-title mb-0">Notation du rapport</h5>
                        <small class="text-white-50">{{ $report->title }}</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('reports.jury-evaluate', $report) }}" class="row g-3">
                        @csrf
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Technique /20</label>
                            <input type="number" name="jury_technical_note" min="0" max="20" step="0.5" required class="form-control" value="{{ old('jury_technical_note', $myEvaluation?->technical_note) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Présentation /20</label>
                            <input type="number" name="jury_presentation_note" min="0" max="20" step="0.5" required class="form-control" value="{{ old('jury_presentation_note', $myEvaluation?->presentation_note) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Contenu /20</label>
                            <input type="number" name="jury_content_note" min="0" max="20" step="0.5" required class="form-control" value="{{ old('jury_content_note', $myEvaluation?->content_note) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Décision</label>
                            <select name="jury_decision" class="form-select" required>
                                <option value="">Choisir</option>
                                <option value="Validé" @selected(old('jury_decision', $myEvaluation?->decision) === 'Validé')>Validé</option>
                                <option value="Rejeté" @selected(old('jury_decision', $myEvaluation?->decision) === 'Rejeté')>Rejeté</option>
                                <option value="À revoir" @selected(old('jury_decision', $myEvaluation?->decision) === 'À revoir')>À revoir</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Commentaire</label>
                            <textarea name="jury_comment" rows="6" class="form-control" placeholder="Commentaire détaillé...">{{ old('jury_comment', $myEvaluation?->comment) }}</textarea>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-info border-0 mb-0">
                                <strong>Progression:</strong> {{ $submitted }}/{{ $juryTotal }} membre(s) ont déjà noté.
                                @if ($report->jury_final_score)
                                <br><strong>Note finale:</strong> {{ number_format((float) $report->jury_final_score, 2) }}/20
                                @endif
                            </div>
                        </div>
                        <div class="col-12 d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check me-2"></i>{{ $myEvaluation ? 'Mettre à jour ma note' : 'Valider ma note' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <script>
        let currentJuryReportId = null;
        let currentJuryReportData = null;

        function openJuryReportDetails(button, openScore) {
            const rawPayload = button.dataset.report || '';
            let report;

            try {
                const decodedPayload = rawPayload.startsWith('ey') ? atob(rawPayload) : rawPayload;
                report = JSON.parse(decodedPayload);
            } catch (error) {
                console.error('Impossible de parser les données du rapport :', error);
                return;
            }

            currentJuryReportId = report.id;
            currentJuryReportData = report;

            const detailsModal = document.getElementById(`teacherJuryDetailsModal${report.id}`);

            if (openScore) {
                detailsModal.addEventListener('shown.bs.modal', () => {
                    openJuryScoreModal(report.id);
                }, {
                    once: true
                });
            }

            bootstrap.Modal.getOrCreateInstance(detailsModal).show();

            detailsModal.addEventListener('hidden.bs.modal', () => {
                currentJuryReportId = null;
                currentJuryReportData = null;
            }, {
                once: true
            });
        }

        function openJuryScoreModal(reportId) {
            const scoreModal = document.getElementById(`teacherJuryScoreModal${reportId}`);
            if (!scoreModal) {
                return;
            }

            bootstrap.Modal.getOrCreateInstance(scoreModal).show();
        }

        document.querySelectorAll('.jury-score-modal').forEach((modalEl) => {
            modalEl.addEventListener('show.bs.modal', () => {
                setTimeout(() => {
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    const backdrop = backdrops[backdrops.length - 1];
                    if (backdrop) {
                        backdrop.classList.add('jury-score-backdrop');
                    }
                }, 0);
            });

            modalEl.addEventListener('hidden.bs.modal', () => {
                currentJuryReportId = null;
            });
        });

    </script>
</x-app-layout>

<x-app-layout>
    @php
    $currentUser = auth()->user();
    @endphp

    <style>
        .teacher-reports-hero {
            background: linear-gradient(135deg, #0f172a 0%, #2563eb 55%, #0f766e 100%);
            color: #fff;
        }

        .teacher-reports-panel {
            border: 0;
            border-radius: 1.5rem;
            box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.1), 0 10px 10px -5px rgba(15, 23, 42, 0.04);
        }

        .teacher-table thead th {
            background: #f8fafc;
            border-bottom: 0;
            color: #475569;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .teacher-badge {
            border-radius: 999px;
            padding: .35rem .75rem;
            font-size: .75rem;
            font-weight: 600;
        }

        .teacher-badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .teacher-badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .teacher-badge-info {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .teacher-badge-muted {
            background: #e5e7eb;
            color: #374151;
        }

        .teacher-preview-frame {
            width: 100%;
            height: 66vh;
            border: 0;
            background: #fff;
        }

    </style>

    <div class="container-fluid py-4">
        <div class="card teacher-reports-hero border-0 shadow-lg rounded-4 overflow-hidden mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <span class="badge bg-white bg-opacity-20 text-white px-3 py-2 mb-3">Enseignant</span>
                        <h1 class="display-6 fw-bold mb-2">Mes rapports à corriger</h1>
                        <p class="lead mb-0 opacity-90">Tableau de suivi avec prévisualisation, téléchargement, commentaire et décision dans un seul modal.</p>
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
        <div class="card teacher-reports-panel">
            <div class="card-body py-5 text-center">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h3 class="h5 fw-bold mb-2">Aucun rapport affecté pour le moment</h3>
                <p class="text-muted mb-0">Les rapports apparaîtront ici dès qu’un admin vous en affectera.</p>
            </div>
        </div>
        @else
        <div class="card teacher-reports-panel overflow-hidden">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h2 class="h5 fw-bold mb-1">Rapports assignés</h2>
                    <div class="text-muted small">{{ $reports->total() }} résultat(s) • page {{ $reports->currentPage() }} / {{ $reports->lastPage() }}</div>
                </div>
                <span class="teacher-badge teacher-badge-muted">{{ $currentUser->name }}</span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0 teacher-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Étudiant</th>
                            <th>Titre</th>
                            <th>Statut</th>
                            <th>Dernière remarque</th>
                            <th>Date</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                        @php
                        $previewFile = $report->latestVersion?->file_path ?? $report->file_path;
                        $previewUrl = route('reports.preview', $report);
                        $downloadUrl = route('reports.download', $report);
                        $canPreview = $previewFile && str_ends_with(strtolower($previewFile), '.pdf');
                        $reportPayloadData = base64_encode(json_encode($report->loadMissing(['student.filiere', 'teacher', 'latestVersion', 'comments.user', 'juryGroup.members'])));
                        $statusClass = match ($report->status) {
                        'Validé' => 'teacher-badge-success',
                        'Rejeté' => 'teacher-badge-warning',
                        'commenté', 'Affecté', 'Soumis' => 'teacher-badge-info',
                        default => 'teacher-badge-muted',
                        };
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $report->id }}</td>
                            <td>
                                <div class="fw-semibold">{{ $report->student?->name ?? '-' }}</div>
                                <div class="text-muted small">{{ $report->student?->matricule ?? $report->student?->email ?? '-' }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ \Illuminate\Support\Str::limit($report->title, 48) }}</div>
                                <div class="text-muted small">Filière: {{ $report->student?->filiere?->name ?? '-' }}</div>
                            </td>
                            <td><span class="teacher-badge {{ $statusClass }}">{{ $report->teacher_status ?? $report->status }}</span></td>
                            <td>
                                <div class="fw-semibold">{{ \Illuminate\Support\Str::limit($report->teacher_comment ?? 'Aucune remarque', 40) }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $report->created_at->format('d/m/Y') }}</div>
                                <div class="text-muted small">{{ $report->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-primary btn-sm px-3" data-report="{{ $reportPayloadData }}" data-preview-url="{{ $previewUrl }}" data-download-url="{{ $downloadUrl }}" data-can-preview="{{ $canPreview ? 1 : 0 }}" onclick="openTeacherReportDetails(this)">
                                    <i class="fas fa-eye me-1"></i>Voir
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

    <div class="modal fade" id="teacherReportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg">
                <div class="modal-header bg-dark text-white border-0">
                    <div>
                        <h5 class="modal-title mb-0" id="teacherReportModalTitle">Détails du rapport</h5>
                        <small class="text-white-50">Prévisualiser, télécharger, commenter puis valider ou rejeter</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-0">
                    <div class="row g-0">
                        <div class="col-lg-8 border-end">
                            <div class="p-4 border-bottom bg-light">
                                <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                                    <span id="teacherReportStatus" class="teacher-badge teacher-badge-muted">Statut</span>
                                    <span id="teacherReportVersion" class="teacher-badge teacher-badge-info">v1</span>
                                </div>
                                <h5 class="fw-bold mb-2" id="teacherReportTitleText">Rapport</h5>
                                <div class="text-muted small" id="teacherReportMeta">Chargement...</div>
                            </div>

                            <div class="p-3">
                                <iframe id="teacherReportPreviewFrame" class="teacher-preview-frame rounded-4 shadow-sm d-none" src=""></iframe>
                                <div id="teacherReportPreviewFallback" class="alert alert-light border mb-0 d-none">
                                    L'aperçu direct est disponible pour les PDF uniquement. Utilisez le téléchargement pour les autres formats.
                                </div>

                                <div class="d-flex gap-2 flex-wrap mt-3">
                                    <a id="teacherReportDownloadLink" href="#" target="_blank" class="btn btn-outline-primary">
                                        <i class="fas fa-download me-2"></i>Télécharger
                                    </a>
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="p-4">
                                <h6 class="fw-bold mb-3">Action enseignant</h6>
                                <form id="teacherReportActionForm" method="POST" action="{{ route('reports.teacher-comment', ['report' => 0]) }}" class="vstack gap-3">
                                    @csrf
                                    <input type="hidden" name="action" id="teacherReportActionField" value="commenter">
                                    <textarea name="comment" id="teacherReportCommentField" rows="8" class="form-control" placeholder="Laissez votre commentaire, appréciation ou demande de correction..." required></textarea>

                                    <div class="alert alert-info border-0 mb-0 small">
                                        Le commentaire est sauvegardé avec la décision choisie.
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-outline-primary btn-lg" onclick="submitTeacherReportAction('commenter')">
                                            <i class="fas fa-pen me-2"></i>Sauvegarder le commentaire
                                        </button>
                                        <button type="button" class="btn btn-success btn-lg" onclick="submitTeacherReportAction('valider')">
                                            <i class="fas fa-check me-2"></i>Valider
                                        </button>
                                        <button type="button" class="btn btn-danger btn-lg" onclick="submitTeacherReportAction('rejeter')">
                                            <i class="fas fa-times me-2"></i>Rejeter
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentTeacherReportId = null;

        function openTeacherReportDetails(button) {
            const rawPayload = button.dataset.report || '';
            let report;

            try {
                const decodedPayload = rawPayload.startsWith('ey') ? atob(rawPayload) : rawPayload;
                report = JSON.parse(decodedPayload);
            } catch (error) {
                console.error('Impossible de parser les données du rapport :', error);
                return;
            }

            currentTeacherReportId = report.id;

            const latestVersion = report.latest_version || {};
            const student = report.student || {};
            const filiere = student.filiere || {};

            const previewUrl = button.dataset.previewUrl || '';
            const downloadUrl = button.dataset.downloadUrl || previewUrl;
            const canPreview = button.dataset.canPreview === '1';
            const modalEl = document.getElementById('teacherReportModal');
            const modalTitle = document.getElementById('teacherReportModalTitle');
            const statusBadge = document.getElementById('teacherReportStatus');
            const versionBadge = document.getElementById('teacherReportVersion');
            const titleText = document.getElementById('teacherReportTitleText');
            const metaText = document.getElementById('teacherReportMeta');
            const previewFrame = document.getElementById('teacherReportPreviewFrame');
            const previewFallback = document.getElementById('teacherReportPreviewFallback');
            const downloadLink = document.getElementById('teacherReportDownloadLink');
            const commentField = document.getElementById('teacherReportCommentField');
            const actionForm = document.getElementById('teacherReportActionForm');

            modalTitle.textContent = `Rapport #${report.id}`;
            titleText.textContent = report.title || 'Rapport';
            metaText.textContent = `Étudiant: ${student.name || '-'} • Filière: ${filiere.name || '-'} • Créé le ${new Date(report.created_at).toLocaleString()}`;
            statusBadge.textContent = report.teacher_status || report.status || 'En attente';
            versionBadge.textContent = latestVersion.version || 'v1';
            downloadLink.href = downloadUrl || '#';
            commentField.value = report.teacher_comment || '';
            actionForm.action = `{{ url('/reports') }}/${report.id}/teacher-comment`;

            if (canPreview && previewUrl) {
                previewFrame.classList.remove('d-none');
                previewFallback.classList.add('d-none');
                previewFrame.src = previewUrl;
            } else {
                previewFrame.src = '';
                previewFrame.classList.add('d-none');
                previewFallback.classList.remove('d-none');
            }

            bootstrap.Modal.getOrCreateInstance(modalEl).show();

            modalEl.addEventListener('hidden.bs.modal', () => {
                previewFrame.src = '';
                currentTeacherReportId = null;
            }, {
                once: true
            });
        }

        function submitTeacherReportAction(action) {
            const form = document.getElementById('teacherReportActionForm');
            const actionField = document.getElementById('teacherReportActionField');

            if (!currentTeacherReportId || !form || !actionField) {
                return;
            }

            actionField.value = action;
            form.submit();
        }

    </script>
</x-app-layout>
